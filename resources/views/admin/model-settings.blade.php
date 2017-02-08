@extends('layouts.admin')

@section('content')
    <h1>Settings</h1>
    <hr class="mb-4">

    <form method="POST" action="{{ route('admin.settings.update') }}" class="row">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

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
@endsection
