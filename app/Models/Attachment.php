<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'collection',
        'meta',
    ];

    /**
     * Get the URL to the file.
     */
    public function getUrlAttribute(): string
    {
        $cacheKey = 'attachment_url_' . $this->id . '_' . $this->updated_at->timestamp;
        return Cache::rememberForever($cacheKey, fn() => Storage::disk($this->disk)->url($this->path));
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
            Cache::forget('attachment_url_' . $attachment->id . '_' . $attachment->getOriginal('updated_at')->timestamp);
        });

        static::updated(function ($attachment) {
            $originalUpdatedAt = $attachment->getOriginal('updated_at');
            if ($originalUpdatedAt) {
                $cacheKey = 'attachment_url_' . $attachment->id . '_' . $originalUpdatedAt->timestamp;
                Cache::forget($cacheKey);
            }
        });
    }
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'size' => 'integer',
        ];
    }
}
