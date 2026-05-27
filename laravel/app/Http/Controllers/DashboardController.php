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

        // Variabel penampung data grafik 1 jam terakhir
        $chartData = ['labels' => [], 'ph' => [], 'temp' => []];

        try {
            $client = new Client([
                'url'   => env('INFLUXDB_URL'),
                'token' => env('INFLUXDB_TOKEN'),
                'org'   => env('INFLUXDB_ORG'),
                'verifySSL' => false,
            ]);

            $queryApi = $client->createQueryApi();
            
            // ----------------------------------------------------
            // 1. QUERY STATUS REALTIME (Data Paling Akhir / last)
            // ----------------------------------------------------
            $fluxQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: -24h)
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
                        
                        if ($measurement === 'aquamonitor') {
                            if (array_key_exists($field, $realtimeData)) $realtimeData[$field] = $value;
                        } else {
                            if (array_key_exists($measurement, $realtimeData)) $realtimeData[$measurement] = $value;
                        }
                        
                        if (!$realtimeData['last_seen']) {
                            $realtimeData['last_seen'] = Carbon::parse($record->getTime())->setTimezone('Asia/Jakarta');
                        }
                    }
                }
            }

            // ----------------------------------------------------
            // 2. QUERY GRAFIK DASHBOARD (Khusus 1 Jam Terakhir)
            // ----------------------------------------------------
            $chartQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: -1h)
                |> filter(fn: (r) => r["_measurement"] == "ph" or r["_measurement"] == "temp")
                |> aggregateWindow(every: 5m, fn: mean, createEmpty: false)
                |> yield(name: "mean")';
                
            $chartRecords = $queryApi->query($chartQuery);
            
            if (count($chartRecords) > 0) {
                $tempMap = [];
                // Menggabungkan data pH dan Temp berdasarkan waktu yang sama
                foreach ($chartRecords as $table) {
                    foreach ($table->records as $record) {
                        $time = Carbon::parse($record->getTime())->setTimezone('Asia/Jakarta')->format('H:i');
                        $measurement = $record->getMeasurement();
                        
                        if (!isset($tempMap[$time])) $tempMap[$time] = ['ph' => null, 'temp' => null];
                        $tempMap[$time][$measurement] = round($record->getValue(), 2);
                    }
                }
                
                ksort($tempMap); // Urutkan berdasarkan waktu
                
                foreach ($tempMap as $time => $vals) {
                    $chartData['labels'][] = $time;
                    $chartData['ph'][] = $vals['ph'];
                    $chartData['temp'][] = $vals['temp'];
                }
            }

            // Cek Status Online
            if ($realtimeData['last_seen']) {
                $secondsDiff = Carbon::now('Asia/Jakarta')->timestamp - $realtimeData['last_seen']->timestamp;
                if ($secondsDiff >= 0 && $secondsDiff <= 300) {
                    $realtimeData['is_online'] = true;
                    $realtimeData['uptime'] = 'Online';
                }
            }

            // ----------------------------------------------------
            // 3. QUERY RINGKASAN HARI INI (Average Hari Ini)
            // ----------------------------------------------------
            $todaySummary = [
                'avg_ph' => null,
                'avg_temp' => null
            ];

            $summaryQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: today())
                |> filter(fn: (r) => 
                    r["_measurement"] == "ph" or 
                    r["_measurement"] == "temp"
                )
                |> mean()';

            $summaryRecords = $queryApi->query($summaryQuery);

            if (count($summaryRecords) > 0) {
                foreach ($summaryRecords as $table) {
                    foreach ($table->records as $record) {
                        $measurement = $record->getMeasurement();
                        $value = round($record->getValue(), 2);

                        if ($measurement == 'ph') {
                            $todaySummary['avg_ph'] = $value;
                        }

                        if ($measurement == 'temp') {
                            $todaySummary['avg_temp'] = $value;
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            session()->now('error_influx', 'Koneksi InfluxDB Gagal: ' . $e->getMessage());
            report($e);
        }

        // TAMBAHKAN chartData ke compact()
        return view('dashboard', compact(
            'settings',
            'activities',
            'realtimeData',
            'chartData',
            'todaySummary'
        ));
    }
}