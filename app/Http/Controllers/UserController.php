<?php

namespace App\Http\Controllers;

use App\User_history;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userHistory() {
        $userHistoryData = User_history::all();
        return view('user.history_list', ['userHistoryData' => $userHistoryData]);
    }
}
