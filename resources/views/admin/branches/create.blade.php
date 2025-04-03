@extends('admin.layouts.master')

@section('page_title')
    {{ __('Create Branch') }}
@endsection

@push('css')
    <style>
        #output {
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <form method="POST" action="{{ route('branches.store') }}">
        @csrf()

        <div class="page-header">
            <div class="card breadcrumb-card">
                <div class="row justify-content-between align-content-between" style="height: 100%;">
                    <div class="col-md-6">
                        <h3 class="page-title">{{ __('Branches') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('branches.index') }}">{{ __('Branches') }}</a>
                            </li>
                            <li class="breadcrumb-item active-breadcrumb">
                                <a href="{{ route('branches.create') }}">{{ __('Create Branch') }}</a>
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

        <section class="crud-body">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">

                        <div class="card-header">
                            <h5 class="card-title"> Branch Information </h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <!-- Branch Name -->
                                    <div class="form-group">
                                        <label for="branch_name" class="required">{{ __('Branch Name') }}:</label>
                                        <input type="text" name="branch_name" id="branch_name"
                                            class="form-control @error('branch_name') form-control-error @enderror" required
                                            value="{{ old('branch_name') }}">

                                        @error('branch_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Location Selection -->
                                    <div class="form-group">
                                        <label for="location_id" class="required">{{ __('Select Location') }}:</label>
                                        <select name="location_id" id="location_id" class="form-control select2" required>
                                            <option value="">{{ __('Select a location') }}</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('location_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Address -->
                                    <div class="form-group">
                                        <label for="address">{{ __('Address') }}:</label>
                                        <textarea name="address" id="address" rows="3"
                                            class="form-control @error('address') form-control-error @enderror">{{ old('address') }}</textarea>

                                        @error('address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="form-group">
                                        <label for="phone_number">{{ __('Phone Number') }}:</label>
                                        <input type="text" name="phone_number" id="phone_number"
                                            class="form-control @error('phone_number') form-control-error @enderror"
                                            value="{{ old('phone_number') }}">

                                        @error('phone_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}:</label>
                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') form-control-error @enderror"
                                            value="{{ old('email') }}">

                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="latitude" class="required">{{ __('latitude') }}:</label>
                                        <input type="latitude" name="latitude" id="latitude"
                                            class="form-control @error('latitude') form-control-error @enderror"
                                            value="{{ old('latitude') }}">

                                        @error('latitude')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="longitude" class="required">{{ __('longitude') }}:</label>
                                        <input type="longitude" name="longitude" id="longitude"
                                            class="form-control @error('longitude') form-control-error @enderror"
                                            value="{{ old('longitude') }}">

                                        @error('longitude')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>




                                </div> <!-- col-md-12-finish -->
                            </div> <!-- row-finish -->
                        </div> <!-- card-body-finish -->

                    </div> <!-- card-finish -->

                </div> <!-- col-md-12-finish -->
            </div> <!-- row-finish -->
        </section>

    </form>
@endsection
