<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceSetting;
use App\Models\ActivityLog;
use App\Models\AlertLog;
use InfluxDB2\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Pengaturan & Log dari MySQL
        // Menggunakan firstOrCreate agar jika data setting kosong, otomatis terisi nilai default
        $settings = DeviceSetting::firstOrCreate(
            ['device_id' => 'alat1'],
            [
                'ph_min_limit' => 6.5,
                'ph_max_limit' => 8.0,
                'drain_duration_seconds' => 60,
                'mode' => 'auto'
            ]
        );
        
        $activities = ActivityLog::latest()->take(5)->get();
        $alerts = AlertLog::where('is_read', false)->latest()->take(5)->get();

        // 2. Ambil Data Real-time dari InfluxDB
        $client = new Client([
            'url'   => env('INFLUXDB_URL'),
            'token' => env('INFLUXDB_TOKEN'),
            'org'   => env('INFLUXDB_ORG'),
        ]);

        $queryApi = $client->createQueryApi();

        // Query Flux untuk mendapatkan data paling terakhir (last) dalam rentang 1 jam terakhir
        // Sesuaikan nama measurement jika di Node-RED kamu menggunakan nama lain (contoh di bawah: "aquamonitor")
        $fluxQuery = "from(bucket: \"" . env('INFLUXDB_BUCKET') . "\")
            |> range(start: -1h)
            |> filter(fn: (r) => r[\"_measurement\"] == \"aquamonitor\")
            |> last()";

        $realtimeData = [
            'ph' => 0.0,
            'temp' => 0.0,
            'water' => 0,
            'relay1' => 'OFF',
            'relay2' => 'OFF',
            'last_seen' => null,
            'is_online' => false,
            'uptime' => 'Offline'
        ];

        try {
            $records = $queryApi->query($fluxQuery);
            
            if (count($records) > 0) {
                foreach ($records as $table) {
                    foreach ($table->records as $record) {
                        $field = $record->getField();
                        $value = $record->getValue();
                        $realtimeData[$field] = $value;
                        
                        // Ambil timestamp dari data terakhir yang masuk
                        if (!$realtimeData['last_seen']) {
                            $realtimeData['last_seen'] = Carbon::parse($record->getTime())->setTimezone('Asia/Jakarta');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika koneksi InfluxDB gagal, data akan kembali ke nilai default
            report($e);
        }

        // 3. Logika Deteksi Online / Offline (Batas toleransi 5 menit)
        if ($realtimeData['last_seen']) {
            $now = Carbon::now('Asia/Jakarta');
            $diffInMinutes = $now->diffInMinutes($realtimeData['last_seen']);

            if ($diffInMinutes <= 5) {
                $realtimeData['is_online'] = true;
                
                // Menghitung perkiraan durasi aktif (Uptime) berdasarkan selisih waktu data terakhir diterima
                $realtimeData['uptime'] = $realtimeData['last_seen']->diffForHumans($now, true) . " yang lalu";
            }
        }

        // Kirim semua variabel ke view dashboard
        return view('dashboard', compact('settings', 'activities', 'alerts', 'realtimeData'));
    }
}