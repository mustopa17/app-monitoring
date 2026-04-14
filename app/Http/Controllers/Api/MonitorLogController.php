<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorLogController extends Controller
{
    public function index()
    {
        $log = \App\Models\MonitorLog::first();

        if (!$log) {
            return response()->json([
                'error' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => [
                'monitor_id' => $log->monitor_id,
                'status' => $log->status,
                'response_time' => $log->response_time,
                'status_code' => $log->status_code,
                'error_message' => $log->error_message,
                'checked_at' => $log->checked_at,
            ]
        ]);
    }
}
