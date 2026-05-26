<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'ph_min_limit',
        'ph_max_limit',
        'drain_duration_seconds',
        'mode',
    ];
}