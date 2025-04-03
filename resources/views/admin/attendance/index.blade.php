@extends('admin.layouts.master')

@section('page_title')
    {{ __('Attendance') }}
@endsection

@push('css')
    <style>
        .table tr td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="card breadcrumb-card">
            <div class="row justify-content-between align-content-between" style="height: 100%;">
                <div class="col-md-6">
                    <h3 class="page-title">{{ __('Attendance') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active-breadcrumb">
                            <a href="{{ route('attendances.index') }}">{{ __('Attendance') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- /card finish -->
    </div><!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('attendances.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="filter_date" class="form-control">
                                    <option value="all" {{ request('filter_date') === 'all' ? 'selected' : '' }}>
                                        {{ __('All Dates') }}</option>
                                    <option value="today" {{ request('filter_date') === 'today' ? 'selected' : '' }}>
                                        {{ __('Today') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="filter_late" class="form-control">
                                    <option value="all" {{ request('filter_late') === 'all' ? 'selected' : '' }}>
                                        {{ __('All Status') }}</option>
                                    <option value="late" {{ request('filter_late') === 'late' ? 'selected' : '' }}>
                                        {{ __('Late') }}</option>
                                    <option value="not_late" {{ request('filter_late') === 'not_late' ? 'selected' : '' }}>
                                        {{ __('Not Late') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="filter_user" class="form-control">
                                    <option value="">{{ __('Select User') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('filter_user') === (string) $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="filter_branch_id" class="form-control">
                                    <option value="">{{ __('Select Branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('filter_branch_id') === (string) $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-hover table-center mb-0" id="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>User</th>
                                <th>Login Time</th>
                                <th>Branch</th>
                                <th>Is Late</th>
                                <th>Late Minutes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($attendance->login_time)->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ optional($attendance->branch)->branch_name ?? 'N/A' }}</td>

                                    <td>{{ $attendance->is_late ? 'Yes' : 'No' }}</td>
                                    <td>{{ $attendance->is_late ? $attendance->late_minutes : '0' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
