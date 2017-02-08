@extends ('layouts.main')

@section('content')
    <h1 class="mb-4">Contact and support</h1>
    <div class="row">
        <div class="col-md-6">
            <form method="POST" action="{{ route('contact-submit') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="name" class="form-control-label">Name</label>
                    <input type="text"
                           id="name"
                           class="form-control{{ $errors->has('name') ? ' form-control-danger' : '' }}"
                           name="name"
                           value="{{ old('name') }}"
                           required>

                    @if ($errors->has('name'))
                        <div class="form-control-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                    <label for="email" class="form-control-label">Email</label>
                    <input type="email"
                           id="email"
                           class="form-control{{ $errors->has('email') ? ' form-control-danger' : '' }}"
                           name="email"
                           value="{{ old('email') }}"
                           required>

                    @if ($errors->has('email'))
                        <div class="form-control-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="email-confirm" class="form-control-label">Confirm Email</label>
                    <input type="email"
                           id="email-confirm"
                           class="form-control"
                           name="email_confirmation"
                           required>
                </div>
                <div class="form-group{{ $errors->has('subject') ? ' has-danger' : '' }}">
                    <label for="subject" class="form-control-label d-block">My question is about</label>
                    <select id="subject" name="subject" class="custom-select">
                        <option value=""{{ old('subject') === '' ? ' selected' : '' }}>Choose a category</option>
                        <option value="subscriptions"{{ old('subject') === 'subscriptions' ? ' selected' : '' }}>Subscriptions (passwords etc.)</option>
                        <option value="support"{{ old('subject') === 'support' ? ' selected' : '' }}>Technical Support (regarding the website)</option>
                        <option value="financial"{{ old('subject') === 'financial' ? ' selected' : '' }}>Economy (regarding payments etc.)</option>
                        <option value="other"{{ old('subject') === 'other' ? ' selected' : '' }}>Other Issues</option>
                        <option value="cancellation"{{ old('subject') === 'cancellation' ? ' selected' : '' }}>Cancel Subscription</option>
                    </select>

                    @if ($errors->has('subject'))
                        <div class="form-control-feedback">
                            {{ $errors->first('subject') }}
                        </div>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }} mb-4">
                    <label for="description" class="form-control-label">Description</label>
                    <textarea id="description"
                              class="form-control{{ $errors->has('description') ? ' form-control-danger' : '' }}"
                              name="description"
                              rows="4"
                              required>{{ old('description') }}</textarea>

                    @if ($errors->has('description'))
                        <span class="form-control-feedback">
                            {{ $errors->first('description') }}
                        </span>
                    @endif
                </div>
                <input type="submit" class="btn btn-primary" value="Send">
            </form>
        </div>
        <div class="col-md-6 p-4">
            <p>
                WPG TO BE WORLD PHOTOGRAPHY LTD Org nr: HE39800.
            </p>
            <p>
                Our customer service is open weekdays from 9:00am - 4:00pm (CET) at e-mail
                <a href="mailto:support@beautiesfromheaven.com">support@beautiesfromheaven.com</a>
            </p>
            <p>
                 Tel: <a href="tel:+357 96569990">+357 96569990</a>  Adress: Alexandrou Papadiamanti 3, 5340 Famagusta Cyprus
            </p>
        </div>
    </div>
@endsection
