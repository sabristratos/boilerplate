<?php

namespace App\Console\Commands;

use App\Models\NotificationDigest;
use App\Notifications\User\DailyDigestNotification;
use App\Notifications\User\WeeklyDigestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendNotificationDigests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notification-digests {--frequency=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily and weekly notification digests';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $frequency = $this->option('frequency');

        if ($frequency === 'all' || $frequency === 'daily') {
            $this->sendDigests('daily');
        }

        if ($frequency === 'all' || $frequency === 'weekly') {
            $this->sendDigests('weekly');
        }

        $this->info('Notification digests sent successfully.');
    }

    private function sendDigests(string $frequency): void
    {
        $digests = NotificationDigest::where('frequency', $frequency)
            ->whereNull('processed_at')
            ->get()
            ->groupBy(['notifiable_type', 'notifiable_id']);

        /** @var string $notifiableType */
        foreach ($digests as $notifiableType => $notifiables) {
            foreach ($notifiables as $notifiableId => $userDigests) {
                $notifiable = ($notifiableType)::find($notifiableId);
                if (!$notifiable) {
                    continue;
                }

                $notification = $frequency === 'daily'
                    ? new DailyDigestNotification($userDigests)
                    : new WeeklyDigestNotification($userDigests);

                Notification::send($notifiable, $notification);

                NotificationDigest::whereIn('id', $userDigests->pluck('id'))->update([
                    'processed_at' => now(),
                ]);
            }
        }
    }
}
