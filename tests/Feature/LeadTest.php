<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lead;
use Laravel\Sanctum\Sanctum;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_create_appointment_request()
    {
        $response = $this->postJson('/api/appointments', [
            'patient_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'country' => 'USA',
            'medical_issue' => 'Knee Pain',
            'preferred_date' => '2026-02-01',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'lead']);

        $this->assertDatabaseHas('leads', ['email' => 'john@example.com']);
    }

    public function test_admin_can_view_all_leads()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        Lead::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/leads');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_coordinator_can_only_view_assigned_leads()
    {
        $coordinator = User::factory()->create(['role' => 'coordinator']);
        $otherCoordinator = User::factory()->create(['role' => 'coordinator']);
        Sanctum::actingAs($coordinator);

        Lead::factory()->create(['assigned_to' => $coordinator->id]);
        Lead::factory()->create(['assigned_to' => $otherCoordinator->id]);

        $response = $this->getJson('/api/coordinator/leads');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_coordinator_can_update_status_of_assigned_lead()
    {
        $coordinator = User::factory()->create(['role' => 'coordinator']);
        Sanctum::actingAs($coordinator);

        $lead = Lead::factory()->create(['assigned_to' => $coordinator->id, 'status' => 'new']);

        $response = $this->patchJson("/api/leads/{$lead->id}/status", [
            'status' => 'contacted'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'contacted']);
    }

    public function test_coordinator_cannot_update_status_of_unassigned_lead()
    {
        $coordinator = User::factory()->create(['role' => 'coordinator']);
        $otherCoordinator = User::factory()->create(['role' => 'coordinator']);
        Sanctum::actingAs($coordinator);

        $lead = Lead::factory()->create(['assigned_to' => $otherCoordinator->id, 'status' => 'new']);

        $response = $this->patchJson("/api/leads/{$lead->id}/status", [
            'status' => 'contacted'
        ]);

        $response->assertStatus(403);
    }
    public function test_lead_is_automatically_assigned_to_coordinator()
    {
        $coordinator1 = User::factory()->create(['role' => 'coordinator']);
        $coordinator2 = User::factory()->create(['role' => 'coordinator']);

        // Create first lead
        $this->postJson('/api/appointments', [
            'patient_name' => 'Patient 1',
            'email' => 'p1@example.com',
            'phone' => '111',
            'country' => 'USA',
            'medical_issue' => 'Issue 1',
            'preferred_date' => '2026-02-01',
        ]);

        // Create second lead
        $this->postJson('/api/appointments', [
            'patient_name' => 'Patient 2',
            'email' => 'p2@example.com',
            'phone' => '222',
            'country' => 'USA',
            'medical_issue' => 'Issue 2',
            'preferred_date' => '2026-02-01',
        ]);

        $this->assertDatabaseHas('leads', ['email' => 'p1@example.com']);
        $this->assertDatabaseHas('leads', ['email' => 'p2@example.com']);

        // Since it's queued, we might need to process the queue or assert pushed
        // But for Feature test with Sync queue (default in testing usually), it should be done.
        // Let's check if assigned_to is not null.
        $lead1 = Lead::where('email', 'p1@example.com')->first();
        $this->assertNotNull($lead1->assigned_to);
    }

    public function test_notification_is_sent_to_coordinator()
    {
        \Illuminate\Support\Facades\Notification::fake();

        $coordinator = User::factory()->create(['role' => 'coordinator']);

        $this->postJson('/api/appointments', [
            'patient_name' => 'Patient Notification',
            'email' => 'notify@example.com',
            'phone' => '333',
            'country' => 'USA',
            'medical_issue' => 'Issue Notification',
            'preferred_date' => '2026-02-01',
        ]);

        \Illuminate\Support\Facades\Notification::assertSentTo(
            [$coordinator],
            \App\Notifications\LeadAssignedNotification::class
        );
    }
}
