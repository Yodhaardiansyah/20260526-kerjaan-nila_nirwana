<?php

namespace App\Http\Controllers;

use App\Models\DeviceSetting;
use App\Models\ActivityLog;
use InfluxDB2\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Setting & Log dari MySQL
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
        
        $activities = ActivityLog::latest()->take(5)->get();

        // 2. Ambil Data Real-time dari InfluxDB
        $realtimeData = [
            'ph' => 0.0, 'temp' => 0.0, 'water' => 0, 
            'relay1' => 'OFF', 'relay2' => 'OFF', 
            'last_seen' => null, 'is_online' => false, 'uptime' => 'Offline'
        ];

        try {
            $client = new Client([
                'url'   => env('INFLUXDB_URL'),
                'token' => env('INFLUXDB_TOKEN'),
                'org'   => env('INFLUXDB_ORG'),
                'verifySSL' => false,
            ]);

            $queryApi = $client->createQueryApi();
            
            // Ambil measurement sensor (ph, temp, water) DAN measurement relay (aquamonitor)
            $fluxQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: -10m)
                |> filter(fn: (r) => 
                    r["_measurement"] == "ph" or 
                    r["_measurement"] == "temp" or 
                    r["_measurement"] == "water" or 
                    r["_measurement"] == "aquamonitor"
                )
                |> last()';

            $records = $queryApi->query($fluxQuery);
            
            if (count($records) > 0) {
                foreach ($records as $table) {
                    foreach ($table->records as $record) {
                        
                        $measurement = $record->getMeasurement();
                        $field = $record->getField();
                        $value = $record->getValue();
                        
                        // JIKA DATA DARI NODE-RED (RELAY): Gunakan nama field-nya
                        if ($measurement === 'aquamonitor') {
                            if (array_key_exists($field, $realtimeData)) {
                                $realtimeData[$field] = $value;
                            }
                        } 
                        // JIKA DATA DARI SENSOR (PH/TEMP): Gunakan nama measurement-nya
                        else {
                            if (array_key_exists($measurement, $realtimeData)) {
                                $realtimeData[$measurement] = $value;
                            }
                        }
                        
                        // Catat waktu data terbaru masuk untuk status Online
                        if (!$realtimeData['last_seen']) {
                            $realtimeData['last_seen'] = Carbon::parse($record->getTime())->setTimezone('Asia/Jakarta');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            session()->now('error_influx', 'Koneksi InfluxDB Gagal: ' . $e->getMessage());
            report($e);
        }

        // 3. Kalkulasi Status Online (Toleransi 5 Menit)
        if ($realtimeData['last_seen']) {
            $now = Carbon::now('Asia/Jakarta');
            if ($now->diffInMinutes($realtimeData['last_seen']) <= 5) {
                $realtimeData['is_online'] = true;
                $realtimeData['uptime'] = $realtimeData['last_seen']->diffForHumans($now, true) . " yang lalu";
            }
        }

        return view('dashboard', compact('settings', 'activities', 'realtimeData'));
    }
}