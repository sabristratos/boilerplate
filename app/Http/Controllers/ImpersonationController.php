<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImpersonationService;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Start impersonating a user.
     *
     * @throws \Exception
     */
    public function start(User $user, ImpersonationService $impersonationService): RedirectResponse
    {
        $this->authorize('impersonate', $user);

        $impersonator = auth()->user();

        $impersonationService->impersonate($impersonator, $user);

        session()->flash('impersonation_success', __('You are now impersonating :name.', ['name' => $user->name]));

        return redirect()->route('admin.dashboard');
    }

    /**
     * Stop impersonating a user.
     */
    public function stop(ImpersonationService $impersonationService): RedirectResponse
    {
        if (! $impersonationService->isImpersonating()) {
            abort(403);
        }

        $impersonationService->leave();

        return redirect()->route('admin.dashboard');
    }
} 