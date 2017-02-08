@extends('layouts.main')

@section('content')
    <div class="container">
        <h1 class="text-uppercase">Reset Password</h1>

        <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                <div class="col-md-6">
                    <input id="email"
                           type="email"
                           class="form-control{{ $errors->has('email') ? ' has-danger' : '' }}"
                           name="email"
                           value="{{ $email or old('email') }}"
                           required
                           autofocus>

                    @if ($errors->has('email'))
                        <span class="form-control-feedback">
                            {{ $errors->first('email') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                <label for="password" class="col-md-4 control-label">Password</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control{{ $errors->has('email') ? ' has-danger' : '' }}" name="password" required>

                    @if ($errors->has('password'))
                        <span class="form-control-feedback">
                            {{ $errors->first('password') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                <div class="col-md-6">
                    <input id="password-confirm"
                           type="password"
                           class="form-control{{ $errors->has('email') ? ' has-danger' : '' }}"
                           name="password_confirmation"
                           required>

                    @if ($errors->has('password_confirmation'))
                        <span class="form-control-feedback">
                            {{ $errors->first('password_confirmation') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Reset Password
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
