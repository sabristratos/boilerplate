<?php

namespace App\Models\Traits;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasAttachments
{
    /**
     * Get all attachments for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Attachment, $this>
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get attachments for a specific collection.
     */
    public function getAttachments(?string $collection = null)
    {
        return $this->attachments()
            ->when($collection, function ($query) use ($collection) {
                return $query->where('collection', $collection);
            })
            ->get();
    }

    /**
     * Add an attachment to the model.
     */
    public function addAttachment(UploadedFile $file, ?string $collection = null, array $meta = [], array $options = [])
    {
        $service = app(AttachmentService::class);
        return $service->upload($file, $this, $collection, $meta, $options);
    }

    /**
     * Remove an attachment from the model.
     */
    public function removeAttachment(Attachment $attachment)
    {
        if ($this->attachments()->where('id', $attachment->id)->exists()) {
            $service = app(AttachmentService::class);
            return $service->delete($attachment);
        }

        return false;
    }

    /**
     * Remove all attachments from the model.
     */
    public function removeAllAttachments(?string $collection = null)
    {
        $service = app(AttachmentService::class);
        $attachments = $this->attachments()
            ->when($collection, function ($query) use ($collection) {
                return $query->where('collection', $collection);
            })
            ->get();

        foreach ($attachments as $attachment) {
            /** @var Attachment $attachment */
            $service->delete($attachment);
        }

        return true;
    }
}
