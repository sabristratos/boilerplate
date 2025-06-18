<?php

namespace Tests\Feature;

use App\Jobs\ProcessAttachmentImage;
use App\Models\Attachment;
use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    public function test_it_can_upload_a_single_file_and_create_an_attachment()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $attachment = app(AttachmentService::class)->upload($file, 'documents');

        $this->assertInstanceOf(Attachment::class, $attachment);
        $this->assertEquals('document.pdf', $attachment->filename);
        $this->assertEquals('documents', $attachment->collection);
        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertExists($attachment->path);
    }

    public function test_it_can_upload_an_image_and_dispatch_processing_job()
    {
        Bus::fake();

        $file = UploadedFile::fake()->image('photo.jpg', 640, 480);

        $attachment = app(AttachmentService::class)->upload($file, 'photos');

        $this->assertInstanceOf(Attachment::class, $attachment);
        $this->assertEquals('photo.jpg', $attachment->filename);
        $this->assertEquals(640, $attachment->meta['width']);
        $this->assertEquals(480, $attachment->meta['height']);

        Bus::assertDispatched(ProcessAttachmentImage::class, function ($job) use ($attachment) {
            return $job->attachment->id === $attachment->id;
        });

        Storage::disk('public')->assertExists($attachment->path);
    }

    public function test_it_can_replace_an_existing_attachment()
    {
        Bus::fake();

        $originalFile = UploadedFile::fake()->image('old.jpg', 100, 100);
        $attachment = app(AttachmentService::class)->upload($originalFile);

        $newFile = UploadedFile::fake()->image('new.jpg', 200, 200);
        $updatedAttachment = app(AttachmentService::class)->replace($attachment, $newFile);

        $this->assertEquals($attachment->id, $updatedAttachment->id);
        $this->assertEquals('new.jpg', $updatedAttachment->filename);
        $this->assertEquals(200, $updatedAttachment->meta['width']);

        Bus::assertDispatched(ProcessAttachmentImage::class);
        Storage::disk('public')->assertExists($attachment->path);
    }

    public function test_it_deletes_the_file_from_storage_when_attachment_is_deleted()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $attachment = app(AttachmentService::class)->upload($file);

        $path = $attachment->path;
        Storage::disk('public')->assertExists($path);

        app(AttachmentService::class)->delete($attachment);

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_livewire_upload_component_handles_multiple_files()
    {
        $user = User::factory()->create();
        $files = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.png'),
        ];

        Livewire::test('attachments.upload-attachment', ['model' => $user, 'multiple' => true])
            ->set('files', $files)
            ->call('processUpload');

        $this->assertCount(2, $user->attachments()->get());
        $this->assertEquals('image1.jpg', $user->attachments()->first()->filename);
        $this->assertEquals('image2.png', $user->attachments()->get()[1]->filename);
    }

    public function test_attachment_url_is_cached_and_cleared_on_update()
    {
        $attachment = Attachment::factory()->create();

        $cacheKey = 'attachment_url_' . $attachment->id . '_' . $attachment->updated_at->timestamp;

        // First access should cache the URL
        $url1 = $attachment->url;
        $this->assertTrue(Cache::has($cacheKey));

        // Second access should get it from cache
        $url2 = $attachment->url;
        $this->assertEquals($url1, $url2);

        // Update the attachment
        sleep(1); // Ensure timestamp changes
        $attachment->touch();
        $attachment->save();

        // The old cache key should be gone
        $this->assertFalse(Cache::has($cacheKey));

        // A new cache key should be created on next access
        $newCacheKey = 'attachment_url_' . $attachment->id . '_' . $attachment->updated_at->timestamp;
        $newUrl = $attachment->url;
        $this->assertTrue(Cache::has($newCacheKey));
    }

    public function test_image_processing_job_optimizes_and_updates_attachment()
    {
        $file = UploadedFile::fake()->image('photo.jpg', 1920, 1080);

        // Manually store the file and create attachment to simulate pre-job state
        $path = $file->store('attachments/'. now()->format('Y/m'), 'public');
        $attachment = Attachment::create([
            'filename' => 'photo.jpg',
            'path' => $path,
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => $file->getSize(),
            'collection' => 'test',
            'meta' => ['width' => 1920, 'height' => 1080],
        ]);

        $originalSize = $attachment->size;

        $job = new ProcessAttachmentImage($attachment, ['quality' => 50]);
        $job->handle(app(AttachmentService::class));

        $attachment->refresh();

        $this->assertTrue($attachment->size < $originalSize);
        $this->assertTrue($attachment->meta['optimized']);
        $this->assertNotNull($attachment->meta['optimized_at']);
    }

    public function test_it_can_add_attachment_via_has_attachments_trait()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $attachment = $user->addAttachment($file, 'documents');

        $this->assertCount(1, $user->attachments);
        $this->assertEquals($attachment->id, $user->attachments->first()->id);
        $this->assertDatabaseHas('attachables', [
            'attachment_id' => $attachment->id,
            'attachable_id' => $user->id,
            'attachable_type' => get_class($user)
        ]);
    }

    public function test_it_can_get_attachments_by_collection_via_trait()
    {
        $user = User::factory()->create();
        $user->addAttachment(UploadedFile::fake()->create('doc1.pdf', 100), 'docs');
        $user->addAttachment(UploadedFile::fake()->create('doc2.pdf', 100), 'docs');
        $user->addAttachment(UploadedFile::fake()->create('image.jpg', 100), 'images');

        $this->assertCount(2, $user->getAttachments('docs'));
        $this->assertCount(1, $user->getAttachments('images'));
        $this->assertCount(3, $user->getAttachments());
    }

    public function test_it_can_remove_an_attachment_via_trait()
    {
        $user = User::factory()->create();
        $attachment = $user->addAttachment(UploadedFile::fake()->create('doc1.pdf', 100));

        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);
        $this->assertCount(1, $user->attachments);

        $user->removeAttachment($attachment);

        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]); // The attachment itself should still exist
        $this->assertCount(0, $user->fresh()->attachments);
    }

    public function test_it_can_remove_all_attachments_via_trait()
    {
        $user = User::factory()->create();
        $user->addAttachment(UploadedFile::fake()->create('doc1.pdf', 100));
        $user->addAttachment(UploadedFile::fake()->create('doc2.pdf', 100));

        $this->assertCount(2, $user->attachments);

        $user->removeAllAttachments();

        $this->assertCount(0, $user->fresh()->attachments);
    }

    public function test_it_can_remove_all_attachments_in_a_collection_via_trait()
    {
        $user = User::factory()->create();
        $user->addAttachment(UploadedFile::fake()->create('doc1.pdf', 100), 'docs');
        $user->addAttachment(UploadedFile::fake()->create('image.jpg', 100), 'images');

        $user->removeAllAttachments('docs');

        $this->assertCount(1, $user->fresh()->attachments);
        $this->assertEquals('images', $user->fresh()->attachments->first()->collection);
    }

    public function test_an_attachment_can_belong_to_multiple_models()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $file = UploadedFile::fake()->create('shared.pdf', 100);

        $attachment = $user1->addAttachment($file);
        $user2->attachments()->attach($attachment->id);

        $this->assertCount(1, $user1->attachments);
        $this->assertCount(1, $user2->attachments);
        $this->assertEquals($user1->attachments->first()->id, $user2->attachments->first()->id);
    }

    public function test_detaching_from_one_model_does_not_affect_others()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $attachment = $user1->addAttachment(UploadedFile::fake()->create('shared.pdf', 100));
        $user2->attachments()->attach($attachment->id);

        $user1->removeAttachment($attachment);

        $this->assertCount(0, $user1->attachments);
        $this->assertCount(1, $user2->attachments);
        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);
    }
}