<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventTicketsResource;
use App\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TicketService;

class TicketController extends Controller
{
    /**
     * @var TicketService $ticketService An instance of the TicketService used to handle ticket-related operations.
     */
    protected $ticketService;

    /**
     * TicketController constructor.
     *
     * @param TicketService $ticketService The service used to handle ticket-related operations.
     */
    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Get the Tickets of the authenticated user, grouped by event.
     * 
     * @param Request $request The request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get tickets for the authenticated user
            $tickets = $this->ticketService->getMyTickets($request->user());

            return response()
                ->json(
                    new EventTicketsResource($tickets),
                    200
                );
        } catch (\Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                400
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request The request object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validated();

            $ticket = $this->ticketService->createTicket($data);

            return response()
                ->json(
                    ['ticket' => $ticket],
                    201
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id The ID of the ticket to retrieve
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $ticket = $this->ticketService->getTicket($id);

            return response()
                ->json(
                    ['ticket' => $ticket],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param string $id The ID of the ticket to edit
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(string $id)
    {
        try {
            $ticket = $this->ticketService->getTicket($id);

            return response()
                ->json(
                    ['ticket' => $ticket],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Request $request The request object
     * @param string $id The ID of the ticket to update
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        Request $request,
        string $id
    ) {
        try {
            $data = $request->validated();

            $ticket = $this->ticketService->updateTicket($id, $data);

            return response()
                ->json(
                    ['ticket' => $ticket],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param string $id The ID of the ticket to delete
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $this->ticketService->deleteTicket($id);

            return response()
                ->json(
                    ['message' => 'The ticket has been deleted.'],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Transfer the ticket to another user.
     * 
     * @param Request $request The request object
     * @param string $id The ID of the ticket to transfer
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(
        Request $request,
        string $id
    ) {
        try {
            $data = $request->validated();

            $ticket = $this->ticketService->transferTicket($id, $data['user_id']);

            return response()
                ->json(
                    ['ticket' => $ticket],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Mark the ticket as used.
     *
     * @param string $id The ID of the ticket to mark as used
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUsed(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user->isOrganizer()) {
            return response()
                ->json(
                    ['message' => 'Unauthorized'],
                    403
                );
        }
        try {
            $ticket = $this->ticketService->markTicketAsUsed(
                $id,
                $user,
            );

            return response()
                ->json(
                    ['ticket' => $ticket],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }
}
