<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadStatusRequest;
use App\Models\Lead;
use App\Services\LeadService;
use App\Http\Resources\LeadResource;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
   
     */
    public function store(StoreLeadRequest $request)
    {
        $lead = $this->leadService->createLead($request->validated());

        return response()->json([
            'message' => 'Appointment request submitted successfully',
            'lead' => new LeadResource($lead)
        ], 201);
    }

    public function index(Request $request)
    {
        $leads = $this->leadService->getLeadsForUser(Auth::user());
        return LeadResource::collection($leads);
    }

    public function updateStatus(UpdateLeadStatusRequest $request, Lead $lead)
    {
        $lead = $this->leadService->updateLeadStatus($lead, $request->status);

        return response()->json([
            'message' => 'Lead status updated successfully',
            'lead' => new LeadResource($lead)
        ]);
    }
}
