<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\Monitor;
use Illuminate\Support\Facades\Request;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/api/monitors', 'GET')
);

echo "--- LIST ---\n";
echo $response->getContent();
echo "\n";

$monitor = Monitor::first();
if ($monitor) {
    $id = $monitor->id;
    echo "--- UPDATE ID $id ---\n";
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::create("/api/monitors/$id", 'PUT', ['name' => 'Updated Name'])
    );
    echo $response->getContent();
    echo "\n";

    echo "--- DELETE ID $id ---\n";
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::create("/api/monitors/$id", 'DELETE')
    );
    echo $response->getContent();
    echo "\n";
} else {
    echo "No monitors found to test update/delete.\n";
}
