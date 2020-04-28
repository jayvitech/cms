<?php

namespace App\Http\Controllers;

use App\Hobby;
use App\User;
use App\User_hobby;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function index() {
        if(Auth::check()){
            return Redirect::to('dashboard');
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
        return Redirect::to("login")->withSuccess('Invalid credentials...');
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

        return Redirect::to("login")->withSuccess('Great! You have Successfully register.');
    }

    public function dashboard() {
        if(Auth::check()){
            return view('dashboard');
        }
        return Redirect::to("login");
    }

    public function logout() {
        Auth::logout();
        return Redirect('login');
    }
}
