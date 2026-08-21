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
        // Tự động quét và áp dụng giảm giá từ 2% - 10% cho 5 sản phẩm có lượt mua ít nhất vào mỗi Thứ Hai lúc 00:00
        $schedule->command('products:auto-discount-low-sales --count=5 --min=2 --max=10')->weeklyOn(1, '00:00');
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
