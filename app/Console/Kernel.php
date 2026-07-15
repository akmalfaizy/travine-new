<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingReminderMail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Send reminder email one day before departure at 09:00 AM
        $schedule->call(function () {
            $bookings = Booking::whereDate('departure_date', now()->addDay()->toDateString())
                               ->where('payment_status', 'paid')
                               ->get();
            foreach ($bookings as $booking) {
                Mail::to($booking->email)->send(new BookingReminderMail($booking));
                // Log the reminder
                \App\Models\NotificationLog::create([
                    'booking_id' => $booking->id,
                    'recipient'  => $booking->email,
                    'type'       => 'email',
                    'status'     => 'success',
                    'sent_at'    => now(),
                ]);
            }
        })->dailyAt('09:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        // require base_path('routes/console.php'); // optional
    }
}
?>
