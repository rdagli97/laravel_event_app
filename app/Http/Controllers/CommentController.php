<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get current user's comments
    public function getMyComments()
    {
        $currentUser = User::find(auth()->user()->id);

        $comments = $currentUser->comments()->get();

        if (!$comments) {
            return response()->json([
                'message' => 'There is no comment of this User yet.',
            ], 404);
        }

        return response()->json([
            'comments' => $comments,
        ], 200);
    }

    // create comment
    public function addCommentAfterEvent(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($userId == auth()->user()->id) {
            return response()->json([
                'message' => 'You can not create comment to yourself',
            ], 403);
        }

        $data = $request->validate([
            'body' => 'required|string|max:122',
            'rate' => 'required|numeric|min:1|max:5',
        ]);

        $haveCommonEvent = $user->joinedEvents()->whereHas('members', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->exists();



        if (!$haveCommonEvent) {
            return response()->json([
                'message' => 'You and the user must have joined at least one event together',
            ], 403);
        }

        $eventFinished = $user->joinedEvents()->whereHas('members', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->where('event_date', '<', now())->exists();

        if (!$eventFinished) {
            return response()->json([
                'message' => 'The event you joined together must be already finished.',
            ], 403);
        }

        $existComment = $user->comments()->where('owner_id', auth()->user()->id)->first();

        if ($existComment) {

            return response()->json([
                'message' => 'You already made a comment for this user',
            ], 403);
        }

        echo (auth()->user()->id);

        $comment = $user->comments()->create([
            'owner_id' => auth()->user()->id,
            'body' => $data['body'],
            'rate' => $data['rate'],
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment,
        ], 201);
    }

    // get average rating of user
    public function getAverageRating($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $averageRating = $user->getAverageRating();

        if ($averageRating !== null) {
            return response()->json([
                'average_rating' => $averageRating,
            ], 200);
        } else {
            return response()->json([
                'message' => 'There is no comment of this user',
            ], 404);
        }
    }
}
