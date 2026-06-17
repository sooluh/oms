<?php

namespace App\Console\Commands;

use App\Models\SearchLog;
use App\Models\ServiceRecord;
use Illuminate\Console\Command;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset';

    protected $description = 'Reset demo data by truncating records and logs';

    public function handle(): int
    {
        if (! config('app.is_demo')) {
            $this->warn('APP_IS_DEMO is not enabled. Skipping reset.');

            return Command::SUCCESS;
        }

        ServiceRecord::query()->truncate();
        SearchLog::query()->truncate();

        $this->info('Demo data reset successfully.');

        return Command::SUCCESS;
    }
}
