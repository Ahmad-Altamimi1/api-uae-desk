@extends('admin.layouts.master')

@section('page_title')
    {{ __('Create Shift') }}
@endsection

@push('css')
    <style>
        #output {
            width: 100%;
        }
    </style>
@endpush
@section('content')
    <div class="crud-body">
        <div class="page-header">
            <div class="card breadcrumb-card">
                <div class="row justify-content-between align-content-between" style="height: 100%;">
                    <div class="col-md-6">
                        <h3 class="page-title">{{ __('shifts') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('shifts.index') }}">{{ __('shifts') }}</a>
                            </li>
                            <li class="breadcrumb-item active-breadcrumb">
                                <a href="{{ route('shifts.create') }}">{{ __('Create Shift') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <div class="create-btn pull-right">
                            <button type="submit" class="btn custom-create-btn">{{ __('Save') }}</button>
                        </div>
                    </div>
                </div>
            </div><!-- /card finish -->
        </div><!-- /Page Header -->
        <div class="card">

            <div class="container mt-4">
                <h4>{{ __('Create Shift') }}</h4>
                <form action="{{ route('shifts.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name">{{ __('Shift Name') }}</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="start_time">{{ __('Start Time') }}</label>
                        <input type="time" name="start_time" id="start_time"
                            class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}">
                        @error('start_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="end_time">{{ __('End Time') }}</label>
                        <input type="time" name="end_time" id="end_time"
                            class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}">
                        @error('end_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
