@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-4">
                <form role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}

                    <div class="col-md-6">

                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                            <label for="email" class="form-control-label">E-Mail Address</label>
                            <input id="email"
                                   type="email"
                                   class="form-control{{ $errors->has('email') ? ' form-control-danger' : '' }}"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus>

                            @if ($errors->has('email'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }} mb-4">
                            <label for="password" class="form-control-label">Password</label>
                            <input id="password"
                                   type="password"
                                   class="form-control{{ $errors->has('password') ? ' has-danger' : '' }}"
                                   name="password"
                                   required>

                            @if ($errors->has('password'))
                                <span class="form-control-feedback">
                                        {{ $errors->first('password') }}
                                    </span>
                            @endif
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="checkbox">--}}
                                {{--<label class="custom-control custom-checkbox">--}}
                                    {{--<input type="checkbox" name="remember" class="custom-control-input">--}}
                                    {{--<span class="custom-control-indicator"></span>--}}
                                    {{--<span class="custom-control-description">Remember Me</span>--}}
                                {{--</label>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>

                        <a href="{{ url('/password/reset') }}" class="d-block">
                            Forgot Your Password?
                        </a>

                        <a href="{{ url('/register') }}" class="d-block">
                            Don't have an account yet?
                        </a>

                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
