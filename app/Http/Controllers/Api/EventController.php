<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;



class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return EventResource::collection(
            Event::with('user')->paginate()
        );

//        return EventResource::collection(Event::with('user')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        //
        $event_data =  $request->validated();

        $event_data['user_id'] = 1;
        $event_data['description'] = fake()->text;

        $event = Event::create($event_data);

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
        $event->load('user', 'attendees');
        return new EventResource($event);

     }

    /**
     * Update the specified resource in storage.
     */   

    public function update(Request $request, Event $event)
    {
        // 

        $event->update(
            
            $request->validate([ 
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            ])
        
        );

        return new EventResource($event);

    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Event $event)
    {
        //
        $event->delete();

        return response(status: 204);
    }

}
