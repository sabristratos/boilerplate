<?php

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\LegalPage;

class LegalPageService
{
    public function save(array $data, ?int $pageId = null): LegalPage
    {
        $page = LegalPage::findOrNew($pageId);
        $isNew = !$page->exists;

        $page->is_published = $data['is_published'];
        
        // Handle translations for each locale
        foreach ($data['title'] as $locale => $title) {
            if (!empty($title)) {
                $page->setTranslation('title', $locale, $title);
            }
        }
        
        foreach ($data['slug'] as $locale => $slug) {
            if (!empty($slug)) {
                $page->setTranslation('slug', $locale, $slug);
            }
        }
        
        foreach ($data['content'] as $locale => $content) {
            if (!empty($content)) {
                $page->setTranslation('content', $locale, $content);
            }
        }
        
        $page->save();

        if ($isNew) {
            ActivityLogger::logCreated($page);
        } else {
            ActivityLogger::logUpdated($page);
        }

        return $page;
    }

    public function deleteLegalPage(LegalPage $legalPage): void
    {
        $legalPage->delete();
        ActivityLogger::logDeleted($legalPage);
    }
} 