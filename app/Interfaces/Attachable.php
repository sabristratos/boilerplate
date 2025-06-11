<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Attachable
{
    /**
     * Get all the model's attachments.
     *
     * @return MorphMany<Attachment>
     */
    public function attachments(): MorphMany;
} 