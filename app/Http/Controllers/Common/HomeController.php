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
        
        $feedData = [
            'upcoming_events' => EventResource::collection($this->eventService->upcomingEvents())
                ->response()
                ->getData(true),
            'nearby_events' => EventResource::collection($this->eventService->nearbyEvents(
                $requestData['recent_latitude'],
                $requestData['recent_longitude'],
            ))->response()->getData(true),
            'for_you' => EventResource::collection($this->eventService->forYouEvents())
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
}
