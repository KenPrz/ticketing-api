<?php

namespace App\Services;

use App\Enums\EventImageType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class EventService 
{
    /**
     * The event model instance.
     *
     * @var Event
     */
    protected $event;

    /**
     * Construct the event service instance.
     *
     * @param  Event  $event  The event model instance
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Create a new event with the given data.
     *
     * @param  array  $data  The data to create the event with
     *
     * @return Event  The created event instance
     */
    public function createEvent(array $data): Event
    {
        $event = $this->event->create($data);

        return $event->fresh();
    }

    /**
     * Add images to an event.
     *
     * @param Event $event The event to add images to
     * @param array $images The images to add
     *
     * @return Event The updated event instance
     */
    public function addImages(Event $event, array $images): Event
    {
        // Handle single image types (banner, thumbnail, venue)
        $singleImageTypes = [
            'banner' => EventImageType::BANNER, 
            'thumbnail' => EventImageType::THUMBNAIL, 
            'venueImage' => EventImageType::VENUE
        ];

        foreach ($singleImageTypes as $key => $type) {
            if (isset($images[$key])) {
                // Delete existing image of this type if it exists
                $event->images()
                    ->where('image_type', $type)
                    ->delete();

                // Add the new image
                $file = $images[$key];
                $imagePath = $file->store('events/' . $event->id . '/' . strtolower($key), 'public');
                $event->images()->create([
                    'image_url' => $imagePath,
                    'image_type' => $type,
                ]);
            }
        }

        // Handle gallery images (multiple)
        if (isset($images['gallery']) && is_array($images['gallery'])) {
            foreach ($images['gallery'] as $galleryImage) {
                $imagePath = $galleryImage->store('events/' . $event->id . '/gallery', 'public');
                $event->images()->create([
                    'image_url' => $imagePath,
                    'image_type' => EventImageType::GALLERY,
                ]);
            }
        }

        return $event->fresh()->load(['banner', 'thumbnail', 'venueImage', 'gallery']);
    }

    /**
     * Publish an event by its ID.
     *
     * @param  Event $event The ID of the event to publish
     *
     * @throws ModelNotFoundException  When event is not found
     * @throws \Exception  When event is already published
     *
     * @return Event  The published event instance
     */
    public function publishEvent(Event $event)
    {
        if ($event->is_published) {
            throw new \Exception('Event is already published.');
        }

        $event->is_published = true;
        $event->save();

        return $event;
    }

    /**
     * Unpublish an event by its ID.
     *
     * @param  Event $event The ID of the event to unpublish
     *
     * @throws ModelNotFoundException  When event is not found
     * @throws \Exception  When event is already unpublished
     *
     * @return Event  The unpublished event instance
     */
    public function unpublishEvent(Event $event)
    {

        if (!$event->is_published) {
            throw new \Exception('Event is already unpublished.');
        }

        $event->is_published = false;
        $event->save();

        return $event;
    }

    /**
     * Get an event by its ID.
     *
     * @param  int $perPage  The number of events to show per page.
     *
     * @throws ModelNotFoundException  When event is not found
     *
     * @return LengthAwarePaginator  The paginated collection of events.
     */
    public function getevents(
        int $perPage = 10,
    ): LengthAwarePaginator {
        return $this->event
            ->where('is_published', true)
            ->paginate($perPage);
    }

    /**
     * Get an event by its ID.
     *
     * @param  string  $id  The ID of the event to retrieve
     *
     * @throws ModelNotFoundException  When event is not found
     *
     * @return Event|null  The found event instance
     */
    public function getEvent(string $id): Event | null
    {
        return $this->event
            ->where('is_published', true)
            ->find($id);
    }

    /**
     * Update an event with the given data.
     *
     * @param  string  $id  The ID of the event to update
     * @param  array  $data  The data to update the event with
     *
     * @throws ModelNotFoundException  When event is not found
     *
     * @return Event  The updated event instance
     */
    public function updateEvent(string $id, array $data): Event
    {
        $event = $this->getEvent($id);
        $event->update($data);

        return $event->fresh();
    }

    /**
     * Delete an event by its ID.
     *
     * @param  string  $id  The ID of the event to delete
     *
     * @throws ModelNotFoundException  When event is not found
     *
     * @return bool  True if deletion was successful
     */
    public function deleteEvent(string $id): bool
    {
        $event = $this->getEvent($id);
        
        return $event->delete();
    }

    /**
     * Fetch the upcoming events in the next one and a half months.
     *
     * @param bool $isPaginated
     * @param bool $isHomeLimited 
     *
     * @return Collection<Event> | LengthAwarePaginator The collection of upcoming events
     */
    public function upcomingEvents(
        bool $isPaginated = false,
        bool $isHomeLimited = true,
    ): Collection | LengthAwarePaginator {
        $inOneAndHalfMonths = now()->addMonths(1)->addDays(15);
        $query = $this->event
            ->where('is_published', true)
            ->where('date', '>=', now())
            ->where('date', '<=', $inOneAndHalfMonths)
            ->orderBy('date', 'asc');

            if($isPaginated) {
                return $query->paginate(config('constants.pagination_limit'));
            }

            if ($isHomeLimited)
            {
                return $query->limit(config('constants.home_limit'))->get();
            }

            return $query->get();
    }

    /**
     * Fetch the nearby events within a 50km radius.
     * 
     * @param  float  $latitude  The latitude of the user
     * @param  float  $longitude  The longitude of the user
     * @param bool $isPaginated
     * @param bool $isHomeLimited 
     * 
     * @return Collection<Event> | LengthAwarePaginator The collection of nearby events
     */
    public function nearbyEvents(
        float $latitude,
        float $longitude,
        bool $isPaginated = false,
        bool $isHomeLimited = true,
    ): Collection | LengthAwarePaginator {
        $userPoint = "POINT({$longitude} {$latitude})";

        // Fetch the default radius from the config file (default 50km)
        $maxDistanceInKm = config('constants.default_radius');

        $query =  $this->event
            ->where('is_published', true)
            ->selectRaw("*, ST_Distance_Sphere(
                POINT(longitude, latitude), 
                ST_GeomFromText(?)
            ) / 1000 AS distance", [$userPoint])
            ->having('distance', '<', $maxDistanceInKm)
            ->orderBy('distance', 'asc');
        
        if($isPaginated) {
            return $query->paginate(config('constants.pagination_limit'));
        }

        if ($isHomeLimited)
        {
            return $query->limit(config('constants.home_limit'))->get();
        }

        return $query->get();
    }

    /**
     * Fetch the events that are recommended for the user.
     * 
     * @param bool $isPaginated
     * @param bool $isHomeLimited 
     * 
     * @return Collection<Event> | LengthAwarePaginator  The collection of recommended events
     */
    public function forYouEvents(
        bool $isPaginated = false,
        bool $isHomeLimited = true,
    ): Collection | LengthAwarePaginator {
        $query = $this->event
            ->where('is_published', true)
            ->inRandomOrder();

        if($isPaginated) {
            return $query->paginate(config('constants.pagination_limit'));
        }

        if ($isHomeLimited)
        {
            return $query->limit(config('constants.home_limit'))->get();
        }

        return $query->get();
    }

    /**
     * Fetch the events that are trending.
     * 
     * @return Collection<Event> | LengthAwarePaginator  The collection of trending events
     */
    public function getBookmarkedEvents(User $user): Collection
    {
        return $user->eventBookmarks;
    }

    /**
     * Fetch the events that are trending.
     * 
     * @param string $eventId The ID of the event to retrieve
     * 
     * @return Event  The found event instance
     */
    public function getEventForPurchase(string $eventId): Event
    {
        return $this->event
            ->where('is_published', true)
            ->with([
                'ticketTiers',
                'seatPlanImage',
            ])
            ->findOrFail($eventId);
    }

    /**
     * Fetch the events that are organized by the user.
     * 
     * @param User $user The user instance
     * 
     * @return mixed
     */
    public function getOrganizerEvents(User $user) 
    {
        return $user->events()
            ->with([
                'images',
                'ticketTiers',
                'seatPlanImage',
            ])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Fetch a specific even that are organized by the user.
     * 
     * @param string $id The user instance
     * 
     * @return mixed
     */
    public function getOrganizerEvent(string $id) 
    {
        return $this->event->findOrFail($id);
    }
    
    /**
     * Fetch the dashboard data for the organizer.
     * 
     * @param User $user The user instance
     * 
     * @return array  The dashboard data
     */
    public function fetchOrganizerDashboardData(User $user): array
    {
        $events = $this->getOrganizerEvents($user)
            ->where('is_published', true)
            ->get();

        $totalTicketSales = $events->sum(function ($event) {
                return $event->ticketTiers()
                    ->withCount('tickets')
                    ->get()
                    ->sum('tickets_count');
            });

        $revenue = $events->sum(function ($event) {
                return $event->ticketTiers()
                    ->withCount('tickets as sold_tickets')
                    ->get()
                    ->sum(function ($tier) {
                        return $tier->sold_tickets * $tier->price;
                    });
            });

        return [
            'activeEvents' => $events->count(),
            'totalTicketSales' => $totalTicketSales,
            'revenue' => $revenue,
            'attendees' => $totalTicketSales,
        ];
    }

    /**
     * Fetch the events that are organized by the user.
     * 
     * @param User $user The user instance
     * 
     * @return mixed
     */
    public function fetchOrganizerUpcomingEvents(User $user)
    {
        $data =  $this->getOrganizerEvents($user)
            ->where('is_published', true)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(config('constants.home_limit'))
            ->get();
        
        return $data->map(function ($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'date' => $event->date->format('M j, Y'),
                'venue' => $event->venue,
                'city' => $event->city,
                'ticketSales' => [
                    'sold' => $event->ticketTiers()
                        ->withCount('tickets')
                        ->get()
                        ->sum('tickets_count'),
                    'available' => $event->ticketTiers()
                        ->withCount('tickets')
                        ->get()
                        ->sum('quantity'),
                ],
            ];
        });
    }
}