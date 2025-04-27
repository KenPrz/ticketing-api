<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Models\Chat;
use App\Models\User;
use App\Enums\ReadStatus;
use App\Http\Resources\{
    ChatResource,
    MessageResource,
    UserChatResource,
};

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Get the authenticated user's chat history.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $chats = $this->chatService->getUserChatHistory($user);

        return response()->json([
            'user' => new UserChatResource($user),
            'chats' => ChatResource::collection($chats),
        ]);
    }

    /**
     * Create a new chat with another user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        $otherUser = User::findOrFail($request->user_id)->first();
        
        $chat = $this->chatService->findOrCreateChat($user, $otherUser);

        return response()->json([
            'chat' => new ChatResource($chat),
            'other_user' => new UserChatResource($chat->getOtherUser($user->id)),
        ], 201);
    }

    /**
     * Display the specified chat with its messages.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $chat = $this->chatService->getChatById($user, $id);

        return response()->json([
            'user' => new UserChatResource($user),
            'chat' => new ChatResource($chat),
            'other_user' => new UserChatResource($chat->getOtherUser($user->id)),
        ]);
    }

    /**
     * Remove the specified chat from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $this->chatService->deleteChat($user, $id);

        return response()->json([
            'message' => 'Chat deleted successfully',
        ], 200);
    }

    /**
     * Get or create a conversation with a specific user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversationWithUser(Request $request, $userId)
    {
        $user = $request->user();
        $chat = $this->chatService->getConversationWithUser($user, $userId);

        return response()->json([
            'user' => new UserChatResource($user),
            'chat' => new ChatResource($chat),
            'other_user' => new UserChatResource($chat->getOtherUser($user->id)),
        ]);
    }

    /**
     * Send a message to a specific chat.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessageToChat(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = $request->user();
        $chat = Chat::findOrFail($id);
        
        // Ensure the user is part of this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat');
        }
        
        // Get the other user in the chat
        $otherUser = $chat->getOtherUser($user->id);
        
        // Use the existing service method to send the message
        $message = $this->chatService->sendMessage($user, $otherUser->id, [
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => new MessageResource($message),
            'chat' => new ChatResource($message->chat),
        ], 201);
    }

    /**
     * Mark all unread messages in a chat as read.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markMessagesAsRead(Request $request, $id)
    {
        $user = $request->user();
        $chat = Chat::findOrFail($id);
        
        // Ensure the user is part of this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat');
        }
        
        // Mark all unread messages from the other user as read
        $updated = $chat->messages()
            ->where('read_status', ReadStatus::UNREAD)
            ->where('user_id', '!=', $user->id)
            ->update(['read_status' => ReadStatus::READ]);
        
        return response()->json([
            'success' => true,
            'updated_count' => $updated
        ]);
    }
}