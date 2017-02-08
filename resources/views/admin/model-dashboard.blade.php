@extends('layouts.admin')

@section('content')
    <h1>
        Dashboard
    </h1>
    <hr class="mb-4">

    <div class="row">
        <div class="col-md-3">
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
        <div class="col-md-9">
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
@endsection
