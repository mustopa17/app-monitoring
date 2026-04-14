<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorLogController extends Controller
{
    public function index()
    {
        $logs = \App\Models\MonitorLog::latest('checked_at')->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'monitor_id' => $log->monitor_id,
                    'status' => $log->status,
                    'response_time' => $log->response_time,
                    'status_code' => $log->status_code,
                    'error_message' => $log->error_message,
                    'checked_at' => $log->checked_at,
                ];
            })
        ]);
    }
}
