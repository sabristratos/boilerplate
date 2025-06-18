<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAttachmentImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Attachment $attachment,
        public array $options = []
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(AttachmentService $attachmentService): void
    {
        try {
            $attachmentService->processImage($this->attachment, $this->options);
        } catch (\Exception $e) {
            Log::error('Failed to process image for attachment: ' . $this->attachment->id, [
                'exception' => $e,
            ]);

            // Optionally, re-throw the exception to have the queue worker handle retries/failures
            throw $e;
        }
    }
} 