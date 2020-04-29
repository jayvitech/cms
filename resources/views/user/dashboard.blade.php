@extends('layouts.app')
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">CMS</a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="container">
                    <div class="table-responsive">
                        <h3>User List</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Hobbies</th>
                                <th>Request Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($userData as $key => $user)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$user['name']}}</td>
                                    <td>{{$user['email']}}</td>
                                    <td>@if($user['gender'] == 0) Male @else Female @endif</td>
                                    @php
                                        $hobbyName = '';
                                        $hobbiesData = \App\User_hobby::select('hobbies.hobby_name as hobbyName')->where('user_id', $user['id'])->join('hobbies', 'hobbies.id', 'user_hobbies.hobby_id')->get();
                                        if(count($hobbiesData) > 0) {
                                            foreach ($hobbiesData as $hobby) {
                                                $hobbyName .= $hobby->hobbyName .', ';
                                            }
                                        }
                                    @endphp

                                    <td>{{$hobbyName}}</td>
                                    @php
                                        $userRequest = \App\User_request::where('send_to_user_id', $user['id'])->where('send_by_user_id', auth()->user()->id)->get();
                                        $userRequest1 = \App\User_request::where('send_to_user_id', auth()->user()->id)->where('send_by_user_id', $user['id'])->get();
                                    @endphp
                                    @if(count($userRequest) > 0 || count($userRequest1) > 0)
                                        @foreach($userRequest as $request)
                                            @if($request->status == 0)
                                                <td><span>Friends</span></td>
                                            @endif
                                            @if($request->status == 2)
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 2)" id="accept_request" class="btn btn-warning accept_request"> Accept Request </button></td>
                                            @endif
                                            @if($request->status == 3)
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>
                                            @endif
                                        @endforeach

                                        @foreach($userRequest1 as $request)
                                            @if($request->status == 0)
                                                <td><span>Friends</span></td>
                                            @endif
                                            @if($request->status == 2)
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 2)" id="accept_request" class="btn btn-warning accept_request"> Accept Request </button></td>
                                            @endif
                                            @if($request->status == 3)
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>
                                            @endif
                                        @endforeach
                                    @else
                                       <td><button type="button" onclick="callajax({{$user['id']}}, 1)" id="send_request" class="btn btn-primary send_request" >Send Friend Request</button></td>
                                    @endif
                                    {{--@if($user['request_status'] == 2)--}}
                                        {{--<button type="button" onclick="callajax({{$user['id']}}, 2)" id="accept_request" class="btn btn-warning accept_request"> Accept Request </button>--}}
                                    {{--@endif--}}
                                    {{--@if($user['request_status'] == 3)--}}
                                         {{--<button type="button" onclick="callajax({{$user['id']}}, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button>--}}
                                    {{--@endif--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function callajax(user_id, request_status) {
            //var id = $("#send_request").text();
            $.ajax({
                type:'GET',
                url:'/change-request-status/' + user_id + '/' + request_status,
                success:function(data){
                    if(data === 1) {
                        $(".send_request").html("Request send");
                    }
                    //alert(data.success);
                }
            });
        }
    </script>
@endsection
