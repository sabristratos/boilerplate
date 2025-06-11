<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Http\Controllers\ImpersonationController;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class StopImpersonation extends Component
{
    public function stopImpersonating(): Redirector
    {
        app(ImpersonationController::class)->stop();

        return redirect()->route('admin.users.index');
    }

    public function render(): View
    {
        return view('livewire.stop-impersonation');
    }
} 