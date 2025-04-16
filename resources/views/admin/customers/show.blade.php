@extends('admin.layouts.master')

@section('page_title')
    {{ __('View Customer') }}
@endsection
@php
    use Illuminate\Support\Str;
    $countries = \App\Models\Country::pluck('name')->all();

@endphp
@push('styles')
    <style>
        .mediaImage {
            transition: transform 0.3s ease;
            transform-origin: center center;
        }
    </style>
@endpush
@section('content')
    <div class="page-header">
        <div class="card breadcrumb-card">
            <div class="row justify-content-between align-content-between" style="height: 100%;">
                <div class="col-md-6">
                    <h3 class="page-title">{{ __('Customer Details') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('customers.index') }}">Customers</a>
                        </li>
                        <li class="breadcrumb-item active-breadcrumb">
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"> {{ $customer->first_name }} {{ $customer->last_name }} {{ __('Detail') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Customer Code:') }}</div>
                                <div class="col-sm-8">{{ $customer->customer_code ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Service:') }}</div>
                                <div class="col-sm-8">{{ $customer->services->pluck('name')->implode(', ') ?? __('N/A') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Business Name:') }}</div>
                                <div class="col-sm-8">{{ $customer->business_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('First Name:') }}</div>
                                <div class="col-sm-8">{{ $customer->first_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Last Name:') }}</div>
                                <div class="col-sm-8">{{ $customer->last_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Phone Number:') }}</div>
                                <div class="col-sm-8">{{ $customer->phone_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Second Number:') }}</div>
                                <div class="col-sm-8">{{ $customer->second_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Email:') }}</div>
                                <div class="col-sm-8">{{ $customer->email }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Price:') }}</div>
                                <div class="col-sm-8">AED {{ number_format($customer->price, 2) ?? __('0') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Invoice Number:') }}</div>
                                <div class="col-sm-8">{{ $customer->invoice_number ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Transaction ID:') }}</div>
                                <div class="col-sm-8">{{ $customer->transaction_refrence_number ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('FTA Username:') }}</div>
                                <div class="col-sm-8">{{ $customer->fta_user_name ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('FTA Password:') }}</div>
                                <div class="col-sm-8">{{ $customer->fta_password ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Email Account:') }}</div>
                                <div class="col-sm-8">{{ $customer->gmail_user_name ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Email Password:') }}</div>
                                <div class="col-sm-8">{{ $customer->gmail_password ?? __('N/A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 font-weight-bold">{{ __('Status:') }}</div>
                                <div class="col-sm-8">
                                    @php
                                        $statusClass = '';
                                        $statusText = '';

                                        switch ($customer->status) {
                                            case 0:
                                                $statusClass = 'badge-warning'; // Yellow for Pending
                                                $statusText = 'Pending';
                                                break;
                                            case 1:
                                                $statusClass = 'badge-info'; // Blue for In Process
                                                $statusText = 'In Process';
                                                break;
                                            case 2:
                                                $statusClass = 'badge-primary'; // Blue for Verified
                                                $statusText = 'Verified';
                                                break;
                                            case 3:
                                                $statusClass = 'badge-success'; // Green for Completed
                                                $statusText = 'Completed';
                                                break;
                                            default:
                                                $statusClass = 'badge-secondary'; // Grey for Unknown
                                                $statusText = 'Unknown';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                            @if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('Admin'))
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">{{ __('Address:') }}</div>
                                    <div class="col-sm-8">{{ $customer->address }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">{{ __('Portal Email:') }}</div>
                                    <div class="col-sm-8">{{ $customer->fta_user_name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">{{ __('Portal Password:') }}</div>
                                    <div class="col-sm-8">{{ $customer->fta_password }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">{{ __('Tax ID:') }}</div>
                                    <div class="col-sm-8">{{ $customer->tax_id }}</div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <div class="col-md-12">

                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">{{ __('Actions') }}</h5>
                            </div>
                            <!-- Edit Status (Script is in the footer) -->
                            <div class="card-body text-center">
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">{{ __('Edit Status:') }}</div>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="editStatus" data-id="{{ $customer->id }}">
                                            <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>
                                                {{ __('Pending') }}</option>
                                            <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>
                                                {{ __('In Process') }}</option>
                                            <option value="2" {{ $customer->status == 2 ? 'selected' : '' }}>
                                                {{ __('Verified') }}</option>
                                            <option value="3" {{ $customer->status == 3 ? 'selected' : '' }}>
                                                {{ __('Completed') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">{{ __('Created By:') }}</div>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="editCreator" data-id="{{ $customer->id }}">
                                            <option value="2"></option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ $customer->creator->id == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- <script>
                                        document.getElementById('editCreator').addEventListener('change', function() {

                                            var creatorId = this.value;
                                            var customerId = this.getAttribute('data-id');


                                            fetch("{{ route('customers.edit.creator', $$customer->id) }}", {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({
                                                        created_by: creatorId
                                                    })
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        alert('Creator updated successfully.');
                                                    } else {
                                                        alert('Failed to update creator.');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                });
                                        });
                                    </script> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-12">
                    <!-- Expert Actions Card -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ __('Expert Actions') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            @if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('Admin'))
                                <button type="button" id="submitForReviewButton" class="btn btn-secondary">
                                    <i class="fe fe-file"></i> {{ __('Review documents') }}
                                </button>


                                <button type="button" id="submitFTADocument" class="btn btn-secondary"
                                    data-bs-toggle="modal" data-bs-target="#uploadFtaDocumentModal">
                                    <i class="fe fe-file"></i> {{ __('Upload FTA Document') }}
                                </button>

                                <!-- Add Tax ID Button -->
                                <button type="button" id="addTaxIdButton" class="btn btn-primary">
                                    <i class="fe fe-plus-circle"></i> {{ __('Add TRN') }}
                                </button>
                            @endif

                            @if (Auth::user()->hasRole('expert'))
                                @if ($customer->status == 2)
                                    <!-- Professional Message -->
                                    <div class="alert alert-success">
                                        <h5 class="mb-3"><i class="fe fe-check-circle"></i>
                                            {{ __('Review in Progress') }}</h5>
                                        <p>{{ __('This customer has already been submitted for review. No further actions can be taken at this time. Please wait for the review process to be completed.') }}
                                        </p>
                                    </div>
                                    <!-- Informative Text -->
                                    <p class="text-muted text-center mt-4">
                                        <i class="fe fe-check-circle"
                                            style="font-size: 2rem;color:#c4ab54 !important;"></i>
                                    </p>
                                    <p class="text-muted mt-4 text-center">
                                        {{ __('The review process is currently underway. For further assistance or inquiries, please reach out to the administrator.') }}
                                    </p>
                                @else
                                    <!-- Submit for Review Button -->
                                    <button id="submitForReviewButton" type="button" class="btn btn-warning mb-3">
                                        <i class="fe fe-check-circle"></i> {{ __('Create Servie') }}
                                    </button>

                                    <!-- Request for Document Button -->
                                    <button type="button" class="btn btn-secondary mb-3" id="requestDocumentButton">
                                        <i class="fe fe-file-plus"></i> {{ __('Request for Document') }}
                                    </button>

                                    <!-- Informative Text -->
                                    <p class="text-muted mt-4">
                                        {{ __('You can submit the customer for review or request additional documents. Make sure all required information is accurate before proceeding.') }}
                                    </p>
                                @endif
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Media Section -->
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('Uploaded Media') }}</h5>
                    </div>
                    @php

                    @endphp
                    <div class="card-body">
                        @if ($customer->media->count())
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('Document Name') }}</th>
                                        <th>{{ __('Preview / View') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer->media as $media)
                                        <tr>
                                            <td>{{ ucwords(str_replace('_', ' ', $media->document_name)) }}</td>

                                            <td>
                                                <button class="btn btn-info btn-sm view-file"
                                                    data-path="{{ asset('storage/' . $media->file_path) }}"
                                                    data-type="{{ pathinfo($media->file_path, PATHINFO_EXTENSION) }}">
                                                    <i class="fe fe-eye"></i> {{ __('View') }}
                                                </button>
                                            </td>
                                            @if (Gate::check('customers-delete-media'))
                                                @if ($customer->status == 0 && auth()->user()->role == 'operator')
                                                    <td>
                                                        <form action="{{ route('customers.media.delete', $media->id) }}"
                                                            method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fe fe-trash"></i> {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                @else
                                                    <td class="text-muted">
                                                        <i class="fe fe-lock"></i>
                                                    </td>
                                                @endif
                                            @else
                                                <td class="text-muted">
                                                    <i class="fe fe-lock"></i>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center">{{ __('No documents uploaded yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            {{-- @php
                dd($customer->media->groupBy('document_name'))
            @endphp --}}
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('FTA Document') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($customer->ftamedia->count())
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('Document Name') }}</th>
                                        <th>{{ __('Preview / View') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    @endphp
                                    @foreach ($customer->ftamedia as $media)
                                        <tr>
                                            <td>{{ ucwords(str_replace('_', ' ', $media->document_name)) }}</td>

                                            <td>
                                                <button class="btn btn-info btn-sm view-file"
                                                    data-path="{{ asset('storage/' . $media->file_path) }}"
                                                    data-type="{{ pathinfo($media->file_path, PATHINFO_EXTENSION) }}">
                                                    <i class="fe fe-eye"></i> {{ __('View') }}
                                                </button>
                                            </td>

                                            <td class="text-muted">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#uploadFtaDocumentModal_{{ $media->id }}">
                                                    <i class="fe fe-edit"></i>
                                                </button>

                                                <!-- Update FTA Document Modal -->
                                                <div class="modal fade" id="uploadFtaDocumentModal_{{ $media->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="uploadFtaDocumentModalLabel_{{ $media->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form id="updateFtaForm_{{ $media->id }}" {{-- action="{{ route('customers.updateFtaDocument', $media->id) }}" --}}
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="submitForReviewModalLabel_{{ $media->id }}">
                                                                        {{ __('Update FTA Document') }}</h5>
                                                                    <button type="button" class="btn btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close">
                                                                        <i class="btn-close"
                                                                            style="color:#c4ab54 !important; cursor:pointer;"
                                                                            data-feather="x-circle"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <!-- Document Name -->
                                                                    <div class="form-group mb-3">
                                                                        <label for="document_name_{{ $media->id }}"
                                                                            class="form-label">
                                                                            {{ __('Document Name') }}</label>
                                                                        <input type="text" name="document_name"
                                                                            id="document_name_{{ $media->id }}"
                                                                            class="form-control"
                                                                            placeholder="{{ __('Enter Document Name') }}"
                                                                            value="{{ $media->document_name }}" disabled
                                                                            readonly>
                                                                    </div>

                                                                    <div class="form-group mb-3">
                                                                        <label for="start_date_{{ $media->id }}"
                                                                            class="form-label">
                                                                            {{ __('Start Date') }}</label>
                                                                        <input type="date" name="start_date"
                                                                            id="start_date_{{ $media->id }}"
                                                                            class="form-control start_date_Update"
                                                                            value="{{ $media->start_date }}" required>
                                                                    </div>
                                                                    <div class="form-group mb-3">
                                                                        <label for="expire_date_{{ $media->id }}"
                                                                            class="form-label">
                                                                            {{ __('Expiration Date') }}</label>
                                                                        <input type="date" name="expire_date"
                                                                            id="expire_date_{{ $media->id }}"
                                                                            class="form-control expire_date_Update"
                                                                            value="{{ $media->expire_date }}" required
                                                                            min="{{ $media->start_date }}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit"
                                                                        class="btn btn-primary">{{ __('Update') }}</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>


                                            </td>

                                        </tr>
                                        <script>
                                            document.getElementById('updateFtaForm_{{ $media->id }}').addEventListener('submit', async function(event) {
                                                event.preventDefault(); // Prevent default form submission

                                                // Get form data
                                                const form = event.target;
                                                const formData = new FormData(form);
                                                const csrfToken = document.querySelector('meta[name="csrf-token"]')
                                                    ?.content;

                                                const data = {
                                                    start_date: formData.get('start_date'),
                                                    expire_date: formData.get('expire_date'),
                                                    _method: 'PUT', // Spoof PUT request
                                                    _token: csrfToken // CSRF token
                                                };

                                                try {
                                                    const response = await fetch("{{ route('customers.updateFtaDocument', $media->id) }}", {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': csrfToken,
                                                            'Accept': 'application/json'
                                                        },
                                                        body: JSON.stringify(data)
                                                    });

                                                    const result = await response.json();

                                                    if (!response.ok) {

                                                        throw new Error("The start date must be before the expire date." || 'Something went wrong');
                                                    }

                                                    // Success case
                                                    toastr.success(result.message || 'FTA document updated successfully.');
                                                    bootstrap.Modal.getInstance(document.getElementById(
                                                        'uploadFtaDocumentModal_{{ $media->id }}')).hide();
                                                    // Optionally, refresh the page or update UI here
                                                    // window.location.reload();

                                                } catch (error) {
                                                    // Handle errors (network, validation, etc.)
                                                    console.error('Error:', error);
                                                    toastr.error(error.message || 'Failed to update FTA document.');
                                                }
                                            });
                                        </script>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center">{{ __('No documents uploaded yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ __('Upcoming payments') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <fieldset class="mb-4 p-3 border rounded">

                                @if ($entries->count())
                                    @foreach ($entries as $entry)
                                        <div class="card mb-3 shadow-sm">
                                            <div class="card-body">
                                                <div class="row mb-2">
                                                    <div class="col-sm-4 text-muted fw-bold">{{ __('Date:') }}</div>
                                                    <div class="col-sm-8">{{ $entry->date }}</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-sm-4 text-muted fw-bold">{{ __('Amount:') }}</div>
                                                    <div class="col-sm-8 text-success">AED
                                                        {{ number_format($entry->amount, 2) }}</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4 text-muted fw-bold">{{ __('Description:') }}
                                                    </div>
                                                    <div class="col-sm-8">{{ $entry->description }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="text-center">
                                        {{ __('Total upcoming payments: ') . $entries->count() }}
                                    </div>
                                @else
                                    <div class=" text-center">
                                        {{ __('No Upcoming payments available.') }}
                                    </div>
                                @endif
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
            </div>
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Tax declaration') }}</h5>
                </div>
                <div class="card-body">
                    @if ($customer)
                        <table class="table table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('Start Date') }}</th>

                                    <th>{{ __('expire Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->ftamedia as $fta)
                                    <tr>

                                        <td>{{ $fta->start_date }}</td>
                                        <td>{{ $fta->expire_date }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center">{{ __('No process data available.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('Process Time Tracking') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($customer)
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('Stage') }}</th>
                                        <th>{{ __('Start Time') }}</th>
                                        <th>{{ __('End Time') }}</th>
                                        <th>{{ __('Time Taken') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('Data Entry') }}</td>
                                        <td>{{ optional($customer->created_at)->format('Y-m-d H:i:s A') ?? '-' }}</td>
                                        @if ($customer->submitted_for_verification_at)
                                            <td>{{ optional(Carbon\Carbon::parse($customer->submitted_for_verification_at))->format('Y-m-d H:i:s A') ?? '-' }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ $customer->getDataEntryTimeAttribute() ? $customer->getDataEntryTimeAttribute() . ' min' : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Expert Verification') }}</td>
                                        @if ($customer->submitted_for_verification_at)
                                            <td>{{ optional(Carbon\Carbon::parse($customer->submitted_for_verification_at))->format('Y-m-d H:i:s A') ?? '-' }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        @if ($customer->expert_submitted_at)
                                            <td>{{ optional(Carbon\Carbon::parse($customer->expert_submitted_at))->format('Y-m-d H:i:s A') ?? '-' }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ $customer->getExpertVerificationTimeAttribute() ? $customer->getExpertVerificationTimeAttribute() . ' min' : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Supervisor Approval') }}</td>
                                        @if ($customer->expert_submitted_at)
                                            <td>{{ optional(Carbon\Carbon::parse($customer->expert_submitted_at))->format('Y-m-d H:i:s A') ?? '-' }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        @if ($customer->supervisor_approved_at)
                                            <td>{{ optional(Carbon\Carbon::parse($customer->supervisor_approved_at))->format('Y-m-d H:i:s A') ?? '-' }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ $customer->getSupervisorApprovalTimeAttribute() ? $customer->getSupervisorApprovalTimeAttribute() . ' min' : '-' }}
                                        </td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td><strong>{{ __('Total Verification Time') }}</strong></td>
                                        <td colspan="2"></td>
                                        <td><strong>{{ $customer->getTotalVerificationTimeAttribute() ? $customer->getTotalVerificationTimeAttribute() . ' min' : '-' }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="text-center">{{ __('No process data available.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Submit for Review Modal -->
        <div class="modal fade" id="submitForReviewModal" tabindex="-1" aria-labelledby="submitForReviewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('customers.submit.review', $customer->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="submitForReviewModalLabel">{{ __('Submit for Review') }}
                            </h5>
                            <i class="btn-close" style="color:#c4ab54 !important; pointer:cursor"
                                data-feather="x-circle"></i>
                        </div>
                        <div class="modal-body">

                            <!-- Portal Email Details Section -->
                            <fieldset class="mb-4">
                                <legend class="text-primary" style="font-size: 1.2rem;">
                                    <i class="fe fe-user"></i> {{ __('Portal Email Details') }}
                                </legend>
                                <div class="form-group mb-3">
                                    <label for="portal_email"
                                        class="form-label">{{ __('Portal Email / Username') }}</label>
                                    <input type="text" name="fta_user_name" id="portal_email" class="form-control"
                                        placeholder="{{ __('Enter Portal Email') }}"
                                        value="{{ $customer->fta_user_name }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="portal_password" class="form-label">{{ __('Portal Password') }}</label>
                                    <input type="text" name="fta_password" id="portal_password" class="form-control"
                                        placeholder="{{ __('Enter Portal Password') }}"
                                        value="{{ $customer->fta_password }}" required>
                                </div>
                            </fieldset>

                            <!-- Gmail Account Details Section -->
                            <fieldset class="mb-4">
                                <legend class="text-success" style="font-size: 1.2rem;">
                                    <i class="fe fe-mail"></i> {{ __('Gmail Account Details') }}
                                </legend>
                                <div class="form-group mb-3">
                                    <label for="email_address" class="form-label">{{ __('Email Address') }}</label>
                                    <input type="email" name="gmail_user_name" id="email_address" class="form-control"
                                        placeholder="{{ __('Enter Email Address') }}"
                                        value="{{ $customer->gmail_user_name }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email_password" class="form-label">{{ __('Email Password') }}</label>
                                    <input type="text" name="gmail_password" id="email_password" class="form-control"
                                        placeholder="{{ __('Enter Email Password') }}"
                                        value="{{ $customer->gmail_password }}">
                                </div>
                            </fieldset>
                        </div>


                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



    </div>
    </div>

    <!-- File Viewer Modal -->
    <div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileViewerModalLabel">{{ __('File Viewer') }}</h5>

                    <i class="btn-close" style="color:#c4ab54 !important; pointer:cursor" data-feather="x-circle"></i>

                </div>
                <div class="modal-body text-center" id="fileViewerContent">
                    <!-- File content will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Tax ID Modal -->
    <div class="modal fade" id="addTaxIdModal" tabindex="-1" aria-labelledby="addTaxIdModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('customers.add.tax_id', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTaxIdModalLabel">{{ __('Add TRN') }}</h5>
                        <i class="btn-close" style="color:#c4ab54 !important; cursor: pointer;"
                            data-feather="x-circle"></i>
                    </div>
                    <div class="modal-body">
                        <!-- Tax ID Input -->
                        <div class="form-group mb-3">
                            <label for="tax_id" class="form-label">{{ __('Tax Return Number') }}</label>
                            <input type="text" name="tax_id" id="tax_id" class="form-control"
                                value={{ $customer->tax_id ? $customer->tax_id : '' }}
                                placeholder="{{ __('Enter TRN') }}">
                        </div>

                        <!-- Send Email Checkbox -->
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="send_email" id="send_email" class="form-check-input"
                                    value="1">
                                <label for="send_email"
                                    class="form-check-label">{{ __('Send Email Notification') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal Structure -->
    <div class="modal fade" id="documentRequestModal" tabindex="-1" aria-labelledby="documentRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentRequestModalLabel">{{ __('Request Document Details') }}</h5>
                    <i class="btn-close" style="color:#c4ab54 !important; pointer:cursor" data-feather="x-circle"></i>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customers.request.document', $customer->id) }}" method="POST"
                        id="documentRequestForm">
                        @csrf
                        <div class="mb-3">
                            <label for="document_type" class="form-label">{{ __('Document Type') }}</label>
                            <select name="document_type" id="document_type"
                                class="form-control @error('document_type') is-invalid @enderror" required>
                                <option value="" disabled selected>{{ __('Select Document Type') }}</option>
                                @foreach (\App\Enums\DocumentType::all() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="document_details" class="form-label">{{ __('Additional Details') }}</label>
                            <textarea class="form-control" id="document_details" name="document_details" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Request Document') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @php
        $groupedMedia = $customer->media->groupBy('document_name');
    @endphp
    <!-- Full-Page Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createServiceModalLabel">{{ __('Create Service') }}</h5>
                    <i class="btn-close" style="color:#c4ab54 !important; cursor:pointer;" data-feather="x-circle"></i>
                </div>
                <div class="modal-body d-flex">
                    <!-- Left Panel: Service Creation Form -->
                    <div class="left-panel p-4" style="width: 40%; border-right: 1px solid #ddd;">
                        <h4>{{ __('Document Details') }}</h4>
                        <form id="documentForm" style="max-height: 75vh; overflow-y: auto;">
                            <!-- Document Name Section -->
                            <fieldset id="documentSection" class="document-section mb-4" style="display: none">
                                <legend class="sectionTitle text-dark font-weight-bold"
                                    style="font-size: 1.25rem; border-bottom: 2px solid #007bff; padding-bottom: 5px;">
                                    <i class="fas fa-file-alt"></i> Document Section
                                </legend>

                                <!-- Static Fields -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="profile_name_en" class="form-label">Profile Name in English</label>
                                        <input type="text" class="form-control" id="profile_name_en"
                                            name="profile_name_en" placeholder="Enter profile name in English"
                                            value="{{ old('profile_name_en', $customerDetails['profile_name_en'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="profile_name_ar" class="form-label">Profile Name in Arabic</label>
                                        <input type="text" class="form-control" id="profile_name_ar"
                                            name="profile_name_ar" placeholder="Enter profile name in Arabic"
                                            value="{{ old('profile_name_ar', $customerDetails['profile_name_ar'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="preferred_language" class="form-label">Preferred Language</label>
                                        <select class="form-control" id="preferred_language" name="preferred_language">
                                            <option value="" disabled>Select Preferred Language</option>
                                            <option value="english"
                                                {{ old('preferred_language', $customerDetails['preferred_language'] ?? '') == 'english' ? 'selected' : '' }}>
                                                English</option>
                                            <option value="arabic"
                                                {{ old('preferred_language', $customerDetails['preferred_language'] ?? '') == 'arabic' ? 'selected' : '' }}>
                                                Arabic</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="communication_channel" class="form-label">Preferred Communication
                                            Channel</label>
                                        <select class="form-control" id="communication_channel"
                                            name="communication_channel">
                                            <option value="" disabled>Select Communication Channel</option>
                                            <option value="email"
                                                {{ old('communication_channel', $customerDetails['communication_channel'] ?? '') == 'email' ? 'selected' : '' }}>
                                                Email</option>
                                            <option value="phone"
                                                {{ old('communication_channel', $customerDetails['communication_channel'] ?? '') == 'phone' ? 'selected' : '' }}>
                                                Phone</option>
                                            <option value="sms"
                                                {{ old('communication_channel', $customerDetails['communication_channel'] ?? '') == 'sms' ? 'selected' : '' }}>
                                                SMS</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Emirates ID Section -->
                            @foreach ($groupedMedia as $documentName => $mediaItems)
                                @if ($documentName == 'emirates_id')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-id-card"></i> Emirates ID Section {{ $index + 1 }}
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="emirates_id_number" class="form-label">Emirates ID
                                                        Number</label>
                                                    <input type="text" class="form-control" id="emirates_id_number"
                                                        name="emirates_id_number" placeholder="Enter Emirates ID number"
                                                        value="{{ old('emirates_id_number', $customerDetails['emirates_id']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="emirates_id_expiry" class="form-label">Emirates ID
                                                        Expiry</label>
                                                    <input type="date" class="form-control" id="emirates_id_expiry"
                                                        name="emirates_id_expiry"
                                                        value="{{ old('emirates_id_expiry', $customerDetails['emirates_id']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="emirates_id_first_name" class="form-label">First
                                                        Name</label>
                                                    <input type="text" class="form-control"
                                                        id="emirates_id_first_name" name="emirates_id_first_name"
                                                        placeholder="Enter first name"
                                                        value="{{ old('emirates_id_first_name', $customerDetails['emirates_id']['first_name'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="emirates_id_last_name" class="form-label">Last
                                                        Name</label>
                                                    <input type="text" class="form-control" id="emirates_id_last_name"
                                                        name="emirates_id_last_name" placeholder="Enter last name"
                                                        value="{{ old('emirates_id_last_name', $customerDetails['emirates_id']['last_name'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="emirates_id_dob" class="form-label">Date of Birth</label>
                                                    <input type="date" class="form-control" id="emirates_id_dob"
                                                        name="emirates_id_dob"
                                                        value="{{ old('emirates_id_dob', $customerDetails['emirates_id']['dob'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="emirates_id_nationality"
                                                        class="form-label">Nationality</label>
                                                    {{-- <input type="text" class="form-control" id="emirates_id_nationality"
                                                name="emirates_id_nationality" placeholder="Enter nationality"
                                                value="{{ old('emirates_id_nationality', $customerDetails['emirates_id']['nationality'] ?? '') }}"> --}}
                                                    <select class="form-control" id="emirates_id_nationality"
                                                        name="emirates_id_nationality">
                                                        <option value="">Select Issuing Country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country }}"
                                                                {{ old('emirates_id_nationality', $customerDetails['emirates_id']['nationality'] ?? '') == $country ? 'selected' : '' }}>
                                                                {{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                @if ($documentName == 'passport')
                                    <!-- Passport Details Section -->
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #ffc107; padding-bottom: 5px;">
                                                <i class="fas fa-passport"></i> Passport Details Section
                                                {{ $index + 1 }}
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="passport_number" class="form-label">Passport
                                                        Number</label>
                                                    <input type="text" class="form-control" id="passport_number"
                                                        name="passport_number" placeholder="Enter passport number"
                                                        value="{{ old('passport_number', $customerDetails['passport']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="passport_expiry" class="form-label">Passport
                                                        Expiry</label>
                                                    <input type="date" class="form-control" id="passport_expiry"
                                                        name="passport_expiry"
                                                        value="{{ old('passport_expiry', $customerDetails['passport']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="passport_issuing_country" class="form-label">Issuing
                                                        Country</label>
                                                    <select class="form-control" id="passport_issuing_country"
                                                        name="passport_issuing_country">
                                                        <option value="">Select Issuing Country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country }}"
                                                                {{ old('passport_issuing_country', $customerDetails['passport']['issuing_country'] ?? '') == $country ? 'selected' : '' }}>
                                                                {{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="passport_holder_name" class="form-label">Passport Holder
                                                        Name</label>
                                                    <input type="text" class="form-control" id="passport_holder_name"
                                                        name="passport_holder_name"
                                                        placeholder="Enter passport holder name"
                                                        value="{{ old('passport_holder_name', $customerDetails['passport']['holder_name'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif


                                <!-- Tax Certificate Details Section -->
                                @if ($documentName == 'tax_certificate')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #dc3545; padding-bottom: 5px;">
                                                <i class="fas fa-certificate"></i> Tax Certificate Details Section
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="tax_certificate_number" class="form-label">Tax Certificate
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="tax_certificate_number" name="tax_certificate_number"
                                                        placeholder="Enter tax certificate number"
                                                        value="{{ old('tax_certificate_number', $customerDetails['tax_certificate']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="tax_certificate_expiry" class="form-label">Tax Certificate
                                                        Expiry</label>
                                                    <input type="date" class="form-control"
                                                        id="tax_certificate_expiry" name="tax_certificate_expiry"
                                                        value="{{ old('tax_certificate_expiry', $customerDetails['tax_certificate']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="tax_registration_date" class="form-label">Tax Registration
                                                        Date</label>
                                                    <input type="date" class="form-control" id="tax_registration_date"
                                                        name="tax_registration_date"
                                                        placeholder="Enter tax registration date"
                                                        value="{{ old('tax_registration_date', $customerDetails['tax_certificate']['registration_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="tax_authority_name" class="form-label">Tax Authority
                                                        Name</label>
                                                    <input type="text" class="form-control" id="tax_authority_name"
                                                        name="tax_authority_name" placeholder="Enter tax authority name"
                                                        value="{{ old('tax_authority_name', $customerDetails['tax_certificate']['authority_name'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                
                                @if ($documentName == 'trade_license')
                                    <!-- Trade License Basic Information Section -->
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-file-contract"></i> Trade License Section
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="trade_license_number" class="form-label">Trade License
                                                        Number</label>
                                                    <input type="text" class="form-control" id="trade_license_number"
                                                        name="trade_license_number"
                                                        placeholder="Enter trade license number"
                                                        value="{{ old('trade_license_number', $customerDetails['trade_license']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="trade_license_expiry" class="form-label">Trade License
                                                        Expiry</label>
                                                    <input type="date" class="form-control" id="trade_license_expiry"
                                                        name="trade_license_expiry"
                                                        value="{{ old('trade_license_expiry', $customerDetails['trade_license']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="trade_license_issuance_date" class="form-label">Issuance
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="trade_license_issuance_date"
                                                        name="trade_license_issuance_date"
                                                        placeholder="Enter issuance date"
                                                        value="{{ old('trade_license_issuance_date', $customerDetails['trade_license']['issuance_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="trade_license_issuing_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="trade_license_issuing_authority"
                                                        name="trade_license_issuing_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('trade_license_issuing_authority', $customerDetails['trade_license']['issuing_authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Chamber Certificate Details Section -->
                                @if ($documentName == 'chamber_certificate')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">
                                                <i class="fas fa-building"></i> Chamber Certificate Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="chamber_certificate_number" class="form-label">Chamber
                                                        Certificate
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="chamber_certificate_number" name="chamber_certificate_number"
                                                        placeholder="Enter chamber certificate number"
                                                        value="{{ old('chamber_certificate_number', $customerDetails['chamber_certificate']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="chamber_certificate_expiry" class="form-label">Chamber
                                                        Certificate
                                                        Expiry</label>
                                                    <input type="date" class="form-control"
                                                        id="chamber_certificate_expiry" name="chamber_certificate_expiry"
                                                        value="{{ old('chamber_certificate_expiry', $customerDetails['chamber_certificate']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="chamber_certificate_issuance_date"
                                                        class="form-label">Issuance
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="chamber_certificate_issuance_date"
                                                        name="chamber_certificate_issuance_date"
                                                        placeholder="Enter issuance date"
                                                        value="{{ old('chamber_certificate_issuance_date', $customerDetails['chamber_certificate']['issuance_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="chamber_certificate_issuing_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="chamber_certificate_issuing_authority"
                                                        name="chamber_certificate_issuing_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('chamber_certificate_issuing_authority', $customerDetails['chamber_certificate']['issuing_authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <!-- Commercial Register Details Section -->
                                @if ($documentName == 'commercial_register')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-file-alt"></i> Commercial Register Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="commercial_register_number" class="form-label">Commercial
                                                        Register
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="commercial_register_number" name="commercial_register_number"
                                                        placeholder="Enter commercial register number"
                                                        value="{{ old('commercial_register_number', $customerDetails['commercial_register']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="commercial_register_expiry" class="form-label">Commercial
                                                        Register
                                                        Expiry</label>
                                                    <input type="date" class="form-control"
                                                        id="commercial_register_expiry" name="commercial_register_expiry"
                                                        value="{{ old('commercial_register_expiry', $customerDetails['commercial_register']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="commercial_register_issuance_date"
                                                        class="form-label">Issuance
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="commercial_register_issuance_date"
                                                        name="commercial_register_issuance_date"
                                                        placeholder="Enter issuance date"
                                                        value="{{ old('commercial_register_issuance_date', $customerDetails['commercial_register']['issuance_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="commercial_register_issuing_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="commercial_register_issuing_authority"
                                                        name="commercial_register_issuing_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('commercial_register_issuing_authority', $customerDetails['commercial_register']['issuing_authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Partnership Agreement Details Section -->
                                @if ($documentName == 'partnership_agreement')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">
                                                <i class="fas fa-handshake"></i> Partnership Agreement Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="partnership_agreement_number" class="form-label">Agreement
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="partnership_agreement_number"
                                                        name="partnership_agreement_number"
                                                        placeholder="Enter agreement number"
                                                        value="{{ old('partnership_agreement_number', $customerDetails['partnership_agreement']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="partnership_agreement_date" class="form-label">Agreement
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="partnership_agreement_date" name="partnership_agreement_date"
                                                        value="{{ old('partnership_agreement_date', $customerDetails['partnership_agreement']['date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="partnership_agreement_expiry" class="form-label">Agreement
                                                        Expiry</label>
                                                    <input type="date" class="form-control"
                                                        id="partnership_agreement_expiry"
                                                        name="partnership_agreement_expiry"
                                                        value="{{ old('partnership_agreement_expiry', $customerDetails['partnership_agreement']['expiry'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="partnership_agreement_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="partnership_agreement_authority"
                                                        name="partnership_agreement_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('partnership_agreement_authority', $customerDetails['partnership_agreement']['authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Corporate Tax Registration Certificate Details Section -->
                                @if ($documentName == 'corporate_tax_registration')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-file-alt"></i> Corporate Tax Registration Certificate
                                                Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="corporate_tax_certificate_number"
                                                        class="form-label">Certificate
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="corporate_tax_certificate_number"
                                                        name="corporate_tax_certificate_number"
                                                        placeholder="Enter certificate number"
                                                        value="{{ old('corporate_tax_certificate_number', $customerDetails['corporate_tax_certificate']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="corporate_tax_certificate_date"
                                                        class="form-label">Certificate
                                                        Issuance Date</label>
                                                    <input type="date" class="form-control"
                                                        id="corporate_tax_certificate_date"
                                                        name="corporate_tax_certificate_date"
                                                        value="{{ old('corporate_tax_certificate_date', $customerDetails['corporate_tax_certificate']['date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="corporate_tax_certificate_expiry"
                                                        class="form-label">Certificate
                                                        Expiry Date</label>
                                                    <input type="date" class="form-control"
                                                        id="corporate_tax_certificate_expiry"
                                                        name="corporate_tax_certificate_expiry"
                                                        value="{{ old('corporate_tax_certificate_expiry', $customerDetails['corporate_tax_certificate']['expiry'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="corporate_tax_certificate_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="corporate_tax_certificate_authority"
                                                        name="corporate_tax_certificate_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('corporate_tax_certificate_authority', $customerDetails['corporate_tax_certificate']['authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- VAT Registration Certificate Details Section -->
                                @if ($documentName == 'vat_certificate')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #ffc107; padding-bottom: 5px;">
                                                <i class="fas fa-file-invoice-dollar"></i> VAT Registration Certificate
                                                Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="vat_certificate_number" class="form-label">Certificate
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="vat_certificate_number" name="vat_certificate_number"
                                                        placeholder="Enter VAT certificate number"
                                                        value="{{ old('vat_certificate_number', $customerDetails['vat_certificate']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="vat_certificate_date" class="form-label">Certificate
                                                        Issuance
                                                        Date</label>
                                                    <input type="date" class="form-control" id="vat_certificate_date"
                                                        name="vat_certificate_date"
                                                        value="{{ old('vat_certificate_date', $customerDetails['vat_certificate']['date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="vat_certificate_expiry" class="form-label">Certificate
                                                        Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="vat_certificate_expiry" name="vat_certificate_expiry"
                                                        value="{{ old('vat_certificate_expiry', $customerDetails['vat_certificate']['expiry'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="vat_certificate_authority" class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="vat_certificate_authority" name="vat_certificate_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('vat_certificate_authority', $customerDetails['vat_certificate']['authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Certificate of Incorporation Details Section -->
                                @if ($documentName == 'certificate_of_incorporation')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">
                                                <i class="fas fa-building"></i> Certificate of Incorporation Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="incorporation_certificate_number"
                                                        class="form-label">Certificate
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="incorporation_certificate_number"
                                                        name="incorporation_certificate_number"
                                                        placeholder="Enter incorporation certificate number"
                                                        value="{{ old('incorporation_certificate_number', $customerDetails['incorporation_certificate']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="incorporation_certificate_date"
                                                        class="form-label">Certificate
                                                        Issuance Date</label>
                                                    <input type="date" class="form-control"
                                                        id="incorporation_certificate_date"
                                                        name="incorporation_certificate_date"
                                                        value="{{ old('incorporation_certificate_date', $customerDetails['incorporation_certificate']['date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="incorporation_certificate_expiry"
                                                        class="form-label">Certificate
                                                        Expiry Date</label>
                                                    <input type="date" class="form-control"
                                                        id="incorporation_certificate_expiry"
                                                        name="incorporation_certificate_expiry"
                                                        value="{{ old('incorporation_certificate_expiry', $customerDetails['incorporation_certificate']['expiry'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="incorporation_certificate_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="incorporation_certificate_authority"
                                                        name="incorporation_certificate_authority"
                                                        placeholder="Enter issuing authority"
                                                        value="{{ old('incorporation_certificate_authority', $customerDetails['incorporation_certificate']['authority'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- UAE National ID Details Section -->
                                @if ($documentName == 'uae_national_id')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-id-card"></i> UAE National ID Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="uae_national_id_number" class="form-label">National ID
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="uae_national_id_number" name="uae_national_id_number"
                                                        placeholder="Enter National ID number"
                                                        value="{{ old('uae_national_id_number', $customerDetails['uae_national_id']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="uae_national_id_expiry" class="form-label">National ID
                                                        Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="uae_national_id_expiry" name="uae_national_id_expiry"
                                                        value="{{ old('uae_national_id_expiry', $customerDetails['uae_national_id']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="uae_national_id_issuance" class="form-label">National ID
                                                        Issuance
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="uae_national_id_issuance" name="uae_national_id_issuance"
                                                        placeholder="Enter issuance date"
                                                        value="{{ old('uae_national_id_issuance', $customerDetails['uae_national_id']['issuance'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="uae_national_id_holder_name"
                                                        class="form-label">Cardholder's
                                                        Name</label>
                                                    <input type="text" class="form-control"
                                                        id="uae_national_id_holder_name"
                                                        name="uae_national_id_holder_name"
                                                        placeholder="Enter cardholder's name"
                                                        value="{{ old('uae_national_id_holder_name', $customerDetails['uae_national_id']['holder_name'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Power of Attorney Details Section -->
                                @if ($documentName == 'power_of_attorney')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">
                                                <i class="fas fa-file-signature"></i> Power of Attorney Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="poa_number" class="form-label">Power of Attorney
                                                        Number</label>
                                                    <input type="text" class="form-control" id="poa_number"
                                                        name="poa_number" placeholder="Enter Power of Attorney number"
                                                        value="{{ old('poa_number', $customerDetails['poa']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="poa_expiry" class="form-label">Power of Attorney Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control" id="poa_expiry"
                                                        name="poa_expiry"
                                                        value="{{ old('poa_expiry', $customerDetails['poa']['expiry'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="poa_issuance_date" class="form-label">Power of Attorney
                                                        Issuance
                                                        Date</label>
                                                    <input type="date" class="form-control" id="poa_issuance_date"
                                                        name="poa_issuance_date" placeholder="Enter issuance date"
                                                        value="{{ old('poa_issuance_date', $customerDetails['poa']['issuance_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="poa_holder_name" class="form-label">Attorney's
                                                        Name</label>
                                                    <input type="text" class="form-control" id="poa_holder_name"
                                                        name="poa_holder_name" placeholder="Enter attorney's name"
                                                        value="{{ old('poa_holder_name', $customerDetails['poa']['holder_name'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="poa_purpose" class="form-label">Purpose of Power of
                                                        Attorney</label>
                                                    <textarea class="form-control" id="poa_purpose" name="poa_purpose"
                                                        placeholder="Describe the purpose of the Power of Attorney">{{ old('poa_purpose', $customerDetails['poa']['purpose'] ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Bank Statement Details Section -->
                                @if ($documentName == 'bank_statement')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">
                                                <i class="fas fa-university"></i> Bank Statement Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="bank_name" class="form-label">Bank Name</label>
                                                    <input type="text" class="form-control" id="bank_name"
                                                        name="bank_name" placeholder="Enter bank name"
                                                        value="{{ old('bank_name', $customerDetails['bank_statement']['bank_name'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="account_number" class="form-label">Account
                                                        Number</label>
                                                    <input type="text" class="form-control" id="account_number"
                                                        name="account_number" placeholder="Enter account number"
                                                        value="{{ old('account_number', $customerDetails['bank_statement']['account_number'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="statement_period_start" class="form-label">Statement
                                                        Start
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="statement_period_start" name="statement_period_start"
                                                        value="{{ old('statement_period_start', $customerDetails['bank_statement']['period_start'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="statement_period_end" class="form-label">Statement End
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="statement_period_end" name="statement_period_end"
                                                        value="{{ old('statement_period_end', $customerDetails['bank_statement']['period_end'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="statement_summary" class="form-label">Statement
                                                        Summary</label>
                                                    <textarea class="form-control" id="statement_summary" name="statement_summary"
                                                        placeholder="Provide a summary of the bank statement">{{ old('statement_summary', $customerDetails['bank_statement']['summary'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="available_balance" class="form-label">Available Balance
                                                        (AED)
                                                    </label>
                                                    <input type="text" class="form-control" id="available_balance"
                                                        name="available_balance" placeholder="Enter available balance"
                                                        value="{{ old('available_balance', $customerDetails['bank_statement']['available_balance'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="currency" class="form-label">Currency</label>
                                                    <input type="text" class="form-control" id="currency"
                                                        name="currency" placeholder="Enter currency (e.g., AED, USD)"
                                                        value="{{ old('currency', $customerDetails['bank_statement']['currency'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Lease Agreement Details Section -->
                                @if ($documentName == 'lease_agreement')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #ffc107; padding-bottom: 5px;">
                                                <i class="fas fa-building"></i> Lease Agreement Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="lease_agreement_number" class="form-label">Lease
                                                        Agreement
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="lease_agreement_number" name="lease_agreement_number"
                                                        placeholder="Enter lease agreement number"
                                                        value="{{ old('lease_agreement_number', $customerDetails['lease']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="lease_expiry_date" class="form-label">Lease Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control" id="lease_expiry_date"
                                                        name="lease_expiry_date"
                                                        value="{{ old('lease_expiry_date', $customerDetails['lease']['expiry_date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="lease_start_date" class="form-label">Lease Start
                                                        Date</label>
                                                    <input type="date" class="form-control" id="lease_start_date"
                                                        name="lease_start_date"
                                                        value="{{ old('lease_start_date', $customerDetails['lease']['start_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="property_address" class="form-label">Property
                                                        Address</label>
                                                    <input type="text" class="form-control" id="property_address"
                                                        name="property_address" placeholder="Enter property address"
                                                        value="{{ old('property_address', $customerDetails['lease']['property_address'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="landlord_name" class="form-label">Landlord's
                                                        Name</label>
                                                    <input type="text" class="form-control" id="landlord_name"
                                                        name="landlord_name" placeholder="Enter landlord's name"
                                                        value="{{ old('landlord_name', $customerDetails['lease']['landlord_name'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="monthly_rent" class="form-label">Monthly Rent
                                                        (AED)
                                                    </label>
                                                    <input type="number" class="form-control" id="monthly_rent"
                                                        name="monthly_rent" placeholder="Enter monthly rent amount"
                                                        value="{{ old('monthly_rent', $customerDetails['lease']['monthly_rent'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="lease_purpose" class="form-label">Purpose of
                                                        Lease</label>
                                                    <textarea class="form-control" id="lease_purpose" name="lease_purpose"
                                                        placeholder="Describe the purpose of the lease">{{ old('lease_purpose', $customerDetails['lease']['purpose'] ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <!-- Trademark Certificate Details Section -->
                                @if ($documentName == 'trade_mark_certificate')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">
                                                <i class="fas fa-shield-alt"></i> Trademark Certificate Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="trademark_certificate_number"
                                                        class="form-label">Trademark Certificate
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="trademark_certificate_number"
                                                        name="trademark_certificate_number"
                                                        placeholder="Enter trademark certificate number"
                                                        value="{{ old('trademark_certificate_number', $customerDetails['trademark']['certificate_number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="trademark_expiry_date" class="form-label">Trademark
                                                        Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="trademark_expiry_date" name="trademark_expiry_date"
                                                        value="{{ old('trademark_expiry_date', $customerDetails['trademark']['expiry_date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="trademark_registration_date"
                                                        class="form-label">Trademark Registration
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="trademark_registration_date"
                                                        name="trademark_registration_date"
                                                        value="{{ old('trademark_registration_date', $customerDetails['trademark']['registration_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="trademark_authority_name" class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="trademark_authority_name" name="trademark_authority_name"
                                                        placeholder="Enter issuing authority name"
                                                        value="{{ old('trademark_authority_name', $customerDetails['trademark']['authority_name'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="trademark_description" class="form-label">Trademark
                                                        Description</label>
                                                    <textarea class="form-control" id="trademark_description" name="trademark_description"
                                                        placeholder="Provide a description of the trademark">{{ old('trademark_description', $customerDetails['trademark']['description'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="trademark_classification" class="form-label">Trademark
                                                        Classification</label>
                                                    <input type="text" class="form-control"
                                                        id="trademark_classification" name="trademark_classification"
                                                        placeholder="Enter classification (e.g., goods, services)"
                                                        value="{{ old('trademark_classification', $customerDetails['trademark']['classification'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Memorandum of Association Details Section -->
                                @if ($documentName == 'memorandum_of_association')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-file-contract"></i> Memorandum of Association Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="memorandum_reference_number"
                                                        class="form-label">Reference
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="memorandum_reference_number"
                                                        name="memorandum_reference_number"
                                                        placeholder="Enter memorandum reference number"
                                                        value="{{ old('memorandum_reference_number', $customerDetails['memorandum']['reference_number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="memorandum_issue_date" class="form-label">Issue
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="memorandum_issue_date" name="memorandum_issue_date"
                                                        value="{{ old('memorandum_issue_date', $customerDetails['memorandum']['issue_date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="memorandum_expiry_date" class="form-label">Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="memorandum_expiry_date" name="memorandum_expiry_date"
                                                        value="{{ old('memorandum_expiry_date', $customerDetails['memorandum']['expiry_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="memorandum_authority_name" class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="memorandum_authority_name" name="memorandum_authority_name"
                                                        placeholder="Enter issuing authority name"
                                                        value="{{ old('memorandum_authority_name', $customerDetails['memorandum']['authority_name'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="memorandum_details" class="form-label">Memorandum
                                                        Details</label>
                                                    <textarea class="form-control" id="memorandum_details" name="memorandum_details"
                                                        placeholder="Provide details about the memorandum">{{ old('memorandum_details', $customerDetails['memorandum']['details'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="memorandum_signatories"
                                                        class="form-label">Signatories</label>
                                                    <textarea class="form-control" id="memorandum_signatories" name="memorandum_signatories"
                                                        placeholder="List the signatories of the memorandum">{{ old('memorandum_signatories', $customerDetails['memorandum']['signatories'] ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Shareholder Agreement Details Section -->
                                @if ($documentName == 'shareholder_agreement')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div id="shareholderSection" class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #ffc107; padding-bottom: 5px;">
                                                <i class="fas fa-handshake"></i> Shareholder Agreement Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="shareholder_agreement_number"
                                                        class="form-label">Agreement
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="shareholder_agreement_number"
                                                        name="shareholder_agreement_number"
                                                        placeholder="Enter agreement number"
                                                        value="{{ old('shareholder_agreement_number', $customerDetails['shareholder_agreement']['number'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="shareholder_agreement_issue_date"
                                                        class="form-label">Issue
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="shareholder_agreement_issue_date"
                                                        name="shareholder_agreement_issue_date"
                                                        value="{{ old('shareholder_agreement_issue_date', $customerDetails['shareholder_agreement']['issue_date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="shareholder_agreement_expiry_date"
                                                        class="form-label">Expiry
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="shareholder_agreement_expiry_date"
                                                        name="shareholder_agreement_expiry_date"
                                                        value="{{ old('shareholder_agreement_expiry_date', $customerDetails['shareholder_agreement']['expiry_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="shareholder_agreement_authority"
                                                        class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="shareholder_agreement_authority"
                                                        name="shareholder_agreement_authority"
                                                        placeholder="Enter issuing authority name"
                                                        value="{{ old('shareholder_agreement_authority', $customerDetails['shareholder_agreement']['authority'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="shareholder_agreement_details"
                                                        class="form-label">Agreement
                                                        Details</label>
                                                    <textarea class="form-control" id="shareholder_agreement_details" name="shareholder_agreement_details"
                                                        placeholder="Provide details about the shareholder agreement">{{ old('shareholder_agreement_details', $customerDetails['shareholder_agreement']['details'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="shareholder_agreement_parties"
                                                        class="form-label">Parties
                                                        Involved</label>
                                                    <textarea class="form-control" id="shareholder_agreement_parties" name="shareholder_agreement_parties"
                                                        placeholder="List the parties involved in the shareholder agreement">{{ old('shareholder_agreement_parties', $customerDetails['shareholder_agreement']['parties'] ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Audited Financial Statement Details Section -->
                                @if ($documentName == 'audited_financial_statement')
                                    @foreach ($mediaItems as $index => $mediaItem)
                                        <div class="document-section mb-4">
                                            <legend class="sectionTitle text-dark font-weight-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                                                <i class="fas fa-file-alt"></i> Audited Financial Statement Details
                                            </legend>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="financial_statement_year" class="form-label">Financial
                                                        Year</label>
                                                    <input type="text" class="form-control"
                                                        id="financial_statement_year" name="financial_statement_year"
                                                        placeholder="Enter financial year"
                                                        value="{{ old('financial_statement_year', $customerDetails['financial_statement']['year'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="financial_statement_issuer" class="form-label">Issuing
                                                        Authority</label>
                                                    <input type="text" class="form-control"
                                                        id="financial_statement_issuer"
                                                        name="financial_statement_issuer"
                                                        placeholder="Enter issuing authority name"
                                                        value="{{ old('financial_statement_issuer', $customerDetails['financial_statement']['issuer'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="financial_statement_issue_date" class="form-label">Issue
                                                        Date</label>
                                                    <input type="date" class="form-control"
                                                        id="financial_statement_issue_date"
                                                        name="financial_statement_issue_date"
                                                        value="{{ old('financial_statement_issue_date', $customerDetails['financial_statement']['issue_date'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="financial_statement_expiry_date"
                                                        class="form-label">Expiry Date (if
                                                        applicable)</label>
                                                    <input type="date" class="form-control"
                                                        id="financial_statement_expiry_date"
                                                        name="financial_statement_expiry_date"
                                                        value="{{ old('financial_statement_expiry_date', $customerDetails['financial_statement']['expiry_date'] ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="financial_statement_details"
                                                        class="form-label">Statement
                                                        Summary</label>
                                                    <textarea class="form-control" id="financial_statement_details" name="financial_statement_details"
                                                        placeholder="Provide a summary of the audited financial statement">{{ old('financial_statement_details', $customerDetails['financial_statement']['details'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="auditor_name" class="form-label">Auditor Name</label>
                                                    <input type="text" class="form-control" id="auditor_name"
                                                        name="auditor_name" placeholder="Enter auditor's name"
                                                        value="{{ old('auditor_name', $customerDetails['financial_statement']['auditor'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="auditor_license_number" class="form-label">Auditor
                                                        License
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="auditor_license_number" name="auditor_license_number"
                                                        placeholder="Enter auditor's license number"
                                                        value="{{ old('auditor_license_number', $customerDetails['financial_statement']['license_number'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach

                            <!-- Other Document Details Section -->
                            <fieldset id="otherSection" class="document-section mb-4">
                                <legend class="sectionTitle text-dark font-weight-bold"
                                    style="font-size: 1.25rem; border-bottom: 2px solid #6c757d; padding-bottom: 5px;">
                                    <i class="fas fa-folder"></i> Other Document Details
                                </legend>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="document_name" class="form-label">Document Name</label>
                                        <input type="text" class="form-control" id="document_name"
                                            name="document_name" placeholder="Enter document name"
                                            value="{{ old('document_name', $customerDetails['other_documents']['document_name'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="document_number" class="form-label">Document Number</label>
                                        <input type="text" class="form-control" id="document_number"
                                            name="document_number" placeholder="Enter document number"
                                            value="{{ old('document_number', $customerDetails['other_documents']['document_number'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="issue_date" class="form-label">Issue Date</label>
                                        <input type="date" class="form-control" id="issue_date" name="issue_date"
                                            value="{{ old('issue_date', $customerDetails['other_documents']['issue_date'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expiry_date" class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" id="expiry_date"
                                            name="expiry_date"
                                            value="{{ old('expiry_date', $customerDetails['other_documents']['expiry_date'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="document_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="document_description" name="document_description"
                                            placeholder="Provide a brief description of the document">{{ old('document_description', $customerDetails['other_documents']['description'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </fieldset>


                        </form>
                        <!-- Submit Button -->
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary save-btn-primary">
                                <i class="fas fa-save"></i> Save
                            </button>
                        </div>
                    </div>

                    <!-- Right Panel: Media Carousel -->
                    <div class="right-panel p-4" style="width: 60%; display: flex; flex-direction: column;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>{{ __('Media Preview') }}</h4>
                            <div class="d-flex">
                                <!-- Previous Button -->
                                <button id="previousSection" class="carousel-control-prev w-100" type="button"
                                    data-bs-target="#mediaCarousel" data-bs-slide="prev"
                                    style="height: 35px; font-size: 18px; color: black; background: #fff; padding: 0; margin-right: 10px; position: relative; border:0">
                                    <i style="color:#c4ab54 !important; cursor:pointer;" data-feather="arrow-left"></i>
                                    {{-- <span class="visually-hidden">{{ __('Previous') }}</span> --}}
                                </button>
                                <!-- Next Button -->
                                <button id="nextSection" class="carousel-control-next w-100" type="button"
                                    data-bs-target="#mediaCarousel" data-bs-slide="next"
                                    style="height: 35px; font-size: 18px; color: black; background: #fff; padding: 0; position: relative;border:0">
                                    <i style="color:#c4ab54 !important; cursor:pointer;" data-feather="arrow-right"></i>
                                    {{-- <span class="visually-hidden">{{ __('Next') }}</span> --}}
                                </button>
                            </div>
                        </div>

                        <div id="mediaCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach ($customer->media as $index => $media)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <fieldset class="document-section mb-4 px-3">
                                            <legend class="sectionTitle text-dark fw-bold"
                                                style="font-size: 1.25rem; border-bottom: 2px solid #007bff; padding-bottom: 5px;">
                                                <i class="fas fa-file-alt me-1"></i>
                                                {{ ucwords(str_replace('_', ' ', $media->document_name ?? 'Media Document')) }}
                                            </legend>

                                            <h5 class="mb-3">
                                                {{ ucwords(str_replace('_', ' ', $media->document_name ?? 'Media Document')) }}
                                            </h5>

                                            <div class="row mb-4">
                                                <div class="col-md-12">
                                                    @if (Str::endsWith($media->file_path, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                                        <div class="media-container position-relative w-100"
                                                            style="height: 500px; overflow: hidden;">
                                                            <!-- Zoom Controls -->
                                                            <div class="zoom-controls position-absolute top-0 end-0 m-2"
                                                                style="z-index: 100000;">
                                                                <div
                                                                    class="btn btn-sm btn-light zoom-in-{{ $index }}">
                                                                    +</div>
                                                                <div
                                                                    class="btn btn-sm btn-light zoom-out-{{ $index }}">
                                                                    -</div>
                                                            </div>

                                                            <!-- Image -->
                                                            <img src="{{ asset('storage/' . $media->file_path) }}"
                                                                class="d-block w-100 mediaImage-{{ $index }}"
                                                                style="max-height: 500px; object-fit: contain; transition: transform 0.3s ease; cursor: grab;"
                                                                alt="Media Image">

                                                        </div>
                                                    @elseif (Str::endsWith($media->file_path, ['.pdf']))
                                                        <iframe src="{{ asset('storage/' . $media->file_path) }}"
                                                            width="100%" height="500px"
                                                            style="border: none;"></iframe>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            {{ __('Unsupported media type') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                let zoomLevel = 1;
                                                const zoomInButton = document.querySelector('.zoom-in-{{ $index }}');
                                                const zoomOutButton = document.querySelector('.zoom-out-{{ $index }}');
                                                const mediaImage = document.querySelector('.mediaImage-{{ $index }}');
                                                let isDragging = false;
                                                let startX, startY, currentX = 0,
                                                    currentY = 0;

                                                // Zoom In
                                                zoomInButton.addEventListener('click', function() {
                                                    zoomLevel = Math.min(zoomLevel + 0.1, 3);
                                                    updateZoom();
                                                });

                                                // Zoom Out
                                                zoomOutButton.addEventListener('click', function() {
                                                    zoomLevel = Math.max(zoomLevel - 0.1, 1);
                                                    if (zoomLevel === 1) resetPosition();
                                                    updateZoom();
                                                });

                                                // Update Zoom
                                                function updateZoom() {
                                                    if (mediaImage) {
                                                        mediaImage.style.transform = `scale(${zoomLevel}) translate(${currentX}px, ${currentY}px)`;
                                                        mediaImage.style.transformOrigin = 'center center';
                                                    }
                                                }

                                                function resetPosition() {
                                                    currentX = 0;
                                                    currentY = 0;
                                                }

                                                // Drag Events
                                                mediaImage.addEventListener('mousedown', (e) => {
                                                    if (zoomLevel <= 1) return;
                                                    isDragging = true;
                                                    startX = e.clientX - currentX;
                                                    startY = e.clientY - currentY;
                                                    mediaImage.style.cursor = 'grabbing';
                                                });

                                                document.addEventListener('mousemove', (e) => {
                                                    if (!isDragging) return;
                                                    e.preventDefault();
                                                    currentX = e.clientX - startX;
                                                    currentY = e.clientY - startY;
                                                    updateZoom();
                                                });

                                                document.addEventListener('mouseup', () => {
                                                    if (!isDragging) return;
                                                    isDragging = false;
                                                    mediaImage.style.cursor = 'grab';
                                                });

                                                // Touch Support
                                                mediaImage.addEventListener('touchstart', (e) => {
                                                    if (zoomLevel <= 1) return;
                                                    isDragging = true;
                                                    const touch = e.touches[0];
                                                    startX = touch.clientX - currentX;
                                                    startY = touch.clientY - currentY;
                                                });

                                                mediaImage.addEventListener('touchmove', (e) => {
                                                    if (!isDragging) return;
                                                    const touch = e.touches[0];
                                                    currentX = touch.clientX - startX;
                                                    currentY = touch.clientY - startY;
                                                    updateZoom();
                                                });

                                                mediaImage.addEventListener('touchend', () => {
                                                    isDragging = false;
                                                });
                                            });
                                        </script>

                                    </div>
                                @endforeach
                            </div>
                        </div>



                        </form>
                        <!-- Submit Button -->
                        <div class="text-end mt-4">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="uploadFtaDocumentModal" tabindex="-1" aria-labelledby="uploadFtaDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('customers.upload.fta_document', $customer->id) }}" method="POST"
                enctype="multipart/form-data" id="uploadFtaDocumentForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFtaDocumentModalLabel">{{ __('Upload FTA Document') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="document_name" class="form-label">{{ __('Document Name') }}</label>
                            <input type="text" name="document_name" id="document_name" class="form-control"
                                placeholder="{{ __('Enter Document Name') }}" required>
                            <div class="invalid-feedback" id="document_name_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="fta_document" class="form-label">{{ __('Upload File') }}</label>
                            <input type="file" name="fta_document" id="fta_document" class="form-control"
                                accept=".pdf,.doc,.docx,.jpg,.png" required>
                            <div class="invalid-feedback" id="fta_document_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('File Preview') }}</label>
                            <div id="filePreview" style="border: 1px solid #ddd; padding: 10px; min-height: 100px;">
                                <p>{{ __('No file selected') }}</p>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                            <div class="invalid-feedback" id="start_date_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="expire_date" class="form-label">{{ __('Expiration Date') }}</label>
                            <input type="date" name="expire_date" id="expire_date" class="form-control" required>
                            <div class="invalid-feedback" id="expire_date_error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="send_email" id="send_email" class="form-check-input"
                                    value="1" checked>
                                <label for="send_email"
                                    class="form-check-label">{{ __('Send Email Notification') }}</label>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="send_invoice" id="send_invoice"
                                    class="form-check-input">
                                <label for="send_invoice" class="form-check-label">{{ __('Email Invoice') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                    </div>
                    <div class="modal-loader" style="display: none;">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"
                                role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('fta_document').addEventListener('change', function(event) {
            const filePreview = document.getElementById('filePreview');
            filePreview.innerHTML = '';
            const file = event.target.files[0];
            if (file) {
                filePreview.innerHTML = `<p>Selected file: ${file.name}</p>`;
            } else {
                filePreview.innerHTML = `<p>{{ __('No file selected') }}</p>`;
            }
        });

        document.getElementById('uploadFtaDocumentForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const loader = document.querySelector('.modal-loader');
            loader.style.display = 'block';

            fetch("{{ route('customers.upload.fta_document', $customer) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async function(response) {
                    loader.style.display = 'none';
                    if (!response.ok) {
                        if (response.status === 422) {
                            const data = await response.json();
                            const errors = data.errors || {};
                            let errorMessages = Object.values(errors).flat().join("\n");
                            swal({
                                title: "{{ __('Validation Error') }}",
                                text: errorMessages,
                                icon: "warning",
                            });
                        } else {
                            const data = await response.json();
                            swal({
                                title: "{{ __('Error') }}",
                                text: data.message || 'Something went wrong.',
                                icon: "error",
                            });
                        }
                        throw new Error('Validation or server error');
                    }

                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'uploadFtaDocumentModal'));
                        modal.hide();
                        swal({
                            title: "{{ __('Success') }}",
                            text: data.message,
                            icon: "success",
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        swal({
                            title: "{{ __('Error') }}",
                            text: data.message,
                            icon: "error",
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        });
    </script>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        console.log(document.getElementById('editCreator'), "yyyy");

        $('#editCreator').on('change', function() {
            console.log("click");

            var creatorId = $(this).val();
            var customerId = $(this).attr('data-id');
            $.ajax({
                url: '{{ route('customers.edit.creator') }}',
                method: 'POST',
                data: {
                    created_by: creatorId,
                    id: customerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Viewer Modal
            const fileViewerModal = new bootstrap.Modal(document.getElementById('fileViewerModal'));

            // Attach click event to view buttons
            document.querySelectorAll('.view-file').forEach(function(button) {
                button.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-path');
                    const fileType = this.getAttribute('data-type');
                    const modalContent = document.getElementById('fileViewerContent');

                    // Clear previous content
                    modalContent.innerHTML = '';

                    // Load appropriate viewer
                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileType.toLowerCase())) {
                        modalContent.innerHTML =
                            `<img src="${filePath}" alt="File Preview" class="img-fluid">`;
                    } else if (fileType.toLowerCase() === 'pdf') {
                        modalContent.innerHTML =
                            `<embed src="${filePath}" width="100%" height="600px" type="application/pdf">`;
                    } else {
                        modalContent.innerHTML =
                            `<a href="${filePath}" target="_blank" class="btn btn-primary">{{ __('Download File') }}</a>`;
                    }

                    // Show modal
                    fileViewerModal.show();
                });
            });

            // Ensure the modal is hidden when the close button is clicked
            document.getElementById('fileViewerModal').querySelector('.btn-close').addEventListener('click',
                function() {
                    fileViewerModal.hide();
                });
        });



        document.addEventListener('DOMContentLoaded', function() {
            // Tax ID Modal
            const taxIdModal = new bootstrap.Modal(document.getElementById('addTaxIdModal'));

            // Button to trigger the Tax ID modal
            const addTaxIdButton = document.getElementById('addTaxIdButton');

            if (addTaxIdButton) {
                addTaxIdButton.addEventListener('click', function() {
                    taxIdModal.show();
                });
            }

            // Ensure the modal is hidden when the close button is clicked
            document.getElementById('addTaxIdModal').querySelector('.btn-close').addEventListener('click',
                function() {
                    taxIdModal.hide();
                });


            const uploadFtaDocumentModal = new bootstrap.Modal(document.getElementById('uploadFtaDocumentModal'));

            // Button to trigger the Tax ID modal
            const submitFTADocument = document.getElementById('submitFTADocument');

            if (submitFTADocument) {
                submitFTADocument.addEventListener('click', function() {
                    uploadFtaDocumentModal.show();
                });
            }

            // Ensure the modal is hidden when the close button is clicked
            document.getElementById('submitFTADocument').querySelector('.btn-close').addEventListener('click',
                function() {
                    uploadFtaDocumentModal.hide();
                });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const requestDocumentButton = document.getElementById("requestDocumentButton");
            const documentRequestModal = new bootstrap.Modal(document.getElementById("documentRequestModal"));

            // Open the modal when the 'Request for Document' button is clicked
            requestDocumentButton.addEventListener("click", function() {
                documentRequestModal.show();
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the modal
            const serviceModal = new bootstrap.Modal(document.getElementById('createServiceModal'));

            // Button to trigger the Create Service modal
            const createServiceButton = document.getElementById('submitForReviewButton');

            if (createServiceButton) {
                createServiceButton.addEventListener('click', function() {
                    serviceModal.show();
                });
            }

            // Ensure the modal is hidden when the close button is clicked
            document.getElementById('createServiceModal')
                .querySelector('.btn-close')
                .addEventListener('click', function() {
                    serviceModal.hide();
                });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let zoomLevel = 1;

            const zoomInButton = document.getElementById('zoom-in');
            const zoomOutButton = document.getElementById('zoom-out');

            // Get the image from the currently active carousel item
            function getVisibleImage() {
                return document.querySelector('.carousel-item.active .mediaImage');
            }

            function updateZoom() {
                const mediaImage = getVisibleImage();
                if (mediaImage) {
                    mediaImage.style.transform = `scale(${zoomLevel})`;
                    mediaImage.style.transformOrigin = 'center center';
                }
            }

            zoomInButton.addEventListener('click', function() {
                zoomLevel = Math.min(zoomLevel + 0.1, 3); // Limit max zoom
                updateZoom();
            });

            zoomOutButton.addEventListener('click', function() {
                zoomLevel = Math.max(zoomLevel - 0.1, 1); // Limit min zoom
                updateZoom();
            });
        });
    </script>


    <script>
        document.querySelector('.save-btn-primary').addEventListener('click', function(event) {
            event.preventDefault();

            // Collect form data
            const documentDetails = {
                profile_name_en: document.getElementById('profile_name_en')?.value,
                profile_name_ar: document.getElementById('profile_name_ar')?.value,
                preferred_language: document.getElementById('preferred_language')?.value,
                communication_channel: document.getElementById('communication_channel')?.value,
                emirates_id: {
                    number: document.getElementById('emirates_id_number')?.value,
                    expiry: document.getElementById('emirates_id_expiry')?.value,
                    first_name: document.getElementById('emirates_id_first_name')?.value,
                    last_name: document.getElementById('emirates_id_last_name')?.value,
                    dob: document.getElementById('emirates_id_dob')?.value,
                    nationality: document.getElementById('emirates_id_nationality')?.value,
                },
                passport: {
                    number: document.getElementById('passport_number')?.value,
                    expiry: document.getElementById('passport_expiry')?.value,
                    issuing_country: document.getElementById('passport_issuing_country')?.value,
                    holder_name: document.getElementById('passport_holder_name')?.value,
                },
                tax_certificate: {
                    number: document.getElementById('tax_certificate_number')?.value,
                    expiry: document.getElementById('tax_certificate_expiry')?.value,
                    registration_date: document.getElementById('tax_registration_date')?.value,
                    authority_name: document.getElementById('tax_authority_name')?.value,
                },
                trade_license: {
                    number: document.getElementById('trade_license_number')?.value,
                    expiry: document.getElementById('trade_license_expiry')?.value,
                    issuance_date: document.getElementById('trade_license_issuance_date')?.value,
                    issuing_authority: document.getElementById('trade_license_issuing_authority')?.value,
                },
                chamber_certificate: {
                    number: document.getElementById('chamber_certificate_number')?.value,
                    expiry: document.getElementById('chamber_certificate_expiry')?.value,
                    issuance_date: document.getElementById('chamber_certificate_issuance_date')?.value,
                    issuing_authority: document.getElementById('chamber_certificate_issuing_authority')?.value,
                },
                commercial_register: {
                    number: document.getElementById('commercial_register_number')?.value,
                    expiry: document.getElementById('commercial_register_expiry')?.value,
                    issuance_date: document.getElementById('commercial_register_issuance_date')?.value,
                    issuing_authority: document.getElementById('commercial_register_issuing_authority')?.value,
                },
                partnership_agreement: {
                    number: document.getElementById('partnership_agreement_number')?.value,
                    date: document.getElementById('partnership_agreement_date')?.value,
                    expiry: document.getElementById('partnership_agreement_expiry')?.value,
                    authority: document.getElementById('partnership_agreement_authority')?.value,
                },
                corporate_tax_certificate: {
                    number: document.getElementById('corporate_tax_certificate_number')?.value,
                    date: document.getElementById('corporate_tax_certificate_date')?.value,
                    expiry: document.getElementById('corporate_tax_certificate_expiry')?.value,
                    authority: document.getElementById('corporate_tax_certificate_authority')?.value,
                },
                vat_certificate: {
                    number: document.getElementById('vat_certificate_number')?.value,
                    date: document.getElementById('vat_certificate_date')?.value,
                    expiry: document.getElementById('vat_certificate_expiry')?.value,
                    authority: document.getElementById('vat_certificate_authority')?.value,
                },
                incorporation_certificate: {
                    number: document.getElementById('incorporation_certificate_number')?.value,
                    date: document.getElementById('incorporation_certificate_date')?.value,
                    expiry: document.getElementById('incorporation_certificate_expiry')?.value,
                    authority: document.getElementById('incorporation_certificate_authority')?.value,
                },
                uae_national_id: {
                    number: document.getElementById('uae_national_id_number')?.value,
                    expiry: document.getElementById('uae_national_id_expiry')?.value,
                    issuance: document.getElementById('uae_national_id_issuance')?.value,
                    holder_name: document.getElementById('uae_national_id_holder_name')?.value,
                },
                poa: {
                    number: document.getElementById('poa_number')?.value,
                    expiry: document.getElementById('poa_expiry')?.value,
                    issuance_date: document.getElementById('poa_issuance_date')?.value,
                    holder_name: document.getElementById('poa_holder_name')?.value,
                    purpose: document.getElementById('poa_purpose')?.value,
                },
                lease: {
                    number: document.getElementById('lease_agreement_number')?.value,
                    expiry_date: document.getElementById('lease_expiry_date')?.value,
                    start_date: document.getElementById('lease_start_date')?.value,
                    property_address: document.getElementById('property_address')?.value,
                    landlord_name: document.getElementById('landlord_name')?.value,
                    monthly_rent: document.getElementById('monthly_rent')?.value,
                    purpose: document.getElementById('lease_purpose')?.value,
                },
                trademark: {
                    certificate_number: document.getElementById('trademark_certificate_number')?.value,
                    expiry_date: document.getElementById('trademark_expiry_date')?.value,
                    registration_date: document.getElementById('trademark_registration_date')?.value,
                    authority_name: document.getElementById('trademark_authority_name')?.value,
                    description: document.getElementById('trademark_description')?.value,
                    classification: document.getElementById('trademark_classification')?.value,
                },
                memorandum: {
                    reference_number: document.getElementById('memorandum_reference_number')?.value,
                    issue_date: document.getElementById('memorandum_issue_date')?.value,
                    expiry_date: document.getElementById('memorandum_expiry_date')?.value,
                    authority_name: document.getElementById('memorandum_authority_name')?.value,
                    details: document.getElementById('memorandum_details')?.value,
                    signatories: document.getElementById('memorandum_signatories')?.value,
                },
                shareholder_agreement: {
                    number: document.getElementById('shareholder_agreement_number')?.value,
                    issue_date: document.getElementById('shareholder_agreement_issue_date')?.value,
                    expiry_date: document.getElementById('shareholder_agreement_expiry_date')?.value,
                    authority: document.getElementById('shareholder_agreement_authority')?.value,
                    details: document.getElementById('shareholder_agreement_details')?.value,
                    parties: document.getElementById('shareholder_agreement_parties')?.value,
                },
                financial_statement: {
                    year: document.getElementById('financial_statement_year')?.value,
                    issuer: document.getElementById('financial_statement_issuer')?.value,
                    issue_date: document.getElementById('financial_statement_issue_date')?.value,
                    expiry_date: document.getElementById('financial_statement_expiry_date')?.value,
                    details: document.getElementById('financial_statement_details')?.value,
                    auditor: document.getElementById('auditor_name')?.value,
                    license_number: document.getElementById('auditor_license_number')?.value,
                },
                bank_statement: {
                    bank_name: document.getElementById('bank_name')?.value,
                    account_number: document.getElementById('account_number')?.value,
                    period_start: document.getElementById('statement_period_start')?.value,
                    period_end: document.getElementById('statement_period_end')?.value,
                    summary: document.getElementById('statement_summary')?.value,
                    available_balance: document.getElementById('available_balance')?.value,
                    currency: document.getElementById('currency')?.value,
                },
                other_documents: {
                    document_name: document.getElementById('document_name')?.value,
                    document_number: document.getElementById('document_number')?.value,
                    issue_date: document.getElementById('issue_date')?.value,
                    expiry_date: document.getElementById('expiry_date')?.value,
                    description: document.getElementById('document_description')?.value,
                },
            };


            // Send data to the backend
            fetch('{{ route('save.document.details', $customer->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(documentDetails),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Details saved successfully!');

                        // Close the current modal
                        const currentModal = bootstrap.Modal.getInstance(document.getElementById(
                            'createServiceModal'));
                        if (currentModal) {
                            currentModal.hide();
                        }
                        const customer = @json($customer);

                        // Open the submitForReviewModal
                        const submitForReviewModal = new bootstrap.Modal(document.getElementById(
                            'submitForReviewModal'));
                        submitForReviewModal.show();

                    } else {
                        alert('An error occurred while saving the details.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    <script>
        $(document).ready(function() {
            // Attach click event listener to the close button in the modal
            $('#submitForReviewModal .btn-close').on('click', function() {
                console.log('yes');
                $('#submitForReviewModal').modal('hide');
            });
        });

        $(document).ready(function() {
            const mediaData = @json($customer->media);
            var formIds = [
                'documentSection', 'emirateSection', 'passportSection', 'taxSection',
                'tradeSection', 'chamberSection', 'commercialSection', 'partnershipSection',
                'corporateSection', 'vatSection', 'certificateSection', 'nationalSection',
                'powerSection', 'leaseSection', 'trademarkSection', 'memorandumSection',
                'shareholderSection', 'auditedSection', 'bankSection'
            ];

            // mediaData.forEach((media) => {
            //     // Map media.document_name to formIds
            //     switch (media.document_name) {
            //         case 'passport':
            //             document.getElementById('passportSection').style.display = 'block';
            //             break;
            //         case 'emirates_id':
            //             document.getElementById('emirateSection').style.display = 'block';
            //             break;
            //         case 'trade_license':
            //             document.getElementById('tradeSection').style.display = 'block';
            //             break;
            //         case 'tax_certificate':
            //             document.getElementById('taxSection').style.display = 'block';
            //             break;
            //         case 'chamber_certificate':
            //             document.getElementById('chamberSection').style.display = 'block';
            //             break;
            //         case 'partnership_agreement':
            //             document.getElementById('partnershipSection').style.display = 'block';
            //             break;
            //         case 'corporate_tax_registration':
            //             document.getElementById('corporateSection').style.display = 'block';
            //             break;
            //         case 'vat_certificate':
            //             document.getElementById('vatSection').style.display = 'block';
            //             break;
            //         case 'certificate_of_incorporation':
            //             document.getElementById('certificateSection').style.display = 'block';
            //             break;
            //         case 'uae_national_id':
            //             document.getElementById('nationalSection').style.display = 'block';
            //             break;
            //         default:
            //             console.log(`No matching section for ${media.document_name}`);
            //             break;
            //     }
            // });

        });
    </script>
    <script>
        document.getElementById('fta_document').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('filePreview');
            preview.innerHTML = ''; // Clear previous preview

            if (file) {
                const fileType = file.type;
                if (fileType.startsWith('image/')) {
                    // Display image preview
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '200px';
                    preview.appendChild(img);
                } else if (fileType === 'application/pdf') {
                    // Display PDF preview
                    const iframe = document.createElement('iframe');
                    iframe.src = URL.createObjectURL(file);
                    iframe.style.width = '100%';
                    iframe.style.height = '200px';
                    preview.appendChild(iframe);
                } else {
                    // Unsupported file type
                    preview.innerHTML = `<p>${file.name}</p>`;
                }
            } else {
                preview.innerHTML = '<p>{{ __('No file selected') }}</p>';
            }
        });
    </script>
    {{-- editStatus Script --}}
    <script>
        $('#editStatus').on('change', function() {
            var status = $(this).val();
            var customerId = $(this).attr('data-id');
            $.ajax({
                url: '{{ route('customers.edit.status') }}',
                method: 'POST',
                data: {
                    status: status,
                    customer_id: customerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });
    </script>
@endpush
