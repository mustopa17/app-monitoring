<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
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
