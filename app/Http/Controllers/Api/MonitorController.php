<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        $monitor = \App\Models\Monitor::first();

        if (!$monitor) {
            return response()->json([
                'error' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => [
                'name' => $monitor->name,
                'url' => $monitor->url,
                'status' => $monitor->status ?? 'UP',
                'response_time' => $monitor->response_time ?? 0,
                'checked_at' => $monitor->checked_at ? $monitor->checked_at : null,
            ]
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->name || !$request->url || !$request->interval) {
            return response()->json([
                'error' => 'Tidak boleh kosong'
            ], 422);
        }

        \App\Models\Monitor::create([
            'name' => $request->name,
            'url' => $request->url,
            'interval' => $request->interval,
        ]);

        return response()->json([
            'data' => 'OK'
        ]);
    }
}
