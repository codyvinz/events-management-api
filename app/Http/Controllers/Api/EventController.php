<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;


use App\Http\Traits\CanLoadRelationships;
use Illuminate\Support\Facades\Gate;


class EventController extends Controller
{
    use CanLoadRelationShips;

    private array $relations= ['user', 'attendees', 'attendees.user'];


    public function __construct() {

        $this->middleware('auth:sanctum')->except(['index', 'show']);

    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = $this->loadRelationships(Event::query());

        // foreach ($relations as $relation) {
        //     $query->when(
        //         $this->shouldIncludeRelation($relation),
        //         fn($q) => $q->with($relation)
        //     );
        // }

        return EventResource::collection(
            $query->latest()->paginate()
        );
    
    }


    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        //
        $event_data =  $request->validated();

        $event_data['user_id'] = 1;
        $event_data['description'] = $event_data['description'] ?? fake()->text ;

        $event = Event::create($event_data);

        return new EventResource($this->loadRelationships($event));
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

        // if(Gate::denies('update-event', $event)){
        //     abort(403, "Unauthorized to update event!");
        // }

        $this->authorize('update-event', $event);

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
