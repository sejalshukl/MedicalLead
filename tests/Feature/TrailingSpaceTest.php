<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class TrailingSpaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_url_with_trailing_space_returns_404()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // Intentionally add a space at the end
        $response = $this->postJson('/api/admin/coordinators ');

        // Assert it fails with 404
        $response->assertStatus(404);
    }

    public function test_url_without_trailing_space_works()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // Correct URL
        $response = $this->postJson('/api/admin/coordinators', [
            'name' => 'Test Coord',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        // Assert it succeeds (201 Created)
        $response->assertStatus(201);
    }
}
