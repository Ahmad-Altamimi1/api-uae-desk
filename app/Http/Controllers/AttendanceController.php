<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    function __construct() {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        $branches = Branch::all();

        // Query for all attendances
        $query = Attendance::query();

        // If the logged-in user is not an Admin or Super Admin, filter by user_id
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Super Admin')) {
            // Filter attendances where user_id is the same as the logged-in user's ID
            $query->where('user_id', auth()->id());
        }

        // Filter by today's date
        if ($request->has('filter_date') && $request->filter_date == 'today') {
            $query->whereDate('login_time', Carbon::today()->toDateString());
        }

        // Filter by late status (either late or not late)
        if ($request->has('filter_late')) {
            $isLate = $request->filter_late === 'late' ? true : false;
            $query->where('is_late', $isLate);
        }

        // Filter by user (only applicable if user is an Admin or Super Admin)
        if ($request->has('filter_user') && $request->filter_user != '' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))) {
            $query->where('user_id', $request->filter_user);
        }

        // Filter by branch (only applicable if user is an Admin or Super Admin)
        if ($request->has('filter_branch_id') && $request->filter_branch_id != '' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))) {
            $query->where('branch_id', $request->filter_branch_id);
        }

        // Fetch the filtered data
        $attendances = $query->with('user', 'branch') // Assuming you have relationships
            ->orderBy('login_time', 'desc')
            ->get();

        return view('admin.attendance.index', compact('attendances', 'users', 'branches'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
