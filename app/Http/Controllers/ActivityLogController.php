<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller {
    public function index() {
        $logs = ActivityLog::with('user')->orderByDesc('log_id')->limit(100)->get();
        return view('activity_logs.index', compact('logs'));
    }
}
