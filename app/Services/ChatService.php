<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Enums\ReadStatus;
use Illuminate\Database\Eloquent\Builder;

class ChatService
{
    /**
     * Get the authenticated user's chat history.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserChatHistory($user)
    {
        return Chat::forUser($user->id)
            ->with(['userOne', 'userTwo', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get();
    }

    /**
     * Find or create a chat between two users.
     *
     * @param \App\Models\User $userOne
     * @param \App\Models\User $userTwo
     *
     * @return \App\Models\Chat
     */
    public function findOrCreateChat($userOne, $userTwo)
    {
        // Sort user IDs to ensure we don't create duplicate chats
        $userIds = [$userOne->id, $userTwo->id];
        sort($userIds);
        
        $chat = Chat::where(function (Builder $query) use ($userIds) {
            $query->where('user_one_id', $userIds[0])
                ->where('user_two_id', $userIds[1]);
        })->first();
        
        if (!$chat) {
            $chat = Chat::create([
                'user_one_id' => $userIds[0],
                'user_two_id' => $userIds[1],
            ]);
        }
        
        return $chat;
    }

    /**
     * Send a message to another user.
     *
     * @param \App\Models\User $sender
     * @param int $receiverId
     * @param array $data
     * @return \App\Models\Message
     */
    public function sendMessage($sender, $receiverId, array $data)
    {
        $receiver = User::findOrFail($receiverId);
        $chat = $this->findOrCreateChat($sender, $receiver);
        
        $message = $chat->messages()->create([
            'content' => $data['content'],
            'read_status' => ReadStatus::UNREAD,
            'user_id' => $sender->id,
        ]);
        
        return $message;
    }

    /**
     * Get a specific chat by ID.
     *
     * @param \App\Models\User $user
     * @param int $id
     * @return \App\Models\Chat
     */
    public function getChatById($user, $id)
    {
        $chat = Chat::with(['userOne', 'userTwo', 'messages.user'])->findOrFail($id);
        
        // Ensure the user is part of this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat');
        }
        
        // Mark all unread messages from the other user as read
        $chat->messages()
            ->where('read_status', ReadStatus::UNREAD)
            ->where('user_id', '!=', $user->id)
            ->update(['read_status' => ReadStatus::READ]);
        
        return $chat;
    }

    /**
     * Delete a specific chat by ID.
     *
     * @param \App\Models\User $user
     * @param int $id
     * @return bool
     */
    public function deleteChat($user, $id)
    {
        $chat = Chat::findOrFail($id);
        
        // Ensure the user is part of this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat');
        }
        
        return $chat->delete();
    }

    /**
     * Get the conversation with a specific user.
     *
     * @param \App\Models\User $user
     * @param int $userId
     * @return \App\Models\Chat
     */
    public function getConversationWithUser($user, $userId)
    {
        $otherUser = User::findOrFail($userId);
        return $this->findOrCreateChat($user, $otherUser);
    }
}