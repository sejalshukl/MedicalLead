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
     * @OA\Post(
     *      path="/api/appointments",
     *      tags={"Leads"},
     *      summary="Create a new appointment request",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"patient_name","email","phone","country","medical_issue","preferred_date"},
     *              @OA\Property(property="patient_name", type="string"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="country", type="string"),
     *              @OA\Property(property="medical_issue", type="string"),
     *              @OA\Property(property="preferred_date", type="string", format="date")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *      )
     * )
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
