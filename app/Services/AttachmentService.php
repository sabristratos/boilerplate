<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Settings;
use App\Interfaces\Attachable;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Interfaces\EncodedImageInterface;

class AttachmentService
{
    /**
     * Upload a file and create an attachment record.
     *
     * @param UploadedFile $file The file to upload.
     * @param \App\Interfaces\Attachable&\Illuminate\Database\Eloquent\Model $attachable The model to attach the file to.
     * @param string|null $collection Optional collection name.
     * @param array<string, mixed> $meta Optional metadata.
     * @param array<string, mixed> $options Options for storage and optimization, overriding global settings.
     * @return \App\Models\Attachment The created attachment record.
     * @throws \Exception If the attachable model does not have a key, or if file type is not allowed.
     */
    public function upload(UploadedFile $file, Attachable&Model $attachable, ?string $collection = null, array $meta = [], array $options = []): Attachment
    {
        $attachableKey = $attachable->getKey();
        if ($attachableKey === null) {
            throw new \Exception(__('Attachable model must have a key.'));
        }

        $this->validateMimeType($file);

        $defaultDisk = Settings::get('attachments_default_disk', 'public');
        $diskName = $options['disk'] ?? $defaultDisk;

        $baseDir = 'attachments';
        $attachableTypeDir = Str::snake(class_basename($attachable));
        $attachableIdDir = (string) $attachableKey;
        $directory = "{$baseDir}/{$attachableTypeDir}/{$attachableIdDir}";

        $fileExtension = $file->getClientOriginalExtension() ? '.' . $file->getClientOriginalExtension() : '';
        $filenameOnDisk = Str::uuid()->toString() . $fileExtension;
        $targetPath = $directory . '/' . $filenameOnDisk;

        $storedPath = $this->storeFile($file, $targetPath, $diskName, $options, false);

        $attachment = $attachable->attachments()->create([
            'filename' => $file->getClientOriginalName(),
            'path' => $storedPath,
            'disk' => $diskName,
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize() ?: 0,
            'collection' => $collection,
            'meta' => $meta,
        ]);

        assert($attachment instanceof Attachment);

        return $attachment;
    }

    /**
     * Replace an existing attachment's file.
     *
     * @param Attachment $attachment The attachment to replace.
     * @param UploadedFile $newFile The new file.
     * @param array<string, mixed> $meta Optional metadata to update.
     * @param array<string, mixed> $options Options for storage and optimization, overriding global settings.
     * @return Attachment The updated attachment record.
     * @throws \Exception If file type is not allowed.
     */
    public function replace(Attachment $attachment, UploadedFile $newFile, array $meta = [], array $options = []): Attachment
    {
        $this->validateMimeType($newFile);

        $originalDisk = $attachment->disk;
        $originalPath = $attachment->path;

        $this->storeFile($newFile, $originalPath, $originalDisk, $options, true);

        $attachment->filename = $newFile->getClientOriginalName();
        $attachment->mime_type = $newFile->getMimeType() ?? 'application/octet-stream';
        $attachment->size = $newFile->getSize() ?: 0;
        if (!empty($meta)) {
            $currentMeta = $attachment->meta ?? [];
            $attachment->meta = array_merge($currentMeta, $meta);
        }
        $attachment->save();

        return $attachment;
    }

    /**
     * Validate the MIME type of the uploaded file against allowed types from settings.
     *
     * @param UploadedFile $file
     * @return void
     * @throws \Exception If file type is not allowed.
     */
    protected function validateMimeType(UploadedFile $file): void
    {
        $allowedMimesSetting = Settings::get('attachments_allowed_mime_types', '');
        if (!empty($allowedMimesSetting)) {
            $allowedMimes = array_map('trim', explode(',', $allowedMimesSetting));
            $fileMime = $file->getMimeType();
            if ($fileMime === null || !in_array($fileMime, $allowedMimes, true)) {
                throw new \Exception(__('File type not allowed: ') . ($fileMime ?? __('unknown')));
            }
        }
    }

    /**
     * Store a file on disk, potentially optimizing if it's an image.
     *
     * @param UploadedFile $file The file to store.
     * @param string $targetPath The full target path including filename on the disk.
     * @param string $diskName The disk to store the file on.
     * @param array<string, mixed> $options Options for storage and optimization.
     * @param bool $overwrite Whether to overwrite if the file exists.
     * @return string The path where the file was stored.
     */
    protected function storeFile(UploadedFile $file, string $targetPath, string $diskName, array $options = [], bool $overwrite = false): string
    {
        $optimizeImages = $options['optimize'] ?? (bool)Settings::get('attachments_image_optimization_enabled', true);

        if ($this->isImage($file) && $optimizeImages) {
            return $this->storeOptimizedImage($file, $targetPath, $diskName, $options, $overwrite);
        }

        $directory = dirname($targetPath);
        $filename = basename($targetPath);

        $file->storeAs($directory, $filename, ['disk' => $diskName]);
        return $targetPath;
    }

    /**
     * Store an optimized image on disk at a specific path.
     *
     * @param UploadedFile $file The image file to store.
     * @param string $targetPath The full target path including filename on the disk.
     * @param string $diskName The disk to store the file on.
     * @param array<string, mixed> $options Options for optimization.
     * @param bool $overwrite Whether to overwrite if the file exists.
     * @return string The path where the image was stored.
     */
    protected function storeOptimizedImage(UploadedFile $file, string $targetPath, string $diskName, array $options = [], bool $overwrite = false): string
    {
        $manager = new ImageManager(new GdDriver());
        $image = $manager->read($file);

        $maxWidth = $options['width'] ?? (int)Settings::get('attachments_image_max_width', 0);
        $maxHeight = $options['height'] ?? (int)Settings::get('attachments_image_max_height', 0);

        if ($maxWidth > 0 || $maxHeight > 0) {
            if ($maxWidth > 0 && $maxHeight > 0) {
                $image->cover($maxWidth, $maxHeight);
            } elseif ($maxWidth > 0) {
                $image->scaleDown(width: $maxWidth);
            } elseif ($maxHeight > 0) {
                $image->scaleDown(height: $maxHeight);
            }
        }

        $quality = $options['quality'] ?? (int)Settings::get('attachments_image_quality', 80);
        $fileExtension = pathinfo($targetPath, PATHINFO_EXTENSION) ?: $file->getClientOriginalExtension() ?: 'png';

        $encodedImage = $image->encodeByExtension($fileExtension, $quality);

        Storage::disk($diskName)->put($targetPath, $encodedImage->toString());

        return $targetPath;
    }

    /**
     * Check if a file is an image based on its MIME type.
     *
     * @param UploadedFile $file The file to check.
     * @return bool True if the file is an image, false otherwise.
     */
    protected function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return $mimeType !== null && Str::startsWith($mimeType, 'image/');
    }

    /**
     * Delete an attachment record and its associated file from storage.
     *
     * @param Attachment $attachment The attachment to delete.
     * @return bool True on success.
     * @throws \Exception If deletion fails.
     */
    public function delete(Attachment $attachment): bool
    {
        return (bool)$attachment->delete();
    }

    /**
     * Get the URL for an attachment.
     *
     * @param Attachment $attachment The attachment.
     * @return string The public URL of the attachment.
     */
    public function getUrl(Attachment $attachment): string
    {
        return $attachment->url;
    }
}
