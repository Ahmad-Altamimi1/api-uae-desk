<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:permission-list', ['only' => ['index', 'store']]);
		$this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
		$this->middleware('permission:permission-edit', ['only' => ['edit', 'update']]);
		$this->middleware('permission:permission-delete', ['only' => ['destroy']]);

		$permission_list = Permission::get()->filter(function ($item) {
			return $item->name == 'permission-list';
		})->first();
		$permission_create = Permission::get()->filter(function ($item) {
			return $item->name == 'permission-create';
		})->first();
		$permission_edit = Permission::get()->filter(function ($item) {
			return $item->name == 'permission-edit';
		})->first();
		$permission_delete = Permission::get()->filter(function ($item) {
			return $item->name == 'permission-delete';
		})->first();


		if ($permission_list == null) {
			Permission::create(['name' => 'permission-list']);
		}
		if ($permission_create == null) {
			Permission::create(['name' => 'permission-create']);
		}
		if ($permission_edit == null) {
			Permission::create(['name' => 'permission-edit']);
		}
		if ($permission_delete == null) {
			Permission::create(['name' => 'permission-delete']);
		}
	}

	public function index(Request $request)
	{
		$permissions = Permission::all();
		// return view('admin.permissions.index', compact('permissions'));
		return response()->json($permissions->map(function ($permission) {
			return [
				'id' => $permission->id,
				'name' => $permission->name,

				// 'actions' => [
				//     'view' => (Gate::check('customers-view') || auth()->user()->hasRole('supervisor'))
				//         ? route('customers.show', $customer->id) : null,
				//     'edit' => (Gate::check('customers-edit') || auth()->user()->hasRole('supervisor')) &&
				//         (auth()->user()->hasRole('operator') && in_array($customer->status, [0, 1]) || auth()->user()->hasRole('supervisor'))
				//         ? route('customers.edit', $customer->id) : null,
				//     'delete' => Gate::check('customers-delete')
				//         ? route('customers.destroy', $customer->id) : null,
				//     'upload_media' => (Gate::check('customers-upload-media') || auth()->user()->hasRole('supervisor'))
				//         ? route('customers.media', $customer->id) : null,
				//     'print_invoice' => route('invoices.view', ['id' => $customer->id]),
				//     'account_statement' => (Gate::check('customers-account') || auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
				//         ? route('customers.account-statement', $customer->id) : null,
				// ],
			];
		}));
	}

	public function create()
	{
		$permissions = Permission::get();
		return view('admin.permissions.create', compact('permissions'));
	}

	public function store(Request $request)
	{
		$rules = [
			'name' => 'required|unique:permissions,name',
		];

		$messages = [
			'name.required' => __('default.form.validation.name.required'),
			'name.unique' => __('default.form.validation.name.unique'),
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'error',
				'message' => $validator->errors()->first(),
			], 422);
		}

		$permissions = Permission::create(['name' => $request->input('name')]);

		Toastr::success(__('permission.message.store.success'));
		return response()->json([
			"data" => $permissions->id,
			'status' => 'success',
			'message' => __('permission.message.store.success'),
		]);
	}

	public function edit($id)
	{
		$permissions = Permission::find($id);
		return view('admin.permissions.edit', compact('permissions'));
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'name' => 'required|unique:permissions,name,' . $id,
		];

		$messages = [
			'name.required' => __('default.form.validation.name.required'),
			'name.unique' => __('default.form.validation.name.unique'),
		];

		$this->validate($request, $rules, $messages);

		try {
			$permissions = Permission::find($id);
			$permissions->name = $request->input('name');
			$permissions->save();

			Toastr::success(__('permission.message.update.success'));
			return redirect()->route('permissions.index');

		} catch (Exception $e) {
			Toastr::error(__('permission.message.update.error'));
			return redirect()->route('permissions.index');
		}
	}

	

public function destroy(Request $request)
{
    $id = $request->input('id');

    try {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => __('permission.message.not_found'),
            ], 404);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => __('permission.message.destroy.success'),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => __('permission.message.destroy.error'),
        ], 500);
    }
}


	public function groupPermission()
	{

		$permissions = [
			['id' => '1', 'name' => 'user-list'],
			['id' => '2', 'name' => 'user-create'],
			['id' => '3', 'name' => 'user-edit'],
			['id' => '4', 'name' => 'user-delete'],
			['id' => '5', 'name' => 'profile-index'],
			['id' => '6', 'name' => 'role-list'],
			['id' => '7', 'name' => 'role-create'],
			['id' => '8', 'name' => 'role-edit'],
			['id' => '9', 'name' => 'role-delete'],
			['id' => '10', 'name' => 'permission-list'],
			['id' => '11', 'name' => 'permission-create'],
			['id' => '12', 'name' => 'permission-edit'],
			['id' => '13', 'name' => 'permission-delete'],
			['id' => '38', 'name' => 'customers-list'],
			['id' => '37', 'name' => 'services-create'],
			['id' => '36', 'name' => 'services-list'],
			['id' => '35', 'name' => 'services-delete'],
			['id' => '34', 'name' => 'services-edit'],
			['id' => '33', 'name' => 'branches-delete'],
			['id' => '32', 'name' => 'branches-edit'],
			['id' => '31', 'name' => 'branches-create'],
			['id' => '30', 'name' => 'branches-list'],
			['id' => '26', 'name' => 'file-manager'],
			['id' => '27', 'name' => 'websetting-edit'],
			['id' => '28', 'name' => 'user-activity'],
			['id' => '29', 'name' => 'log-view'],
			['id' => '39', 'name' => 'customers-create'],
			['id' => '40', 'name' => 'customers-edit'],
			['id' => '41', 'name' => 'customers-delete'],
			['id' => '42', 'name' => 'customers-view'],
			['id' => '43', 'name' => 'customers-upload-media'],
			['id' => '44', 'name' => 'customers-delete-media'],
			['id' => '45', 'name' => 'customers-status'],
			['id' => '46', 'name' => 'dashboard'],
			['id' => '47', 'name' => 'can-see-all-branches-data'],
		];

		// Define group mappings
		$groups = [
			'user-' => 'User',
			'profile-' => 'Profile',
			'role-' => 'Role',
			'permission-' => 'Permission',
			'customers-' => 'Customer',
			'services-' => 'Service',
			'branches-' => 'Branch',
			'file-' => 'File Manager',
			'websetting-' => 'Web Settings',
			'user-activity' => 'Activity Log',
			'log-view' => 'Activity Log',
			'dashboard' => 'Dashboard',
			'can-see-all-branches-data' => 'Branch Data',
		];

		// Loop through permissions and assign group_name
		foreach ($permissions as $permission) {
			$groupName = null;

			foreach ($groups as $prefix => $group) {
				if (strpos($permission['name'], $prefix) === 0) { // Use strpos for prefix matching
					$groupName = $group;
					break;
				}
			}

			// Update group_name in the database
			Permission::where('name', $permission['name'])->update(['group_name' => $groupName]);
		}

		echo "Permissions updated with group names successfully!";

	}
}
