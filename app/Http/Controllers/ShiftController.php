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
        if ($request->ajax()) {
            $data = Shift::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('start_time', function ($row) {
                    return $row->start_time; // format if needed
                })
                ->addColumn('end_time', function ($row) {
                    return $row->end_time;
                })
                ->addColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';

                    if (Gate::check('shifts-edit') || auth()->user()->hasRole('Super Admin')) {
                        $btn .= '<a href="' . route('shifts.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                    }

                    if (Gate::check('shifts-delete') || auth()->user()->hasRole('Super Admin')) {
                        $btn .= '<button type="button" data-id="' . $row->id . '" data-action="' . route('shifts.destroy', $row->id) . '" class="btn btn-sm btn-danger remove-shift">Delete</button>';
                    }

                    return $btn;
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('admin.shifts.index');
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

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully!');
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

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully!');
    }
}
