<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ImageProcessingException;
use App\Exceptions\UploadFailedException;
use App\Facades\Settings;
use App\Interfaces\Attachable;
use App\Jobs\ProcessAttachmentImage;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class AttachmentService
{
    /**
     * Upload a file and create an attachment record.
     *
     * @param UploadedFile $file The file to upload.
     * @param string|null $collection Optional collection name.
     * @param array<string, mixed> $meta Optional metadata.
     * @param array<string, mixed> $options Options for storage and optimization, overriding global settings.
     * @return \App\Models\Attachment The created attachment record.
     * @throws UploadFailedException
     */
    public function upload(UploadedFile $file, ?string $collection = null, array $meta = [], array $options = []): Attachment
    {
        try {
            Log::debug('AttachmentService: Starting file upload.', [
                'original_filename' => $file->getClientOriginalName(),
                'is_valid' => $file->isValid(),
                'original_mime_type' => $file->getMimeType(),
            ]);

            $defaultDisk = Settings::get('attachments_default_disk', 'public');
            $diskName = $options['disk'] ?? $defaultDisk;

            $baseDir = 'attachments';
            $dateDir = now()->format('Y/m');
            $directory = "{$baseDir}/{$dateDir}";

            // Fix for missing mime type and extension
            $originalMimeType = $file->getMimeType();
            $mimeType = $originalMimeType ?: 'application/octet-stream';
            $originalExtension = $file->getClientOriginalExtension();
            $extension = $originalExtension ?: $file->guessExtension() ?: '';
            $fileExtension = $extension ? '.' . $extension : '';

            $filenameOnDisk = Str::uuid()->toString() . $fileExtension;
            $targetPath = $directory . '/' . $filenameOnDisk;

            $fileData = $this->storeFile($file, $directory, $filenameOnDisk, $diskName, $mimeType, $options);
            $mergedMeta = array_merge($meta, $fileData['meta']);

            $attachment = Attachment::create([
                'filename' => $file->getClientOriginalName(),
                'path' => $fileData['path'],
                'disk' => $diskName,
                'mime_type' => $mimeType,
                'size' => $fileData['size'],
                'collection' => $collection,
                'meta' => $mergedMeta,
            ]);

            if ($this->isImageMimeType($mimeType) && ($options['optimize'] ?? (bool)Settings::get('attachments_image_optimization_enabled', true))) {
                ProcessAttachmentImage::dispatch($attachment, $options);
            }

            return $attachment;
        } catch (\Exception $e) {
            Log::error('Failed to upload file.', [
                'original_filename' => $file->getClientOriginalName(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new UploadFailedException(__('Failed to upload file: :error', ['error' => $e->getMessage()]), 0, $e);
        }
    }

    /**
     * Replace an existing attachment's file.
     *
     * @param Attachment $attachment The attachment to replace.
     * @param UploadedFile $newFile The new file.
     * @param array<string, mixed> $meta Optional metadata to update.
     * @param array<string, mixed> $options Options for storage and optimization, overriding global settings.
     * @return Attachment The updated attachment record.
     * @throws UploadFailedException
     */
    public function replace(Attachment $attachment, UploadedFile $newFile, array $meta = [], array $options = []): Attachment
    {
        try {
            $originalDisk = $attachment->disk;
            $originalPath = $attachment->path;

            $fileData = $this->storeFile($newFile, $originalPath, $originalDisk, $this->getRealMimeType($newFile), $options);
            $mergedMeta = array_merge($attachment->meta ?? [], $meta, $fileData['meta']);

            $attachment->filename = $newFile->getClientOriginalName();
            $attachment->mime_type = $this->getRealMimeType($newFile) ?? 'application/octet-stream';
            $attachment->size = $fileData['size'] ?? $newFile->getSize() ?: 0;
            $attachment->meta = $mergedMeta;
            $attachment->save();

            if ($this->isImage($newFile) && ($options['optimize'] ?? (bool)Settings::get('attachments_image_optimization_enabled', true))) {
                ProcessAttachmentImage::dispatch($attachment, $options);
            }

            return $attachment;
        } catch (\Exception $e) {
            Log::error('Failed to replace file.', [
                'attachment_id' => $attachment->id,
                'original_filename' => $newFile->getClientOriginalName(),
                'exception' => $e,
            ]);

            throw new UploadFailedException(__('Failed to replace file: :error', ['error' => $e->getMessage()]), 0, $e);
        }
    }

    /**
     * Get the real MIME type of the file.
     */
    public function getRealMimeType(UploadedFile $file): ?string
    {
        // Use finfo for reliable MIME type detection, similar to the validation rule.
        if (!$file->isValid()) {
            return null;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return $file->getMimeType(); // Fallback to less reliable method
        }
        $mimeType = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);
        return $mimeType ?: null;
    }

    /**
     * Store a file on disk, potentially optimizing if it's an image.
     *
     * @param UploadedFile $file The file to store.
     * @param string $directory The directory to store the file in.
     * @param string $filename The filename to store the file as.
     * @param string $diskName The disk to store the file on.
     * @param string|null $mimeType The MIME type of the file.
     * @param array<string, mixed> $options Options for storage and optimization.
     * @return array{path: string, meta: array} The path where the file was stored and its metadata.
     * @throws ImageProcessingException|UploadFailedException
     */
    protected function storeFile(UploadedFile $file, string $directory, string $filename, string $diskName, ?string $mimeType, array $options = []): array
    {
        try {
            Log::debug('AttachmentService: Starting storeFile with storeAs.', [
                'directory' => $directory,
                'filename' => $filename,
                'diskName' => $diskName,
                'is_file_valid' => $file->isValid(),
            ]);
            
            $storedPath = $file->storeAs($directory, $filename, ['disk' => $diskName]);
            if ($storedPath === false) {
                throw new UploadFailedException(__('Failed to store file on disk using storeAs.'));
            }

            $finalSize = Storage::disk($diskName)->size($storedPath);
            $metadata = [];

            if ($this->isImageMimeType($mimeType)) {
                try {
                    $fileContents = Storage::disk($diskName)->get($storedPath);
                    if ($fileContents) {
                        $driver = extension_loaded('imagick') ? ImagickDriver::class : GdDriver::class;
                        $manager = new ImageManager($driver);
                        $image = $manager->read($fileContents);
                        $metadata = [
                            'width' => $image->width(),
                            'height' => $image->height(),
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning('AttachmentService: Failed to read image metadata during initial store.', [
                        'path' => $storedPath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            return ['path' => $storedPath, 'meta' => $metadata, 'size' => $finalSize];
        } catch (\Throwable $e) {
            if ($e instanceof UploadFailedException) {
                throw $e;
            }
            throw new UploadFailedException(__('Error storing file: :error', ['error' => $e->getMessage()]), 0, $e);
        }
    }

    /**
     * Process and optimize an image from an attachment.
     *
     * @param Attachment $attachment The attachment to process.
     * @param array<string, mixed> $options Options for optimization.
     * @throws ImageProcessingException
     */
    public function processImage(Attachment $attachment, array $options = []): void
    {
        if (!$attachment->isImage()) {
            return;
        }

        try {
            Log::debug('AttachmentService: Starting image processing.', [
                'attachment_id' => $attachment->id,
                'path' => $attachment->path,
                'disk' => $attachment->disk,
            ]);

            $driver = extension_loaded('imagick') ? ImagickDriver::class : GdDriver::class;
            $manager = new ImageManager($driver);

            $imageContents = Storage::disk($attachment->disk)->get($attachment->path);
            if (empty($imageContents)) {
                throw new ImageProcessingException('Could not read image contents from storage for optimization.');
            }

            $image = $manager->read($imageContents);

            $attachment->meta = array_merge($attachment->meta, [
                'width' => $image->width(),
                'height' => $image->height(),
            ]);

            $quality = $options['quality'] ?? (int)Settings::get('attachments_image_quality', 80);
            $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION) ?: 'jpg');
            
            Log::debug('AttachmentService: Starting image encoding.', [
                'attachment_id' => $attachment->id,
                'quality' => $quality,
                'extension' => $extension,
            ]);

            $encodedImage = $image->encodeByExtension($extension, $quality);

            // Explicitly get string content and check if it's valid
            $contents = (string) $encodedImage;

            if (is_string($contents) && !empty($contents)) {
                if (Storage::disk($attachment->disk)->put($attachment->path, $contents)) {
                    $attachment->size = strlen($contents);
                } else {
                    throw new ImageProcessingException('Failed to store optimized image, original file remains.');
                }
            } else {
                // Log if encoding results in empty/invalid data, but don't throw an error,
                // as we can just keep the original, un-optimized file.
                Log::warning('Image encoding resulted in empty or invalid content, original file remains.', [
                    'attachment_id' => $attachment->id,
                    'path' => $attachment->path,
                ]);
            }

            $attachment->save();

        } catch (\Throwable $e) {
            throw new ImageProcessingException(
                __('Failed to process image for attachment :id. Error: :error', ['id' => $attachment->id, 'error' => $e->getMessage()]),
                0,
                $e
            );
        }
    }

    /**
     * Check if a file is an image based on its MIME type.
     *
     * @param UploadedFile $file The file to check.
     * @return bool True if the file is an image, false otherwise.
     */
    protected function isImage(UploadedFile $file): bool
    {
        $mimeType = $this->getRealMimeType($file);
        return $this->isImageMimeType($mimeType);
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
        try {
            return (bool)$attachment->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete attachment.', [
                'attachment_id' => $attachment->id,
                'exception' => $e,
            ]);

            throw new \Exception(__('Failed to delete attachment.'), 0, $e);
        }
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

    protected function isImageMimeType(?string $mimeType): bool
    {
        return $mimeType !== null && Str::startsWith($mimeType, 'image/');
    }
}
