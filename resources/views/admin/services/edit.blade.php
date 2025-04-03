@extends('admin.layouts.master')

@section('page_title')
    {{ __('Edit Service') }}
@endsection

@push('css')
	<style>
		#output {
			width: 100%;
		}
	</style>
@endpush

@section('content')
	<form method="post" action="{{ route('services.update', $service->id) }}">
		@csrf()

		<!-- Page Header -->
		<div class="page-header">
			<div class="card breadcrumb-card">
				<div class="row justify-content-between align-content-between" style="height: 100%;">
					<div class="col-md-6">
						<h3 class="page-title">{{ __('Services') }}</h3>
						<ul class="breadcrumb">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard') }}">Dashboard</a>
							</li>
							<li class="breadcrumb-item">
								<a href="{{ route('services.index') }}">{{ __('Services') }}</a>
							</li>
							<li class="breadcrumb-item active-breadcrumb">
								<a href="{{ route('services.edit', $service->id) }}">{{ __('Edit Service') }} - ({{ $service->name }})</a>
							</li>
						</ul>
					</div>
					<div class="col-md-3">
						<div class="create-btn pull-right">
							<button type="submit" class="btn custom-create-btn">{{ __('Update') }}</button>
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
							<h5 class="card-title"> Service Information - ({{ $service->name }})</h5>
						</div>

						<div class="card-body">
							<div class="row">
								<div class="col-md-12">

									<div class="form-group">
										<label for="name" class="required">{{ __('Service Name') }}:</label>
										<input type="text" name="name" id="name" class="form-control @error('name') form-control-error @enderror" required value="{{ $service->name }}">

										@error('name')
											<span class="text-danger">{{ $message }}</span>
										@enderror
									</div>

								</div> <!-- /col-md-12 -->
							</div> <!-- /row -->
						</div> <!-- /card-body-finish -->

					</div> <!-- card-finish -->

				</div> <!-- /col-md-12 -->
			</div> <!-- row-finish -->
		</section> <!-- card-body-finish -->

	</form>
@endsection


@push('scripts')
<script type="text/javascript">
	$("#name").keyup(function(){
		var name = this.value;
		name = name.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase();
		$("#slug").val(name);
	})
</script>
@endpush
