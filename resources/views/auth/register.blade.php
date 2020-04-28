@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mx-auto">
                <h3 class="login-heading mb-4 text-center">CMS</h3>
                <form action="{{url('post-register')}}" method="POST" id="registerForm">
                    @csrf
                    <div class="form-group row">
                        <input type="text" id="inputName" name="name" class="form-control" placeholder="Full name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @if ($errors->has('name'))
                            <span class="error">{{ $errors->first('name') }}</span>
                        @endif

                    </div>
                    <div class="form-group row">
                        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" value="{{ old('email') }}" required autocomplete="email">

                        @if ($errors->has('email'))
                            <span class="error">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required autocomplete="new-password">

                        @if ($errors->has('password'))
                            <span class="error">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                    </div>

                    <div class="form-group row form-control">
                        <input type="radio" name="gender" value="0" checked> Male
                        <input type="radio" name="gender" value="1"> Female
                    </div>

                    <div class="form-group">
                        @foreach($hobbiesData as $hobby)
                            <input type="checkbox" name="hobbies[]" value="{{$hobby->id}}">{{$hobby->hobby_name}}
                        @endforeach
                    </div>

                    <div class="form-group row mb-0">
                        <button type="submit" class="btn btn-primary btn-block">Sign Up</button> </br>
                        <div class="text-center">If you have an account?
                            <a class="small" href="{{url('login')}}">Sign In</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection