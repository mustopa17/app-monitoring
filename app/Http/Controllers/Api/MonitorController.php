<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonitorController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->can('monitor.view'), 403, 'Anda tidak memiliki izin untuk melihat monitor.');
        $monitors = Monitor::all();

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
            }),
        ]);
    }

    public function store(Request $request)
    {
        abort_if(! auth()->user()->can('monitor.create'), 403, 'Anda tidak memiliki izin untuk menambah monitor.');
        if ($request->has('url')) {
            $url = $request->input('url');
            if ($url && ! preg_match('~^(?:f|ht)tps?://~i', $url)) {
                $request->merge(['url' => 'http://'.$url]);
            }
        }

        Log::info('Add Monitor Input:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|unique:monitors,url',
            'interval' => 'required|integer|min:1',
        ]);

        Monitor::create($validated);

        return response()->json([
            'data' => 'OK',
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_if(! auth()->user()->can('monitor.edit'), 403, 'Anda tidak memiliki izin untuk mengubah monitor.');
        $monitor = Monitor::find($id);

        if (! $monitor) {
            return response()->json([
                'error' => 'Data tidak ditemukan',
            ], 404);
        }

        if ($request->has('url')) {
            $url = $request->input('url');
            if ($url && ! preg_match('~^(?:f|ht)tps?://~i', $url)) {
                $request->merge(['url' => 'http://'.$url]);
            }
        }

        Log::info('Update Monitor Input:', $request->all());

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|url|unique:monitors,url,'.$id,
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
            ],
        ]);
    }

    public function destroy($id)
    {
        abort_if(! auth()->user()->can('monitor.delete'), 403, 'Anda tidak memiliki izin untuk menghapus monitor.');
        $monitor = Monitor::find($id);

        if (! $monitor) {
            return response()->json([
                'error' => 'Data tidak ditemukan',
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
            'data' => $data,
        ]);
    }
}
