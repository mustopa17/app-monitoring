<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitorLog;

class MonitorLogController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->can('log.view'), 403, 'Anda tidak memiliki izin untuk melihat log.');
        $logs = MonitorLog::with('monitor')->latest('checked_at')->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'monitor_id' => $log->monitor_id,
                    'monitor_name' => $log->monitor ? $log->monitor->name : ('Deleted Monitor (ID: '.$log->monitor_id.')'),
                    'status' => $log->status,
                    'response_time' => $log->response_time,
                    'status_code' => $log->status_code,
                    'error_message' => $log->error_message,
                    'checked_at' => $log->checked_at,
                ];
            }),
        ]);
    }

    public function destroy($id)
    {
        abort_if(! auth()->user()->can('log.clear'), 403, 'Anda tidak memiliki izin untuk menghapus log.');
        $log = MonitorLog::find($id);
        if (! $log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        $log->delete();

        return response()->json(['message' => 'Log deleted successfully']);
    }

    public function clearAll()
    {
        abort_if(! auth()->user()->can('log.clear'), 403, 'Anda tidak memiliki izin untuk membersihkan semua log.');
        MonitorLog::query()->delete();

        return response()->json(['message' => 'All logs cleared successfully']);
    }

    public function export()
    {
        abort_if(! auth()->user()->can('log.export'), 403, 'Anda tidak memiliki izin untuk mengekspor log.');
        $logs = MonitorLog::with('monitor')->latest('checked_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Timestamp', 'Monitor Name', 'Status', 'Response Time (ms)', 'Code', 'Error Message']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->checked_at,
                    $log->monitor ? $log->monitor->name : ('Deleted Monitor (ID: '.$log->monitor_id.')'),
                    $log->status,
                    $log->response_time,
                    $log->status_code,
                    $log->error_message ?? 'OK',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
