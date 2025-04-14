<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Toastr;
use Illuminate\Support\Facades\Gate;
use DataTables;
use App\Models\Customer;
use App\Models\Location;
use App\Models\DocumentRequest;
use App\Models\User;
use Auth;

class BranchController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:branches-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:branches-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:branches-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:branches-delete', ['only' => ['destroy']]);
        $this->middleware('permission:can-see-all-branches-data', ['only' => ['allBranchesData']]);
    }
    public function index(Request $request)
    {
            $data = Branch::with('location')->get();

        
        return response()->json($data);
    }

    public function create()
    {
        $locations = Location::all();
        return view('admin.branches.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'branch_name' => 'required|string',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email|unique:branches,email',
            'location_id' => 'required|exists:locations,id',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $messages = [
            'branch_name.required' => __('Branch name is required.'),
            'address.required' => __('Address is required.'),
            'phone_number.required' => __('Phone number is required.'),
            'email.required' => __('Email is required.'),
            'email.unique' => __('Email must be unique.'),
            'latitude.required' => __('latitude is required.'),
            'longitude.required' => __('longitude is required.'),
        ];

        $this->validate($request, $rules, $messages);

        $input = $request->all();

        try {
            Branch::create($input);
            Toastr::success(__('Branch created successfully.'));
            return redirect()->route('branches.index');
        } catch (\Exception $e) {
            Toastr::error(__('Error creating branch.'));
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id); // Retrieve the branch by ID
        $locations = Location::all();

        return view('admin.branches.edit', compact('branch', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id); // Retrieve the branch by ID

        $rules = [
            'branch_name' => 'required|string',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email|unique:branches,email,' . $branch->id,
            'location_id' => 'required|exists:locations,id',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $messages = [
            'branch_name.required' => __('Branch name is required.'),
            'address.required' => __('Address is required.'),
            'phone_number.required' => __('Phone number is required.'),
            'email.required' => __('Email is required.'),
            'email.unique' => __('Email must be unique.'),
            'latitude.required' => __('latitude is required.'),
            'longitude.required' => __('longitude is required.'),
        ];
        $this->validate($request, $rules, $messages);

        try {
            $branch->update($request->all());
            Toastr::success(__('Branch updated successfully.'));
            return redirect()->route('branches.index');
        } catch (\Exception $e) {
            Toastr::error(__('Error updating branch.'));
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        $branch = Branch::findOrFail($request->id); // Retrieve the branch by ID

        try {
            $branch->delete();
            Toastr::success(__('Branch deleted successfully.'));
            return redirect()->route('branches.index');
        } catch (\Exception $e) {
            Toastr::error(__('Error deleting branch.'));
            return redirect()->back();
        }
    }

    // public function allBranchesData(Request $request)
    // {
    //     $locations = Location::all();

    //     if (auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('expert')) {

    //         $branches = Branch::where('location_id', Auth::user()->location_id)->get(); // Fetch all branches without eager loading
    //         $branchData = $branches->map(function ($branch) {
    //             if (auth()->user()->hasRole('expert')) {
    //                 $branch->customers = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                     ->where('status', 1)
    //                     ->orderBy('created_at', 'desc')
    //                     ->paginate(10);
    //             } else if (auth()->user()->hasRole('supervisor')) {
    //                 $branch->customers = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                     ->where(function ($query) {
    //                         $query->where('status', 2)->orWhere('status', 3);
    //                     })
    //                     ->orderBy('created_at', 'desc')
    //                     ->paginate(10);
    //             } else {
    //                 $branch->customers = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                     ->orderBy('created_at', 'desc')
    //                     ->paginate(10);
    //             }

    //             // Count pending customers for the branch
    //             if (auth()->user()->hasRole('expert')) {
    //                 $branch->pendingCount = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                     ->where('status', 1)
    //                     ->count();
    //             } else if (auth()->user()->hasRole('supervisor')) {
    //                 $branch->pendingCount = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                     ->where('status', 2)
    //                     ->count();
    //             } else {
    //                 $branch->pendingCount = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                     ->count();
    //             }
    //             return $branch;
    //         });
    //     }
    //     $currentLocation = $request['location'] ? $request['location'] : 'Dubai';

    //     if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
    //         // Fetch branches in Dubai
    //         $branches = Branch::whereHas('location', function ($query) use($currentLocation) {
    //             $query->where('name', $currentLocation);
    //         })->get();

    //         // Process branches and customers
    //         $branchData = $branches->map(function ($branch) {

    //             $branch->customers = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                 ->orderBy('created_at', 'desc')
    //                 ->paginate(10);



    //             $branch->pendingCount = Customer::whereIn('created_by', $branch->users->pluck('id'))
    //                 ->count();

    //             return $branch;
    //         });
    //     }

    //     return view('admin.branches.customers', compact('branchData','locations','currentLocation'));

    // }

    public function getBranches($locationId)
    {
        $branches = Branch::where('location_id', $locationId)->get(['id', 'branch_name']);
        return response()->json($branches);
    }


    public function allBranchesData(Request $request)
    {
        if ($request->ajax()) {
            // Initialize the query for customers
            if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
                $customersQuery = Customer::with('service')->with('branch')->orderBy('updated_at', 'desc');
            } else {
                $customersQuery = Customer::with('service')->orderBy('updated_at', 'desc')->with('branch')
                    ->where('status', '!=', 0);  // Exclude customers with status 0

            }
            // Apply role-based filtering on status
            if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
                // Admin and Super Admin can see all statuses, so no additional filter
                $customersQuery->whereIn('status', [0, 1, 2, 3]); // Include all statuses (example: 0-5)
            } elseif (auth()->user()->hasRole('supervisor')) {
                // For Supervisor, exclude statuses 1 and 3
                $customersQuery->whereNotIn('status', [1]);  // Exclude statuses 1 and 3
            } elseif (auth()->user()->hasRole('expert')) {
                // For Expert, exclude statuses 0 and 3
                $customersQuery->whereNotIn('status', [0, 3]);  // Exclude statuses 0 and 3
            } else {
                // For other roles, exclude only status 1
                $customersQuery->where('status', '!=', 1);  // Exclude status 1
            }

            // Execute the query
            $customers = $customersQuery->orderBy('updated_at', 'desc')->get();

            // Check if the 'branch' parameter is present in the request
            if ($request->has('branch') && !empty($request->branch)) {
                // Get the users associated with the specified branch
                $branchUsers = User::where('branch_id', $request->branch)->pluck('id');

                // Check if the branch has users before applying the filter
                if ($branchUsers->isNotEmpty()) {
                    $customersQuery->whereIn('created_by', $branchUsers);  // Filter customers whose created_by matches any user in the branch
                } else {
                    // If no users exist for the branch, return an empty result or handle it as needed
                    $customersQuery->whereRaw('1 = 0');  // This will result in no customers
                }
            }

            // Apply ordering
            $customers = $customersQuery->orderBy('updated_at', 'desc')->get();

            return datatables()->of($customers)
                ->addIndexColumn()
                ->addColumn('branch', function ($row) {
                    return $row->c ? $row->branch->branch_name : null;
                })

                ->addColumn('created_reviewed_by', function ($row) {
                    if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('supervisor')) {
                        $createdBy = $row->creator ? $row->creator->name : 'N/A';
                        $reviewedBy = $row->review ? $row->review->name : 'N/A';

                        return "<strong>Created By:</strong> $createdBy <br> <strong>Reviewed By:</strong> $reviewedBy";
                    }
                    return ''; // Hide for non-admins
                })
                ->addColumn('action', function ($row) {
                    if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('supervisor')) {
                        $view = '<a href="' . route('customers.show', $row->id) . '" class="btn btn-info btn-sm mr-1">
                                <i class="fe fe-eye"></i> ' . __('View') . '
                            </a>';

                        $mediaButton = '<a href="' . route('customers.media', $row->id) . '" class="btn btn-secondary btn-sm mr-1">
                                        <i class="fe fe-upload"></i> ' . __('Upload Media') . '
                                    </a>';

                        $edit = '<a href="' . route('customers.edit', $row->id) . '" class="btn btn-warning btn-sm mr-1">
                                <i class="fe fe-pencil"></i> ' . __('Edit') . '
                            </a>';

                        $printInvoice = '<a href="' . route('invoices.view', ['id' => $row->id]) . '" target="_blank" class="btn btn-primary btn-sm mr-1">
                                        <i class="fe fe-printer"></i> ' . __('View Invoice') . '
                                    </a>';

                        $indicator = null;
                        $documentRequestExists = DocumentRequest::where('customer_id', $row->id)
                            ->where('is_viewed', false)
                            ->exists();

                        if ($documentRequestExists) {
                            $indicator = '<button type="button" class="btn btn-danger btn-sm blink-indicator" 
                                        data-customer-id="' . $row->id . '" 
                                        data-customer-name="' . $row->first_name . '">
                                        <i class="fe fe-bell"></i>
                                    </button>';
                        }

                        return $view . ' ' . $edit . ' ' . $mediaButton . ' ' . $printInvoice . ' ' . $indicator;
                    }

                    return '<a href="' . route('customers.show', $row->id) . '" class="btn btn-info btn-sm mr-1">
                            <i class="fe fe-eye"></i> ' . __('View') . '
                        </a>';
                })
                ->addColumn('status', function ($customer) {
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

                    $statusClass = $statusClasses[$customer->status] ?? 'badge-secondary';
                    $statusText = $statusTexts[$customer->status] ?? 'Unknown';

                    return '<span class="badge ' . $statusClass . '">' . $statusText . '</span>';
                })
                ->rawColumns(['status', 'action', 'created_reviewed_by']) // Allow HTML rendering
                ->make(true);
        }

        return view('admin.branches.customers');
    }
}
