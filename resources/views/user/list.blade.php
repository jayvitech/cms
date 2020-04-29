@extends('layouts.app')
@include('dashboard')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="container">
                    <div class="table-responsive">
                        <h2>User List</h2>
                        <a href="{{ url('users/add') }}" class="btn btn-primary float-right">Add</a>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Role Name</th>
                                <th>User Name</th>
                                <th>Email</th>
                                @if(auth()->user()->role_id == 1)
                                    <th colspan="2">Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roleData as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$item['role_id']}}</td>
                                    <td>{{$item['name']}}</td>
                                    <td>{{$item['email']}}</td>
                                    @if(auth()->user()->role_id == 1)
                                        <td><a href="{{ url('users/edit/' . $item->id )}}"  class="btn btn-warning">Edit</a></td>
                                        <td><a href="{{ url('users/delete/' . $item->id )}}"  class="btn btn-danger">Delete</td>
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
@endsection
