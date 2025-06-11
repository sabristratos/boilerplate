<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Facades\ActivityLogger;
use App\Facades\Settings;
use App\Models\Attachment;
use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Flux\Flux;

/**
 * Attachment management component
 */
#[Layout('components.layouts.admin')]
class AttachmentManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public mixed $file = null;
    public ?string $collection = null;
    public string $search = '';
    public int $perPage = 10;
    public ?Attachment $attachmentToReplace = null;
    public mixed $replacementFile = null;
    public bool $showingReplaceModal = false;

    /**
     * Get the validation rules for file uploads.
     * These rules are dynamically generated based on system settings.
     *
     * @return array<string, string>
     */
    protected function getFileValidationRules(): array
    {
        $maxSizeKb = (int)Settings::get('attachments_max_upload_size_kb', 10240);
        $allowedExtensionsSetting = Settings::get('attachments_allowed_extensions', '');

        $rules = ['required', 'file', "max:{$maxSizeKb}"];

        if (!empty($allowedExtensionsSetting)) {
            $extensions = str_replace(' ', '', $allowedExtensionsSetting);
            $rules[] = "mimes:{$extensions}";
        }

        return ['file' => implode('|', $rules)];
    }

    /**
     * Get the validation rules for replacement file uploads.
     *
     * @return array<string, string>
     */
    protected function getReplacementFileValidationRules(): array
    {
        $rules = $this->getFileValidationRules();
        return ['replacementFile' => $rules['file']];
    }


    /**
     * @return array<string,string>
     */
    protected function rules(): array
    {
        return array_merge(
            $this->getFileValidationRules(),
            $this->getReplacementFileValidationRules()
        );
    }

    /**
     * Upload a new file and create an attachment record.
     *
     * @param AttachmentService $attachmentService
     * @return void
     */
    public function uploadFile(AttachmentService $attachmentService): void
    {
        Gate::authorize('create-attachments');
        $this->validate($this->getFileValidationRules());

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            Flux::toast(
                text: __('User not authenticated.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        try {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $this->file;
            $attachment = $attachmentService->upload(
                $uploadedFile,
                $user,
                $this->collection
            );

            ActivityLogger::logCreated(
                $attachment,
                $user,
                [
                    'filename' => $attachment->filename,
                    'size' => $attachment->size,
                    'mime_type' => $attachment->mime_type,
                    'collection' => $attachment->collection,
                ],
                'attachment'
            );

            $this->reset('file', 'collection');
            Flux::toast(
                text: __('File uploaded successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->resetPage();
            $this->dispatch('lightbox:refresh');
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Failed to upload file:') . ' ' . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Show the modal to replace an existing attachment.
     *
     * @param int $attachmentId
     * @return void
     */
    public function showReplaceModal(int $attachmentId): void
    {
        $attachment = Attachment::find($attachmentId);
        if (!$attachment) {
            Flux::toast(
                text: __('Attachment not found.'),
                heading: __('Error'),
                variant: 'danger'
            );
            $this->showingReplaceModal = false;
            return;
        }
        $this->attachmentToReplace = $attachment;
        $this->showingReplaceModal = true;
        $this->resetErrorBag();
        $this->replacementFile = null;
    }

    /**
     * Process the replacement of an attachment file.
     *
     * @param AttachmentService $attachmentService
     * @return void
     */
    public function processReplace(AttachmentService $attachmentService): void
    {
        if (!$this->attachmentToReplace) {
            Flux::toast(
                text: __('No attachment selected for replacement.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        $this->validate($this->getReplacementFileValidationRules());

        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            Flux::toast(
                text: __('User not authenticated for this action.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        try {
            /** @var UploadedFile $newUploadedFile */
            $newUploadedFile = $this->replacementFile;

            $originalData = [
                'filename' => $this->attachmentToReplace->filename,
                'size' => $this->attachmentToReplace->size,
                'mime_type' => $this->attachmentToReplace->mime_type,
            ];

            $updatedAttachment = $attachmentService->replace(
                $this->attachmentToReplace,
                $newUploadedFile
            );

            ActivityLogger::logUpdated(
                $updatedAttachment,
                $user,
                [
                    'action' => 'replaced',
                    'old_details' => $originalData,
                    'new_details' => [
                        'filename' => $updatedAttachment->filename,
                        'size' => $updatedAttachment->size,
                        'mime_type' => $updatedAttachment->mime_type,
                    ],
                    'collection' => $updatedAttachment->collection,
                ],
                'attachment'
            );

            $this->closeReplaceModal();
            Flux::toast(
                text: __('File replaced successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->dispatch('$refresh');
            $this->dispatch('lightbox:refresh');
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Failed to replace file:') . ' ' . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Delete an attachment.
     *
     * @param Attachment $attachment
     * @param AttachmentService $attachmentService
     * @return void
     */
    public function delete(Attachment $attachment, AttachmentService $attachmentService): void
    {
        Gate::authorize('delete-attachments');
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            Flux::toast(
                text: __('User not authenticated for this action.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        try {
            ActivityLogger::logDeleted(
                $attachment,
                $user,
                [
                    'filename' => $attachment->filename,
                    'size' => $attachment->size,
                    'mime_type' => $attachment->mime_type,
                    'collection' => $attachment->collection,
                ],
                'attachment'
            );
            $attachmentService->delete($attachment);
            Flux::toast(
                text: __('File deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->resetPage();
            $this->dispatch('lightbox:refresh');
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Failed to delete file:') . ' ' . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Close the replacement modal and reset related properties.
     * @return void
     */
    public function closeReplaceModal(): void
    {
        $this->showingReplaceModal = false;
        $this->attachmentToReplace = null;
        $this->replacementFile = null;
        $this->resetErrorBag();
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        $attachments = Attachment::query()
            ->when((bool)$this->search, function ($query) {
                $query->where('filename', 'like', '%' . $this->search . '%')
                    ->orWhere('collection', 'like', '%' . $this->search . '%')
                    ->orWhere('mime_type', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.attachment-management', [
            'attachments' => $attachments,
        ]);
    }
}
