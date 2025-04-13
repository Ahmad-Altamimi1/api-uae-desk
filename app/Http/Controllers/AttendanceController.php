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
        // Apply filters based on role and request parameters
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Super Admin')) {
            $query->where('user_id', auth()->id());
        }
    
        if ($request->has('filter_date') && $request->filter_date === 'today') {
            $query->whereDate('login_time', Carbon::today()->toDateString());
        }
    
        if ($request->has('filter_late')) {
            $isLate = $request->filter_late === 'late';
            $query->where('is_late', $isLate);
        }
    
        if ($request->has('filter_user') && $request->filter_user !== '' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))) {
            $query->where('user_id', $request->filter_user);
        }
    
        if ($request->has('filter_branch_id') && $request->filter_branch_id !== '' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))) {
            $query->where('branch_id', $request->filter_branch_id);
        }
    
        // Fetch data with relationships
        $attendances = $query->with('user', 'branch')
            ->orderBy('login_time', 'desc')
            ->get();
    
        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
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
