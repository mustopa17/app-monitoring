<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use App\Models\MonitorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform health checks on all registered monitors based on their intervals.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monitors = Monitor::all();
        $this->info('Checking '.$monitors->count().' monitors...');

        foreach ($monitors as $monitor) {
            // Check if it's time to perform the health check (precision in seconds)
            $diffSeconds = is_null($monitor->checked_at) ? null : abs(now()->diffInSeconds($monitor->checked_at));
            $due = is_null($monitor->checked_at) || $diffSeconds >= ($monitor->interval * 60);

            if (! $due) {
                // Optional: decrease noise in logs if desired, but for now we keep it
                continue;
            }

            $this->info("Checking {$monitor->url}...");

            $startTime = microtime(true);
            $status = 'DOWN';
            $statusCode = null;
            $errorMessage = null;

            try {
                $response = Http::timeout(10)
                    ->get($monitor->url);

                $statusCode = $response->status();

                if ($response->successful()) {
                    $status = 'UP';
                } else {
                    $errorMessage = 'HTTP Error: '.$statusCode;
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }

            $responseTime = round((microtime(true) - $startTime) * 1000);
            $checkedAt = now();

            // Update Monitor table
            $monitor->update([
                'status' => $status,
                'response_time' => $responseTime,
                'checked_at' => $checkedAt,
            ]);

            // Create Log entry
            MonitorLog::create([
                'monitor_id' => $monitor->id,
                'status' => $status,
                'response_time' => $responseTime,
                'status_code' => $statusCode,
                'error_message' => $errorMessage,
                'checked_at' => $checkedAt,
            ]);

            $this->line("Result: {$status} ({$responseTime}ms)");
        }

        $this->info('Monitoring check complete.');
    }
}
