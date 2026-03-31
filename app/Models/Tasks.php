<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'difficulty',
        'deadline',
        'is_completed',
        'created_at',
        'updated_at',
        'completed_at',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
