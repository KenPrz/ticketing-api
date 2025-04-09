<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\HomeRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Services\EventService;

class HomeController extends Controller
{
    /**
     * @var EventService $eventService An instance of the EventService used to handle event-related operations.
     */
    protected $eventService;

    /**
     * HomeController constructor.
     *
     * @param EventService $eventService The service used to handle event-related operations.
     *
     * @return void
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display the feed of events.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $requestData = $request->user()
            ->only(['recent_latitude', 'recent_longitude']);
        
        $upcomingEvents = $this->eventService->upcomingEvents();
        $nearbyEvents = $this->eventService->nearbyEvents(
            $requestData['recent_latitude'],
            $requestData['recent_longitude'],
        );
        $forYouEvents = $this->eventService->forYouEvents();
        
        $feedData = [
            'upcoming_events' => EventResource::collection($upcomingEvents)
                ->response()
                ->getData(true),
            'nearby_events' => EventResource::collection($nearbyEvents)
                ->response()
                ->getData(true),
            'for_you' => EventResource::collection($forYouEvents)
                ->response()->getData(true),
        ];

        return response()
            ->json(
                [
                    $feedData,
                ],
                200
            );
    }

    /**
     * Display the list of upcoming events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUpcomingEvents()
    {
        $upcomingEvents = $this->eventService->upcomingEvents(
            false,
            false,
        );

        return response()
            ->json(
                [
                    EventResource::collection($upcomingEvents)
                        ->response()
                        ->getData(true),
                ],
                200
            );
    }

    /**
     * Display the list of nearby events.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listNearbyEvents(Request $request)
    {
        $requestData = $request->user()
            ->only(
                [
                'recent_latitude',
                'recent_longitude'
            ]
        );
    
        
        $nearbyEvents = $this->eventService->nearbyEvents(
            $requestData['recent_latitude'],
            $requestData['recent_longitude'],
            false,
            false,
        );

        return response()
            ->json(
                [
                    EventResource::collection($nearbyEvents)
                        ->response()
                        ->getData(true),
                ],
                200
            );
    }

    /**
     * Display the list of events recommended for the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listForYouEvents()
    {
        $forYouEvents = $this->eventService->forYouEvents(
            false,
            false,
        );

        return response()
            ->json(
                [
                    EventResource::collection($forYouEvents)
                        ->response()
                        ->getData(true),
                ],
                200
            );
    }

    /**
     * Update the user's location.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $request->user()->update([
            'recent_latitude' => $request->latitude,
            'recent_longitude' => $request->longitude,
        ]);

        return response()
            ->json(
                [
                    'message' => 'Location updated successfully',
                ],
                200
            );
    }
}
