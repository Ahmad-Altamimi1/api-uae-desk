@extends('admin.layouts.master')

@section('page_title')
    {{ __('Customers') }}
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
					<h3 class="page-title">{{ __('Customers') }}</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
						<li class="breadcrumb-item active-breadcrumb">
							<a href="{{ route('customers.index') }}">{{ __('Customers') }}</a>
						</li>
					</ul>
				</div>
				@if (Gate::check('customers-create'))
					<div class="col-md-3">
						<div class="create-btn pull-right">
							<a href="{{ route('customers.create') }}" class="btn custom-create-btn">{{ __('Add Customer') }}</a>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<table class="table table-hover table-center mb-0" id="table">
						<thead>
							<tr>
								<th>{{ __('SL') }}</th>
                                <th>{{ __('Application ID') }}</th>
								<th>{{ __('Business Name') }}</th>
								<th>{{ __('Phone Number') }}</th>
								<th>{{ __('Email') }}</th>
								<th>{{ __('Branch') }}</th>
								<th>{{ __('Services') }}</th>
								<th>{{ __('Status') }}</th>
								@if (Gate::check('customers-edit') || Gate::check('customers-delete'))
									<th>{{ __('Actions') }}</th>
								@endif
							</tr>
						</thead>
						<tbody>
							{{-- Data populated dynamically using DataTables --}}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="documentRequestModal" tabindex="-1" aria-labelledby="documentRequestModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="documentRequestModalLabel">{{ __('Document Request Details') }}</h5>
                    <i class="btn-close" style="color:#c4ab54 !important; pointer:cursor" data-feather="x-circle"></i>
				</div>
				<div class="modal-body">
					<p><strong>{{ __('Customer Name:') }}</strong> <span id="modalCustomerName"></span></p>
					<p><strong>{{ __('Document Requests:') }}</strong></p>
					<ul id="documentRequestList"></ul>
				</div>
				<div class="modal-footer d-none">
					<button type="button" class="btn btn-secondary btn-close">{{ __('Close') }}</button>
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
			ajax: '{{ route('customers.index') }}',
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'customer_code', name: 'customer_code' },
				{ data: 'business_name', name: 'business_name' },
				{ data: 'phone_number', name: 'phone_number' },
				{ data: 'email', name: 'email' },
				{ data: 'branch', name:'branch'},
				{ data: 'services', name:'services'},
				{ data: 'status', name: 'status'}, // Updated to reflect status with badges
				@if (Gate::check('customers-edit') || Gate::check('customers-delete')  ||auth()->user()->hasRole('supervisor'))
					{ data: 'action', name: 'action', orderable: false, searchable: false },
				@endif
			],
			"drawCallback": function(settings) {
				// Attach the click event listener for document requests button after DataTable redraw
				
				$(".blink-indicator").on("click", function() {
					var customerId = $(this).data("customer-id");
					var customerName = $(this).data("customer-name");
					var modalElement = $("#documentRequestModal");
					var requestList = modalElement.find("#documentRequestList");

					// Set the customer name in the modal
					modalElement.find("#modalCustomerName").text(customerName);

					// Fetch document requests via AJAX
					$.ajax({
						url: '/admin/document-requests/' + customerId,
						method: 'GET',
						success: function(data) {
							// Clear previous list
							requestList.empty();

							// Populate the list with new data
							if (data.length === 0) {
								requestList.append("<li>No requests found for this customer.</li>");
							} else {
								data.forEach(function(request) {
									var listItem = `<li>${request.document_type.replace(/_/g, ' ')}: ${request.document_details}</li>`;
									requestList.append(listItem);
								});
							}
						},
						error: function() {
							console.error('Error fetching document requests.');
							requestList.append("<li>Error loading requests.</li>");
						}
					});

					// Show the modal
					var bootstrapModal = new bootstrap.Modal(document.getElementById("documentRequestModal"));
					bootstrapModal.show();

					document.getElementById('documentRequestModal').querySelector('.btn-close').addEventListener('click',
						function() {
							bootstrapModal.hide();
						});
				});

			}
		});
	});
</script>
<script type="text/javascript">
	$("body").on("click", ".remove-service", function() {
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
