<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    const NOT_STARTED = 1;
    const START = 2;
    const PAUSE = 3;
    const FINISH = 4;

    protected $fillable = [
        'name',
        'description',
        'leader',
        'status',
        'started_at',
        'finished_at'
    ];

    public $dates = [
        'started_at',
        'finished_at',
        'created_at',
        'updated_at',
    ];
}
