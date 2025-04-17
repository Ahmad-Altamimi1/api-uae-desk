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
    

    public function edit($id)
    {
        try {
            $shift = Shift::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $shift,

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
                'error' => $e->getMessage()
            ], 404);
        }
        // return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:shifts,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ];

        $messages = [
            'name.required' => __('Shift name is required.'),
            'start_time.required' => __('Start time is required.'),
            'end_time.required' => __('End time is required.'),
        ];

        $this->validate($request, $rules, $messages);
        try {
            $shift = Shift::findOrFail($request->id);
            $shift->update($request->all());
            return response()->json(['success' => true, 'message' => __('shift updated successfully.')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('shift not found: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('shift not found.')], 404);
        } catch (\Exception $e) {
            \Log::error('Error updating shift: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Error updating shift.')], 500);
        }
      
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
