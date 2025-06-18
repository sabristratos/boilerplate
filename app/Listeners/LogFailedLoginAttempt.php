<?php

namespace App\Listeners;

use App\Facades\Settings;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogFailedLoginAttempt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        /**
         * The request instance.
         */
        public Request $request
    )
    {
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        if (Settings::get('log_failed_login_attempts', true)) {
            $user = $event->user;
            Log::warning('Failed login attempt', [
                'guard' => $event->guard,
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'credentials' => $event->credentials,
                'user_id' => $user instanceof User ? $user->id : null,
            ]);
        }
    }
}
