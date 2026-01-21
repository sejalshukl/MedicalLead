<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Events\LeadCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadService
{
    /**
     * Create a new lead and trigger assignment.
     */
    public function createLead(array $data): Lead
    {
        $lead = Lead::create($data);

        // Trigger Event instead of dispatching Job directly (Better Architecture)
        event(new LeadCreated($lead));

        return $lead;
    }

    /**
     * Get leads based on user role.
     */
    public function getLeadsForUser(User $user): LengthAwarePaginator
    {
        if ($user->role === 'admin') {
            return Lead::with('assignedTo')->latest()->paginate(10);
        }

        return Lead::forCoordinator($user)->with('assignedTo')->latest()->paginate(10);
    }

    /**
     * Update lead status.
     */
    public function updateLeadStatus(Lead $lead, string $status): Lead
    {
        $lead->update(['status' => $status]);
        return $lead;
    }
}
