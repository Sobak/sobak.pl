<?php

declare(strict_types=1);

namespace App\Content\Indexing;

use App\Content\Indexing\Indexers\CreatesRedirects;
use App\Content\Indexing\Indexers\PageIndexer;
use App\Content\Indexing\Indexers\PostIndexer;
use App\Content\Indexing\Indexers\ProjectIndexer;
use App\Content\Translation\TranslationsIndexerService;
use App\Models\Post;
use DirectoryIterator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SplFileInfo;
use Symfony\Component\Console\Output\NullOutput;

class Indexer
{
    use CreatesRedirects;

    /** @var ContentTypeIndexerInterface[] */
    private array $contentTypeIndexers;

    private IndexerOutputInterface $output;

    public function __construct(IndexerOutputInterface $output)
    {
        $this->output = $output;

        $this->registerContentTypeIndexer('pages', new PageIndexer($output));
        // Posts can optionally link to projects, so we have to index them in right order
        $this->registerContentTypeIndexer('projects', new ProjectIndexer($output));
        $this->registerContentTypeIndexer('posts', new PostIndexer($output));
    }

    public function index(bool $isDryRun, bool $enableAssetsProcessing): void
    {
        $this->prepareIndexerDatabase($isDryRun);
        $translations = $this->loadTranslations();
        $this->indexContentTypes($translations);
        $this->indexRedirects($isDryRun);
        $this->cacheBlogStats($isDryRun);
        $this->processAssets($isDryRun, $enableAssetsProcessing);
        $this->switchWebsiteDatabase($isDryRun);
    }

    public function registerContentTypeIndexer(string $directoryName, ContentTypeIndexerInterface $indexer): void
    {
        $this->contentTypeIndexers[$directoryName] = $indexer;
    }

    private function prepareIndexerDatabase(bool $isDryRun): void
    {
        if ($isDryRun === false) {
            $this->output->line('Initializing temporary indexer database');
        } else {
            $this->output->line('Running in dry run mode - no changes will be applied');
        }

        $indexerDatabase = config('database.connections.indexer.database');

        // There might be indexer database lying
        // around after failed validation or dry run
        if (file_exists($indexerDatabase)) {
            $this->output->line('Removed old indexer database', IndexerOutputInterface::VERBOSITY_VERBOSE);
            unlink($indexerDatabase);
        }

        touch($indexerDatabase);

        $originalConnection = config('database.default');

        // Write to "indexer" database for the duration of a script
        config()->set('database.default', 'indexer');

        // Disconnect from the original connection & forget the cache
        DB::purge($originalConnection);

        Artisan::call(
            'migrate',
            [
                '--database' => 'indexer',
                '--force' => true,
            ],
            new NullOutput()
        );
    }

    private function switchWebsiteDatabase(bool $isDryRun): void
    {
        if ($isDryRun) {
            $this->output->line('Dry run finished - website database has not been changed');
            return;
        }

        $this->output->line('Switching website database to new build');

        $indexerDatabase = config('database.connections.indexer.database');
        $websiteDatabase = config('database.connections.website.database');

        DB::disconnect('indexer');

        // Website database doesn't exist on first run
        if (file_exists($websiteDatabase)) {
            unlink($websiteDatabase);
        }

        rename($indexerDatabase, $websiteDatabase);
    }

    private function loadTranslations(): array
    {
        $translationsPath = config('content.path') . '/translations.php';

        if (file_exists($translationsPath) === false) {
            $this->output->error('No translations.php file found');

            throw new IndexerException('', 1);
        }

        $this->output->line("\nReading translations");

        return require $translationsPath;
    }

    private function indexContentTypes(array $translations): void
    {
        $iterator = new DirectoryIterator(config('content.path'));

        $directories = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir() && $fileInfo->isDot() === false) {
                $directories[] = $fileInfo->getBasename();
            }
        }

        $directories = array_intersect_key(array_keys($this->contentTypeIndexers), $directories);

        foreach ($directories as $directory) {
            $this->indexContentType($directory, $translations);
        }
    }

    private function indexContentType(string $contentType, array $translations): void
    {
        if (isset($this->contentTypeIndexers[$contentType]) === false) {
            return;
        }

        $indexer = $this->contentTypeIndexers[$contentType];

        $this->output->line("\nIndexing $contentType");

        $iterator = new DirectoryIterator(config('content.path') . '/' . $contentType);

        /** @var SplFileInfo[] $files */
        $files = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'md') {
                // See https://bugs.php.net/bug.php?id=49755
                $files[] = clone $fileInfo;
            }
        }

        usort($files, function (SplFileInfo $a, SplFileInfo $b) {
            return $a->getRealPath() <=> $b->getRealPath();
        });

        $indexer->setTranslations($translations);

        foreach ($files as $fileInfo) {
            $indexer->index($fileInfo);
        }

        $this->output->indentedLine('');
        $this->output->indentedLine("Verifying that all translations for $contentType exist");

        TranslationsIndexerService::registerTranslatableModel($indexer::getModelClass());

        $translationsIndexer = new TranslationsIndexerService($this->output);
        $translationsIndexer->ensureAllTranslationsExist($indexer::getTranslatableType());
    }

    private function indexRedirects(bool $isDryRun): void
    {
        if ($isDryRun) {
            $this->output->line("\nSkipped indexing redirects");
            return;
        }

        $redirectsPath = config('content.path') . '/redirects.php';

        if (file_exists($redirectsPath) === false) {
            $this->output->warning('No redirects.php file found');
            return;
        }

        $this->output->line("\nIndexing redirects");

        $redirects = require $redirectsPath;

        foreach ($redirects as $from => $to) {
            $this->output->indentedLine("> $from => $to", 2, IndexerOutputInterface::VERBOSITY_VERBOSE);

            $this->createRedirect($from, $to);
        }
    }

    private function cacheBlogStats(bool $isDryRun): void
    {
        if ($isDryRun) {
            return;
        }

        $wordCountQuery = DB::raw("SUM(LENGTH(content) - LENGTH(REPLACE(content, ' ', '')) + 1) AS words");

        Cache::rememberForever('blog_stats', function () use ($wordCountQuery) {
            return [
                'total_posts' => Post::count(),
                'total_words' => Post::select($wordCountQuery)->first()['words'],
            ];
        });
    }

    private function processAssets(bool $isDryRun, bool $enableAssetsProcessing): void
    {
        if ($enableAssetsProcessing === false || $isDryRun) {
            $this->output->line("\nSkipped assets processing\n");
            return;
        }

        $this->output->line("\nProcessing assets");

        $iterator = new DirectoryIterator(config('content.path') . '/assets/');

        $targetPath = public_path('assets/images/');

        if (is_dir($targetPath) === false) {
            mkdir($targetPath, 0777, true);
        }

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $this->output->indentedLine($fileInfo->getFilename());

                $this->copyAsset($fileInfo, $targetPath);
            }
        }

        $this->output->line('');
    }

    private function copyAsset(SplFileInfo $file, string $targetPath): void
    {
        $copy = copy($file->getPathname(), $targetPath . $file->getFilename());

        if ($copy === false) {
            $this->output->indentedLine('> FAIL when copying the file', 2);
        }
    }
}
