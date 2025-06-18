<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Attachable
{
    /**
     * Get all the model's attachments.
     */
    public function attachments(): MorphToMany;
} 