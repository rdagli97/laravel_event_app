<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Http\Request;

class EventRequestController extends Controller
{

    // get my event requests
    public function getMyEventRequests()
    {
        $user = User::find(auth()->user()->id);

        $sentEventRequests = $user->sentEventRequests()->with('receiver')->get();
        $receivedEventRequests = $user->receivedEventRequests()->with('sender')->get();

        return response()->json([
            'sent_event_requests' => $sentEventRequests,
            'received_event_requests' => $receivedEventRequests,
        ], 200);
    }



    // create event request
    public function createEventRequest(Request $request, $eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
            ], 404);
        }

        $data = $request->validate([
            'request_text' => 'required|string|max:122',
        ]);

        if ($event->user_id == auth()->user()->id) {
            return response()->json([
                'message' => 'You can not send event request to yourself',
            ], 403);
        }

        $eventOwner = User::find($event->user_id);
        $existingEventRequest = EventRequest::where('sender_id', auth()->user()->id)
            ->where('receiver_id', $eventOwner->id)
            ->first();

        if (!$existingEventRequest && $eventOwner) {
            $createdEventRequest = $eventOwner->receivedEventRequests()->create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => $event->user_id,
                'request_text' => $data['request_text'],
                'event_id' => $eventId,
            ]);

            return response()->json([
                'message' => 'Event request sended successfully',
                'event_request' => $createdEventRequest,
            ], 201);
        } else {
            return response()->json([
                'message' => 'You already send a request or join',
            ], 405);
        }
    }

    // delete event request
    public function deleteEventRequest($eventRequestId)
    {

        $eventRequest = EventRequest::find($eventRequestId);

        if (!$eventRequest) {
            return response()->json([
                'message' => 'Event request not found',
            ], 404);
        }

        if ($eventRequest->receiver_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to reject this event request',
            ], 403);
        }

        $eventRequest->delete();

        return response()->json([
            'message' => 'Event request rejected successfully',
        ], 200);
    }
}
