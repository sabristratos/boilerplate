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
        
        $page->title = $data['title'];
        $page->slug = $data['slug'];
        $page->content = $data['content'];
        
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