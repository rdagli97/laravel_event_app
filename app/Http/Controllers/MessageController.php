<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request, $receiverUserId)
    {
        $receiverUser = User::find($receiverUserId);
        $currentUserId = auth()->user()->id;

        if ($receiverUserId == auth()->user()->id) {
            return response()->json([
                'message' => 'You can not send message to yourself',
            ], 403);
        }

        if (!$receiverUser) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $data = $request->validate([
            'body' => 'required|string|max:255',
        ]);

        $existingConversation = Conversation::where(function ($query) use ($receiverUserId, $currentUserId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $receiverUserId);
        })->orWhere(function ($query) use ($receiverUserId, $currentUserId) {
            $query->where('sender_id', $receiverUserId)
                ->where('receiver_id', $currentUserId);
        })->first();

        if (!$existingConversation) {
            $createdConversation = Conversation::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $receiverUserId,
            ]);

            $sendedBody = Message::create([
                'body' => $data['body'],
                'sender_id' => $currentUserId,
                'receiver_id' => $receiverUserId,
                'conversation_id' => $createdConversation->id,
            ]);

            return response()->json([
                'message' => 'Conversation started',
                'conversation' => $createdConversation,
                'body' => $sendedBody,
            ], 201);
        }

        $sendedBody = Message::create([
            'body' => $data['body'],
            'sender_id' => $currentUserId,
            'receiver_id' => $receiverUserId,
            'covnersation_id' => $existingConversation->id,
        ]);

        return response()->json([
            'message' => 'Message sended',
            'conversation' => $existingConversation,
            'body' => $sendedBody,
        ], 201);
    }
}
