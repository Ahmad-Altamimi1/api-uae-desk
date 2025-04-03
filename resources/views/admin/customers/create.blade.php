@extends('admin.layouts.master')

@section('page_title')
    {{ __('Add Customer') }}
@endsection
@push('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 0.75rem 1.25rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .btn-primary {
            border-radius: 20px;
            font-size: 1.1rem;
        }

        .btn-outline-danger {
            border-radius: 20px;
            font-size: 1.1rem;
        }

        .remove-entry:hover {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
            font-size: 1rem;
        }

        .invalid-feedback {
            font-size: 0.875rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Adjust the remove button for proper positioning in the header */
        .remove-entry {
            font-size: 0.9rem;
            /* color: #dc3545; */
        }

        .remove-entry:hover {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
    <style>
        #output {
            width: 100%;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endpush
@section('content')
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        <div class="page-header">
            <div class="card breadcrumb-card">
                <div class="row justify-content-between align-content-between" style="height: 100%;">
                    <div class="col-md-6">
                        <h3 class="page-title">{{ __('Customers') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('Customers') }}</a>
                            </li>
                            <li class="breadcrumb-item active-breadcrumb"><a
                                    href="{{ route('customers.create') }}">{{ __('Add Customer') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="crud-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"> Customer Information </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="service_id" class="required">{{ __('Service') }}</label>
                                    <select name="service_id[]" id="service_id" multiple
                                        class="form-control @error('service_id') is-invalid @enderror" required
                                        onchange="updateServicePriceFields()">
                                        <option value="">{{ __('Select Service') }}</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                {{ collect(old('service_id'))->contains($service->id) ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <!-- Dynamic Service Prices Section -->
                                    <div id="service-prices" class="row mt-2">
                                        @if (old('service_id'))
                                            @foreach (old('service_id') as $serviceId)
                                                @php $serviceObj = $services->where('id', $serviceId)->first(); @endphp
                                                @if ($serviceObj)
                                                    <div class="form-group col-6">
                                                        <label
                                                            for="service_price_{{ $serviceId }}">{{ $serviceObj->name }}
                                                            Price (AED)</label>
                                                        <input type="number" name="service_price[{{ $serviceId }}]"
                                                            id="service_price_{{ $serviceId }}"
                                                            class="form-control service-price" min="0" required
                                                            data-service-id="{{ $serviceId }}"
                                                            value="{{ old('service_price.' . $serviceId, 0) }}"
                                                            oninput="calculateTotalPrice()">
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    @error('service_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="branch_id" class="required">{{ __('Branch') }}</label>
                                    <select name="branch_id" id="branch_id"
                                        class="form-control @error('branch_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="first_name" class="required">{{ __('First Name') }}</label>
                                    <input type="text" name="first_name" id="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="last_name" class="required">{{ __('Last Name') }}</label>
                                    <input type="text" name="last_name" id="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="business_name" class="required">{{ __('Business Name') }}</label>
                                    <input type="text" name="business_name" id="business_name"
                                        class="form-control @error('business_name') is-invalid @enderror"
                                        value="{{ old('business_name') }}">
                                    @error('business_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="email" class="required">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="phone_number" class="required">{{ __('Phone Number') }}</label>
                                    <input type="text" name="phone_number" id="phone_number"
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number') }}" required
                                        pattern="^((050|051|052|054|055|056|057|058|059)\d{7})$"
                                        title="{{ __('Enter a valid UAE mobile number starting with valid prefixes like 050, 052, etc., followed by 7 digits.') }}">
                                    @error('phone_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="second_number">{{ __('Second Number') }}</label>
                                    <input type="text" name="second_number" id="second_number"
                                        class="form-control @error('second_number') is-invalid @enderror"
                                        value="{{ old('second_number') }}"
                                        pattern="^((050|051|052|054|055|056|057|058|059)\d{7})$"
                                        title="{{ __('Enter a valid UAE mobile number starting with valid prefixes like 050, 052, etc., followed by 7 digits.') }}">
                                    @error('second_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="required">{{ __('Address') }}</label>
                                <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                    required>{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="payment_method" class="required">{{ __('Payment Method') }}</label>
                                    <select name="payment_method" id="payment_method"
                                        class="form-control @error('payment_method') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Payment Method') }}</option>
                                        <option value="cashier"
                                            {{ old('payment_method') == 'cashier' ? 'selected' : '' }}>
                                            {{ __('Cashier') }}
                                        </option>
                                        {{-- <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>
                                            {{ __('Stripe') }}
                                        </option> --}}
                                        <option value="by_link"
                                            {{ old('payment_method') == 'by_link' ? 'selected' : '' }}>
                                            {{ __('By Link') }}
                                        </option>
                                        <option value="by_machine"
                                            {{ old('payment_method') == 'by_machine' ? 'selected' : '' }}>
                                            {{ __('Machine') }}
                                        </option>
                                        <option value="bank_transfer"
                                            {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                                            {{ __('Bank Transfer') }}
                                        </option>
                                    </select>

                                    @error('payment_method')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group col-6">
                                    <label for="price" class="required">{{ __('Total Price') }}</label>
                                    <input type="number" name="price" id="price" min="0"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price') }}" readonly required>
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="vat">{{ __('VAT') }}</label>
                                    <select name="vat_value" id="vat"
                                        class="form-control @error('vat') is-invalid @enderror" required>
                                        <option value="{{ $settings->vat_value }}"
                                            {{ old('vat') == $settings->vat_value ? 'selected' : '' }}>
                                            {{ __('VAT ' . $settings->vat_value . '%') }}
                                        </option>
                                        <option value="0.0" {{ old('vat') == '0.0' ? 'selected' : '' }}>
                                            {{ __('VAT 0%') }}</option>

                                    </select>
                                    @error('vat')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="transaction_refrence_number">{{ __('Refrence / Transaction #') }}</label>
                                    <input type="text" name="transaction_refrence_number"
                                        class="form-control @error('transaction_refrence_number') is-invalid @enderror"
                                        value="{{ old('transaction_refrence_number') }}">
                                    @error('transaction_refrence_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="tax_id">{{ __('TRN #') }}</label>
                                    <input type="text" name="tax_id"
                                        class="form-control @error('tax_id') is-invalid @enderror"
                                        value="{{ old('tax_id') }}">
                                    @error('tax_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="fta_user_name">{{ __('FTA Email / Username') }}</label>
                                    <input type="text" name="fta_user_name"
                                        class="form-control @error('fta_user_name') is-invalid @enderror"
                                        value="{{ old('fta_user_name') }}">
                                    @error('fta_user_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group col-6">
                                    <label for="fta_password">{{ __('FTA Password') }}</label>
                                    <input type="text" name="fta_password"
                                        class="form-control @error('fta_password') is-invalid @enderror"
                                        value="{{ old('fta_password') }}">
                                    @error('fta_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="fta_refrence">{{ __('FTA Refrence Number') }}</label>
                                    <input type="text" name="fta_refrence"
                                        class="form-control @error('fta_refrence') is-invalid @enderror"
                                        value="{{ old('fta_refrence') }}">
                                    @error('fta_refrence')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="gmail_user_name">{{ __('FTA Gmail Account') }}</label>
                                    <input type="text" name="gmail_user_name"
                                        class="form-control @error('gmail_user_name') is-invalid @enderror"
                                        value="{{ old('gmail_user_name') }}">
                                    @error('gmail_user_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row">


                                <div class="form-group col-6">
                                    <label for="gmail_password">{{ __('FTA Gmail Password') }}</label>
                                    <input type="text" name="gmail_password"
                                        class="form-control @error('gmail_password') is-invalid @enderror"
                                        value="{{ old('gmail_password') }}">
                                    @error('gmail_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if (Gate::check('customers-status'))
                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                            {{ __('Pending') }}</option>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>
                                            {{ __('In Progress') }}</option>
                                        <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>
                                            {{ __('Verified') }}</option>
                                        <option value="3" {{ old('status') == '3' ? 'selected' : '' }}>
                                            {{ __('Completed') }}</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-primary" id="add-entry">
                                    <i class="bi bi-plus-circle"></i> Add Upcoming Payment
                                </button>
                            </div>

                            <div id="entries-container">
                                {{-- @foreach (old('entries', []) as $index => $entry)
                                    <div class="card mb-4 shadow-sm entry-group pb-4 ">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"> Upcoming Payment #{{ $index + 1 }}</h5>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger remove-entry mb-4">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <!-- Date Field -->
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="entries[{{ $index }}][date]"
                                                        class="form-label required">{{ __('Date') }}</label>
                                                    <input type="date" name="entries[{{ $index }}][date]"
                                                        id="date_{{ $index }}"
                                                        class="form-control @error('entries.' . $index . '.date') is-invalid @enderror"
                                                        value="{{ old('entries.' . $index . '.date') }}" readonly
                                                        required>
                                                    @error('entries.' . $index . '.date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Amount Field -->
                                                <div class="col-md-6">
                                                    <label for="entries[{{ $index }}][amount]"
                                                        class="form-label required">{{ __('Amount') }}</label>
                                                    <input type="number" name="entries[{{ $index }}][amount]"
                                                        id="amount_{{ $index }}"
                                                        class="form-control @error('entries.' . $index . '.amount') is-invalid @enderror"
                                                        value="{{ old('entries.' . $index . '.amount') }}" readonly
                                                        required>
                                                    @error('entries.' . $index . '.amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Description Field -->
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label for="entries[{{ $index }}][description]"
                                                        class="form-label">{{ __('Description') }}</label>
                                                    <textarea name="entries[{{ $index }}][description]" id="description_{{ $index }}"
                                                        class="form-control" readonly>{{ old('entries.' . $index . '.description') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach --}}
                            </div>






                            <div class="card-footer create-btn text-right">
                                <button type="submit" class="btn btn-lg custom-create-btn">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            updateServicePriceFields(); // Ensure fields are reloaded on page refresh
        });

        function updateServicePriceFields() {
            let selectedServices = document.getElementById('service_id').selectedOptions;
            let servicePricesDiv = document.getElementById('service-prices');

            // Store existing service price values
            let existingPrices = {};
            document.querySelectorAll('.service-price').forEach(input => {
                existingPrices[input.dataset.serviceId] = input.value;
            });

            // Loop through selected services and add or retain price inputs
            Array.from(selectedServices).forEach(service => {
                let serviceId = service.value;
                let serviceName = service.text;

                // If price field does not already exist, add it
                if (!document.getElementById(`service_price_${serviceId}`)) {
                    let priceValue = existingPrices[serviceId] ?? (window.oldServicePrices?.[serviceId] ?? '');
                    servicePricesDiv.innerHTML += `
                    <div class="form-group col-6">
                        <label for="service_price_${serviceId}">${serviceName} Price (AED)</label>
                        <input type="number" name="service_price[${serviceId}]" id="service_price_${serviceId}" 
                               class="form-control service-price"  required 
                               data-service-id="${serviceId}"
                               value="${priceValue}"
                               oninput="calculateTotalPrice()">
                    </div>
                `;
                }
            });

            // Recalculate total when services are updated
            calculateTotalPrice();
        }

        function calculateTotalPrice() {
            let total = 0;

            // Loop through all price input fields and sum values
            document.querySelectorAll('.service-price').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            // Update the total price field
            document.getElementById('price').value = total.toFixed(2);
        }


        document.addEventListener('DOMContentLoaded', function() {
            let entryIndex = document.querySelectorAll('.entry-group').length;

            document.getElementById('add-entry').addEventListener('click', function() {
                const container = document.getElementById('entries-container');
                const newEntry = `
    <div class="card mb-4 shadow-sm entry-group pb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"></h5>
            <button type="button" class="btn btn-sm btn-outline-danger remove-entry mb-4">
                <i class="bi bi-trash remove-entry"></i> 
            </button>
        </div>
        <div class="card-body">
            <!-- Date Field -->
            <div class="row g-3">
               <div class="col-md-6">
                    <label for="entries[${entryIndex}][date]" class="form-label required">{{ __('Date') }}</label>
                    <input type="date" name="entries[${entryIndex}][date]" id="date_${entryIndex}"
                        class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
                <!-- Amount Field -->
                <div class="col-md-6">
                    <label for="entries[${entryIndex}][amount]" class="form-label required">{{ __('Amount') }}</label>
                    <input type="number" name="entries[${entryIndex}][amount]" id="amount_${entryIndex}"
                        class="form-control" required>
                </div>
            </div>
            <!-- Description Field -->
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="entries[${entryIndex}][description]" class="form-label">{{ __('Description') }}</label>
                    <textarea name="entries[${entryIndex}][description]" id="description_${entryIndex}"
                        class="form-control"></textarea>
                </div>
            </div>
        </div>
    </div>`;

                container.insertAdjacentHTML('beforeend', newEntry);
                entryIndex++;
            });

            document.getElementById('entries-container').addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-entry')) {
                    event.target.closest('.entry-group').remove();
                }
            });
        });
    </script>
@endsection
