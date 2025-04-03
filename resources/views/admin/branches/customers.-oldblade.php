@extends('admin.layouts.master')

@section('page_title')
    {{ __('Branch-wise Customer Data') }}
@endsection
<style>
    .nav-tabs .nav-link.active {
        background-color: #c4ab54 !important;
        color: #fff !important;
        border: 1px solid #c4ab54;
        border-radius: 4px;
    }
</style>
@section('content')
    <div class="page-header">
        <div class="card breadcrumb-card">
            <div class="row justify-content-between align-content-between" style="height: 100%;">
                <div class="col-md-6">
                    <h3 class="page-title">{{ __('Branch-wise Customer Data') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active-breadcrumb">
                            {{ __('Customers by Branch') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <!-- Left-aligned text -->
                    <h5 class="mb-0">{{ __('Branch-wise Customers') }}</h5>
                
                    <!-- Right-aligned dropdown -->
                    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                        <div class="form-group mb-0 col-3">
                            <select id="locationDropdown" class="form-control">
                                <option value="">{{ __('Select Location') }}</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ isset($currentLocation) && $currentLocation == $location->name ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                  
                    @if(count($branchData) > 0)
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="branchTabs" role="tablist">
                            @foreach ($branchData as $branch)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        data-target="branch-{{ $branch->id }}" type="button" role="tab">
                                        {{ $branch->branch_name }}
                                        <span class="badge bg-warning">{{ $branch->pendingCount }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="branchTabsContent">
                            @foreach ($branchData as $branch)
                                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="branch-{{ $branch->id }}"
                                    role="tabpanel">
                                    <h5 class="mt-3">{{ __('Customers for ') . $branch->branch_name }}</h5>

                                    @if (isset($branch->customers) && count($branch->customers) > 0)
                                        <table class="table table-bordered table-hover text-center">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Customer Code') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Phone Number') }}</th>
                                                    <th>{{ __('Email') }}</th>
                                                    <th>{{ __('Service') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Created By') }}</th>
                                                    @if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('Admin'))
                                                        <th>{{ __('Reviewed By') }}</th>
                                                    @endif
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($branch->customers as $customer)
                                                    <tr>
                                                        <td>{{ $customer->customer_code }}</td>
                                                        <td>{{ $customer->first_name . ' ' . $customer->last_name }}</td>
                                                        <td>{{ $customer->phone_number }}</td>
                                                        <td>{{ $customer->email }}</td>
                                                        <td>{{ $customer->service->name ?? __('N/A') }}</td>
                                                        <td>
                                                            @php
                                                                $statusClasses = [
                                                                    0 => 'badge-warning',
                                                                    1 => 'badge-info',
                                                                    2 => 'badge-primary',
                                                                    3 => 'badge-success',
                                                                ];
                                                                $statusTexts = [
                                                                    0 => 'Pending',
                                                                    1 => 'In Process',
                                                                    2 => 'Verified',
                                                                    3 => 'Completed',
                                                                ];
                                                                $statusClass =
                                                                    $statusClasses[$customer->status] ?? 'badge-secondary';
                                                                $statusText = $statusTexts[$customer->status] ?? 'Unknown';
                                                            @endphp
                                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary view-info"
                                                                data-created-by="{{ $customer->creator->name ?? __('N/A') }}"
                                                                data-email="{{ $customer->creator->email ?? __('N/A') }}"
                                                                data-phone="{{ $customer->creator->mobile ?? __('N/A') }}">
                                                                {{ __('View Information') }}
                                                            </button>
                                                        </td>
                                                        @if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('Admin'))
                                                            <td>
                                                                <button type="button" class="btn btn-primary view-info"
                                                                    data-created-by="{{ $customer->review->name ?? __('N/A') }}"
                                                                    data-email="{{ $customer->review->email ?? __('N/A') }}"
                                                                    data-phone="{{ $customer->review->mobile ?? __('N/A') }}">
                                                                    {{ __('View Information') }}
                                                                </button>
                                                            </td>
                                                        @endif

                                                        <td>
                                                            <a href="{{ route('customers.show', $customer->id) }}"
                                                                class="btn btn-info btn-sm">
                                                                <i class="fe fe-eye"></i> {{ __('View') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <p class="mb-0">
                                                {{ __('Showing') }} {{ $branch->customers->firstItem() ?? 0 }}
                                                {{ __('to') }} {{ $branch->customers->lastItem() ?? 0 }}
                                                {{ __('of') }} {{ $branch->customers->total() }} {{ __('results') }}
                                            </p>
                                            <div>
                                                {{ $branch->customers->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-center mt-3">{{ __('No customers for this branch.') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else 
                        <div class="alert alert-info text-center">
                            {{ __('No data found.') }}
                        </div>
                        <div class="text-center text-bold">{{ __('No record found') }}</div>                   
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="viewInfoModal" tabindex="-1" aria-labelledby="viewInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewInfoModalLabel">{{ __('Created By Information') }}</h5>
                    <i class="btn-close" style="color:#c4ab54 !important; pointer:cursor" data-feather="x-circle"></i>
                </div>
                <div class="modal-body">
                    <p><strong>{{ __('Name:') }}</strong> <span id="createdByName">{{ __('N/A') }}</span></p>
                    <p><strong>{{ __('Email:') }}</strong> <span id="createdByEmail">{{ __('N/A') }}</span></p>
                    <p><strong>{{ __('Phone:') }}</strong> <span id="createdByPhone">{{ __('N/A') }}</span></p>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modal
            const viewInfoModal = new bootstrap.Modal(document.getElementById('viewInfoModal'));

            // Attach event listener to all "View Information" buttons
            document.querySelectorAll('.view-info').forEach(button => {
                button.addEventListener('click', function() {
                    // Populate modal content with data attributes
                    document.getElementById('createdByName').textContent = this.getAttribute(
                        'data-created-by');
                    document.getElementById('createdByEmail').textContent = this.getAttribute(
                        'data-email');
                    document.getElementById('createdByPhone').textContent = this.getAttribute(
                        'data-phone');
                    // Show modal
                    viewInfoModal.show();
                });
            });

            document.getElementById('viewInfoModal').querySelector('.btn-close').addEventListener('click',
                function() {
                    viewInfoModal.hide();
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#branchTabs button');
            const tabPanes = document.querySelectorAll('.tab-pane');

            // Load active tab from localStorage
            const activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                tabs.forEach((tab) => tab.classList.remove('active'));
                tabPanes.forEach((pane) => pane.classList.remove('active', 'show'));

                const selectedTab = document.querySelector(`[data-target="${activeTab}"]`);
                const selectedPane = document.getElementById(activeTab);

                if (selectedTab && selectedPane) {
                    selectedTab.classList.add('active');
                    selectedPane.classList.add('active', 'show');
                }
            }

            // Add click event listener to each tab
            tabs.forEach((tab) => {
                tab.addEventListener('click', function() {
                    tabs.forEach((tab) => tab.classList.remove('active'));
                    tabPanes.forEach((pane) => pane.classList.remove('active', 'show'));

                    const target = this.getAttribute('data-target');
                    this.classList.add('active');
                    document.getElementById(target).classList.add('active', 'show');

                    // Save active tab to localStorage
                    localStorage.setItem('activeTab', target);
                });
            });
        });
        $(document).ready(function() {
            $('#locationDropdown').on('change', function() {
                const selectedLocationName = $(this).find('option:selected').text().trim(); // Get the selected location name

                if (selectedLocationName) {
                    // Add location parameter (name) to the URL and reload the page
                    const url = new URL(window.location.href);
                    url.searchParams.set('location', selectedLocationName);
                    window.location.href = url.toString();
                } else {
                    // Remove location parameter if no location is selected
                    const url = new URL(window.location.href);
                    url.searchParams.delete('location');
                    window.location.href = url.toString();
                }
            });
        });

    </script>
@endpush
