<?php

namespace App\Http\Controllers\Events;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\{
    EventService,
    TicketTierService,
};
use App\Http\Resources\EventResource;
use App\Http\Requests\{
    EventStoreRequest,
    EventUpdateRequest,
    EventAddImagesRequest,
};
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    /**
     * @var EventService $eventService An instance of the EventService used to handle event-related operations.
     */
    protected $eventService;

    /**
     * @var TicketTierService $ticketTierService An instance of the TicketTierService used to handle ticket tier-related operations.
     */
    protected $ticketTierService;

    /**
     * EventController constructor.
     *
     * @param EventService $eventService The service used to handle event-related operations.
     * @param TicketTierService $ticketTierService The service used to handle ticket tier-related operations.
     */
    public function __construct(
        EventService $eventService,
        TicketTierService $ticketTierService
    ) {
        $this->eventService = $eventService;
        $this->ticketTierService = $ticketTierService;
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
                    200,
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
     * Get detailed event information including all related data.
     * 
     * @param Request $request The request object
     * @param string $id The ID of the event to retrieve
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventDetails(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $event = $this->eventService->getEventFullDetails($id);
            
            if (!$event) {
                return response()
                    ->json(
                        ['message' => 'Event not found'],
                        404
                    );
            }
            
            // Check if the user is authorized to view the details
            if ($event->organizer_id !== $user->id) {
                return response()
                    ->json(
                        ['message' => 'Unauthorized'],
                        403
                    );
            }
            
            // Transform the event data to include all relations
            $eventData = [
                'id' => $event->id,
                'name' => $event->name,
                'date' => $event->date->format('Y-m-d'),
                'time' => $event->time,
                'description' => $event->description,
                'venue' => $event->venue,
                'city' => $event->city,
                'longitude' => (float) $event->longitude,
                'latitude' => (float) $event->latitude,
                'category' => $event->category->value,
                'isPublished' => (bool) $event->is_published,
                'isCancelled' => (bool) $event->is_cancelled,
                'cancelledReason' => $event->cancelled_reason,
                'publishedAt' => $event->published_at ? $event->published_at->toDateTimeString() : null,
                'createdAt' => $event->created_at->toDateTimeString(),
                'updatedAt' => $event->updated_at->toDateTimeString(),
            ];
            
            // Add image URLs
            if ($event->banner) {
                $eventData['bannerUrl'] = $event->banner->image_url;
            }
            
            if ($event->thumbnail) {
                $eventData['thumbnailUrl'] = $event->thumbnail->image_url;
            }
            
            if ($event->venueImage) {
                $eventData['venueImageUrl'] = $event->venueImage->image_url;
            }
            
            if ($event->seatPlanImage) {
                $eventData['seatPlanImageUrl'] = $event->seatPlanImage->image_url;
            }
            
            if ($event->gallery && $event->gallery->count() > 0) {
                $eventData['galleryUrls'] = $event->gallery->pluck('image_url')->toArray();
            }
            
            // Add ticket tiers data with sold count
            if ($event->ticketTiers && $event->ticketTiers->count() > 0) {
                $eventData['ticketTiers'] = $event->ticketTiers
                    ->map(function ($tier) {
                        return [
                            'id' => $tier->id,
                            'tier_name' => $tier->tier_name,
                            'price' => (float) $tier->price,
                            'quantity' => $tier->quantity,
                            'ticket_type' => $tier->ticket_type->value,
                            'sold' => $tier->tickets->count(),
                        ];
                    })
                    ->toArray();
                
                // Calculate total tickets sold and available
                $soldTickets = 0;
                $totalTickets = 0;
                
                foreach ($eventData['ticketTiers'] as $tier) {
                    $soldTickets += $tier['sold'];
                    $totalTickets += $tier['quantity'];
                }
                
                $eventData['ticketSales'] = [
                    'sold' => $soldTickets,
                    'available' => $totalTickets - $soldTickets,
                ];
            } else {
                $eventData['ticketTiers'] = [];
                $eventData['ticketSales'] = [
                    'sold' => 0,
                    'available' => 0,
                ];
            }

            return response()
                ->json(
                    ['event' => $eventData],
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
     * @param EventUpdateRequest $request The request object
     * @param string $id The ID of the event to update
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EventUpdateRequest $request, string $id)
    {
        try {
            // Get the event
            $event = $this->eventService->getOrganizerEvent($id);
            
            // Get validated data
            $data = $request->validated();
            
            // Update event basic data (filter out image and ticket fields)
            $eventData = array_filter($data, function($key) {
                return !in_array($key, ['banner', 'thumbnail', 'venueImage', 'seatPlanImage', 'gallery', 'tickets', 'tickets_data']);
            }, ARRAY_FILTER_USE_KEY);
            
            // Update the event with basic data
            $event->update($eventData);
            $event = $event->fresh();
            
            // Handle image updates if provided
            $images = [];
            $imageFields = ['banner', 'thumbnail', 'seatPlanImage', 'venueImage', 'gallery'];
            
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    if ($field === 'gallery') {
                        $images[$field] = $request->file($field);
                    } else {
                        $images[$field] = $request->file($field);
                    }
                }
            }
            
            // Update images if any were provided
            if (!empty($images)) {
                $event = $this->eventService->addImages($event, $images);
            }
            
            // Handle ticket updates if provided
            if ($request->has('tickets') && $request->input('tickets') !== '[]') {
                $tickets = json_decode($request->input('tickets')) ?? [];
                
                // Update ticket tiers
                // First, delete existing ticket tiers if they exist
                if ($event->ticketTiers && $event->ticketTiers->count() > 0) {
                    $this->ticketTierService->deleteTicketTiers($event);
                }
                
                // Create new ticket tiers
                $this->ticketTierService->createTicketsAndSeats($event, $tickets);
            }
            
            // Get the updated event with all relationships
            $updatedEvent = $this->eventService->getEventFullDetails($event->id);
            
            return response()
                ->json(
                    ['event' => $updatedEvent, 'message' => 'Event updated successfully'],
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
            $eventData = [
                'name' => $data['name'],
                'organizer_id' => $data['organizer_id'],
                'date' => $data['date'],
                'time' => $data['time'],
                'description' => $data['description'],
                'venue' => $data['venue'],
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'city' => $data['city'],
                'category' => $data['category'],
                'is_published' => $data['is_published'] ?? false,
            ];

            $images = [
                'banner' => $data['banner'],
                'thumbnail' => $data['thumbnail'],
                'seatPlanImage' => $data['seatPlanImage'],
                'venueImage' => $data['venueImage'],
                'gallery' => $data['gallery'] ?? [],
            ];

            $tickets = json_decode($data['tickets']) ?? [];
            $event = $this->eventService->createEvent($eventData);

            $updatedEvent = $this->eventService->addImages($event, $images);
            $this->ticketTierService->createTicketsAndSeats(
                $updatedEvent,
                $tickets
            );

            return response()
                ->json(
                    ['event' => $updatedEvent],
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
            $user = $request->user();
            $event = $this->eventService->getOrganizerEvent($id);
            // Check if user is authorized to update this event
            if ($event->organizer_id !== $user->id) {
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

    /**
     * Publish an event.
     *
     * @param Request $request The request object
     * @param string $eventId The ID of the event to publish
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishEvent(Request $request, string $eventId)
    {
        try {
            $event = $this->eventService->getOrganizerEvent($eventId);
            $user = $request->user();

            // Check if user is authorized to publish this event
            if ($event->organizer_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->eventService->publishEvent($event);

            return response()
                ->json(
                    ['message' => 'Event published successfully', 'success' => true],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage(), 'success' => false],
                    400,
                );
        }
    }

    /**
     * Unpublish an event.
     *
     * @param Request $request The request object
     * @param string $eventId The ID of the event to unpublish
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublishEvent(Request $request, string $eventId)
    {
        try {
            $event = $this->eventService->getOrganizerEvent($eventId);
            $user = $request->user();

            // Check if user is authorized to unpublish this event
            if ($event->organizer_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->eventService->unpublishEvent($event);

            return response()
                ->json(
                    ['message' => 'Event unpublished successfully', 'success' => true],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage(), 'success' => false],
                    400,
                );
        }
    }

    /**
     * Get events created by the authenticated organizer.
     *
     * @param Request $request The request object
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function organizerEvents(Request $request)
    {
        try {
            $user = $request->user();

            return response()
                ->json(
                    [
                        'upcomingEvents' => $this->eventService->fetchOrganizerUpcomingEvents($user),
                        'pastEvents' => $this->eventService->fetchOrganizerPastEvents($user),
                        'draftEvents' => $this->eventService->fetchOrganizerUnpublishedEvents($user),
                    ],
                    200,
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