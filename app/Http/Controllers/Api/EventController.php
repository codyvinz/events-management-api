<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Http\Requests\EventRequest;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Event::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        //
        $event_data=  $request->validated();

        $event_data['user_id'] = 1;
        $event_data['description'] = fake()->text;

        $event = Event::create($event_data);

        return $event;
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //

        return $event;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
