<?php

namespace App\Http\Controllers;


use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;

class FriendRequestController extends Controller
{
    // get my friend requests
    public function getMyFriendRequests()
    {
        $user = User::find(auth()->user()->id);

        $sentFriendRequests = $user->sentFriendRequest()->with('receiver')->get();
        $receivedFriendRequests = $user->receivedFriendRequest()->with('sender')->get();

        return response()->json([
            'sent_friend_requests' => $sentFriendRequests,
            'received_friend_requests' => $receivedFriendRequests,
        ], 200);
    }

    // create friend request

    public function createFriendRequest(Request $request, $userId)
    {
        $data = $request->validate([
            'request_text' => 'required|string|max:122',
        ]);

        $receiverUser = User::find($userId);

        if (!$receiverUser) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($userId == auth()->user()->id) {
            return response()->json([
                'message' => 'You can not send friend request to yourself',
            ], 403);
        }

        $friend = $receiverUser->friends()->where('user_id', auth()->user()->id)->first();
        $existingFriendRequest = FriendRequest::where('sender_id', auth()->user()->id)
            ->where('receiver_id', $userId)
            ->first();

        if (!$friend && !$existingFriendRequest) {
            $createdFriendRequest =  $receiverUser->receivedFriendRequest()->create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => $userId,
                'request_text' => $data['request_text'],
            ]);

            return response()->json([
                'message' => 'Friend request sended',
                'friend_request' => $createdFriendRequest,
            ], 201);
        } else {
            return response()->json([
                'message' => 'You already friend or your request is still waiting...',
            ], 405);
        }
    }

    // delete friend request

    public function deleteFriendRequest($friendRequestId)
    {
        $friendRequest = FriendRequest::find($friendRequestId);

        if (!$friendRequest) {
            return response()->json([
                'message' => 'Friend request not found',
            ], 404);
        }


        if ($friendRequest->receiver_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to reject this friend request',
            ], 403);
        }

        $friendRequest->delete();

        return response()->json([
            'message' => 'Friend request rejected successfully',
        ], 200);
    }
}
