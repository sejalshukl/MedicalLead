<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AssignLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lead;

    /**
     * Create a new job instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            // Find the last assigned coordinator
            $lastAssignedId = DB::table('leads')
                ->whereNotNull('assigned_to')
                ->where('id', '<', $this->lead->id)
                ->orderBy('id', 'desc')
                ->value('assigned_to');

            // Get all coordinators ordered by ID
            $coordinators = User::where('role', 'coordinator')->orderBy('id')->pluck('id');

            if ($coordinators->isEmpty()) {
                return; // No coordinators to assign
            }

            // Determine next coordinator
            $nextCoordinatorId = null;
            if ($lastAssignedId) {
                $nextCoordinatorId = $coordinators->first(function ($id) use ($lastAssignedId) {
                    return $id > $lastAssignedId;
                });
            }

            // If no next coordinator found (end of list or first assignment), pick the first one
            if (!$nextCoordinatorId) {
                $nextCoordinatorId = $coordinators->first();
            }

            // Assign lead
            $this->lead->update(['assigned_to' => $nextCoordinatorId]);

            // Send Notification
            $coordinator = User::find($nextCoordinatorId);
            if ($coordinator) {
                try {
                    $coordinator->notify(new LeadAssignedNotification($this->lead));
                    \Illuminate\Support\Facades\Log::info("Email sent to coordinator {$coordinator->email} for lead {$this->lead->id}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send email to coordinator {$coordinator->email}: " . $e->getMessage());
                }
            }
        });
    }
}
