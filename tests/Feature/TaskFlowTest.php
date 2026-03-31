<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class TaskFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_success(): void
    {
        $user = User::factory()->create();
        $user->garden()->create(['exp' => 0, 'hp' => 100]);
        Sanctum::actingAs($user);

        $res = $this->postJson('/api/tasks', [
            'title' => 'Task 1',
            'description' => 'Desc',
            'difficulty' => 'medium',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('message', 'Task created successfully');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Task 1',
            'difficulty' => 'medium',
        ]);
    }

    public function test_complete_task_updates_garden_and_marks_task_done(): void
    {
        $user = User::factory()->create();
        $garden = $user->garden()->create(['exp' => 0, 'hp' => 90]);
        $task = $user->tasks()->create([
            'title' => 'Task hard',
            'difficulty' => 'hard',
            'deadline' => now()->addDays(1),
            'is_completed' => false,
        ]);

        Sanctum::actingAs($user);

        $res = $this->patchJson('/api/tasks/' . $task->id . '/complete');

        $res->assertOk()
            ->assertJsonPath('message', 'Task completed successfully')
            ->assertJsonPath('data.current_exp', 30)
            ->assertJsonPath('data.current_hp', 100);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_completed' => true,
        ]);

        $this->assertDatabaseHas('gardens', [
            'id' => $garden->id,
            'exp' => 30,
            'hp' => 100,
        ]);
    }

    public function test_complete_task_cannot_be_claimed_twice(): void
    {
        $user = User::factory()->create();
        $garden = $user->garden()->create(['exp' => 0, 'hp' => 100]);
        $task = $user->tasks()->create([
            'title' => 'Task easy',
            'difficulty' => 'easy',
            'deadline' => now()->addDay(),
            'is_completed' => false,
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/tasks/' . $task->id . '/complete')->assertOk();
        $this->patchJson('/api/tasks/' . $task->id . '/complete')->assertStatus(404);

        $this->assertDatabaseHas('gardens', [
            'id' => $garden->id,
            'exp' => 10,
            'hp' => 100,
        ]);
    }

    public function test_garden_endpoint_returns_user_garden(): void
    {
        $user = User::factory()->create();
        $user->garden()->create(['exp' => 12, 'hp' => 88]);
        Sanctum::actingAs($user);

        $res = $this->getJson('/api/garden');

        $res->assertOk()
            ->assertJsonPath('message', 'Garden fetched successfully')
            ->assertJsonPath('data.exp', 12)
            ->assertJsonPath('data.hp', 88);
    }
}