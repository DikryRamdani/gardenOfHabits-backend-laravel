<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_garden_and_token(): void
    {
        $res = $this->postJson('/api/register', [
            'name' => 'A',
            'email' => 'a@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('message', 'User registered successfully')
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'email'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'a@example.com']);

        $user = User::where('email', 'a@example.com')->first();
        $this->assertDatabaseHas('gardens', [
            'user_id'     => $user->id,
            'plant_stage' => 'seed',
            'hp'          => 100,
        ]);
    }

    public function test_login_returns_token(): void
    {
        User::factory()->create([
            'email' => 'b@example.com',
            'password' => 'password123',
        ]);

        $res = $this->postJson('/api/login', [
            'email' => 'b@example.com',
            'password' => 'password123',
        ]);

        $res->assertOk()
            ->assertJsonPath('message', 'Login successful')
            ->assertJsonStructure([
                'message',
                'data' => ['user', 'token'],
            ]);
    }
}