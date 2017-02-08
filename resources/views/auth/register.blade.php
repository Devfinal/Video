@extends('layouts.main')

@section('content')
    <div class="container">
        <h2 class="text-uppercase">Get full access now!</h2>
        <p class="mb-4">
            To get full access to all articles and blogposts for ALL our girls you need an active subscription.
        </p>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Oops!</strong>
                The form contains errors
            </div>
        @endif

        <form role="form" method="POST" action="{{ url('/register') }}" class="row registration-form">
            {{ csrf_field() }}
            <input type="hidden" name="model" value="{{ Request::get('model') }}">

            <div class="col-md-5">
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

                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
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

                <div class="form-group">
                    <label for="password-confirm" class="form-control-label">Confirm Password</label>
                    <input id="password-confirm"
                           type="password"
                           class="form-control"
                           name="password_confirmation"
                           required>
                </div>

                <div class="form-group{{ $errors->has('username') ? ' has-danger' : '' }}">
                    <label for="username" class="form-control-label">Desired username</label>
                    <input id="username"
                           type="text"
                           class="form-control{{ $errors->has('username') ? ' form-control-danger' : '' }}"
                           name="username"
                           value="{{ old('username') }}"
                           required>

                    @if ($errors->has('username'))
                        <div class="form-control-feedback">
                            {{ $errors->first('username') }}
                        </div>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('phone_number') ? ' has-danger' : '' }}">
                    <label for="phone_number" class="form-control-label">Phone number <span class="text-muted">(optional)</span></label>
                    <input id="phone_number"
                           type="text"
                           class="form-control{{ $errors->has('phone_number') ? ' form-control-danger' : '' }}"
                           name="phone_number"
                           value="{{ old('phone_number') }}">

                    @if ($errors->has('phone_number'))
                        <div class="form-control-feedback">
                            {{ $errors->first('phone_number') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-7 subscription-plans">
                <div class="card">
                    <ul class="list-group list-group-flush">

                        @foreach (\App\Subscription::enum() as $subscription)
                            <?php $subscriptionData = \App\Subscription::getData($subscription); ?>

                            <li class="list-group-item">
                                <input type="radio"
                                       name="subscription"
                                       id="subscription-choice-{{ $loop->index }}"
                                       class="form-check-input"
                                       value="{{ $subscription }}"
                                        {{ old('subscription') == $subscription || (is_null(old('subscription')) && $loop->first) ? 'checked' : '' }}>
                                <label for="subscription-choice-{{ $loop->index }}" class="form-check-label">
                                    <h3 class="subscription-price float-xs-right">
                                        €{{ sprintf('%.2f', $subscriptionData['amount']) }}
                                    </h3>
                                    <h4 class="subscription-title text-uppercase">
                                        {{ $subscriptionData['duration'] }}
                                    </h4>
                                    <small class="text-muted text-uppercase subscription-description">
                                        {{ $subscriptionData['description'] }}
                                    </small>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <p class="text-muted">
                    To buy subscriptions you have to be over 18 years of age. This site contains adult content. All our
                    subscriptions is automatically renewed until cancellation.
                </p>
            </div>

            <div class="col-xs-12">
                <div class="form-group{{ $errors->has('terms') ? ' has-danger' : '' }}">
                    <div class="checkbox">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   name="terms"
                                   class="custom-control-input"{{ old('terms') === 'on' ? ' checked' : '' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">
                                I agree on <a href="{{ route('terms') }}">subscription terms</a> and automatically
                                renewed subscription.
                            </span>

                            @if ($errors->has('terms'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('terms') }}
                                </div>
                            @endif
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-display">Get Access Now</button>
                </div>

                <a href="{{ route('login') }}" class="d-block mb-4">
                    Already have an account?
                </a>
            </div>
        </form>

        <div class="card card-inverse">
            <img class="card-img img-fluid w-100"
                 src="{{ isset($model->profile->cover) ? Storage::url($model->profile->cover->path) : '/images/get-full-access.jpg' }}">
        </div>
    </div>
@endsection
