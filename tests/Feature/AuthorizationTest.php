<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lead;
use Laravel\Sanctum\Sanctum;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_cannot_update_unassigned_lead()
    {
        $coordinator = User::factory()->create(['role' => 'coordinator']);
        $otherCoordinator = User::factory()->create(['role' => 'coordinator']);
        Sanctum::actingAs($coordinator);

        // Create a lead assigned to SOMEONE ELSE
        $lead = Lead::factory()->create(['assigned_to' => $otherCoordinator->id]);

        $response = $this->patchJson("/api/leads/{$lead->id}/status", [
            'status' => 'contacted'
        ]);

        // This MUST fail with 403
        $response->assertStatus(403);
    }
}
