<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'collection',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'size' => 'integer',
    ];

    /**
     * Get the parent attachable model.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the URL to the file.
     */
    public function getUrlAttribute()
    {
        return \Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Determine if the attachment is an image.
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Delete the attachment from storage.
     */
    public function deleteFile()
    {
        \Storage::disk($this->disk)->delete($this->path);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($attachment) {
            $attachment->deleteFile();
        });
    }
}
