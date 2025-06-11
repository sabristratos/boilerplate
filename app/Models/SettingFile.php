<?php

namespace App\Models;

use App\Interfaces\Attachable;
use App\Models\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingFile extends Model implements Attachable
{
    use HasFactory;
    use HasAttachments;
}
