<?php

namespace App\Observers;
use App\User_request;
use Illuminate\Support\Facades\Auth;
use App\User_history;

class UserActionsObserver
{
    public function created(User_request $user_request)
    {
        if (Auth::check()) {
            $userHistory = new User_history();
            $userHistory->created_by = Auth::user()->id;
            $userHistory->created_for = $user_request->send_to_user_id;
            $userHistory->action = 'send friend request';
            $userHistory->save();
        }
    }

    public function updated(User_request $user_request)
    {
        if (Auth::check()) {
            $userHistory = new User_request();
            $userHistory->created_by = Auth::user()->id;
            $userHistory->created_for = $user_request->send_to_user_id;
            if( $user_request->status == 1 ) {
                $userHistory->action = 'Send Friend Request';
            }
            if( $user_request->status == 2 ) {
                $userHistory->action = 'Accept Friend Request';
            }
            if( $user_request->status == 3 ) {
                $userHistory->action = 'Block Friend Request';
            }
            $userHistory->save();
        }
    }
}
