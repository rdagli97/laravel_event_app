<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    // get my friends
    public function getMyFriends()
    {
        $user = User::find(auth()->user()->id);

        $friends = $user->friends()->with('friend')->get();

        return response()->json([
            'friends' => $friends,
        ], 200);
    }

    // create friend
    public function createFriend($friendRequestId)
    {
        $friendRequest = FriendRequest::find($friendRequestId);

        if (!$friendRequest) {
            return response()->json([
                'message' => 'Friend request not found',
            ], 404);
        }

        if ($friendRequest->receiver_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to accept this friend request',
            ], 403);
        }

        $user = User::find(auth()->user()->id);
        $friend = User::find($friendRequest->sender_id);

        $user->friends()->create([
            'friend_id' => $friend->id,
            'user_id' => $friend->id,
        ]);

        $friend->friends()->create([
            'friend_id' => auth()->user()->id,
            'user_id' => auth()->user()->id,
        ]);

        $friendRequest->delete();

        return response()->json([
            'message' => 'Friend request accepted successfully',
            'friend' => $friend,
        ]);
    }

    // delete friend

    public function deleteFriend($userId)
    {
        $user = User::find(auth()->user()->id);
        $friend = User::find($userId);

        if (!$friend) {
            return response()->json([
                'message' => 'User not found which you trying to delete',
            ], 404);
        }

        if ($userId == auth()->user()->id) {
            return response()->json([
                'message' => 'You can not remove yourself from your friend list',
            ], 403);
        }

        $myFriend = $user->friends()->where('friend_id', $userId);
        $hisFriend = $friend->friends()->where('friend_id', auth()->user()->id);

        if (!$myFriend) {
            return response()->json([
                'message' => 'Friend not found'
            ], 404);
        }

        $myFriend->delete();
        $hisFriend->delete();

        return response()->json([
            'message' => 'User removed from your friend list successfully',
        ], 200);
    }
}
