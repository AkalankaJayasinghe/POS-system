<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Clean up old session data daily
        $schedule->call(function () {
            DB::table('sessions')->where('last_activity', '<', now()->subDays(7)->getTimestamp())->delete();
            Log::info('Old session data cleaned up');
        })->daily();

        // Optimize database weekly
        $schedule->call(function () {
            try {
                DB::statement('VACUUM;');
                Log::info('Database optimized');
            } catch (\Exception $e) {
                Log::error('Database optimization failed: ' . $e->getMessage());
            }
        })->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
