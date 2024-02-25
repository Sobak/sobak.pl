<?php

namespace App\Console\Commands;

use App\Console\Adapters\ConsoleOutput;
use App\Content\Indexer;
use App\Content\IndexerException;
use App\Interfaces\OutputInterface;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\ConsoleOutput as SymfonyConsoleOutput;

class IndexContent extends Command
{
    /** @var OutputInterface Cannot define type in PHP since the base class doesn't do it */
    protected $output;

    /** @var string Cannot define type in PHP since the base class doesn't do it */
    protected $signature = 'content:index
        {--D|dry-run : Dry run does not alter the live database}
        {--N|no-assets : Skip assets processing (default in dry run mode)}';

    /** @var string Cannot define type in PHP since the base class doesn't do it */
    protected $description = 'Indexes content for the homepage';

    public function handle()
    {
        // The indexer object cannot be created in the constructor because command's
        // verbosity is not yet known at this point.
        $consoleOutput = new OutputStyle(
            new ArgvInput($this->getArguments(), new InputDefinition()),
            new SymfonyConsoleOutput()
        );
        $consoleOutput->setVerbosity($this->getOutput()->getVerbosity());

        $indexerOutput = new ConsoleOutput($consoleOutput);
        $indexer = new Indexer($indexerOutput);

        $timeStart = microtime(true);

        $this->info('Indexing started');

        try {
            $indexer->index($this->option('dry-run'), ! $this->option('no-assets'));
        } catch (IndexerException $exception) {
            $this->sendFailureNotification();
            exit($exception->getCode());
        }

        $this->sendSuccessNotification();

        $time = number_format(microtime(true) - $timeStart, 4);

        $this->info("Indexing finished in {$time}");
    }

    private function sendSuccessNotification()
    {
        $this->sendSystemNotification('Build successful', resource_path('assets/cli/pass.png'));
    }

    private function sendFailureNotification()
    {
        $this->sendSystemNotification('Build failed', resource_path('assets/cli/fail.png'));
    }

    private function sendSystemNotification($body, $icon)
    {
        if (app()->environment() !== 'local' || class_exists(NotifierFactory::class) === false) {
            return true;
        }

        $notifier = NotifierFactory::create();

        $notification =
            (new Notification())
                ->setTitle('Perception')
                ->setBody($body)
                ->setIcon($icon);

        $notifier->send($notification);

        return true;
    }
}
