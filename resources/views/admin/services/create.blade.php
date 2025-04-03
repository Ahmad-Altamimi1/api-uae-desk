@extends('admin.layouts.master')

@section('page_title')
    {{ __('Create Service') }}
@endsection

@push('css')
	<style>
		#output {
			width: 100%;
		}
	</style>
@endpush

@section('content')
	<form method="POST" action="{{ route('services.store') }}">
		@csrf()

		<div class="page-header">
            <div class="card breadcrumb-card">
                <div class="row justify-content-between align-content-between" style="height: 100%;">
                    <div class="col-md-6">
                        <h3 class="page-title">{{ __('Services') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">
								<a href="{{ route('services.index') }}">{{ __('Services') }}</a>
							</li>
                            <li class="breadcrumb-item active-breadcrumb">
								<a href="{{ route('services.create') }}">{{ __('Create Service') }}</a>
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
							<h5 class="card-title"> Service Information </h5>
						</div>

						<div class="card-body">
							<div class="row">
								<div class="col-md-12">

									<div class="form-group">
										<label for="name" class="required">{{ __('Service Name') }}:</label>
										<input type="text" name="name" id="name" class="form-control @error('name') form-control-error @enderror" required value="{{ old('name') }}">
										
										@error('name')
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
