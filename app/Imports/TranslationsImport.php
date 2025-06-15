<?php

namespace App\Imports;

use App\Models\Translation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TranslationsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return void
    */
    public function model(array $row)
    {
        $key = $row['original_value'] ?? null;

        if (empty($key)) {
            return;
        }

        $localesData = array_diff_key($row, ['original_value' => '']);

        foreach ($localesData as $locale => $value) {
            if (empty($locale)) {
                continue;
            }

            Translation::updateOrCreate(
                [
                    'key' => $key,
                    'locale' => (string) $locale,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }
} 