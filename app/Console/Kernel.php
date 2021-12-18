<?php

namespace App\Console;

use App\Models\Products;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $products = Products::all();
            foreach ($products as $key => $value) {
                $expirationDate = strtotime($value['timestamp-4']);
                if (time() >= $expirationDate) {
                    $imgPath = storage_path() . '/app/images/' . Products::find($value['id'])['imgName'];
                    unlink($imgPath);
                    return Products::destroy($value['id']);
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
