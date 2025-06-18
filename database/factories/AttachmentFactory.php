<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        $file = UploadedFile::fake()->create(Str::random(10) . '.jpg', 100);
        $path = $file->store('attachments', 'public');

        return [
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'collection' => 'default',
            'meta' => [],
        ];
    }
} 