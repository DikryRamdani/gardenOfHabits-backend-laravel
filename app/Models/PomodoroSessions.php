<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PomodoroSessions extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'duration_minutes',
        'status',
        'cooldown_until',
        'created_at',
    ];

    protected $casts = [
        'cooldown_until' => 'datetime',
        'created_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
