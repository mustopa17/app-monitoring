<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusController extends Controller
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
                'checked_at' => $monitor->checked_at ?? now()->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}
