<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Facades\ActivityLogger;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

/**
 * Attachment management component
 */
#[Layout('components.layouts.admin')]
class AttachmentManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 24;

    public function delete(Attachment $attachment, AttachmentService $attachmentService): void
    {
        Gate::authorize('delete', $attachment);
        
        try {
            // Log before deleting
            ActivityLogger::logDeleted($attachment, Auth::user(), ['filename' => $attachment->filename], 'attachment');
            
            // Delete the attachment
            $attachmentService->delete($attachment);
            
            Flux::toast(
                text: __('Attachment deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Failed to delete attachment:') . ' ' . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        $query = Attachment::query()
            ->when($this->search, function ($q) {
                $q->where('filename', 'like', '%' . $this->search . '%')
                  ->orWhere('meta->title', 'like', '%' . $this->search . '%')
                  ->orWhere('meta->alt_text', 'like', '%' . $this->search . '%');
            })
            ->latest();

        $attachments = $query->paginate($this->perPage);

        return view('livewire.admin.attachment-management', [
            'attachments' => $attachments,
        ]);
    }
}
