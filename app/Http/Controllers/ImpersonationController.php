<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Flux\Flux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function start(User $user): RedirectResponse
    {
        // Ensure user is authorized to impersonate (e.g., is an admin)
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        // Cannot impersonate another admin
        if ($user->hasRole('admin')) {
            abort(403, 'You cannot impersonate another administrator.');
        }

        // Cannot impersonate self
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot impersonate yourself.');
        }

        session()->put([
            'impersonator_id' => auth()->id(),
            'impersonator_name' => auth()->user()->name,
        ]);

        Auth::login($user);

        session()->flash('impersonation_success', __('You are now impersonating :name.', ['name' => $user->name]));

        return redirect()->route('admin.dashboard');
    }

    /**
     * Stop impersonating a user.
     *
     * @return void
     */
    public function stop(): void
    {
        $impersonatorId = session()->get('impersonator_id');

        if (! $impersonatorId) {
            abort(403);
        }

        Auth::loginUsingId($impersonatorId);

        session()->forget('impersonator_id');
        session()->forget('impersonator_name');
    }
} 