<?php

namespace App\Services;

use App\Enums\FriendStatus;
use App\Models\UserFriend;

class FriendService
{

    protected $model;

    /**
     * Constructor to initialize the model.
     *
     * @param \App\Models\UserFriend $model
     */
    public function __construct(UserFriend $model)
    {
        $this->model = $model;
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
        return $this->model->create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendStatus::PENDING,
        ]);
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
        return $this->model->where([
            'user_id' => $friendId,
            'friend_id' => $userId,
            'status' => FriendStatus::PENDING,
        ])->update(['status' => FriendStatus::ACCEPTED]);
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
    public function getFriends(
        int $userId
    ): \Illuminate\Database\Eloquent\Collection {
        return $this->model->where('user_id', $userId)
            ->where('status', FriendStatus::ACCEPTED)
            ->with('friend')
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
}