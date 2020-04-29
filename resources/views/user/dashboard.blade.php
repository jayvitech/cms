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
                        <div class="row">
                            <div>
                                <label for="genderFilter">Filter</label>
                                <select name="genderFilter" id="genderFilter" onchange="callFilterGender(this.value, 1)">
                                    <option value="99">Select Gender</option>
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                            </div>
                            <div>
                                <label for="hobbyFilter">Filter</label>
                                <select name="hobbyFilter" id="hobbyFilter" onchange="callFilterHobby(this.value, 2)">
                                    <option value="99">Select Hobby</option>
                                    @foreach($hobbiesData as $hobby)
                                        <option value="{{$hobby->id}}">{{$hobby->hobby_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Hobbies</th>
                                <th colspan="2">Request Status</th>
                            </tr>
                            </thead>
                            <tbody id="user_table" class="user_table">
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
                                            $hobbyName = rtrim($hobbyName, ', ');
                                        }
                                    @endphp

                                    <td>{{$hobbyName}}</td>
                                    @php
                                        $userRequest = \App\User_request::where('send_to_user_id', $user['id'])->where('send_by_user_id', auth()->user()->id)->get();
                                        $userByRequest = \App\User_request::where('send_to_user_id', auth()->user()->id)->where('send_by_user_id', $user['id'])->get();
                                    @endphp
                                    @if(count($userRequest) > 0 || count($userByRequest) > 0)
                                        @foreach($userRequest as $request)
                                            @if($request->status == 0)
                                                <td>
                                                    <span style="color: green">Your Friend</span>
                                                </td>
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>
                                            @endif
                                            @if($request->status == 2)
                                                <td><span style="color: green">Friend Request Send</span></td>
                                            @endif
                                        @endforeach

                                        @foreach($userByRequest as $request)
                                            @if($request->status == 0)
                                                <td>
                                                    <span style="color: green">Your Friend</span>
                                                </td>
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>
                                                @endif
                                            @if($request->status == 2)
                                                <td><button type="button" onclick="callajax({{$user['id']}}, 2)" id="accept_request" class="btn btn-warning accept_request"> Accept Request </button></td>
                                            @endif
                                        @endforeach
                                    @else
                                        <td><button type="button" onclick="callajax({{$user['id']}}, 1)" id="send_request" class="btn btn-primary send_request" >Send Friend Request</button></td>
                                        <td><button type="button" onclick="callajax({{$user['id']}}, 3)" id="block_request" class="btn btn-danger block_request"> Block Request </button></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}" defer></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function callajax(user_id, request_status) {
            if(request_status == 3) {
                var r = confirm("Are you sure to block this person?");
                if (r == true) {
                    $.ajax({
                        type:'GET',
                        url:'/change-request-status/' + user_id + '/' + request_status,
                        success:function(data){
                            if(data == 1) {
                                window.location.reload();
                            }
                        }
                    });
                }
            }

            if(request_status == 1 || request_status == 2) {
                $.ajax({
                    type: 'GET',
                    url: '/change-request-status/' + user_id + '/' + request_status,
                    success: function (data) {
                        if (data == 1) {
                            window.location.reload();
                        }
                    }
                });
            }
        }

        function callFilterGender(value, filterType) {
            $.ajax({
                type: 'GET',
                url: '/call-filter/' + value,
                success: function (data) {
                    $(".user_table").html(data);
                }
            });
        }
        function callFilterHobby(value, filterType) {
            $.ajax({
                type: 'GET',
                url: '/call-filter-hobby/' + value,
                success: function (data) {
                    $(".user_table").html(data);
                }
            });
        }

    </script>
@endsection
