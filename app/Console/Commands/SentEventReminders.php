<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Illuminate\Support\Str;

use App\Notifications\EventReminderNotification;

class SentEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Event Reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()]);

        $eventCount = $events->count(); 
        $eventLabel = Str::plural('event', $eventCount);

        $this->info("Found {$eventCount} ${eventLabel}");

        $events->each(
            fn ($event) => $event->attendees->each(
                fn($attendee) => $attendee->user->notify(
                    new EventReminderNotification(
                        $event
                    )
                )            
            )
        );

        $this->info("Reminder notification sent successfully!");
    }
}
