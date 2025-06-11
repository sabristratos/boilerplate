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
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    public Request $request;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Failed  $event
     * @return void
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
