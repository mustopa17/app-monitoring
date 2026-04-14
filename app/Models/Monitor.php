<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
        'interval',
        'status',
        'response_time',
        'checked_at',
    ];
}
