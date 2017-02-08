@extends('layouts.main')

@section('content')
    <h1>Settings</h1>
    <hr class="mb-4">

    <div class="row">

        <div class="col-md-4 mb-4">
            <label>Customer id</label>
            <h5 class="mb-4">{{ $user->id }}</h5>
            <label for="email" class="form-control-label">Email</label>
            <h5 class="mb-4">{{ $user->email }}</h5>
        </div>
        <div class="col-md-7 offset-md-1 mb-4">
            <h4>
                Subscription
                <i class="fa fa-fw fa-{{ $user->hasActiveSubscription() ? 'check text-success' : 'times text-danger' }}"></i>
            </h4>
            @if ($user->hasActiveSubscription())
                <p class="text-muted">
                    You have a subscription that
                    will {{ $user->hasGracePeriod() ? 'end on' : 'automatically renew' }} {{ $subscriptionEnding }}
                </p>
            @else
                <p class="text-muted">
                    You don't have a subscription, choose one to get full access.
                </p>
                <form method="POST"
                      action="{{ route('customers.subscription.store', $user) }}"
                      class="registration-form">
                    {{ csrf_field() }}

                    <div class="subscription-plans p-0 mb-4">
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
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-display">Get Access Now</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
