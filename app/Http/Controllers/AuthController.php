<?php

namespace App\Http\Controllers;

use App\Hobby;
use App\User;
use App\User_hobby;
use App\User_request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function index() {
        if(Auth::check()){
            $userData = User::where('id', '!=', auth()->user()->id)->get();
            return view('user.dashboard', ['userData' => $userData]);
        }
        return view('auth.login');
    }

    public function register()
    {
        $hobbiesData = Hobby::where('status', 1)->get();
        return view('auth.register', ['hobbiesData' => $hobbiesData]);
    }

    public function postLogin(Request $request)
    {
        request()->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            /* Authentication passed */
            return redirect()->to('dashboard');
        }
        return Redirect::to("login")->with('error', 'You entered invalid credential...');
    }

    public function postRegister(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $data = $request->all();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'password' => Hash::make($data['password'])
        ]);

        if($request->input('hobbies')) {
            foreach ($request->input('hobbies') as $hobby) {
                $saveHobby = new User_hobby();
                $saveHobby->user_id = $user->id;
                $saveHobby->hobby_name = $hobby;
                $saveHobby->save();
            }
        }

        return Redirect::to("login")->with('success', 'You are register successfully please log in.');
    }

    public function dashboard() {
        if(Auth::check()){
            $userData = User::where('id', '!=', auth()->user()->id)->get();
            return view('user.dashboard', ['userData' => $userData]);
        }
    }

    public function logout() {
        Auth::logout();
        return Redirect('login');
    }

    public function changeRequestStatus($user_id, $request_status) {
        if($request_status == 1) {
            $userRequest = new User_request();
            $userRequest->send_by_user_id = \auth()->user()->id;
            $userRequest->send_to_user_id = $user_id;
            $userRequest->status = 2;
            $userRequest->save();
            return 1;
        }

        if($request_status == 2) {
            $updateStatus = User_request::where('send_by_user_id', $user_id)->update(['status' => 0]);
            return 1;
        }

        if($request_status == 3) {
            $updateStatus = User_request::where('send_to_user_id', $user_id)->update(['status' => $request_status]);
            return 1;
        }
    }
}
