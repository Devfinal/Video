@extends('layouts.admin')

@section('content')
    <div class="float-xs-right">
        <button type="submit"
                form="activate-user-form"
                class="btn btn-outline-{{ $user->active && $user->getMeta('deactivated', false) === false ? 'warning' : 'success' }}">
            {{ $user->active && $user->getMeta('deactivated', false) === false ? 'Deactivate' : 'Activate' }}
        </button>
        <button type="submit"
                form="delete-user-form"
                class="btn btn-outline-danger"
                onclick="javascript:return confirm('Are you sure you want to permanently delete the model from the system?')">
            Delete
        </button>
    </div>

    <h1>{{ $user->username }}</h1>
    <hr class="mb-4">

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a href="#profile"
               class="nav-link{{ !Session::has('tab') || Session::get('tab') === 'profile' ? ' active' : '' }}"
               data-toggle="tab">Profile</a>
        </li>
        <li class="nav-item">
            <a href="#settings"
               class="nav-link{{ Session::has('tab') && Session::get('tab') === 'settings' ? ' active' : '' }}"
               data-toggle="tab">Settings</a>
        </li>
        <li class="nav-item">
            <a href="#balance"
               class="nav-link{{ Session::has('tab') && Session::get('tab') === 'balance' ? ' active' : '' }}"
               data-toggle="tab">Balance</a>
        </li>
        <li class="nav-item">
            <a href="#portfolio"
               class="nav-link{{ Session::has('tab') && Session::get('tab') === 'portfolio' ? ' active' : '' }}"
               data-toggle="tab">Portfolio</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="profile"
             class="tab-pane{{ !Session::has('tab') || Session::get('tab') === 'profile' ? ' active' : '' }}">
            <form method="POST" action="{{ route('admin.models.update-profile', $user) }}" class="row">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="col-md-3 mb-4">
                    <div class="form-group{{ $errors->has('username') ? ' has-danger' : '' }}">
                        <label for="username" class="form-control-label">Username</label>
                        <input id="username"
                               type="text"
                               class="form-control{{ $errors->has('username') ? ' form-control-danger' : '' }}"
                               name="username"
                               value="{{ old('username') ?: $user->username }}"
                               autocomplete="off">
                        @if ($errors->has('username'))
                            <div class="form-control-feedback">
                                {{ $errors->first('username') }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-2">
                        <label>Profile Image</label>
                        @if (isset($user->profile->cover))
                            <img src="{{ Storage::url($user->profile->cover->path) }}"
                                 class=" img-thumbnail img-fluid w-100 mb-3">
                        @endif

                        <div class="form-group{{ $errors->has('cover') ? ' has-danger' : '' }}">
                            <input type="file"
                                   id="cover"
                                   name="cover"
                                   form="profile-update-cover-form"
                                   class="form-control"
                                   accept="image/*">

                            @if ($errors->has('cover'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('cover') }}
                                </div>
                            @endif
                        </div>

                        <button type="button"
                                class="btn btn-success btn-sm"
                                onclick="event.preventDefault();document.getElementById('profile-update-cover-form').submit()">
                            Update
                        </button>
                        <button type="button"
                                class="btn btn-danger btn-sm"
                                onclick="event.preventDefault();document.getElementById('profile-delete-cover-form').submit()">
                            Remove
                        </button>
                    </div>

                    <div class="mb-2">
                        <label>Background Image</label>
                        @if (isset($user->profile->background))
                            <img src="{{ Storage::url($user->profile->background->path) }}"
                                 class=" img-thumbnail img-fluid w-100 mb-3">
                        @endif

                        <div class="form-group{{ $errors->has('background') ? ' has-danger' : '' }}">
                            <input type="file"
                                   id="background"
                                   name="background"
                                   form="profile-update-background-form"
                                   class="form-control"
                                   accept="image/*">

                            @if ($errors->has('background'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('background') }}
                                </div>
                            @endif
                        </div>

                        <button type="button"
                                class="btn btn-success btn-sm"
                                onclick="event.preventDefault();document.getElementById('profile-update-background-form').submit()">
                            Update
                        </button>
                        <button type="button"
                                class="btn btn-danger btn-sm"
                                onclick="event.preventDefault();document.getElementById('profile-delete-background-form').submit()">
                            Remove
                        </button>
                    </div>
                </div>
                <div class="col-md-9 mb-4">
                    {{-- ... --}}
                </div>
                <div class="col-xs-12 mb-4">
                    <div class="form-group{{ $errors->has('presentation') ? ' has-danger' : '' }}">
                        <label for="presentation" class="form-control-label">Presentation</label>
                        <textarea id="presentation"
                                  class="form-control{{ $errors->has('presentation') ? ' form-control-danger' : '' }} tinymce"
                                  name="presentation">
                            {{ isset($user->profile->presentation) ? $user->profile->presentation : old('presentation') }}
                        </textarea>

                        @if ($errors->has('presentation'))
                            <span class="form-control-feedback">
                                {{ $errors->first('presentation') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>

        <div id="settings"
             class="tab-pane{{ Session::get('tab') === 'settings' ? ' active' : '' }}">
            <form method="POST" action="{{ route('admin.models.update-settings', $user) }}" class="row">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="col-xs-12 row mb-4">
                    <div class="col-md-4">
                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                            <label for="email" class="form-control-label">Email</label>
                            <input id="email"
                                   type="email"
                                   class="form-control{{ $errors->has('email') ? ' form-control-danger' : '' }}"
                                   name="email"
                                   value="{{ old('email') ?: $user->email }}"
                                   autocomplete="off">

                            @if ($errors->has('email'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                            <label for="password" class="form-control-label">Password</label>
                            <input id="password"
                                   type="text"
                                   class="form-control{{ $errors->has('password') ? ' form-control-danger' : '' }}"
                                   name="password"
                                   value="{{ old('password') }}"
                                   autocomplete="off">

                            @if ($errors->has('password'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('commission') ? ' has-danger' : '' }}">
                                    <label for="commission" class="form-control-label">Commission</label>
                                    <div class="input-group">
                                        <input id="commission"
                                               type="number"
                                               class="form-control{{ $errors->has('commission') ? ' form-control-danger' : '' }}"
                                               name="commission"
                                               value="{{ old('commission') ?: $settings->get('commission', 2) }}"
                                               autocomplete="off">
                                        <span class="input-group-addon">
                                            <i class="fa fa-eur"></i>
                                        </span>
                                    </div>

                                    @if ($errors->has('commission'))
                                        <div class="form-control-feedback">
                                            {{ $errors->first('commission') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="form-group{{ $errors->has('first_name') ? ' has-danger' : '' }}">
                        <label for="first_name" class="form-control-label">First name</label>
                        <input id="first_name"
                               type="text"
                               class="form-control{{ $errors->has('first_name') ? ' form-control-danger' : '' }}"
                               name="first_name"
                               value="{{ old('first_name') ?: $settings->get('first_name') }}"
                               autocomplete="off">
                        @if ($errors->has('first_name'))
                            <div class="form-control-feedback">
                                {{ $errors->first('first_name') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('street_address') ? ' has-danger' : '' }}">
                        <label for="street_address" class="form-control-label">Street address</label>
                        <input id="street_address"
                               type="text"
                               class="form-control{{ $errors->has('street_address') ? ' form-control-danger' : '' }}"
                               name="street_address"
                               value="{{ old('street_address') ?: $settings->get('street_address') }}"
                               autocomplete="off">
                        @if ($errors->has('street_address'))
                            <div class="form-control-feedback">
                                {{ $errors->first('street_address') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('phone_number') ? ' has-danger' : '' }}">
                        <label for="phone_number" class="form-control-label">Phone number</label>
                        <input id="phone_number"
                               type="text"
                               class="form-control{{ $errors->has('phone_number') ? ' form-control-danger' : '' }}"
                               name="phone_number"
                               value="{{ old('phone_number') ?: $settings->get('phone_number') }}"
                               autocomplete="off">
                        @if ($errors->has('phone_number'))
                            <div class="form-control-feedback">
                                {{ $errors->first('phone_number') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="form-group{{ $errors->has('last_name') ? ' has-danger' : '' }}">
                        <label for="last_name" class="form-control-label">Last name</label>
                        <input id="last_name"
                               type="text"
                               class="form-control{{ $errors->has('last_name') ? ' form-control-danger' : '' }}"
                               name="last_name"
                               value="{{ old('last_name') ?: $settings->get('last_name') }}"
                               autocomplete="off">
                        @if ($errors->has('last_name'))
                            <div class="form-control-feedback">
                                {{ $errors->first('last_name') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('zip_code') ? ' has-danger' : '' }}">
                        <label for="zip_code" class="form-control-label">Zip code</label>
                        <input id="zip_code"
                               type="text"
                               class="form-control{{ $errors->has('zip_code') ? ' form-control-danger' : '' }}"
                               name="zip_code"
                               value="{{ old('zip_code') ?: $settings->get('zip_code') }}"
                               autocomplete="off">
                        @if ($errors->has('zip_code'))
                            <div class="form-control-feedback">
                                {{ $errors->first('zip_code') }}
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('country') ? ' has-danger' : '' }}">
                                <label for="country" class="form-control-label d-block">Country</label>
                                <select id="country" name="country" class="custom-select w-100">
                                    <optgroup>
                                        @foreach (collect(\App\Country::enum())->slice(0, 4) as $code)
                                            <option value="{{ $code }}"{{ $settings->get('country') == $code ? ' selected' : '' }}>{{ \App\Country::getString($code) }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup>
                                        @foreach (collect(\App\Country::enum())->slice(4) as $code)
                                            <option value="{{ $code }}"{{ $settings->get('country') == $code ? ' selected' : '' }}>{{ \App\Country::getString($code) }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>

                                @if ($errors->has('country'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('country') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('currency') ? ' has-danger' : '' }}">
                                <label for="currency" class="form-control-label d-block">Currency</label>
                                <select id="currency" name="currency" class="custom-select w-100">
                                    @foreach (['EUR' => \App\TransactionCurrency::EUR] as $currency => $code)
                                        <option value="{{ $code }}"{{ $settings->get('currency') == $code ? ' selected' : '' }}>{{ $currency }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('currency'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('currency') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="form-group{{ $errors->has('ssn') ? ' has-danger' : '' }}">
                        <label for="ssn" class="form-control-label">Social security number</label>
                        <input id="ssn"
                               type="text"
                               class="form-control{{ $errors->has('ssn') ? ' form-control-danger' : '' }}"
                               name="ssn"
                               value="{{ old('ssn') ?: $settings->get('ssn') }}"
                               autocomplete="off">
                        @if ($errors->has('ssn'))
                            <div class="form-control-feedback">
                                {{ $errors->first('ssn') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
                        <label for="city" class="form-control-label">City</label>
                        <input id="city"
                               type="text"
                               class="form-control{{ $errors->has('city') ? ' form-control-danger' : '' }}"
                               name="city"
                               value="{{ old('city') ?: $settings->get('city') }}"
                               autocomplete="off">
                        @if ($errors->has('city'))
                            <div class="form-control-feedback">
                                {{ $errors->first('city') }}
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('bic') ? ' has-danger' : '' }}">
                                <label for="bic" class="form-control-label">BIC</label>
                                <input id="bic"
                                       type="text"
                                       class="form-control{{ $errors->has('bic') ? ' form-control-danger' : '' }}"
                                       name="bic"
                                       value="{{ old('bic') ?: $settings->get('bic') }}"
                                       autocomplete="off">
                                @if ($errors->has('bic'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('bic') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('iban') ? ' has-danger' : '' }}">
                                <label for="iban" class="form-control-label">IBAN</label>
                                <input id="iban"
                                       type="text"
                                       class="form-control{{ $errors->has('iban') ? ' form-control-danger' : '' }}"
                                       name="iban"
                                       value="{{ old('iban') ?: $settings->get('iban') }}"
                                       autocomplete="off">
                                @if ($errors->has('iban'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('iban') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>

        <div id="balance"
             class="tab-pane{{ Session::get('tab') === 'balance' ? ' active' : '' }}">
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <h4>Balance</h4>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fa fa-fw fa-heart"></i>
                            <span class="text-muted">{{ $user->getBalance(\App\TransactionCurrency::HEARTS) }}</span>
                        </li>
                        <li>
                            <i class="fa fa-fw fa-eur"></i>
                            <span class="text-muted">{{ $user->getBalance(\App\TransactionCurrency::EUR) }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 offset-md-3 mb-4">
                    <h4>Recent transactions</h4>
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Reason</th>
                                <th>Date</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody class="text-muted">
                            @foreach ($recentTransactions as $transaction)
                                <?php $decimalPoints = $transaction->currency === \App\TransactionCurrency::HEARTS ? 0 : 2; ?>
                                <tr>
                                    <td>{{ sprintf("%.{$decimalPoints}f", $transaction->amount) }}</td>
                                    <td>{{ \App\TransactionCurrency::getString($transaction->currency) }}</td>
                                    <td>{{ \App\TransactionReason::getString($transaction->reason) }}</td>
                                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    <td style="white-space:pre">{{ $transaction->note }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.models.update-balance', $user) }}" class="row">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="col-md-3">
                    <h4>Create transaction</h4>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('amount') ? ' has-danger' : '' }}">
                                <label for="amount" class="form-control-label">Amount</label>
                                <input id="amount"
                                       type="number"
                                       class="form-control{{ $errors->has('amount') ? ' form-control-danger' : '' }}"
                                       name="amount"
                                       value="{{ old('amount') ?: $settings->get('amount') }}"
                                       autocomplete="off">
                                @if ($errors->has('amount'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('amount') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('currency') ? ' has-danger' : '' }}">
                                <label for="currency" class="form-control-label d-block">Currency</label>
                                <select id="currency" name="currency" class="custom-select w-100">
                                    @foreach (\App\TransactionCurrency::enum() as $currency)
                                        <option value="{{ $currency }}"{{ old('currency') == $currency ? ' selected' : '' }}>
                                            {{ \App\TransactionCurrency::getString($currency) }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('currency'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('currency') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group{{ $errors->has('note') ? ' has-danger' : '' }}">
                                <label for="note" class="form-control-label">Note</label>
                                <textarea id="note"
                                          class="form-control{{ $errors->has('note') ? ' form-control-danger' : '' }}"
                                          name="note"
                                          rows="3"
                                          autocomplete="off">{{ old('note') ?: '' }}</textarea>

                                @if ($errors->has('note'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('note') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Add</button>
                </div>
            </form>
        </div>

        <div id="portfolio"
             class="tab-pane{{ Session::get('tab') === 'portfolio' ? ' active' : '' }}">
            <div class="row">
                @foreach ($user->profile->portfolio as $media)
                    <div class="col-xs-6 col-md-4 col-xl-3">
                        @if ($media->type === 'image')
                            <img src="{{ Storage::url($media->getImage()->path) }}" class="img-thumbnail">
                        @elseif ($media->type === 'video')
                            <video class="video-js" preload="auto" data-setup='{"height": "auto"}' controls>
                                <source src="{{ Storage::url($media->path) }}">
                            </video>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <form id="activate-user-form"
          method="POST"
          action="{{ $user->active && $user->getMeta('deactivated', false) === false ? route('admin.models.deactivate', $user) : route('admin.models.activate', $user) }}">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
    </form>
    <form id="delete-user-form"
          method="POST"
          action="{{ route('admin.models.destroy', $user) }}">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
    <form id="profile-update-cover-form"
          method="POST"
          action="{{ route('admin.profile.cover', $user) }}"
          enctype="multipart/form-data"
          style="display:none">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
    </form>
    <form id="profile-delete-cover-form"
          method="POST"
          action="{{ route('admin.profile.cover', $user) }}"
          style="display:none">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
    <form id="profile-update-background-form"
          method="POST"
          action="{{ route('admin.profile.background', $user) }}"
          enctype="multipart/form-data"
          style="display:none">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
    </form>
    <form id="profile-delete-background-form"
          method="POST"
          action="{{ route('admin.profile.background', $user) }}"
          style="display:none">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endsection
