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

    protected function key(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn(string $value) => ['key' => $value, 'key_hash' => hash('sha256', $value)]);
    }
}
