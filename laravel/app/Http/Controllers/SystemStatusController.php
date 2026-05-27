<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InfluxDB2\Client;
use Carbon\Carbon;

class SystemStatusController extends Controller
{
    public function index()
    {
        // Nilai bawaan (default) jika alat benar-benar mati
        $status = [
            'is_online' => false,
            'last_seen' => '-',
            'last_seen_human' => '-',
            'message' => 'Menunggu data...',
            'color' => 'red'
        ];

        try {
            $client = new Client([
                'url'   => env('INFLUXDB_URL'),
                'token' => env('INFLUXDB_TOKEN'),
                'org'   => env('INFLUXDB_ORG'),
                'verifySSL' => false,
            ]);

            $queryApi = $client->createQueryApi();
            
            // Cari data terakhir dari measurement 'ph' atau 'temp' dalam 24 jam terakhir
            $fluxQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: -24h)
                |> filter(fn: (r) => r["_measurement"] == "ph" or r["_measurement"] == "temp")
                |> last()';

            $records = $queryApi->query($fluxQuery);

            if (count($records) > 0) {
                $latestTime = null;
                
                // Cari timestamp yang paling baru dari semua data yang ditarik
                foreach ($records as $table) {
                    foreach ($table->records as $record) {
                        $time = Carbon::parse($record->getTime())->setTimezone('Asia/Jakarta');
                        if (!$latestTime || $time->greaterThan($latestTime)) {
                            $latestTime = $time;
                        }
                    }
                }

                if ($latestTime) {
                    $status['last_seen'] = $latestTime->format('d M Y - H:i:s');
                    $status['last_seen_human'] = $latestTime->diffForHumans();
                    
                    // ====================================================
                    // 🛠️ PERBAIKAN: MENGGUNAKAN UNIX TIMESTAMP (DETIK KILAT)
                    // Mengurangi risiko bug akibat perbedaan zona waktu server
                    // ====================================================
                    $currentTimestamp = Carbon::now()->timestamp;
                    $dataTimestamp = $latestTime->timestamp;
                    $secondsDiff = $currentTimestamp - $dataTimestamp; // Selisih dalam hitungan detik
                    
                    // Alat dianggap ONLINE hanya jika mengirim data dalam 5 menit terakhir (300 detik)
                    if ($secondsDiff >= 0 && $secondsDiff <= 300) {
                        $status['is_online'] = true;
                        $status['message'] = 'Sistem berjalan normal dan terhubung ke server.';
                        $status['color'] = 'green';
                    } else {
                        // Hitung berapa menit keterlambatannya
                        $minutesPast = floor(max(0, $secondsDiff) / 60);
                        
                        $status['is_online'] = false;
                        $status['message'] = 'Alat tidak mengirim data selama lebih dari 5 menit (Tepatnya ' . $minutesPast . ' menit lalu). Perangkat ESP32 mati atau koneksi terputus.';
                        $status['color'] = 'red';
                    }
                }
            } else {
                $status['message'] = 'Tidak ada riwayat data sama sekali dalam 24 jam terakhir. Sistem OFFLINE.';
            }

        } catch (\Exception $e) {
            $status['message'] = 'Gagal terhubung ke Database InfluxDB: ' . $e->getMessage();
        }

        return view('status.index', compact('status'));
    }
}