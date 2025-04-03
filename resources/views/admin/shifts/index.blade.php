@extends('admin.layouts.master')

@section('page_title')
    {{ __('Shifts') }}
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
                    <h3 class="page-title">{{ __('Shifts') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active-breadcrumb">
                            <a href="{{ route('shifts.index') }}">{{ __('Shifts') }}</a>
                        </li>
                    </ul>
                </div>
                @if (Gate::check('shifts-create') || auth()->user()->hasRole('Super Admin'))
                    <div class="col-md-3">
                        <div class="create-btn pull-right">
                            <a href="{{ route('shifts.create') }}" class="btn custom-create-btn">{{ __('Add Shift') }}</a>
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
                                <th>{{ __('Shift Name') }}</th>
                                <th>{{ __('Start Time') }}</th>
                                <th>{{ __('End Time') }}</th>
                                <th>{{ __('Status') }}</th>
                                @if (Gate::check('shifts-edit') || Gate::check('shifts-delete') || auth()->user()->hasRole('Super Admin'))
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
                order: [
                    [0, 'desc']
                ],
                ajax: '{{ route('shifts.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                        searchable: false
                    },
                    @if (Gate::check('shifts-edit') || Gate::check('shifts-delete') || auth()->user()->hasRole('Super Admin'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ],

            });
        });
    </script>

    <script type="text/javascript">
        $("body").on("click", ".remove-shift", function() {
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

                    // Create a form with the DELETE method
                    var form = $('<form>', {
                        'action': action,
                        'method': 'POST',
                        'style': 'display: none;'
                    });

                    // Append necessary inputs
                    form.append('<input name="_method" type="hidden" value="DELETE">');
                    form.append('<input name="_token" type="hidden" value="' + token + '">');
                    form.append('<input name="id" type="hidden" value="' + id + '">');

                    // Append form to the body and submit it
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    </script>
@endpush
