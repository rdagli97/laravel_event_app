<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // create conversation
    public function createConversation($receiverUserId)
    {

        $receiverUser = User::find($receiverUserId);

        if (!$receiverUser) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $currentUserId  = auth()->user()->id;

        if ($receiverUserId == $currentUserId) {
            return response()->json([
                'message' => 'You can not create a conversation with yourself',
            ], 403);
        }

        $existingConversation = Conversation::where(function ($query) use ($receiverUserId, $currentUserId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $receiverUserId);
        })->orWhere(function ($query) use ($receiverUserId, $currentUserId) {
            $query->where('sender_id', $receiverUserId)
                ->where('receiver_id', $currentUserId);
        })->first();

        if ($existingConversation) {
            return response()->json([
                'conversation' => $existingConversation,
            ], 200);
        }

        $createdConversation = Conversation::create([
            'sender_id' => $currentUserId,
            'receiver_id' => $receiverUserId,
        ]);

        return response()->json([
            'message' => 'Conversation created',
            'conversation' => $createdConversation,
        ], 201);
    }

    // get my conversations
    public function getMyConversations()
    {
        $currentUserId = auth()->user()->id;

        $conversations = Conversation::where(function ($conversations) use ($currentUserId) {
            $conversations->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId);
        })->with('messages', function ($messages) {
            return $messages->orderBy('created_at', 'desc')->get();
        })->get();

        if (!$conversations) {
            return response()->json([
                'message' => 'There is no conversation yet',
            ], 404);
        }

        return response()->json([
            'conversations' => $conversations,
        ]);
    }
}
