<?php
namespace App\Http\Controllers\Admin;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Toastr;
use Illuminate\Support\Facades\Gate;
use DataTables;

class ServiceController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:services-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:services-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:services-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:services-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::all();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = Gate::check('services-edit') ? 
                        '<a href="'.route('services.edit', $row->id).'" class="custom-edit-btn mr-1">
                            <i class="fe fe-pencil"></i> '.__('Edit').'
                         </a>' : '';

                    $delete = Gate::check('services-delete') ? 
                        '<button class="custom-delete-btn remove-service" data-id="'.$row->id.'" data-action="'.route('services.destroy', $row->id).'">
                            <i class="fe fe-trash"></i> '.__('Delete').'
                         </button>' : '';

                    return $edit . ' ' . $delete;
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('admin.services.index');
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        $messages = [
            'name.required' => __('Service name is required.'),
        ];

        $this->validate($request, $rules, $messages);

        try {
            Service::create($request->all());
            Toastr::success(__('Service created successfully.'));
            return redirect()->route('services.index');
        } catch (\Exception $e) {
            Toastr::error(__('Error creating service.'));
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);

        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
        ];

        $messages = [
            'name.required' => __('Service name is required.'),
        ];

        $this->validate($request, $rules, $messages);

        try {
            $service->update($request->all());
            Toastr::success(__('Service updated successfully.'));
            return redirect()->route('services.index');
        } catch (\Exception $e) {
            Toastr::error(__('Error updating service.'));
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        $service = Service::findOrFail($request->id);

        try {
            $service->delete();
            Toastr::success(__('Service deleted successfully.'));
            return redirect()->route('services.index');
        } catch (\Exception $e) {
            Toastr::error(__('Error deleting service.'));
            return redirect()->back();
        }
    }
}
