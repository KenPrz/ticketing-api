<?php

namespace App\Http\Controllers\Events;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Http\Resources\EventResource;

class EventController extends Controller
{

    /**
     * @var EventService $eventService An instance of the EventService used to handle event-related operations.
     */
    protected $eventService;

    /**
     * EventController constructor.
     *
     * @param EventService $eventService The service used to handle event-related operations.
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $eventdata = $this->eventService->getEvents();

            $events = EventResource::collection($eventdata)
                ->response()
                ->getData(true);

            return response()
                ->json(
                    ['events' => $events],
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
     * Display the specified resource.
     * 
     * @param string $id The ID of the event to retrieve
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $eventData = $this->eventService->getEvent($id);
            $event = EventResource::make($eventData)
                ->response()
                ->getData(true);

            return response()
                ->json(
                    $event,
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
     * @param string $id The ID of the event to edit
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(string $id)
    {
        try {
            $event = $this->eventService->getEvent($id);

            return response()
                ->json(
                    ['event' => $event],
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
     * @param string $id The ID of the event to update
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        Request $request,
        string $id
    ) {
        try {
            $data = $request->validated();

            $event = $this->eventService->getEvent($id);

            $event->update($data);

            return response()
                ->json(
                    ['event' => $event],
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
     * @param string $id The ID of the event to delete
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $event = $this->eventService->getEvent($id);

            $event->delete();

            return response()
                ->json(
                    ['message' => 'The event has been deleted.'],
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
