<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InfluxDB2\Client;
use Carbon\Carbon;

class GraphController extends Controller
{
    public function index()
    {
        return view('grafik.index');
    }

    public function getData(Request $request)
    {
        $type = $request->query('type', 'ph'); 
        $range = $request->query('range', '1d');
        
        $start = '-1d';
        $stop = 'now()';
        $window = '10m'; 

        // Logika Filter Rentang Waktu
        if ($range === '7d') {
            $start = '-7d';
            $window = '1h';
        } elseif ($range === '30d') {
            $start = '-30d';
            $window = '6h';
        } 
        // ==========================================
        // FITUR BARU: FILTER JAM CUSTOM
        // ==========================================
        elseif ($range === 'hours') {
            $hours = (int) $request->query('hours', 2); // Default 2 jam jika tidak diisi
            $start = '-' . $hours . 'h';
            
            // Atur kerapatan data berdasarkan jumlah jam agar tidak berat
            if ($hours <= 3) {
                $window = '1m';  // Sangat detail, rata-rata per 1 menit
            } elseif ($hours <= 12) {
                $window = '5m';  // Rata-rata per 5 menit
            } else {
                $window = '10m'; // Rata-rata per 10 menit
            }
        } 
        // ==========================================
        elseif ($range === 'custom') {
            $startDate = $request->query('start');
            $endDate = $request->query('end');
            
            $start = Carbon::parse($startDate, 'Asia/Jakarta')->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $stop = Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $window = '1h'; 
        }

        $labels = [];
        $values = [];
        $tableData = [];

        try {
            $client = new Client([
                'url'   => env('INFLUXDB_URL'),
                'token' => env('INFLUXDB_TOKEN'),
                'org'   => env('INFLUXDB_ORG'),
                'verifySSL' => false,
            ]);

            $queryApi = $client->createQueryApi();
            
            $fluxQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: ' . $start . ', stop: ' . $stop . ')
                |> filter(fn: (r) => r["_measurement"] == "' . $type . '")
                |> aggregateWindow(every: ' . $window . ', fn: mean, createEmpty: false)
                |> yield(name: "mean")';

            $records = $queryApi->query($fluxQuery);

            if (count($records) > 0) {
                foreach ($records as $table) {
                    foreach ($table->records as $record) {
                        $time = Carbon::parse($record->getTime())->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');
                        $val = round($record->getValue(), 2);
                        
                        $labels[] = $time;
                        $values[] = $val;
                        
                        $tableData[] = [
                            'time' => $time,
                            'value' => $val
                        ];
                    }
                }
            }
            
            $tableData = array_reverse($tableData);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'tableData' => $tableData,
            'unit' => $type === 'ph' ? '' : '°C',
            'labelName' => $type === 'ph' ? 'Nilai pH' : 'Suhu Air'
        ]);
    }
}