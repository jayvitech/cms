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
            $hobbiesData = Hobby::where('status', 1)->get();
            return view('user.dashboard', ['userData' => $userData, 'hobbiesData' => $hobbiesData]);
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
                $saveHobby->hobby_id = $hobby;
                $saveHobby->save();
            }
        }

        return Redirect::to("login")->with('success', 'You are register successfully please log in.');
    }

    public function dashboard() {
        if(Auth::check()){
            $userData = User::where('id', '!=', auth()->user()->id)->get();
            $hobbiesData = Hobby::where('status', 1)->get();
            return view('user.dashboard', ['userData' => $userData, 'hobbiesData' => $hobbiesData]);
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
            $checkRecordExists = User_request::where('send_to_user_id', \auth()->user()->id)->where('send_by_user_id', $user_id)->count();
            if($checkRecordExists > 0) {
                $updateStatus = User_request::where('send_to_user_id', \auth()->user()->id)->where('send_by_user_id', $user_id)->update(['status' => $request_status]);
            } else {
                $userRequest = new User_request();
                $userRequest->send_by_user_id = \auth()->user()->id;
                $userRequest->send_to_user_id = $user_id;
                $userRequest->status = 3;
                $userRequest->save();
            }
            return 1;
        }
    }

    public function callFilter($value) {
        $data = '';
        $userData = User::where('id', '!=', auth()->user()->id);

        if($value != 99) {
            $userData = $userData->where('gender', $value)->get();
        } else {
            $userData = $userData->get();
        }

        foreach ($userData as $key => $user) {
            $data .= '<tr>
                <td>'. ($key+1) .'</td>
                <td>'. $user->name .'</td>
                <td>'. $user->email .'</td>';
                if($user->gender == 0) {
                    $data .= '<td>Male</td>';
                } else {
                    $data .= '<td>Female</td>';
                }

                $hobbyName = '';
                $hobbiesData = User_hobby::select('hobbies.hobby_name as hobbyName')->where('user_id', $user['id'])->join('hobbies', 'hobbies.id', 'user_hobbies.hobby_id')->get();
                if(count($hobbiesData) > 0) {
                    foreach ($hobbiesData as $hobby) {
                        $hobbyName .= $hobby->hobbyName .', ';
                    }
                    $hobbyName = rtrim($hobbyName, ', ');
                }
                $data .= '<td>'. $hobbyName .'</td>';

                $userRequest = \App\User_request::where('send_to_user_id', $user['id'])->where('send_by_user_id', auth()->user()->id)->get();
                $userByRequest = \App\User_request::where('send_to_user_id', auth()->user()->id)->where('send_by_user_id', $user['id'])->get();

                if(count($userRequest) > 0 || count($userByRequest) > 0) {
                    foreach($userRequest as $request) {
                        if($request->status == 0) {
                            $data .= '<td><span style="color: green">Your Friend</span></td>
                                      <td><button type="button" onclick="callajax($user->id, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>';
                        }
                        if($request->status == 2) {
                            $data .= '<td><span style="color: green">Friend Request Send</span></td>';
                        }
                    }
                    foreach($userByRequest as $request) {
                        if ($request->status == 0) {
                            $data .= '<td><span style="color: green">Your Friend</span></td>
                                      <td><button type="button" onclick="callajax($user->id, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>';
                        }
                        if ($request->status == 2) {
                            $data .= '<td><button type="button" onclick="callajax($user->id, 2)" id="accept_request" class="btn btn-warning accept_request"> Accept Request </button></td>';
                        }
                    }
                }
                else {
                    $data .= '<td><button type="button" onclick="callajax({{$user->id, 1)" id="send_request" class="btn btn-primary send_request" >Send Friend Request</button></td>
                              <td><button type="button" onclick="callajax({{$user->id, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>';
                }
            $data .= '</tr>';
        }
        return $data;
    }

    public function callFilterHobby($value) {
        $data = '';
        $userData = User::where('id', '!=', auth()->user()->id)->get();

        foreach ($userData as $key => $user) {
            $data .= '<tr>
                <td>'. ($key+1) .'</td>
                <td>'. $user->name .'</td>
                <td>'. $user->email .'</td>';
            if($user->gender == 0) {
                $data .= '<td>Male</td>';
            } else {
                $data .= '<td>Female</td>';
            }

            $hobbyName = '';
            $hobbiesData = User_hobby::select('hobbies.hobby_name as hobbyName')->where('user_id', $user['id'])->join('hobbies', 'hobbies.id', 'user_hobbies.hobby_id');
            if($value != 99) {
                $hobbiesData = $hobbiesData->where('hobbies.id', $value)->get();
            } else {
                $hobbiesData = $hobbiesData->get();
            }

            if(count($hobbiesData) > 0) {
                foreach ($hobbiesData as $hobby) {
                    $hobbyName .= $hobby->hobbyName .', ';
                }
                $hobbyName = rtrim($hobbyName, ', ');
            }
            $data .= '<td>'. $hobbyName .'</td>';

            $userRequest = \App\User_request::where('send_to_user_id', $user['id'])->where('send_by_user_id', auth()->user()->id)->get();
            $userByRequest = \App\User_request::where('send_to_user_id', auth()->user()->id)->where('send_by_user_id', $user['id'])->get();

            if(count($userRequest) > 0 || count($userByRequest) > 0) {
                foreach($userRequest as $request) {
                    if($request->status == 0) {
                        $data .= '<td><span style="color: green">Your Friend</span></td>
                                      <td><button type="button" onclick="callajax($user->id, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>';
                    }
                    if($request->status == 2) {
                        $data .= '<td><span style="color: green">Friend Request Send</span></td>';
                    }
                }
                foreach($userByRequest as $request) {
                    if ($request->status == 0) {
                        $data .= '<td><span style="color: green">Your Friend</span></td>
                                      <td><button type="button" onclick="callajax($user->id, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>';
                    }
                    if ($request->status == 2) {
                        $data .= '<td><button type="button" onclick="callajax($user->id, 2)" id="accept_request" class="btn btn-warning accept_request"> Accept Request </button></td>';
                    }
                }
            }
            else {
                $data .= '<td><button type="button" onclick="callajax({{$user->id, 1)" id="send_request" class="btn btn-primary send_request" >Send Friend Request</button></td>
                              <td><button type="button" onclick="callajax({{$user->id, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>';
            }
            $data .= '</tr>';
        }
        return $data;
    }

}
