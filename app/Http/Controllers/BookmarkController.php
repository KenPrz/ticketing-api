<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Services\EventService;
use Illuminate\Http\Request;

class BookmarkController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $events = $this->eventService->getBookmarkedEvents($user);

        $data = EventResource::collection($events)
            ->response()
            ->getData(true);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $request->user()->eventBookmarks()->attach($request->event_id);

        return response()->json(['message' => 'Event bookmarked']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function destroy(Request $request, $id)
    {
        $request->user()->eventBookmarks()->detach($id);

        return response()->json(['message' => 'Event unbookmarked']);
    }
}
