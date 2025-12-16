<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Tự động backup database mỗi ngày lúc 2:00 AM (UR-04.4)
        $schedule->command('backup:auto')
                 ->dailyAt('02:00')
                 ->timezone('Asia/Ho_Chi_Minh')
                 ->appendOutputTo(storage_path('logs/backup.log'));
        
        // Có thể thêm backup mỗi 12 giờ nếu cần
        // $schedule->command('backup:auto')->twiceDaily(2, 14);
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
