<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Contracts\GardenRepositoryInterface;
use App\Repositories\Eloquent\EloquentGardenRepository;
use App\Repositories\Contracts\PomodoroRepositoryInterface;
use App\Repositories\Eloquent\EloquentPomodoroRepository;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;
use App\Repositories\Eloquent\EloquentRefreshTokenRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Eloquent\EloquentTaskRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        # hubungkan interface repository dengan implementasi eloquent konkret
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(GardenRepositoryInterface::class, EloquentGardenRepository::class);
        $this->app->bind(PomodoroRepositoryInterface::class, EloquentPomodoroRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, EloquentRefreshTokenRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
    }

    public function boot(): void
    {
    }
}
