<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // get my joined events
    public function getMyJoinedEvents()
    {

        $currentUser = User::find(auth()->user()->id);

        $joinedEvents = $currentUser->joinedEvents()->with('members')->get();

        return response()->json([
            'joined_events' => $joinedEvents,
        ], 200);
    }

    // create event
    public function createEvent(Request $request)
    {

        $data = $request->validate([
            'title' => 'required|string',
            'subtitle' => 'required|string',
            'price' => 'required|numeric',
            'city' => 'required|string',
            'town' => 'required|string',
            'address' => 'required|string',
            'event_date' => 'required|date',
            'member_count' => 'required|integer',
        ]);

        $event = Event::create([
            'user_id' => auth()->user()->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
            'member_count' => $data['member_count'],
            'price' => $data['price'],
            'city' => $data['city'],
            'town' => $data['town'],
            'address' => $data['address'],
            'event_date' => $data['event_date'],
        ]);

        $event->members()->create([
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event,
        ], 201);
    }

    // get current user events
    public function getCurrentUserEvents()
    {

        $user = User::find(auth()->user()->id);

        $events = $user->events;

        return response()->json([
            'events' => $events,
        ], 200);
    }

    // get all events
    public function getAllEvents()
    {

        return response()->json([
            'events' => Event::orderBy('created_at', 'desc')
                ->with('user:id,name,surname,phone,image')
                ->withCount('members')
                ->get(),
        ], 200);
    }

    // delete event
    public function deleteEvent($eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found',
            ], 404);
        }

        if ($event->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
        ], 200);
    }
}
