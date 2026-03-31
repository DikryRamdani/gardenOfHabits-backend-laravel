<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gardens extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'plant_stage',
        'hp',
        'last_decay_check',
    ];

    protected $casts = [
        'last_decay_check' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
