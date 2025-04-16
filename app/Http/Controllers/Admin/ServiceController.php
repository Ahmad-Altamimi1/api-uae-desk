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
        // $this->middleware('auth');
        // $this->middleware('permission:services-list', ['only' => ['index', 'store']]);
        // $this->middleware('permission:services-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:services-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:services-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $services = Service::all();
    
        return response()->json([
            'success' => true,
            'message' => 'Services Added successfully',
            'data' => $services
        ]);
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
        try {
            $service = Service::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $service,
                
            ], 200);    

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'id'=>'required',
        ];

        $messages = [
            'name.required' => __('Service name is required.'),
        ];

        $this->validate($request, $rules, $messages);

        try {
            $service = Service::findOrFail($request->id);
            $service->update($request->all());
            Toastr::success(__('Service updated successfully.'));
            return response()->json(['success' => true, 'message' => __('Service updated successfully.')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Service not found: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Service not found.')], 404);
        } catch (\Exception $e) {
            \Log::error('Error updating service: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Error updating service.')], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $service = Service::findOrFail($request->id);
            $service->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
