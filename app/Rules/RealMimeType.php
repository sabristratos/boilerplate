<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class RealMimeType implements ValidationRule
{
    /**
     * The allowed MIME types.
     *
     * @var array<int, string>
     */
    protected $allowedMimes;

    /**
     * Create a new rule instance.
     *
     * @param  array<int, string>  $allowedMimes
     * @return void
     */
    public function __construct(array $allowedMimes)
    {
        $this->allowedMimes = $allowedMimes;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            return;
        }

        $realMimeType = $this->getRealMimeType($value);

        if (!in_array($realMimeType, $this->allowedMimes)) {
            $fail(__('The :attribute has an invalid file type.'));
        }
    }

    /**
     * Get the real MIME type of the file.
     *
     * @param UploadedFile $file
     * @return string|null
     */
    protected function getRealMimeType(UploadedFile $file): ?string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        $mimeType = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        return $mimeType ?: null;
    }
} 