<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\UserHasEvent;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon;
class UserEventController extends BaseController
{
    public function userEvent($id)
    {
        $user_event_count = UserHasEvent::where('event_id',$id)->count();
        $event = Event::findOrFail($id);
        $today_date = Carbon\Carbon::today()->toDateString();
        $event_date =$event->date;
        
        if($user_event_count < $event->person_limit_amount &&   $event_date > $today_date){
            $userHasEvent = new UserHasEvent();
            $userHasEvent->user_id = auth()->user()->id;
            $userHasEvent->event_id = $id;
            $userHasEvent->save();
        }else{
            return $this->handleResponse('', 'User Limit is reached');
        }
        return $this->handleResponse($userHasEvent, 'User-Event created!');
    }
}
