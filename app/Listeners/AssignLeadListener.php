<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Jobs\AssignLeadJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignLeadListener
{
    /**
     * Handle the event.
     */
    public function handle(LeadCreated $event): void
    {
        AssignLeadJob::dispatch($event->lead);
    }
}
