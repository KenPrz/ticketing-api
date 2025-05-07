<?php

namespace App\Services;

use App\Enums\FriendStatus;
use App\Events\{
    FriendRequestAccepted,
    FriendRequestSent,
};
use App\Models\{
    User,
    UserFriend,
};
use App\Enums\UserTypes;

class FriendService
{
    protected $model;
    protected $userModel;

    /**
     * Constructor to initialize the model.
     *
     * @param \App\Models\UserFriend $model
     * @param \App\Models\User $userModel
     */
    public function __construct(UserFriend $model, User $userModel)
    {
        $this->model = $model;
        $this->userModel = $userModel;
    }

    /**
     * Send a friend request.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return \App\Models\UserFriend
     */
    public function sendFriendRequest(
        int $userId,
        int $friendId
    ): UserFriend {

        // Check if the friend request already exists
        $existingRequest = $this->model->where([
            'user_id' => $userId,
            'friend_id' => $friendId,
        ])->first();

        if ($existingRequest) {
            // If the request already exists, return it
            return $existingRequest;
        }

        // Check if the user is already friends with the friend
        $existingFriendship = $this->model->where([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendStatus::ACCEPTED,
        ])->first();

        if ($existingFriendship) {
            // If they are already friends, return the existing friendship
            return $existingFriendship;
        }

        // Create a new friend request.
        $userFriend = $this->model->create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendStatus::PENDING,
        ]);

        // Get the user and friend models
        $sender = $this->userModel->findOrFail($userId);
        $recipient = $this->userModel->findOrFail($friendId);

        // Fire the event
        event(new FriendRequestSent($userFriend, $sender, $recipient));

        return $userFriend->load('friend'); // Load the friend relationship
    }

    /**
     * Accept a friend request.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return bool whether the update was successful
     */
    public function acceptFriendRequest(
        int $userId,
        int $friendId,
    ): bool {
        $updated = $this->model->where([
            'user_id' => $friendId,
            'friend_id' => $userId,
            'status' => FriendStatus::PENDING,
        ])->update(['status' => FriendStatus::ACCEPTED]);

        if ($updated) {
            // Get the user and friend models
            $recipient = $this->userModel->findOrFail($userId);
            $sender = $this->userModel->findOrFail($friendId);

            // Get the UserFriend record
            $userFriend = $this->model->where([
                'user_id' => $friendId,
                'friend_id' => $userId,
            ])->first();

            // Fire the event
            event(new FriendRequestAccepted($userFriend, $sender, $recipient));
        }

        return (bool) $updated;
    }

    /**
     * Reject a friend request.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return bool whether the update was successful
     */
    public function rejectFriendRequest(
        int $userId,
        int $friendId,
    ): bool {
        return $this->model->where([
            'user_id' => $friendId,
            'friend_id' => $userId,
            'status' => FriendStatus::PENDING,
        ])->update(['status' => FriendStatus::REJECTED]);
    }

    /**
     * Block a user.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return bool whether the update was successful
     */
    public function blockUser(
        int $userId,
        int $friendId,
    ): bool {
        return $this->model->where([
            'user_id' => $friendId,
            'friend_id' => $userId,
            'status' => FriendStatus::PENDING,
        ])->update(['status' => FriendStatus::BLOCKED]);
    }

    /**
     * Unblock a user.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return bool whether the update was successful
     */
    public function unblockUser(
        int $userId,
        int $friendId,
    ): bool {
        return $this->model->where([
            'user_id' => $friendId,
            'friend_id' => $userId,
            'status' => FriendStatus::BLOCKED,
        ])->update(['status' => FriendStatus::ACCEPTED]);
    }

    /**
     * Get all friends of a user.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFriends(int $userId): \Illuminate\Database\Eloquent\Collection {
        return $this->model
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhere('friend_id', $userId);
            })
            ->where('status', FriendStatus::ACCEPTED)
            ->with(['friend', 'user'])  // Load both relationships
            ->get();
    }

    /**
     * Get all pending friend requests sent by a user.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSentFriendRequests(
        int $userId
    ): \Illuminate\Database\Eloquent\Collection {
        return $this->model->where('user_id', $userId)
            ->where('status', FriendStatus::PENDING)
            ->with('friend')
            ->get();
    }

    /**
     * Get all pending friend requests received by a user.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReceivedFriendRequests(
        int $userId,
    ): \Illuminate\Database\Eloquent\Collection {
        return $this->model->where('friend_id', $userId)
            ->where('status', FriendStatus::PENDING)
            ->with('user')
            ->get();
    }
    
    /**
     * Get a specific friendship record.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return \App\Models\UserFriend|null
     */
    public function getFriendship(
        int $userId,
        int $friendId
    ): ?UserFriend {
        return $this->model->where([
            'user_id' => $userId,
            'friend_id' => $friendId,
        ])
        ->with(['user', 'friend'])
        ->first();
    }

    /**
     * Cancel a friend request.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return bool whether the deletion was successful
     */
    public function cancelFriendRequest(
        int $userId,
        int $friendId
    ): bool {
        return $this->model->where([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendStatus::PENDING,
        ])->delete();
    }

    /**
     * Remove a friend.
     *
     * @param int $userId
     * @param int $friendId
     *
     * @return bool whether the deletion was successful
     */
    public function removeFriend(
        int $userId,
        int $friendId
    ): bool {
        return $this->model->where([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendStatus::ACCEPTED,
        ])->delete();
    }

    /**
     * Fetch users by their phone numbers.
     *
     * @param array $phoneNumbers
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function fetchByContacts(
        array $phoneNumbers,
        User $user,
    ): \Illuminate\Database\Eloquent\Collection {
        return $this->userModel->whereIn('mobile', $phoneNumbers)
            ->whereIn('user_type', UserTypes::exposedUserTypes())
            ->where('id', '!=', $user->id)->get();
    }
}