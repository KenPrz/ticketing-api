<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
}