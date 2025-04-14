<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:role-list', ['only' => ['index', 'store']]);
		$this->middleware('permission:role-create', ['only' => ['create', 'store']]);
		$this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
		$this->middleware('permission:role-delete', ['only' => ['destroy']]);

		$role_list = Permission::get()->filter(function ($item) {
			return $item->name == 'role-list';
		})->first();
		$role_create = Permission::get()->filter(function ($item) {
			return $item->name == 'role-create';
		})->first();
		$role_edit = Permission::get()->filter(function ($item) {
			return $item->name == 'role-edit';
		})->first();
		$role_delete = Permission::get()->filter(function ($item) {
			return $item->name == 'role-delete';
		})->first();


		if ($role_list == null) {
			Permission::create(['name' => 'role-list']);
		}
		if ($role_create == null) {
			Permission::create(['name' => 'role-create']);
		}
		if ($role_edit == null) {
			Permission::create(['name' => 'role-edit']);
		}
		if ($role_delete == null) {
			Permission::create(['name' => 'role-delete']);
		}
	}

	public function index(Request $request)
	{
		$roles = Role::all();
		// return view('admin.roles.index',compact('roles'));
		return response()->json($roles->map(function ($role) {
			return [
				'id' => $role->id,
				'name' => $role->name,
				'code' => $role->code,

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
		$permissions = Permission::all();
		$groupedPermissions = $permissions->groupBy(function ($permission) {
			return isset($permission->group_name) ? trim($permission->group_name) : 'others'; // Group by trimmed 'group_name', default to 'others' if null
		});
		return response()->json($groupedPermissions);
	}

	public function store(Request $request)
	{
		$rules = [
			'name' 					=> 'required|unique:roles,name',
			'code' 					=> 'required|unique:roles,code',
			'permission' 			=> 'required',

		];

		$messages = [
			'name.required'    		=> __('default.form.validation.name.required'),
			'name.unique'    		=> __('default.form.validation.name.unique'),
			'code.required'    		=> __('default.form.validation.code.required'),
			'code.unique'    		=> __('default.form.validation.code.unique'),
			'permission.required'   => __('default.form.validation.permission.required'),
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
				'status' => 'error',
				'message' => $validator->errors()->first(),
			], 422);
		}

		$role = Role::create([
			'name' => $request->input('name'),
			'code' => $request->input('code')
		]);
		$role->syncPermissions($request->input('permission'));



		Toastr::success(__('role.message.store.success'));
		return response()->json([
			"data" => $role->id,
			'status' => 'success',
			'message' => __('role.message.store.success'),
		]);
		// $role->syncPermissions($request->input('permission'));

		// Toastr::success(__('role.message.store.success'));


	}

	public function edit($id)
	{
		$role = Role::find($id);
		$permissions = Permission::all();
		$groupedPermissions = $permissions->groupBy('group_name'); // Assuming 'group' is a column in your permissions table

		return view('admin.roles.edit', compact('role', 'groupedPermissions'));
	}

	public function update(Request $request, $id)
	{
		$rules = [
			'name' 					=> 'required|unique:roles,name,' . $id,
			'code' 					=> 'required|unique:roles,code,' . $id,
			'permission' 			=> 'required',
		];

		$messages = [
			'name.required'    		=> __('default.form.validation.name.required'),
			'name.unique'    		=> __('default.form.validation.name.unique'),
			'code.required'    		=> __('default.form.validation.code.required'),
			'code.unique'    		=> __('default.form.validation.code.unique'),
			'permission.required'   => __('default.form.validation.permission.required'),
		];

		$this->validate($request, $rules, $messages);

		try {
			$role = Role::find($id);
			$role->name = $request->input('name');
			$role->code = $request->input('code');
			$role->save();
			$role->syncPermissions($request->input('permission'));

			Toastr::success(__('role.message.update.success'));
			return redirect()->route('roles.index');
		} catch (Exception $e) {
			Toastr::error(__('role.message.update.error'));
			return redirect()->route('roles.index');
		}
	}

	public function destroy()
	{
		$id = request()->input('id');
		$allrole = Role::all();
		$countallrole = $allrole->count();

		if ($countallrole <= 1) {
			Toastr::error(__('role.message.warning_last_role'));
			return redirect()->route('users.index');
		} else {
			$getrole = Role::find($id);
			try {
				Role::find($id)->delete();
				return back()->with(Toastr::error(__('role.message.destroy.success')));
			} catch (Exception $e) {
				$error_msg = Toastr::error(__('user.message.destroy.error'));
				return redirect()->route('roles.index')->with($error_msg);
			}
		}
	}
}
