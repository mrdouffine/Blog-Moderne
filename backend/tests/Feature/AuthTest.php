<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@blog.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                    ],
                    'token',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@blog.local',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::create([
            'name' => 'Existing User',
            'email' => 'existing@blog.local',
            'password' => Hash::make('password123'),
            'role' => 'reader',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'existing@blog.local',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                    'token',
                ],
                'message',
            ]);
    }

    public function test_user_can_get_profile(): void
    {
        $user = User::create([
            'name' => 'Authenticated User',
            'email' => 'auth@blog.local',
            'password' => Hash::make('password123'),
            'role' => 'reader',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'auth@blog.local',
                'name' => 'Authenticated User',
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout@blog.local',
            'password' => Hash::make('password123'),
            'role' => 'reader',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => null,
                'message' => 'Déconnexion réussie.',
            ]);

        $this->assertCount(0, $user->tokens);
    }
}
