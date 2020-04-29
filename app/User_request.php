<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_request extends Model
{
    public static function boot()
    {
        parent::boot();
        User_request::observe(new \App\Observers\UserActionsObserver);
    }
}
