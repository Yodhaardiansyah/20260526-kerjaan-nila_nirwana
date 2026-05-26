<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSetting extends Model {
    protected $fillable = [
        'device_id', 'mode', 'ph_min_limit', 'ph_max_limit', 'drain_duration_seconds', 'is_active'
    ];
}