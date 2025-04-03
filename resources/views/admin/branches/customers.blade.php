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
                        <li class="breadcrumb-item active-breadcrumb">
                            @if (request()->has('branch') && request('branch') != '')
                                <a href="#">
                                    {{ __('Customers for Branch: ') . \App\Models\Branch::find(request('branch'))->branch_name }}
                                </a>
                            @else
                                {{ __('All Locations') }}
                            @endif
                        </li>
                    </ul>
                </div>
                @if (auth()->user()->hasRole('super Admin') || auth()->user()->hasRole('Admin'))
                    <div class="col-md-6 text-right">
                        <!-- Export Button -->
                        <a href="{{ route('customers.export') }}" class="btn btn-primary mt-3">
                            {{ __('Export to Excel') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover table-center mb-0" id="table">
                        <thead>
                            <tr>
                                <th>{{ __('SL') }}</th>
                                <th>{{ __('Customer Code') }}</th>
                                <th>{{ __('Business Name') }}</th>
                                <th>{{ __('Phone Number') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Branch') }}</th>
                                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('supervisor'))
                                    <th>{{ __('Created / Reviewed By') }}</th>
                                @endif
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
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
@endsection

@push('scripts')
    <script>
        $(function() {
            let columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'customer_code',
                    name: 'customer_code'
                },
                {
                    data: 'business_name',
                    name: 'business_name'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'branch',
                    name: 'branch'
                },
                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('supervisor'))
                    {
                        data: 'created_reviewed_by',
                        name: 'created_reviewed_by',
                        orderable: false,
                        searchable: false
                    },
                @endif {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ];

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('branches.allBranchesData') }}', // Adjust the route as needed
                    data: function(d) {
                        d.branch = new URLSearchParams(window.location.search).get(
                            'branch'); // Get the 'branch' parameter from the URL
                    }
                },
                columns: columns,

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
                                    requestList.append(
                                        "<li>No requests found for this customer.</li>"
                                    );
                                } else {
                                    data.forEach(function(request) {
                                        var listItem =
                                            `<li>${request.document_type.replace(/_/g, ' ')}: ${request.document_details}</li>`;
                                        requestList.append(listItem);
                                    });
                                }
                            },
                            error: function() {
                                console.error('Error fetching document requests.');
                                requestList.append(
                                    "<li>Error loading requests.</li>");
                            }
                        });

                        // Show the modal
                        var bootstrapModal = new bootstrap.Modal(document.getElementById(
                            "documentRequestModal"));
                        bootstrapModal.show();

                        document.getElementById('documentRequestModal').querySelector(
                            '.btn-close').addEventListener('click',
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
            }, function(result) {
                if (result) {
                    var action = current_object.attr('data-action');
                    var token = jQuery('meta[name="csrf-token"]').attr('content');
                    var id = current_object.attr('data-id');

                    $('body').html("<form class='form-inline remove-form' method='POST' action='" + action +
                        "'></form>");
                    $('body').find('.remove-form').append(
                        '<input name="_method" type="hidden" value="post">');
                    $('body').find('.remove-form').append('<input name="_token" type="hidden" value="' +
                        token + '">');
                    $('body').find('.remove-form').append('<input name="id" type="hidden" value="' + id +
                        '">');
                    $('body').find('.remove-form').submit();
                }
            });
        });
    </script>
@endpush
