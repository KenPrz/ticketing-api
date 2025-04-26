<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendFriendRequest;
use App\Http\Requests\FriendActionRequest;
use App\Http\Resources\UserFriendResource;
use App\Http\Resources\UserFriendCollection;
use App\Services\FriendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * The service instance.
     *
     * @var \App\Services\FriendService
     */
    protected $friendService;

    /**
     * Constructor to initialize the service.
     *
     * @param \App\Services\FriendService $friendService
     */
    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    /**
     * Get the authenticated user's friend list.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $friends = $this->friendService->getFriends($userId);

        return response()->json(new UserFriendCollection($friends));
    }

    /**
     * Send a friend request.
     *
     * @param \App\Http\Requests\SendFriendRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendRequest(SendFriendRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;
        
        $result = $this->friendService->sendFriendRequest($userId, $friendId);
        
        return response()->json([
            'message' => 'Friend request sent successfully',
            'data' => new UserFriendResource($result)
        ], 201);
    }
    
    /**
     * Accept a friend request.
     *
     * @param \App\Http\Requests\FriendActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptRequest(FriendActionRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;
        
        $result = $this->friendService->acceptFriendRequest($userId, $friendId);
        
        if (!$result) {
            return response()->json([
                'message' => 'Friend request not found or already processed'
            ], 404);
        }
        
        // Get the friendship record to return
        $friendship = $this->friendService->getFriendship($friendId, $userId);
        
        return response()->json([
            'message' => 'Friend request accepted successfully',
            'data' => $friendship ? new UserFriendResource($friendship) : null
        ]);
    }
    
    /**
     * Reject a friend request.
     *
     * @param \App\Http\Requests\FriendActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRequest(FriendActionRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;
        
        $result = $this->friendService->rejectFriendRequest($userId, $friendId);
        
        if (!$result) {
            return response()->json([
                'message' => 'Friend request not found or already processed'
            ], 404);
        }
        
        return response()->json([
            'message' => 'Friend request rejected successfully'
        ]);
    }
    
    /**
     * Block a user.
     *
     * @param \App\Http\Requests\FriendActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function blockUser(FriendActionRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;
        
        $result = $this->friendService->blockUser($userId, $friendId);
        
        if (!$result) {
            return response()->json([
                'message' => 'User not found or already blocked'
            ], 404);
        }

        return response()->json([
            'message' => 'User blocked successfully'
        ]);
    }
    
    /**
     * Unblock a user.
     *
     * @param \App\Http\Requests\FriendActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblockUser(FriendActionRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;
        
        $result = $this->friendService->unblockUser($userId, $friendId);

        if (!$result) {
            return response()->json([
                'message' => 'User not found or not blocked'
            ], 404);
        }
        
        return response()->json([
            'message' => 'User unblocked successfully'
        ]);
    }
    
    /**
     * Get all pending friend requests sent by the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSentRequests(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        
        $sentRequests = $this->friendService->getSentFriendRequests($userId);
        
        return response()->json(new UserFriendCollection($sentRequests));
    }
    
    /**
     * Get all pending friend requests received by the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReceivedRequests(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        
        $receivedRequests = $this->friendService->getReceivedFriendRequests($userId);
        
        return response()->json(new UserFriendCollection($receivedRequests));
    }

    /**
     * Remove a friend.
     *
     * @param \App\Http\Requests\FriendActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelFriendRequest(FriendActionRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;

        $result = $this->friendService->cancelFriendRequest($userId, $friendId);

        if (!$result) {
            return response()->json([
                'message' => 'Friend request not found or already processed'
            ], 404);
        }

        return response()->json([
            'message' => 'Friend request cancelled successfully'
        ]);
    }

    /**
     * Remove a friend.
     *
     * @param \App\Http\Requests\FriendActionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFriend(FriendActionRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $friendId = $request->friend_id;

        $result = $this->friendService->removeFriend($userId, $friendId);

        if (!$result) {
            return response()->json([
                'message' => 'Friendship not found or already removed'
            ], 404);
        }

        return response()->json([
            'message' => 'Friend removed successfully'
        ]);
    }
}