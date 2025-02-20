<?php

namespace App\Http\Controllers;

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
     * Cancel the ticket.
     * 
     * @param Request $request The request object
     * @param string $id The ID of the ticket to cancel
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(
        Request $request,
        string $id
    ) {
        try {
            $ticket = $this->ticketService->cancelTicket($id);

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
     * Use the ticket.
     * 
     * @param Request $request The request object
     * @param string $id The ID of the ticket to use
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function use(
        Request $request,
        string $id
    ) {
        try {
            $ticket = $this->ticketService->useTicket($id);

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