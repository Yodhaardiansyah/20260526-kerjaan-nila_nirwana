<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceSetting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;

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

    public function controlRelay(Request $request)
    {
        $request->validate([
            'relay' => 'required|integer|in:1,2',
            'state' => 'required|in:ON,OFF'
        ]);

        // URL Node-RED kamu (sesuaikan dengan IP/Port tempat Node-RED berjalan)
        // Pastikan di Node-RED kamu membuat node "http in" dengan URL "/api/manual-relay"
        $nodeRedUrl = env('NODERED_URL', 'http://127.0.0.1:1880') . '/api/manual-relay';

        try {
            // Tembak perintah ke Node-RED
            $response = Http::timeout(5)->post($nodeRedUrl, [
                'relay' => $request->relay,
                'state' => $request->state
            ]);

            if ($response->successful()) {
                // Catat aktivitas ini ke log agar ketahuan siapa yang menekan manual
                ActivityLog::create([
                    'type' => 'warning', 
                    'description' => "Pengguna mengontrol Relay " . $request->relay . " secara manual menjadi " . $request->state
                ]);

                return response()->json(['success' => true, 'message' => 'Perintah berhasil dikirim']);
            }

            return response()->json(['success' => false, 'message' => 'Node-RED menolak permintaan.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke Node-RED.']);
        }
    }
}