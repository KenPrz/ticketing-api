<?php
namespace App\Services;

use App\Models\User;

class UserService 
{
    /**
     * The ticket model instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Construct the ticket service instance.
     *
     * @param User $ticket The ticket model instance
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get all users for the authenticated user.
     *
     * @param int $eventId The event ID
     * @param int $ticketTierId The ticket tier ID
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function updateUser(
        array $data,
        User $user,
    ): User {
        // Update user details
        $user->update($data);

        // Return the updated user
        return $user;
    }
}