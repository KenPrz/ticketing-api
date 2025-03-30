<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\Request;

class MapController extends Controller
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
     * Return the near events.
     *
     * @param Request $request The HTTP request instance
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validate latitude and longitude
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'sometimes|numeric'
        ]);

        // Get nearby events
        $events = $this->eventService->nearbyEvents(
            (float) $request->latitude,
            (float) $request->longitude,
            false // We don't need pagination for map display
        );

        // Transform the events to include only necessary data for markers
        $mapEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'date' => $event->date,
                'latitude' => $event->latitude,
                'longitude' => $event->longitude,
                'distance' => $event->distance,
                'image' => $event->image_url ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapEvents
        ]);
    }
}