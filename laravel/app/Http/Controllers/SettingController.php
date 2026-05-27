<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceSetting;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil data pengaturan alat dari database
        $settings = DeviceSetting::firstOrCreate(
            ['device_id' => 'alat1'],
            [
                'mode' => 'auto',
                'ph_min_limit' => 6.5,
                'ph_max_limit' => 8.0,
                'drain_duration_seconds' => 60,
                'is_active' => true
            ]
        );

        return view('pengaturan.index', compact('settings'));
    }
}