<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Term;
use Illuminate\Support\Str;

class TermService
{
    public function createTerm(array $data): Term
    {
        $data['slug'] = Str::slug($data['name']);
        $term = Term::create($data);

        ActivityLogger::logCreated(
            $term,
            auth()->user(),
            $term->toArray(),
            'term'
        );

        return $term;
    }

    public function updateTerm(Term $term, array $data): Term
    {
        $oldValues = $term->getOriginal();
        $data['slug'] = Str::slug($data['name']);
        $term->update($data);

        ActivityLogger::logUpdated(
            $term,
            auth()->user(),
            [
                'old' => $oldValues,
                'new' => $term->toArray(),
            ],
            'term'
        );

        return $term;
    }

    public function deleteTerm(Term $term): void
    {
        ActivityLogger::logDeleted(
            $term,
            auth()->user(),
            $term->toArray(),
            'term'
        );

        $term->delete();
    }
} 