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
        if ($request->has('url')) {
            $url = $request->input('url');
            if ($url && !preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $request->merge(['url' => 'http://' . $url]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|unique:monitors,url',
            'interval' => 'required|integer|min:1',
        ]);

        \App\Models\Monitor::create($validated);

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

        if ($request->has('url')) {
            $url = $request->input('url');
            if ($url && !preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $request->merge(['url' => 'http://' . $url]);
            }
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|url|unique:monitors,url,' . $id,
            'interval' => 'sometimes|required|integer|min:1',
        ]);

        $monitor->update($validated);

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
