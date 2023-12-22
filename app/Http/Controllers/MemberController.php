<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{

    // get members of an event

    public function getMembersOfAnEvent($eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
            ], 404);
        }

        return response()->json([
            'members' => $event->members()->get(),
        ], 200);
    }

    // create member of an evvent

    public function createMemberOfAnEvent($eventRequestId)
    {
        $eventRequest = EventRequest::find($eventRequestId);

        if (!$eventRequest) {
            return response()->json([
                'message' => 'Event request not found'
            ], 404);
        }

        $event = Event::find($eventRequest->event_id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        if ($eventRequest->receiver_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to accept this event request'
            ]);
        }

        $event->members()->create([
            'user_id' => $eventRequest->sender_id,
        ]);

        $eventRequest->delete();

        return response()->json([
            'message' => 'Event request accepted succesfully',
            'event' => $event,
        ]);
    }

    // delete a member from an event
    public function deleteMemberFromEvent($eventId, $userId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
            ], 404);
        }

        $member = Member::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            return response()->json([
                'message' => 'Member not found in the event',
            ], 404);
        }

        if (auth()->user()->id !== $event->user_id) {
            return response()->json([
                'message' => 'You are not authorized to remove a member from this event',
            ], 403);
        }

        $member->delete();

        return response()->json([
            'message' => 'Member removed from the event successfully',
        ], 200);
    }
}
