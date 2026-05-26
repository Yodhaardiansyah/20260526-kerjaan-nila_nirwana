<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceSetting;
use App\Models\ActivityLog;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class DeviceController extends Controller
{
    // API GET untuk Node-RED
    public function getApiSettings($device_id = 'alat1')
    {
        $settings = DeviceSetting::firstOrCreate(
            ['device_id' => $device_id],
            [
                'mode' => 'auto',
                'ph_min_limit' => 6.5,
                'ph_max_limit' => 8.0,
                'drain_duration_seconds' => 60,
                'is_active' => true
            ]
        );
        return response()->json($settings);
    }

    // Simpan Pengaturan dari Web
    public function updateSettings(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:auto,manual',
            'ph_min_limit' => 'required|numeric',
            'ph_max_limit' => 'required|numeric',
            'drain_duration_seconds' => 'required|integer|min:1',
        ]);

        $settings = DeviceSetting::where('device_id', 'alat1')->first();
        $settings->update($request->all());

        ActivityLog::create([
            'type' => 'info',
            'description' => 'Pengaturan diperbarui. Mode: ' . strtoupper($request->mode),
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    // Kontrol Relay Manual via Web
    public function controlRelay(Request $request)
    {
        $request->validate([
            'relay' => 'required|in:relay1,relay2',
            'state' => 'required|in:ON,OFF'
        ]);

        $settings = DeviceSetting::where('device_id', 'alat1')->first();
        if ($settings->mode !== 'manual') {
            return redirect()->back()->with('error', 'Gagal! Ubah ke Mode Manual terlebih dahulu.');
        }

        $topic = "barto/ta/alat1/cmd/" . $request->relay;
        $this->publishToMqtt($topic, $request->state);

        ActivityLog::create([
            'type' => 'warning',
            'description' => strtoupper($request->relay) . ' disetel ' . $request->state . ' secara manual.',
        ]);

        return redirect()->back()->with('success', 'Perintah ' . $request->state . ' dikirim ke ' . $request->relay);
    }

    // Fungsi Internal MQTT
    private function publishToMqtt($topic, $payload)
    {
        try {
            $mqtt = new MqttClient('broker.emqx.io', 1883, 'laravel_cmd_' . rand(5, 15));
            $connectionSettings = (new ConnectionSettings())->setKeepAliveInterval(60)->setConnectTimeout(3);
            $mqtt->connect($connectionSettings, false);
            $mqtt->publish($topic, $payload, 0);
            $mqtt->disconnect();
        } catch (\Exception $e) {
            report($e);
        }
    }

    // Fungsi untuk mereset variabel siklus di Node-RED
    public function resetCycle()
    {
        // Kirim trigger ke Node-RED
        $this->publishToMqtt('barto/ta/alat1/cmd/reset_cycle', 'RESET');

        ActivityLog::create([
            'type' => 'warning',
            'description' => 'Siklus Node-RED di-reset secara manual oleh pengguna.',
        ]);

        return redirect()->back()->with('success', 'Sistem Node-RED berhasil di-reset ke kondisi awal!');
    }

    // Fungsi untuk merestart ESP32 secara fisik (Hardware)
    public function restartDevice()
    {
        // Kirim perintah restart ke ESP32
        $this->publishToMqtt('barto/ta/alat1/cmd/restart', 'RESTART');

        ActivityLog::create([
            'type' => 'warning',
            'description' => 'Perintah Restart Hardware dikirim ke ESP32.',
        ]);

        return redirect()->back()->with('success', 'Perintah Restart dikirim. Alat akan offline beberapa detik.');
    }
}