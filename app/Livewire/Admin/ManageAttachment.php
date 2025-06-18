<?php

namespace App\Livewire\Admin;

use App\Facades\ActivityLogger;
use App\Models\Attachment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Flux\Flux;

#[Layout('components.layouts.admin')]
class ManageAttachment extends Component
{
    public Attachment $attachment;
    public string $title = '';
    public string $altText = '';

    public function mount(Attachment $attachment): void
    {
        Gate::authorize('update', $attachment);
        $this->attachment = $attachment;
        $this->title = $attachment->meta['title'] ?? '';
        $this->altText = $attachment->meta['alt_text'] ?? '';
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'altText' => 'nullable|string|max:255',
        ];
    }

    public function save(): void
    {
        $this->validate();
        Gate::authorize('update', $this->attachment);

        $this->attachment->meta = array_merge($this->attachment->meta, [
            'title' => $this->title,
            'alt_text' => $this->altText,
        ]);

        $this->attachment->save();

        ActivityLogger::logUpdated(
            $this->attachment,
            Auth::user(),
            ['attributes' => ['title', 'alt_text']],
            'attachment'
        );

        Flux::toast(
            text: __('Attachment updated successfully.'),
            heading: __('Success'),
            variant: 'success'
        );

        $this->redirect(route('admin.attachments'));
    }

    public function render(): View
    {
        return view('livewire.admin.manage-attachment');
    }
}
