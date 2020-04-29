@extends('layouts.app')
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">CMS</a>
        <a class="navbar-brand" href="{{ url('/users') }}"> User List</a>
        <a class="navbar-brand" href="{{ url('/user-history') }}"> User History</a>

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
                        <h3>User History List</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Created By Name</th>
                                <th>Created for Name</th>
                                <th>Action</th>
                                <th>Created Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($userHistoryData))
                                @foreach($userHistoryData as $key => $userHistory)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        @php
                                            $createdByName = \App\User::select('name')->whereId($userHistory['created_by'])->first();
                                            $createdForName = \App\User::select('name')->whereId($userHistory['created_for'])->first();
                                        @endphp
                                        <td>{{$createdByName->name}}</td>
                                        <td>{{$createdForName->name}}</td>
                                        <td>{{$userHistory['action']}}</td>
                                        <td>{{$userHistory['created_at']}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
