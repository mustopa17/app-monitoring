<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorLog extends Model
{
    protected $table = 'monitors_log';

    public $timestamps = false;

    protected $fillable = [
        'monitor_id',
        'status',
        'response_time',
        'status_code',
        'error_message',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
