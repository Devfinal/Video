@extends('layouts.admin')

@section('content')
    <h1>Reports</h1>
    <hr class="mb-4">

    <form method="POST" action="{{ route('admin.reports.generate') }}" class="row mb-4">
        {{ csrf_field() }}

        <div class="col-md-3">
            <div class="form-group{{ $errors->has('report') ? ' has-danger' : '' }}">
                <label for="report" class="form-control-label d-block">Report</label>
                <select id="report" name="report" class="custom-select w-100">
                    <option value="">Select report</option>
                    <option value="1"{{ (old('report') ?: Request::get('report')) === '1' ? ' selected' : '' }}>
                        Purchase/customer
                    </option>
                    <option value="2"{{ (old('report') ?: Request::get('report')) === '2' ? ' selected' : '' }}>
                        Purchase/country
                    </option>
                </select>

                @if ($errors->has('report'))
                    <div class="form-control-feedback">
                        {{ $errors->first('report') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group{{ $errors->has('start') ? ' has-danger' : '' }}">
                <label for="start" class="form-control-label d-block">
                    Start
                    <small class="text-muted">(YYYY-MM-DD HH:MM)</small>
                </label>
                <input type="datetime-local"
                       id="start"
                       name="start"
                       class="form-control{{ $errors->has('start') ? ' form-control-danger' : '' }}"
                       value="{{ old('start') ?: Request::get('start') }}">

                @if ($errors->has('start'))
                    <div class="form-control-feedback">
                        {{ $errors->first('start') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group{{ $errors->has('end') ? ' has-danger' : '' }}">
                <label for="end" class="form-control-label d-block">
                    End
                    <small class="text-muted">(YYYY-MM-DD HH:MM)</small>
                </label>
                <input type="datetime-local"
                       id="end"
                       name="end"
                       class="form-control{{ $errors->has('end') ? ' form-control-danger' : '' }}"
                       value="{{ old('end') ?: Request::get('end') }}">

                @if ($errors->has('end'))
                    <div class="form-control-feedback">
                        {{ $errors->first('end') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group{{ $errors->has('limit') ? ' has-danger' : '' }}">
                <label for="limit" class="form-control-label">
                    Limit
                    <small class="text-muted">(0 for unlimited)</small>
                </label>
                <input id="limit"
                       type="number"
                       min="0"
                       class="form-control{{ $errors->has('limit') ? ' form-control-danger' : '' }}"
                       name="limit"
                       value="{{ old('limit') ?: Request::get('limit', 0) }}"
                       autocomplete="off">

                @if ($errors->has('limit'))
                    <div class="form-control-feedback">
                        {{ $errors->first('limit') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xs-12">
            <input type="submit" class="btn btn-primary" value="Generate report">
        </div>
    </form>

    @if (!is_null($report))
        <div class="col-xs-12 mb-4">
            <h4>
                Total:
                <i class="fa fa-eur text-muted"></i>{{ sprintf('%.2f', $report->sum('amount')) }}
            </h4>
            <a href="{{ route('admin.reports.download', Request::all()) }}" class="btn btn-sm btn-success">Download report</a>
        </div>

        @if (Request::get('report') === '1')
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'id', 'sort_direction' => Request::get('sort') === 'id' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                ID
                                @if (Request::get('sort') === 'id')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'email', 'sort_direction' => Request::get('sort') === 'email' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Email
                                @if (Request::get('sort') === 'email')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'date', 'sort_direction' => Request::get('sort') === 'date' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Date
                                @if (Request::get('sort') === 'date')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'country', 'sort_direction' => Request::get('sort') === 'country' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Country
                                @if (Request::get('sort') === 'country')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'subscription', 'sort_direction' => Request::get('sort') === 'subscription' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Subscription
                                @if (Request::get('sort') === 'subscription')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'amount', 'sort_direction' => Request::get('sort') === 'amount' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Amount
                                @if (Request::get('sort') === 'amount')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report as $row)
                        <tr>
                            <td>{{ $row->get('id') }}</td>
                            <td>{{ $row->get('email') }}</td>
                            <td>{{ $row->get('date') }}</td>
                            <td>{{ $row->get('country') }}</td>
                            <td>{{ $row->get('subscription') }}</td>
                            <td>
                                <i class="fa fa-eur text-muted"></i>{{ sprintf('%.2f', $row->get('amount')) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif (Request::get('report') === '2')
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'country', 'sort_direction' => Request::get('sort') === 'country' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Country
                                @if (Request::get('sort') === 'country')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.reports.index', collect(Request::all())->merge(['sort' => 'amount', 'sort_direction' => Request::get('sort') === 'amount' && Request::get('sort_direction') === 'desc' ? 'asc' : 'desc'])->all()) }}">
                                Amount
                                @if (Request::get('sort') === 'amount')
                                    <i class="fa fa-caret-{{ Request::get('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report as $row)
                        <tr>
                            <td>{{ $row->get('country') }}</td>
                            <td>
                                <i class="fa fa-eur text-muted"></i>{{ sprintf('%.2f', $row->get('amount')) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
@endsection
