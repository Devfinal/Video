@extends('layouts.admin')

@section('content')
    <div class="float-xs-right">
        @if (!is_null($user->getMeta('subscription_id')))
            <button type="submit" form="cancel-subscription-form" class="btn btn-warning" onclick="javascript:return confirm('Are you sure you want to permanently delete the customers subscription?')">Cancel subscription</button>
        @endif
        <button type="submit" form="delete-customer-form" class="btn btn-danger" onclick="javascript:return confirm('Are you sure you want to permanently delete the customer from the system?')">Delete customer</button>
    </div>
    <h1>{{ $user->username }}</h1>
    <hr class="mb-4">

    <div class="row">
        <div class="col-md-3 mb-4">
            <form method="POST" action="{{ route('admin.customers.update', $user) }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

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

                <button type="submit" class="btn btn-success mt-4">Update</button>
            </form>
        </div>
        <div class="col-md-8 offset-md-1 mb-4">
            <h4>Recent transactions</h4>
            <table class="table table-sm table-striped table-hover mb-4">
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
                    @foreach ($user->transactions()->orderBy('created_at', 'desc')->take(10)->get() as $transaction)
                        <tr>
                            <td>{{ sprintf('%.2f', $transaction->amount) }}</td>
                            <td>{{ \App\TransactionCurrency::getString($transaction->currency) }}</td>
                            <td>{{ \App\TransactionReason::getString($transaction->reason) }}</td>
                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td style="white-space:pre">{{ $transaction->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>



            <form method="POST" action="{{ route('admin.customers.update-balance', $user) }}" class="row">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="col-md-6 offset-md-6">
                    <h4>Create transaction</h4>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('amount') ? ' has-danger' : '' }}">
                                <label for="amount" class="form-control-label">Amount</label>
                                <input id="amount"
                                       type="number"
                                       class="form-control{{ $errors->has('amount') ? ' form-control-danger' : '' }}"
                                       name="amount"
                                       value="{{ old('amount') ?: '' }}"
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
                                    <option value="{{ \App\TransactionCurrency::EUR }}">{{ \App\TransactionCurrency::getString(\App\TransactionCurrency::EUR) }}</option>
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
    </div>

    <form id="cancel-subscription-form" method="POST" action="{{ route('admin.customers.cancel', $user) }}">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
    <form id="delete-customer-form" method="POST" action="{{ route('admin.customers.destroy', $user) }}">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endsection
