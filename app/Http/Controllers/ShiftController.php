<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Gate;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::latest()->get();
    
        return response()->json([
            'success' => true,
            'message' => 'Shifts Added successfully',
            'data' => $shifts,
        ]);
    }
    

    public function show($id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin/shifts.index', compact('shift'));
    }
    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
    
        $shift = new Shift();
        $shift->name = $request->name;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->is_active = $request->is_active ? 1 : 0;
        $shift->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Shift created successfully!',
            'data' => $shift
        ], 201);
    }
    

    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);


        $shift->name = $request->name;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->is_active = $request->is_active == 'on' ? 1 : 0;
        $shift->save();
        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully!');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:shifts,id',
            'status' => 'required',
        ]);

        $shift = Shift::findOrFail($request->id);
        $shift->is_active = $request->status;
        $shift->save();

        return response()->json([
            'success' => true,
            'message' => 'Shift status updated successfully!',
        ]);
    }
    public function destroy(Request $request)
    {
        try {
            $shift = Shift::findOrFail($request->id);
            $shift->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'shift deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting shift.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
