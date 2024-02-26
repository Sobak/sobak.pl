<?php

namespace App\Console\Commands;

use App\Models\RedirectLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class RedirectsSummary extends Command
{
    protected $signature = 'redirects:summary';

    protected $description = 'Shows summary for the redirect logs';

    public function handle()
    {
        $redirectsSummary = RedirectLog::query()
            ->select(['redirect_logs.*', DB::raw('COUNT(*) AS total')])
            ->orderBy('total', 'desc')
            ->groupBy('source_url')
            ->get();

        $tableRows = [];
        foreach ($redirectsSummary as $redirect) {
            $tableRows[] = [
                $redirect->source_url,
                $redirect->target_url,
                $redirect->total,
                $this->getLastUsedDate($redirect->target_url),
            ];
        }

        $this->table(
            ['Source URL', 'Target URL', 'Total redirects', 'Last used'],
            $tableRows
        );
    }

    private function getLastUsedDate(string $targetUrl): string
    {
        $lastLog = RedirectLog::query()
            ->select('created_at')
            ->where('target_url', '=', $targetUrl)
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();

        if ($lastLog === null) {
            return 'not used';
        }

        return $lastLog->created_at->format('Y-m-d');
    }
}
