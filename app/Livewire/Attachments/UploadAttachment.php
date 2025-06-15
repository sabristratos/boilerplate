<?php

namespace App\Livewire\Attachments;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Facades\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Flux\Flux;
use App\Interfaces\Attachable;

/**
 * A Livewire component for uploading files and attaching them to an Eloquent model.
 * This component allows customization of collection, labels, image optimization,
 * and dispatches an event upon successful upload.
 */
class UploadAttachment extends Component
{
    use WithFileUploads;

    /**
     * The file to upload.
     * Validation rules are dynamically fetched based on settings.
     */
    public mixed $file = null;

    /**
     * The collection to assign the attachment to.
     */
    public ?string $collection = null;

    /**
     * The model to attach the file to.
     * @var (Model&Attachable)|null
     */
    public ?Model $model;

    /**
     * Whether to show the collection input field.
     */
    public bool $showCollectionInput = true;

    /**
     * Whether to show the main label for the component.
     */
    public bool $showLabel = true;

    /**
     * The main label text for the component.
     */
    public string $label;

    /**
     * The text for the upload button.
     */
    public string $buttonText;

    /**
     * Whether to optimize images during upload.
     * Defaults to the global 'attachments_image_optimization_enabled' setting if not overridden.
     */
    public bool $optimizeImages;

    /**
     * The quality for image optimization (1-100).
     * Defaults to the global 'attachments_image_quality' setting if not overridden.
     */
    public int $imageQuality;

    /**
     * The event to dispatch after a successful upload.
     */
    public string $uploadedEvent;

    /**
     * Get the validation rules for the file upload.
     *
     * @return array<string, string>
     */
    protected function getFileUploadRules(): array
    {
        $maxSizeKb = (int) Settings::get('attachments_max_upload_size_kb', 10240);
        $allowedMimesSetting = Settings::get('attachments_allowed_mime_types', '');

        $rules = ['required', 'file', "max:{$maxSizeKb}"];

        if (!empty($allowedMimesSetting)) {
            $extensions = collect(explode(',', $allowedMimesSetting))
                ->map(fn($mime) => trim(explode('/', $mime)[1] ?? ''))
                ->filter()
                ->implode(',');
            if(!empty($extensions)) {
                $rules[] = "mimes:{$extensions}";
            }
        }
        return ['file' => implode('|', $rules)];
    }

    /**
     * Dynamically define validation rules.
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return $this->getFileUploadRules();
    }

    /**
     * Custom validation messages.
     * @return array<string,string>
     */
    protected function messages(): array
    {
        $maxSizeKb = (int) Settings::get('attachments_max_upload_size_kb', 10240);
        $allowedMimesSetting = Settings::get('attachments_allowed_mime_types', '');
        $extensions = collect(explode(',', $allowedMimesSetting))
            ->map(fn($mime) => trim(explode('/', $mime)[1] ?? ''))
            ->filter()
            ->implode(',');

        return [
            'file.required' => __('Please select a file to upload.'),
            'file.file' => __('The uploaded item must be a file.'),
            'file.max' => __('The file may not be greater than :max kilobytes.', ['max' => $maxSizeKb / 1024]),
            'file.mimes' => __('The file type is not allowed. Allowed types: :values.', ['values' => $extensions]),
        ];
    }

    /**
     * Mount the component and initialize its properties.
     *
     * @param (Model&Attachable)|null $model The Eloquent model to attach files to.
     * @param string|null $collection Optional collection name for the attachment.
     * @param bool $showCollectionInput Whether to display the collection input field.
     * @param bool $showLabel Whether to display the main component label.
     * @param string $label The main label for the component.
     * @param string $buttonText Text for the upload button.
     * @param bool|null $optimizeImages Override for image optimization. Falls back to global setting if null.
     * @param int|null $imageQuality Override for image quality. Falls back to global setting if null.
     * @param string $uploadedEvent Event name to dispatch on successful upload.
     * @return void
     */
    public function mount(
        $model = null,
        ?string $collection = null,
        bool $showCollectionInput = true,
        bool $showLabel = true,
        string $label = 'Upload File',
        string $buttonText = 'Upload',
        ?bool $optimizeImages = null,
        ?int $imageQuality = null,
        string $uploadedEvent = 'attachment-uploaded'
    ): void {
        $this->model = $model;
        $this->collection = $collection;
        $this->showCollectionInput = $showCollectionInput;
        $this->showLabel = $showLabel;
        $this->label = __($label);
        $this->buttonText = __($buttonText);

        if ($optimizeImages !== null) {
            $this->optimizeImages = $optimizeImages;
        } else {
            $this->optimizeImages = (bool) Settings::get('attachments_image_optimization_enabled', true);
        }

        if ($imageQuality !== null) {
            $this->imageQuality = $imageQuality;
        } else {
            $this->imageQuality = (int) Settings::get('attachments_image_quality', 80);
        }
        $this->uploadedEvent = $uploadedEvent;
    }

    /**
     * Process the file upload.
     * Validates the file, uses AttachmentService to store it, and dispatches events.
     *
     * @return void
     */
    public function processUpload(): void
    {
        if (!$this->model) {
            Flux::toast(
                text: __('No model provided to attach the file to.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        $this->validate();

        $service = app(AttachmentService::class);

        try {
            /** @var UploadedFile $uploadedFileInstance */
            $uploadedFileInstance = $this->file;

            $attachment = $service->upload(
                $uploadedFileInstance,
                $this->model,
                $this->collection,
                [],
                [
                    'optimize' => $this->optimizeImages,
                    'quality' => $this->imageQuality,
                ]
            );

            $this->reset('file');
            $this->dispatch('file-uploaded-reset-upload-attachment');


            Flux::toast(
                text: __('File uploaded successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

            $this->dispatch($this->uploadedEvent, attachmentId: $attachment->id, collection: $this->collection);
        } catch (\Throwable $e) {
            Log::error(__('Failed to upload file for UploadAttachment component: ') . $e->getMessage(), ['exception' => $e]);
            Flux::toast(
                text: __('Failed to upload file. Please ensure the file type and size are correct or try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.attachments.upload-attachment');
    }
}
