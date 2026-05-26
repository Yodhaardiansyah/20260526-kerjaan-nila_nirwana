<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\AlertLog;

class ApiLogController extends Controller
{
    // Fungsi ini menerima request HTTP POST dari Node-RED
    public function storeLog(Request $request)
    {
        $request->validate([
            'log_type' => 'required|in:activity,alert',
            'message' => 'required|string'
        ]);

        // Jika log_type adalah 'alert', simpan ke tabel AlertLog
        if ($request->log_type === 'alert') {
            AlertLog::create([
                'alert_type' => $request->alert_type ?? 'peringatan_sistem',
                'message' => $request->message,
                'is_read' => false
            ]);
        } 
        // Jika log_type adalah 'activity', simpan ke tabel ActivityLog
        else {
            ActivityLog::create([
                'type' => $request->level ?? 'info', 
                'description' => $request->message
            ]);
        }

        return response()->json([
            'status' => 'success', 
            'message' => 'Log berhasil dicatat ke database'
        ]);
    }
}