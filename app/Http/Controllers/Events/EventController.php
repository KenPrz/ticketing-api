<?php

namespace App\Http\Controllers\Events;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Http\Resources\EventResource;
use App\Http\Requests\{
    EventStoreRequest,
    EventAddImagesRequest,
};

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
            if (!$eventData) {
                return response()
                    ->json(
                        ['message' => 'Event not found'],
                        404
                    );
            }
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

    /**
     * Store a newly created resource in storage. (Step 1).
     * 
     * @param EventStoreRequest The request object
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EventStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $event = $this->eventService->createEvent($data);

            return response()
                ->json(
                    ['event' => $event],
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
     * Add images to an event (Step 2).
     *
     * @param EventAddImagesRequest $request The request object
     * @param string $id The ID of the event
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function addImages(EventAddImagesRequest $request, string $id)
    {
        try {
            $event = $this->eventService->getEvent($id);
            
            // Check if user is authorized to update this event
            if ($event->organizer_id !== $request->auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Get validated data
            $images = $request->validated();

            $updatedEvent = $this->eventService->addImages($event, $images);

            return response()
                ->json(
                    [
                        'message' => 'Event images added successfully',
                        'event' => $updatedEvent
                    ],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400,
                );
        }
    }

    public function organizerEvents(Request $request)
    {
        try {
            $events = $this->eventService->getOrganizerEvents($request->user());

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
}
