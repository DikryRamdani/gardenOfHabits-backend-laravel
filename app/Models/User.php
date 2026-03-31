<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PomodoroSessions;
use App\Models\RefreshToken;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'profile_picture',
        'total_exp',
        'exp_gained',
        'level',
        'streak_count',
        'streak_expired_at',
        'last_streak_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'streak_expired_at'  => 'datetime',
        ];
    }

    public function getExpRequiredAttribute(): int
    {
        $levelService = app(\App\Services\Gamification\LevelService::class);
        return $levelService->getExpReqForNextLevel($this->level ?? 1);
    }

    public function getMultiplierAttribute(): float
    {
        $streakService = app(\App\Services\Gamification\StreakService::class);
        return $streakService->getStreakMultiplier($this->streak_count ?? 0);
    }

    protected $appends = ['exp_required', 'multiplier'];

    public function garden()
    {
        return $this->hasOne(Gardens::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'user_id');
    }

    public function pomodoroSessions()
    {
        return $this->hasMany(PomodoroSessions::class, 'user_id');
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class, 'user_id');
    }
}
