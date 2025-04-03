@extends('admin.layouts.master')

@section('page_title')
    {{ __('Customer Account ') }}
@endsection

@section('content')
    <div class="page-header">
        <div class="card breadcrumb-card">
            <div class="row justify-content-between align-content-between" style="height: 100%;">
                <div class="col-md-6">
                    <h3 class="page-title">({{ $customer->first_name }} {{ $customer->last_name }}) {{ __('Account ') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active-breadcrumb">{{ $customer->first_name }} {{ $customer->last_name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>{{ __('Purchase Invoices') }}</h5>
            <table class="table table-hover">
                <thead>
                    <tr>

                        <th>{{ __('Service') }}</th>
                        <th>{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $serviceItem)
                        <tr>
                            <td> {{ $loop->iteration }}. {{ $serviceItem['name'] }}</td>
                            <td>AED {{ number_format($serviceItem['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
