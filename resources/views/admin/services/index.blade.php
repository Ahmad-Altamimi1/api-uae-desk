@extends('admin.layouts.master')

@section('page_title')
    {{ __('Services') }}
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
					<h3 class="page-title">{{ __('Services') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard') }}">Dashboard</a>
						</li>
						<li class="breadcrumb-item active-breadcrumb">
							<a href="{{ route('services.index') }}">{{ __('Services') }}</a>
						</li>
					</ul>
				</div>
				@if (Gate::check('services-create'))
					<div class="col-md-3">
						<div class="create-btn pull-right">
							<a href="{{ route('services.create') }}" class="btn custom-create-btn">{{ __('Add Service') }}</a>
						</div>
					</div>
				@endif
			</div>
		</div><!-- /card finish -->	
	</div><!-- /Page Header -->

	<div class="row">
		<div class="col-md-12">
			<div class="card">

				<div class="card-body">
					<table class="table table-hover table-center mb-0" id="table">
						<thead>
							<tr>
								<th>{{ __('SL') }}</th>
								<th>{{ __('Service Name') }}</th>
								@if (Gate::check('services-edit') || Gate::check('services-delete'))
									<th>{{ __('Actions') }}</th>
								@endif 
							</tr>
						</thead>

						<tbody>
							{{-- Data will be populated via DataTables --}}
						</tbody>					
					</table>
				</div>

			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script>
	$(function() {
		$('#table').DataTable({
			processing: true,
			serverSide: true,
			order: [[0, 'desc']],
			ajax: '{{ route('services.index') }}',
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'name', name: 'name' },
				@if (Gate::check('services-edit') || Gate::check('services-delete'))
					{ data: 'action', name: 'action', orderable: false, searchable: false }
				@endif 
			],
		});
	});
</script>

<script type="text/javascript">
	$("body").on("click", ".remove-customer", function() {
		var current_object = $(this);
		swal({
			title: "Are you sure?",
			text: "You will not be able to recover this data!",
			type: "error",
			showCancelButton: true,
			dangerMode: true,
			cancelButtonClass: '#DD6B55',
			confirmButtonColor: '#dc3545',
			confirmButtonText: 'Delete!',
		}, function (result) {
			if (result) {
				var action = current_object.attr('data-action');
				var token = jQuery('meta[name="csrf-token"]').attr('content');
				var id = current_object.attr('data-id');

				$('body').html("<form class='form-inline remove-form' method='POST' action='" + action + "'></form>");
				$('body').find('.remove-form').append('<input name="_method" type="hidden" value="post">');
				$('body').find('.remove-form').append('<input name="_token" type="hidden" value="' + token + '">');
				$('body').find('.remove-form').append('<input name="id" type="hidden" value="' + id + '">');
				$('body').find('.remove-form').submit();
			}
		});
	});
</script>
@endpush
