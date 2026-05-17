<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AdminTicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function pendingAdmin1(): JsonResponse
    {
        //Using scope to get tickets for admin 1
        $tickets = Ticket::pendingAdmin1()->get();

        return response()->json([
            'data' => TicketResource::collection($tickets)
        ]);
    }

    public function pendingAdmin2(): JsonResponse
    {
        //Using scope to get tickets for admin 2
        $tickets = Ticket::pendingAdmin2()->get();

        return response()->json([
            'data' => TicketResource::collection($tickets)
        ]);
    }

    public function approveAdmin1(Ticket $ticket): JsonResponse
    {
        //Checking through policy, whether admin can approve this ticket or not
        $this->authorize('approveAdmin1', $ticket);

        $ticket = $this->ticketService
            ->approveByAdmin1($ticket, auth()->user());

        return response()->json([
            'data' => new TicketResource($ticket)
        ]);
    }

    public function rejectAdmin1(Ticket $ticket)
    {
        //Checking through policy, whether admin can reject this ticket or not
        $this->authorize('rejectAdmin1', $ticket);

        $this->ticketService->rejectByAdmin1(
            $ticket,
            auth()->user()
        );

        return response()->json([
            'data' => new TicketResource($ticket)
        ]);
    }

    public function approveAdmin2(Ticket $ticket): JsonResponse
    {
        //Checking through policy, whether admin can approve this ticket or not
        $this->authorize('approveAdmin2', $ticket);

        $ticket = $this->ticketService
            ->approveByAdmin2($ticket, auth()->user());

        return response()->json([
            'data' => new TicketResource($ticket)
        ]);
    }

    public function rejectAdmin2(Ticket $ticket): JsonResponse
    {
        //Checking through policy, whether admin can reject this ticket or not
        $this->authorize('rejectAdmin2', $ticket);

        $ticket = $this->ticketService
            ->rejectByAdmin2($ticket, auth()->user());

        return response()->json([
            'data' => new TicketResource($ticket)
        ]);
    }

    public function bulkApproveAdmin1(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_ids' => ['required', 'array'],
            'ticket_ids.*' => ['exists:tickets,id']
        ]);

        $tickets = Ticket::whereIn('id', $request->ticket_ids)->get();

        foreach ($tickets as $ticket)
        {
            if (auth()->user()->can('approveAdmin1', $ticket))
            {
                $this->ticketService->approveByAdmin1($ticket, auth()->user());
            }
        }

        return response()->json([
            'message' => 'Tickets approved successfully'
        ]);
    }

    public function bulkApproveAdmin2(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_ids' => ['required', 'array'],
            'ticket_ids.*' => ['exists:tickets,id']
        ]);

        $tickets = Ticket::whereIn('id', $request->ticket_ids)->get();

        foreach ($tickets as $ticket)
        {
            if (auth()->user()->can('approveAdmin2', $ticket))
            {
                $this->ticketService->approveByAdmin2($ticket, auth()->user());
            }
        }

        return response()->json([
            'message' => 'Tickets approved successfully'
        ]);
    }
}
