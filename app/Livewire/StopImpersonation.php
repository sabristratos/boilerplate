<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ImpersonationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class StopImpersonation extends Component
{
    public function stopImpersonating(ImpersonationService $impersonationService): Redirector
    {
        if ($impersonationService->isImpersonating()) {
            $impersonationService->leave();
        }

        return $this->redirect(route('admin.dashboard'), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.stop-impersonation');
    }
} 