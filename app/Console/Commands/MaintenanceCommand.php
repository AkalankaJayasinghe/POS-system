<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class MaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform system maintenance tasks (clean logs, optimize database)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting system maintenance...');

        // Clean up old logs
        $this->cleanLogs();

        // Clean up old sessions
        $this->cleanSessions();

        // Optimize the database
        $this->optimizeDatabase();

        $this->info('Maintenance completed successfully!');
    }

    /**
     * Clean up old log files.
     */
    protected function cleanLogs()
    {
        $this->info('Cleaning up old logs...');

        $logPath = storage_path('logs');
        $files = File::files($logPath);

        $count = 0;
        foreach ($files as $file) {
            // Keep the latest log file
            if ($file->getFilename() !== 'laravel.log' && $file->getMTime() < now()->subDays(3)->getTimestamp()) {
                File::delete($file->getPathname());
                $count++;
            }
        }

        $this->info("Removed {$count} old log files.");
    }

    /**
     * Clean up old sessions.
     */
    protected function cleanSessions()
    {
        $this->info('Cleaning up old sessions...');

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(7)->getTimestamp())
            ->delete();

        $this->info("Removed {$deleted} old sessions.");
    }

    /**
     * Optimize the database.
     */
    protected function optimizeDatabase()
    {
        $this->info('Optimizing database...');

        try {
            DB::statement('VACUUM;');
            $this->info('Database optimized successfully.');
        } catch (\Exception $e) {
            $this->error('Database optimization failed: ' . $e->getMessage());
        }
    }
}
