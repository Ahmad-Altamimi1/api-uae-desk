@extends('admin.layouts.master')

@section('page_title')
    {{ __('Branches') }}
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
					<h3 class="page-title">{{ __('Branches') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard') }}">Dashboard</a>
						</li>
						<li class="breadcrumb-item active-breadcrumb">
							<a href="{{ route('branches.index') }}">{{ __('Branches') }}</a>
						</li>
					</ul>
				</div>
				@if (Gate::check('branches-create'))
					<div class="col-md-3">
						<div class="create-btn pull-right">
							<a href="{{ route('branches.create') }}" class="btn custom-create-btn">{{ __('Add Branch') }}</a>
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
								<th>{{ __('Branch Name') }}</th>
                                <th>{{ __('Location') }}</th>
								<th>{{ __('Address') }}</th>
								<th>{{ __('Phone Number') }}</th>
								<th>{{ __('Email') }}</th>
								{{-- <th>{{ __('Status') }}</th> --}}

								@if (Gate::check('branches-edit') || Gate::check('branches-delete'))
									<th>{{ __('Actions') }}</th>
								@endif 
							</tr>
						</thead>

						<tbody>
							
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
			responsive: false,
			serverSide: true,
			order: [[0, 'desc']],
			ajax: '{{ route('branches.index') }}',
			columns: [
				{ data: 'DT_RowIndex',  name: 'DT_RowIndex' },
				{ data: 'branch_name',  name: 'branch_name' },
                { data: 'location',     name:'Location'},
				{ data: 'address',      name: 'address' },
				{ data: 'phone_number', name: 'phone_number' },
				{ data: 'email',        name: 'email' },
				// { data: 'status', name: 'status' },						        

				@if (Gate::check('branches-edit') || Gate::check('branches-delete'))
					{ data: 'action', name: 'action', orderable: false, searchable: false }
				@endif 
			],
		});
	});
</script>

<script type="text/javascript">
	$("body").on("click", ".remove-branch", function() {
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

{{-- <script type="text/javascript">
	function changeBranchStatus(_this, id) {
		var status = $(_this).prop('checked') == true ? 1 : 0;
		let _token = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
			url: `{{ route('branches.status_update') }}`,
			type: 'get',
			data: {
				_token: _token,
				id: id,
				status: status 
			},
			success: function (result) {
				if (status == 1) {
					toastr.success(result.message);
				} else {
					toastr.error(result.message);
				} 
			}
		});
	}
</script> --}}
@endpush
