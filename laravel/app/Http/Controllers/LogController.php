<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AlertLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        // Menggunakan nama parameter page yang berbeda agar paginasi tidak berbenturan dalam satu halaman
        $alerts = AlertLog::latest()->paginate(15, ['*'], 'alerts_page');
        $activities = ActivityLog::latest()->paginate(15, ['*'], 'activities_page');

        return view('logs.index', compact('alerts', 'activities'));
    }
}