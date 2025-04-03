<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Branch;
use DB;
use PDF; // Import PDF Library

class FinancialReportController extends Controller
{
    public function financialReport(Request $request)
    {
        $query = Customer::query()->whereNotNull('transaction_refrence_number');

        // ✅ Apply Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', // Start of the day
                $request->end_date . ' 23:59:59'    // ✅ Include entire end date
            ]);
        }

        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_method', $request->payment_mode);
        }

        // ✅ Fetch Data
        $payments = $query->with('branch')->get();

        // ✅ Totals Calculation
        $totals = [
            'cashier' => number_format((float) (clone $query)->where('payment_method', 'cashier')->sum('price'), 2, '.', ''),
            'stripe' => number_format((float) (clone $query)->where('payment_method', 'stripe')->sum('price'), 2, '.', ''),
            'by_link' => number_format((float) (clone $query)->where('payment_method', 'by_link')->sum('price'), 2, '.', ''),
            'by_machine' => number_format((float) (clone $query)->where('payment_method', 'by_machine')->sum('price'), 2, '.', ''),
            'bank_transfer' => number_format((float) (clone $query)->where('payment_method', 'bank_transfer')->sum('price'), 2, '.', ''),

        ];

        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Operator']);
        })->get();

        $branches = Branch::all();

        return view('admin.financial.financial_report', compact('payments', 'totals', 'users', 'branches'));
    }

    // ✅ Export as PDF
    public function exportPdf(Request $request)
    {
        // ✅ Fetch only required columns
        $query = Customer::query()->whereNotNull('transaction_refrence_number')
            ->select(['customer_code', 'invoice_number', 'branch_id', 'payment_method', 'price', 'created_at'])
            ->with(['branch:id,branch_name']); // Load only necessary branch data

        // ✅ Apply Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', // Start of the day
                $request->end_date . ' 23:59:59'    // ✅ Include entire end date
            ]);
        }
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('payment_mode')) {
            $query->where('payment_method', $request->payment_mode);
        }

        // ✅ Fetch Data Efficiently
        $payments = $query->get();

        // ✅ Generate PDF using Snappy for better performance (Alternative)
        $pdf = PDF::loadView('admin.financial.financial_report_pdf', compact('payments'))->setPaper('a4', 'landscape');

        return $pdf->download('Financial_Report.pdf');
    }


}
