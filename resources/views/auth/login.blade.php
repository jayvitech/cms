@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mx-auto">
                <h3 class="login-heading mb-4 text-center">CMS</h3>
                <form action="{{url('post-login')}}" method="POST" id="loginForm">
                    @csrf
                    <div class="form-group row">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email address" required autocomplete="email" autofocus>

                        @if ($errors->has('email'))
                            <span class="error">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required autocomplete="current-password">

                        @if ($errors->has('password'))
                            <span class="error">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-group row mb-0">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button> </br>
                        <div class="text-center">Create your Account
                            <a class="small" href="{{url('register')}}">Sign Up</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection