@extends('admin.layouts.master')

@section('page_title')
    {{ __('Financial Report') }}
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">{{ __('Financial Report') }}</h2>
    
    <!-- Filter Form -->
    <div id="pdfLoader" class="text-center mt-3" style="display: none;">
        <span class="spinner-border text-success" role="status"></span>
        <p class="text-danger mt-2">{{ __('Generating PDF... Please wait') }}</p>
    </div>
    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.financial_report') }}" class="mb-4 bg-light p-3 rounded shadow-sm">
        <div class="row">
            <!-- ✅ Start Date Filter -->
            <div class="col-md-2">
                <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
        
            <!-- ✅ End Date Filter -->
            <div class="col-md-2">
                <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <!-- ✅ Branch Filter -->
            <div class="col-md-2">
                <label for="branch_id" class="form-label">{{ __('Filter by Branch') }}</label>
                <select id="branch_id" name="branch_id" class="form-control">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <!-- ✅ User Filter -->
            <div class="col-md-2">
                <label for="user_id" class="form-label">{{ __('Filter by User') }}</label>
                <select id="user_id" name="user_id" class="form-control">
                    <option value="">{{ __('All Users') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ✅ Payment Mode Filter -->
            <div class="col-md-2">
                <label for="payment_mode" class="form-label">{{ __('Payment Mode') }}</label>
                <select id="payment_mode" name="payment_mode" class="form-control">
                    <option value="">{{ __('All Payment Modes') }}</option>
                    <option value="cashier" {{ request('payment_mode') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="stripe" {{ request('payment_mode') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                    <option value="by_link" {{ request('payment_mode') == 'by_link' ? 'selected' : '' }}>By Link</option>
                    <option value="by_machine" {{ request('payment_mode') == 'by_machine' ? 'selected' : '' }}>By Machine</option>
                </select>
            </div>

            <!-- ✅ Apply Filters Button -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">{{ __('Apply Filters') }}</button>
            </div>
        </div>
    </form>
    

    <!-- Financial Report Table -->
    <div class="table-responsive shadow-sm rounded bg-white mt-2">
        <!-- ✅ Totals Display -->
        <div class="d-flex justify-content-end align-items-center p-3">
            <strong>{{ __('Total: ') }}</strong>
            <span class="ms-3 ml-2"><strong> AED {{ $totals['cashier'] + $totals['stripe'] + $totals['by_link'] + $totals['by_machine'] + $totals['bank_transfer']  }}</strong></span>
            <button type="button" id="downloadPdfBtn" class="btn btn-primary w-15 float-right ml-4">
                <i class="fe fe-download"></i> {{ __('PDF') }}
            </button>
        </div>

        <table class="table table-hover table-striped align-middle">
            <thead class="bg-primary text-white">
                <tr>
                    <th>{{ __('Application ID') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Cashier') }}</th>
                    <th>{{ __('Stripe') }}</th>
                    <th>{{ __('By Link') }}</th>
                    <th>{{ __('By Machine') }}</th>
                    <th>{{ __('Bank Transfer') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->customer_code }}</td>
                        <td>{{ $payment->branch ? $payment->branch->branch_name : '-' }}</td>
                        <td>{{ $payment->payment_method === 'cashier' ? $payment->price : '-' }}</td>
                        <td>{{ $payment->payment_method === 'stripe' ? $payment->price : '-' }}</td>
                        <td>{{ $payment->payment_method === 'by_link' ? $payment->price : '-' }}</td>
                        <td>{{ $payment->payment_method === 'by_machine' ? $payment->price : '-' }}</td>
                        <td>{{ $payment->payment_method === 'bank_transfer' ? $payment->price : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <th colspan="2" class="text-end">{{ __('Totals') }}</th>
                    <th>AED {{ $totals['cashier'] }}</th>
                    <th>AED {{ $totals['stripe'] }}</th>
                    <th>AED {{ $totals['by_link'] }}</th>
                    <th>AED {{ $totals['by_machine'] }}</th>
                    <th>AED {{ $totals['bank_transfer'] }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#downloadPdfBtn").on("click", function() {
            // ✅ Show Loader
            $("#pdfLoader").show();

            // ✅ Delay navigation to ensure loader is visible
            setTimeout(function() {
                window.location.href = "{{ route('admin.financial_report.pdf', request()->all()) }}";
                $("#pdfLoader").hide();

            }, 500);
        });
    });
</script>


