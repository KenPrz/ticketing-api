<?php

namespace App\Services;

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
        return $this->event->paginate($perPage);
    }

    /**
     * Get an event by its ID.
     *
     * @param  string  $id  The ID of the event to retrieve
     *
     * @throws ModelNotFoundException  When event is not found
     *
     * @return Event  The found event instance
     */
    public function getEvent(string $id): Event
    {
        return $this->event->findOrFail($id);
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
        $query = $this->event->inRandomOrder();

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

    public function getEventForPurchase(string $eventId): Event
    {
        return $this->event
            ->with([
                'ticketTiers',
                'seatPlanImage',
            ])
            ->findOrFail($eventId);
    }
}