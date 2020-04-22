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
            ];
        }

        $this->table(
            ['Source URL', 'Target URL', 'Total redirects'],
            $tableRows
        );
    }
}
