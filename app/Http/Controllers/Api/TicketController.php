<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;


class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function index(): JsonResponse
    {
        $tickets = auth()->user()->tickets()->latest()->get();

        return response()->json([
            'data' => TicketResource::collection($tickets)
        ]);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['attachment_path'] = $request->file('attachment_path')->store('tickets', 'public');

        $ticket = $this->ticketService->createTicket(auth()->user(), $data);

        if($ticket)
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => new TicketResource($ticket),
            ], 201);

        return response()->json([
            'success' => false,
            'message' => 'Ticket was not craeted',
        ], 500);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $this->authorize('view', $ticket);

        return response()->json([
            'data' => new TicketResource($ticket)
        ]);
    }
}
