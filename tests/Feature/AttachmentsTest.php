<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_user_can_have_attachments()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a fake file
        $file = UploadedFile::fake()->image('test.jpg');

        // Add attachment to user
        $attachment = $user->addAttachment($file, 'test-collection');

        // Check if attachment was created
        $this->assertDatabaseHas('attachments', [
            'filename' => 'test.jpg',
            'collection' => 'test-collection',
            'attachable_type' => User::class,
            'attachable_id' => $user->id,
        ]);

        // Check if user has the attachment
        $this->assertTrue($user->attachments()->where('id', $attachment->id)->exists());
    }

    public function test_attachment_can_be_removed()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a fake file
        $file = UploadedFile::fake()->image('test.jpg');

        // Add attachment to user
        $attachment = $user->addAttachment($file);

        // Check if attachment exists
        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
        ]);

        // Remove attachment
        $user->removeAttachment($attachment);

        // Check if attachment was removed
        $this->assertDatabaseMissing('attachments', [
            'id' => $attachment->id,
        ]);

        // Check if file was removed from storage
        Storage::disk('public')->assertMissing($attachment->path);
    }

    public function test_attachment_service_can_upload_file()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a fake file
        $file = UploadedFile::fake()->image('test.jpg');

        // Use service to upload file
        $service = new AttachmentService();
        $attachment = $service->upload($file, $user, 'test-collection');

        // Check if attachment was created
        $this->assertDatabaseHas('attachments', [
            'filename' => 'test.jpg',
            'collection' => 'test-collection',
            'attachable_type' => User::class,
            'attachable_id' => $user->id,
        ]);

        // Check if file exists in storage
        Storage::disk('public')->assertExists($attachment->path);
    }

    public function test_attachment_service_can_optimize_images()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a fake file
        $file = UploadedFile::fake()->image('test.jpg', 1000, 1000);

        // Use service to upload and optimize file
        $service = new AttachmentService();
        $attachment = $service->upload($file, $user, 'test-collection', [], [
            'optimize' => true,
            'width' => 500,
            'height' => 500,
            'quality' => 80,
        ]);

        // Check if attachment was created
        $this->assertDatabaseHas('attachments', [
            'filename' => 'test.jpg',
            'collection' => 'test-collection',
            'attachable_type' => User::class,
            'attachable_id' => $user->id,
        ]);

        // Check if file exists in storage
        Storage::disk('public')->assertExists($attachment->path);
    }

    public function test_user_can_get_attachments_by_collection()
    {
        // Create a user
        $user = User::factory()->create();

        // Create fake files
        $file1 = UploadedFile::fake()->image('test1.jpg');
        $file2 = UploadedFile::fake()->image('test2.jpg');
        $file3 = UploadedFile::fake()->image('test3.jpg');

        // Add attachments to user with different collections
        $user->addAttachment($file1, 'collection1');
        $user->addAttachment($file2, 'collection1');
        $user->addAttachment($file3, 'collection2');

        // Get attachments by collection
        $collection1Attachments = $user->getAttachments('collection1');
        $collection2Attachments = $user->getAttachments('collection2');

        // Check if collections have the correct number of attachments
        $this->assertCount(2, $collection1Attachments);
        $this->assertCount(1, $collection2Attachments);
    }
}
