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
        // ====================================================
        // 1. AMBIL SETTING & LOG DARI MYSQL
        // ====================================================
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

        // ====================================================
        // 2. DEFAULT DATA REALTIME
        // ====================================================
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

        // ====================================================
        // 3. DEFAULT DATA GRAFIK
        // ====================================================
        $chartData = [
            'labels' => [],
            'ph' => [],
            'temp' => []
        ];

        // ====================================================
        // 4. DEFAULT RINGKASAN HARI INI
        // ====================================================
        $todaySummary = [
            'avg_ph' => null,
            'avg_temp' => null
        ];

        try {

            // ====================================================
            // KONEKSI INFLUXDB
            // ====================================================
            $client = new Client([
                'url' => env('INFLUXDB_URL'),
                'token' => env('INFLUXDB_TOKEN'),
                'org' => env('INFLUXDB_ORG'),
                'verifySSL' => false,
            ]);

            $queryApi = $client->createQueryApi();

            // ====================================================
            // 5. QUERY STATUS REALTIME
            // ====================================================
            $fluxQuery = '
                from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                    |> range(start: -24h)
                    |> filter(fn: (r) =>
                        r["_measurement"] == "ph" or
                        r["_measurement"] == "temp" or
                        r["_measurement"] == "water" or
                        r["_measurement"] == "aquamonitor"
                    )
                    |> last()
            ';

            $records = $queryApi->query($fluxQuery);

            $latestTime = null;

            if (count($records) > 0) {

                foreach ($records as $table) {

                    foreach ($table->records as $record) {

                        $measurement = $record->getMeasurement();
                        $field = $record->getField();
                        $value = $record->getValue();

                        // ============================================
                        // SIMPAN DATA REALTIME
                        // ============================================
                        if ($measurement === 'aquamonitor') {

                            if (array_key_exists($field, $realtimeData)) {
                                $realtimeData[$field] = $value;
                            }

                        } else {

                            if (array_key_exists($measurement, $realtimeData)) {
                                $realtimeData[$measurement] = $value;
                            }
                        }

                        // ============================================
                        // CARI TIMESTAMP PALING BARU
                        // ============================================
                        $recordTime = Carbon::parse($record->getTime())
                            ->setTimezone('Asia/Jakarta');

                        if (!$latestTime || $recordTime->greaterThan($latestTime)) {
                            $latestTime = $recordTime;
                        }
                    }
                }
            }

            // Simpan waktu terakhir
            $realtimeData['last_seen'] = $latestTime;

           // ====================================================
// 6. CEK STATUS ONLINE / OFFLINE + HITUNG UPTIME
// ====================================================
if ($realtimeData['last_seen']) {

    $now = Carbon::now('Asia/Jakarta');

    $secondsDiff = $realtimeData['last_seen']->diffInSeconds($now);

    // online jika data terakhir <= 5 menit
    if ($secondsDiff <= 20) {

        $realtimeData['is_online'] = true;

        /*
        |--------------------------------------------------------------------------
        | Ambil histori timestamp
        |--------------------------------------------------------------------------
        */

        $uptimeQuery = '
            from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: -7d)
                |> filter(fn: (r) => r["_measurement"] == "ph")
                |> keep(columns: ["_time"])
        ';

        $uptimeRecords = $queryApi->query($uptimeQuery);

        $times = [];

        foreach ($uptimeRecords as $table) {

            foreach ($table->records as $record) {

                $times[] = Carbon::parse($record->getTime())
                    ->setTimezone('Asia/Jakarta');
            }
        }

        // ====================================================
        // URUTKAN DARI TERBARU
        // ====================================================
        usort($times, function ($a, $b) {
            return $b->timestamp <=> $a->timestamp;
        });

        $onlineStart = $realtimeData['last_seen'];

        // ====================================================
        // CEK GAP > 5 MENIT
        // ====================================================
        for ($i = 0; $i < count($times) - 1; $i++) {

            $current = $times[$i];
            $next = $times[$i + 1];

            $gap = abs($current->timestamp - $next->timestamp);

            // jika gap > 5 menit maka stop
            if ($gap > 20) {
                break;
            }

            $onlineStart = $next;
        }

        // ====================================================
        // HITUNG DURASI
        // ====================================================
        $durationSeconds = $onlineStart->diffInSeconds(
            $realtimeData['last_seen']
        );

        $days = floor($durationSeconds / 86400);
        $hours = floor(($durationSeconds % 86400) / 3600);
        $minutes = floor(($durationSeconds % 3600) / 60);

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' hari';
        }

        if ($hours > 0) {
            $parts[] = $hours . ' jam';
        }

        $parts[] = $minutes . ' menit';

        $realtimeData['uptime'] = implode(' ', $parts);

    } else {

        $realtimeData['is_online'] = false;
        $realtimeData['uptime'] = 'Offline';
    }
}
            // ====================================================
            // 7. QUERY DATA GRAFIK 1 JAM TERAKHIR
            // ====================================================
            $chartQuery = '
                from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                    |> range(start: -1h)
                    |> filter(fn: (r) =>
                        r["_measurement"] == "ph" or
                        r["_measurement"] == "temp"
                    )
                    |> aggregateWindow(
                        every: 5m,
                        fn: mean,
                        createEmpty: false
                    )
                    |> yield(name: "mean")
            ';

            $chartRecords = $queryApi->query($chartQuery);

            if (count($chartRecords) > 0) {

                $tempMap = [];

                foreach ($chartRecords as $table) {

                    foreach ($table->records as $record) {

                        $time = Carbon::parse($record->getTime())
                            ->setTimezone('Asia/Jakarta')
                            ->format('H:i');

                        $measurement = $record->getMeasurement();

                        if (!isset($tempMap[$time])) {
                            $tempMap[$time] = [
                                'ph' => null,
                                'temp' => null
                            ];
                        }

                        $tempMap[$time][$measurement] = round(
                            $record->getValue(),
                            2
                        );
                    }
                }

                // Urutkan waktu
                ksort($tempMap);

                foreach ($tempMap as $time => $vals) {

                    $chartData['labels'][] = $time;
                    $chartData['ph'][] = $vals['ph'];
                    $chartData['temp'][] = $vals['temp'];
                }
            }

            // ====================================================
            // 8. QUERY RINGKASAN HARI INI
            // ====================================================
            $summaryQuery = '
                from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                    |> range(start: today())
                    |> filter(fn: (r) =>
                        r["_measurement"] == "ph" or
                        r["_measurement"] == "temp"
                    )
                    |> mean()
            ';

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

            session()->now(
                'error_influx',
                'Koneksi InfluxDB Gagal: ' . $e->getMessage()
            );

            report($e);
        }

        // ====================================================
        // RETURN VIEW
        // ====================================================
        return view('dashboard', compact(
            'settings',
            'activities',
            'realtimeData',
            'chartData',
            'todaySummary'
        ));
    }
    
}