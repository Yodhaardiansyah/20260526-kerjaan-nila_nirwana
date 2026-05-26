<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceSetting;
use App\Models\ActivityLog;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class DeviceSettingController extends Controller
{
    // Fungsi internal untuk mempublikasikan pesan ke MQTT Broker
    private function publishToMqtt($topic, $payload)
    {
        $server   = 'broker.emqx.io';
        $port     = 1883;
        $clientId = 'laravel_client_' . rand(5, 15);

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $connectionSettings = (new ConnectionSettings())
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(3);
                
            $mqtt->connect($connectionSettings, false);
            $mqtt->publish($topic, $payload, 0);
            $mqtt->disconnect();
        } catch (\Exception $e) {
            // Log error jika koneksi ke broker MQTT gagal agar web tidak crash
            report($e);
        }
    }

    // 1. Update Batas pH, Durasi Kuras, dan Mode Alat
    public function update(Request $request)
    {
        $request->validate([
            'ph_min_limit' => 'required|numeric',
            'ph_max_limit' => 'required|numeric',
            'drain_duration_seconds' => 'required|integer|min:1',
            'mode' => 'required|in:auto,manual',
        ]);

        // Ambil data setting alat1 atau buat baru jika belum ada
        $settings = DeviceSetting::where('device_id', 'alat1')->firstOrFail();
        
        $settings->update([
            'ph_min_limit' => $request->ph_min_limit,
            'ph_max_limit' => $request->ph_max_limit,
            'drain_duration_seconds' => $request->drain_duration_seconds,
            'mode' => $request->mode,
        ]);

        // Siapkan payload JSON untuk dikirim ke Node-RED / ESP32
        $mqttPayload = json_encode([
            'ph_min' => (float)$request->ph_min_limit,
            'ph_max' => (float)$request->ph_max_limit,
            'drain_duration' => (int)$request->drain_duration_seconds,
            'mode' => $request->mode
        ]);

        // Kirim konfigurasi baru ke topik MQTT khusus pengaturan
        $this->publishToMqtt('barto/ta/alat1/settings', $mqttPayload);

        // Catat ke Log Aktivitas MySQL
        ActivityLog::create([
            'type' => 'info',
            'description' => 'Pengaturan alat diperbarui. Mode: ' . strtoupper($request->mode),
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui dan dikirim ke alat!');
    }

    // 2. Trigger Satu Siklus Manual (Kuras -> Isi)
    public function triggerManualCycle()
    {
        $settings = DeviceSetting::where('device_id', 'alat1')->firstOrFail();

        // Pastikan sistem sedang dalam mode manual sebelum menjalankan siklus manual
        if ($settings->mode !== 'manual') {
            return redirect()->back()->with('error', 'Ubah mode sistem ke Manual terlebih dahulu!');
        }

        // Kirim perintah mulai satu siklus via MQTT
        // Payload berupa JSON agar mudah dikembangkan jika ada parameter tambahan nanti
        $mqttPayload = json_encode([
            'cmd' => 'manual_cycle',
            'drain_duration' => $settings->drain_duration_seconds
        ]);

        $this->publishToMqtt('barto/ta/alat1/cmd', $mqttPayload);

        // Catat ke Log Aktivitas MySQL
        ActivityLog::create([
            'type' => 'warning',
            'description' => 'Siklus manual dijalankan oleh pengguna.',
        ]);

        return redirect()->back()->with('success', 'Perintah satu siklus manual telah dikirim!');
    }
}