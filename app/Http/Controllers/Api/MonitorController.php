<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        $monitors = \App\Models\Monitor::all();

        return response()->json([
            'data' => $monitors->map(function ($monitor) {
                return [
                    'id' => $monitor->id,
                    'name' => $monitor->name,
                    'url' => $monitor->url,
                    'interval' => $monitor->interval,
                    'status' => $monitor->status ?? 'UP',
                    'response_time' => $monitor->response_time ?? 0,
                    'checked_at' => $monitor->checked_at ? $monitor->checked_at : null,
                ];
            })
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

    public function update(Request $request, $id)
    {
        $monitor = \App\Models\Monitor::find($id);

        if (!$monitor) {
            return response()->json([
                'error' => 'Data tidak ditemukan'
            ], 404);
        }

        $monitor->update([
            'name' => $request->name ?? $monitor->name,
            'url' => $request->url ?? $monitor->url,
            'interval' => $request->interval ?? $monitor->interval,
        ]);

        return response()->json([
            'data' => [
                'name' => $monitor->name,
                'url' => $monitor->url,
                'status' => $monitor->status ?? 'UP',
                'response_time' => $monitor->response_time ?? 0,
                'checked_at' => $monitor->checked_at ?? null,
            ]
        ]);
    }

    public function destroy($id)
    {
        $monitor = \App\Models\Monitor::find($id);

        if (!$monitor) {
            return response()->json([
                'error' => 'Data tidak ditemukan'
            ], 404);
        }

        $data = [
            'name' => $monitor->name,
            'url' => $monitor->url,
            'status' => $monitor->status ?? 'UP',
            'response_time' => $monitor->response_time ?? 0,
            'checked_at' => $monitor->checked_at ?? null,
        ];

        $monitor->delete();

        return response()->json([
            'data' => $data
        ]);
    }
}
