<?php

namespace App\Models\Traits;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait HasAttachments
{
    /**
     * Get all attachments for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<\App\Models\Attachment>
     */
    public function attachments(): MorphToMany
    {
        return $this->morphToMany(Attachment::class, 'attachable');
    }

    /**
     * Get attachments for a specific collection.
     */
    public function getAttachments(?string $collection = null)
    {
        return $this->attachments()
            ->when($collection, fn($query) => $query->where('collection', $collection))
            ->get();
    }

    /**
     * Add an attachment to the model.
     */
    public function addAttachment(UploadedFile $file, ?string $collection = null, array $meta = [], array $options = [])
    {
        $service = app(AttachmentService::class);
        $attachment = $service->upload($file, $collection, $meta, $options);
        $this->attachments()->attach($attachment->id);
        return $attachment;
    }

    /**
     * Remove an attachment from the model.
     */
    public function removeAttachment(Attachment $attachment)
    {
        // Detach the attachment from the current model.
        $this->attachments()->detach($attachment->id);

        // Check if the attachment is still linked to any other models.
        $isOrphaned = DB::table('attachables')
            ->where('attachment_id', $attachment->id)
            ->doesntExist();
            
        // If it's orphaned, delete the attachment record and the physical file.
        if ($isOrphaned) {
            $attachment->delete();
        }

        return true;
    }

    /**
     * Remove all attachments from the model.
     */
    public function removeAllAttachments(?string $collection = null)
    {
        $attachments = $this->attachments()
            ->when($collection, fn($query) => $query->where('collection', $collection))
            ->get();
            
        $this->attachments()->detach($attachments->pluck('id'));

        return true;
    }
}
