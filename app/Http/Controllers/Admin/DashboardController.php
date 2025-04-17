<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Toastr;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard', ['only' => ['index']]);
    }
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('Admin') || $user->hasRole('super-admin') || $user->hasRole('Super Admin')) {
            $usersCount = User::count();
            $adminCount = User::role('admin')->count();
            $appointmentsCount = Customer::count();
            $revenue = Customer::whereNotNull('transaction_refrence_number')->sum('price');

            $maxValue = max($usersCount, $adminCount, $appointmentsCount, $revenue);

            return response()->json([
                'usersCount' => $usersCount,
                'adminCount' => $adminCount,
                'appointmentsCount' => $appointmentsCount,
                'revenue' => $revenue,
                'usersPercentage' => $maxValue > 0 ? ($usersCount / $maxValue) * 100 : 0,
                'adminPercentage' => $maxValue > 0 ? ($adminCount / $maxValue) * 100 : 0,
                'appointmentsPercentage' => $maxValue > 0 ? ($appointmentsCount / $maxValue) * 100 : 0,
                'revenuePercentage' => $maxValue > 0 ? ($revenue / $maxValue) * 100 : 0,
            ]);
        }
        if (auth()->user()->hasRole('operator')) {
            // Redirect the operator to the customer creation page
            return redirect()->route('customers.create');
        }

        if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('expert')) {
            // Redirect the supervisor or expert to the allBranchesData page
            return redirect()->route('branches.allBranchesData');
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // public function dashboard()
    // {
    //     if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('Super Admin')) {
    //         $usersCount = User::count();
    //         $adminCount = User::role('admin')->count();
    //         $appointmentsCount = Customer::count();
    //         $revenue = Customer::whereNotNull('transaction_refrence_number')->sum('price');

    //         // Calculate dynamic percentages
    //         $maxValue = max($usersCount, $adminCount, $appointmentsCount, $revenue); // Find the max value for scaling

    //         $usersPercentage = $maxValue > 0 ? ($usersCount / $maxValue) * 100 : 0;
    //         $adminPercentage = $maxValue > 0 ? ($adminCount / $maxValue) * 100 : 0;
    //         $appointmentsPercentage = $maxValue > 0 ? ($appointmentsCount / $maxValue) * 100 : 0;
    //         $revenuePercentage = $maxValue > 0 ? ($revenue / $maxValue) * 100 : 0;

    //         return view('admin.dashboard', compact(
    //             'usersCount',
    //             'adminCount',
    //             'appointmentsCount',
    //             'revenue',
    //             'usersPercentage',
    //             'adminPercentage',
    //             'appointmentsPercentage',
    //             'revenuePercentage'
    //         ));
    //     }

    //     if (auth()->user()->hasRole('operator')) {
    //         // Redirect the operator to the customer creation page
    //         return redirect()->route('customers.create');
    //     }

    //     if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('expert')) {
    //         // Redirect the supervisor or expert to the allBranchesData page
    //         return redirect()->route('branches.allBranchesData');
    //     }

    //     // Default case: Redirect to home/dashboard with a message if no valid role is matched
    //     Toastr::error(__('You do not have access to this dashboard.'));
    //     return redirect()->route('home');
    // }


}
