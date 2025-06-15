<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'key',
        'value',
    ];

    public function setKeyAttribute(string $value): void
    {
        $this->attributes['key'] = $value;
        $this->attributes['key_hash'] = hash('sha256', $value);
    }
}
