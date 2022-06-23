<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon;
class EventController extends BaseController
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $events = Event::all();
        return $this->handleResponse(EventResource::collection($events), 'Events have been retrieved!');
    }
    
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'date' => 'required',
            'location_address' => 'required',
            'person_limit_amount' => 'required',
            'ticket_price' => 'required',
        ]);
        
        $event = new Event();
        $event->name = $request->name;
        $event->description = $request->description;
        $event['over_photo'] =  $request->file('over_photo')->store('public/overphotos');
        $event->user_id = auth()->user()->id;
        $event->date = $request->date;
        $event->location_address = $request->location_address;
        $event->person_limit_amount = $request->person_limit_amount;
        
        
        $event->ticket_price = $request->ticket_price;
        $event->save();
        $now = Carbon\Carbon::now()->format('Y-m-d');
        \QrCode::size(500)
        ->format('png')
        ->generate('http://127.0.0.1:8000/api/user/event/' . $event->id, public_path('/qrcodes/' . $event->name . '.' . $now . '.png'));
        $qr_path =  request()->getHttpHost().'/qrcodes/'.$event->name. '.' . $now . '.png';
        $event->qr_code = $qr_path;
        $event->update();
        return $this->handleResponse(new EventResource($event), 'Event created!');
    }
    
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $event = Event::find($id);
        if (is_null($event)) {
            return $this->handleError('Event not found!');
        }
        return $this->handleResponse(new EventResource($event), 'Event retrieved.');
    }
    
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $request->validate([
            
            'name' => 'required',
            'description' => 'required',
            'over_photo' => 'required',
            'date' => 'required',
            'location_address' => 'required',
            'person_limit_amount' => 'required',
            'ticket_price' => 'required',
        ]);
        $event = Event::find($id);
        $event->name = $request->name;
        $event->description = $request->description;
        $event->over_photo = $request->over_photo;
        $event->date = $request->date;
        $event->location_address = $request->location_address;
        $event->person_limit_amount = $request->person_limit_amount;
        $event->ticket_price = $request->ticket_price;
        $event->update();
        return $this->handleResponse(new EventResource($event), 'Event successfully updated!');
    }
    
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        Event::destroy($id);
        return $this->handleResponse([], 'Event deleted!');
    }
}
