<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Image;
use Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Branch;
use App\Models\Location;
use App\Models\Shift;
use Symfony\Component\Console\Input\Input;
// php artisan l5-swagger:generate
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="UAE Desk API Documentation",
 *      description=" Swagger for UAE Desk APIs",
 * )
 */
class UserController extends Controller
{


	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:user-list', ['only' => ['index', 'store']]);
		$this->middleware('permission:user-create', ['only' => ['create', 'store']]);
		$this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
		$this->middleware('permission:user-delete', ['only' => ['destroy']]);
		$this->middleware('permission:profile-index', ['only' => ['profile', 'profile_update']]);

		$user_list = Permission::get()->filter(function ($item) {
			return $item->name == 'user-list';
		})->first();
		$user_create = Permission::get()->filter(function ($item) {
			return $item->name == 'user-create';
		})->first();
		$user_edit = Permission::get()->filter(function ($item) {
			return $item->name == 'user-edit';
		})->first();
		$user_delete = Permission::get()->filter(function ($item) {
			return $item->name == 'user-delete';
		})->first();
		$profile_index = Permission::get()->filter(function ($item) {
			return $item->name == 'profile-index';
		})->first();


		if ($user_list == null) {
			Permission::create(['name' => 'user-list']);
		}
		if ($user_create == null) {
			Permission::create(['name' => 'user-create']);
		}
		if ($user_edit == null) {
			Permission::create(['name' => 'user-edit']);
		}
		if ($user_delete == null) {
			Permission::create(['name' => 'user-delete']);
		}
		if ($profile_index == null) {
			Permission::create(['name' => 'profile-index']);
		}
	}



	/**
	 * @OA\Get(
	 *     path="/api/admin/users/index",
	 *     operationId="showUsers",
	 *     tags={"Users"},
	 *     summary="Get All Users",
	 *     description="Get All Users",
	 *     security={{"sanctum": {}}},
	 *     @OA\Response(
	 *         response=200,
	 *         description="Form data retrieved"
	 *     )
	 * )
	 */
	public function index(Request $request)
	{

		if ($request->ajax()) {
			$shifts = Shift::all();
			$query = User::with(['createBy', 'updatedBy', 'shift']);
			// Apply filters based on roles
			if (auth()->user()->hasRole('Admin')) {
				// Exclude users with roles 'admin' and 'super admin'
				$query->whereDoesntHave('roles', function ($q) {
					$q->whereIn('name', ['Admin', 'Super Admin']);
				});
			}

			if (auth()->user()->hasRole('Super Admin')) {
				// Exclude users with roles 'super admin'
				$query->whereDoesntHave('roles', function ($q) {
					$q->where('name', 'Super Admin');
				});
			}

			if (auth()->user()->hasRole('supervisor')) {
				// Exclude users with roles 'admin', 'super admin', and 'supervisor'
				$query->whereDoesntHave('roles', function ($q) {
					$q->whereIn('name', ['Admin', 'Super Admin', 'supervisor']);
				})
					->where('created_by', auth()->user()->id); // Filter by created_by for supervisor
			}

			$data = $query->get();

			return Datatables::of($data)
				->addIndexColumn()
				->addColumn('action', function ($row) {
					$edit = Gate::check('user-edit')
						? '<a href="' . route('users.edit', $row->id) . '" class="custom-edit-btn mr-1">
								<i class="fe fe-pencil"></i>
								' . __('default.form.edit-button') . '
						   </a>'
						: '';

					$delete = Gate::check('user-delete')
						? '<button class="custom-delete-btn remove-user" data-id="' . $row->id . '" data-action="' . route('users.destroy') . '">
								<i class="fe fe-trash"></i>
								' . __('default.form.delete-button') . '
						   </button>'
						: '';

					return $edit . ' ' . $delete;
				})
				->addColumn('created_by', function ($row) {
					return $row->createBy ? $row->createBy->name : __('N/A');
				})
				->addColumn('updated_by', function ($row) {
					return $row->updatedBy ? $row->updatedBy->name : __('N/A');
				})
				->addColumn('status', function ($row) {
					$current_status = $row->status == 1 ? 'Checked' : '';
					return "
						<input type='checkbox' id='status_$row->id' class='check' onclick='changeUserStatus(event.target, $row->id);' " . $current_status . ">
						<label for='status_$row->id' class='checktoggle'>checkbox</label>
					";
				})
				->addColumn('image', function ($row) {
					if (empty($row->image)) {
						return '<img src="/assets/admin/img/default-user.png" class="rounded-circle img-fluid img-thumbnail" style="max-width: 50px;">';
					}
					return '<img src="' . $row->image . '" class="rounded-circle img-fluid img-thumbnail" style="max-width: 60px; height: 45px;">';
				})
				->addColumn('role', function ($user) {
					$role = str_replace(['[', ']', '"'], '', $user->getRoleNames());
					return $role;
				})
				->addColumn('shift', function ($row) use ($shifts) {
					$options = '';
					foreach ($shifts as $shift) {
						$selected = $row->shift_id == $shift->id ? 'selected' : '';
						$options .= "<option value='{$shift->id}' {$selected}>{$shift->name}</option>";
					}
					return "
						<select class='form-control shift-dropdown' data-user-id='{$row->id}'>
							$options
						</select>
					";
				})
				->editColumn('created_at', '{{ date("jS M Y", strtotime($created_at)) }}')
				->editColumn('updated_at', '{{ date("jS M Y", strtotime($updated_at)) }}')
				->escapeColumns([])
				->rawColumns(['action', 'image', 'shift'])
				->make(true);
		}
		// return view('admin.users.index');
		return response()->json(['data' => "e"]);
	}

	/**
	 * @OA\Get(
	 *     path="/api/users/create",
	 *     operationId="getUserCreateForm",
	 *     tags={"Users"},
	 *     summary="Get form data to create a new user",
	 *     description="Returns roles, locations, shifts, and branches available for user creation",
	 *     security={{"sanctum": {}}},
	 *     @OA\Response(
	 *         response=200,
	 *         description="Form data retrieved"
	 *     )
	 * )
	 */
	public function create()
	{

		$roles = Role::when(auth()->user()->hasRole('supervisor'), function ($query) {
			// Exclude Admin, Super Admin, and Supervisor for Supervisors
			$query->whereNotIn('name', ['Admin', 'Super Admin', 'Supervisor']);
		})
			->when(auth()->user()->hasRole('Admin'), function ($query) {
				// Exclude Super Admin for Admins
				$query->whereNotIn('name', ['Super Admin']);
			})
			->when(auth()->user()->hasRole('Super Admin'), function ($query) {
				// Include Admin for Super Admins
				$query->where('name', '!=', 'Super Admin'); // Only exclude Super Admin
			}, function ($query) {
				// Default exclusion for non-Super Admins
				$query->whereNotIn('name', ['Admin', 'Super Admin']);
			})
			->get();

		// Return branches normally
		$branches = Branch::all();

		// If the user is a Supervisor, only return their location
		$locations = auth()->user()->hasRole('supervisor')
			? Location::where('id', auth()->user()->location_id)->get()
			: Location::all();
		$shifts = Shift::where('is_active', 1)->get();
		return view('admin.users.create', compact('roles', 'branches', 'locations', 'shifts'));
	}

	/**
	 * @OA\Post(
	 *     path="/api/users",
	 *     operationId="storeUser",
	 *     tags={"Users"},
	 *     summary="Store new user",
	 *     description="Creates a new user with role and details",
	 *     security={{"sanctum": {}}},
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             required={"name","email","password","roles"},
	 *             @OA\Property(property="name", type="string", example="John Doe"),
	 *             @OA\Property(property="email", type="string", example="john@example.com"),
	 *             @OA\Property(property="password", type="string", example="password123"),
	 *             @OA\Property(property="confirm-password", type="string", example="password123"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 @OA\Items(type="string", example="User")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="User created successfully"
	 *     ),
	 *     @OA\Response(
	 *         response=422,
	 *         description="Validation error"
	 *     )
	 * )
	 */
	public function store(Request $request)
	{
		$rules = [
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|same:confirm-password|min:8',
			'roles' => 'required|array', // Ensure roles are passed as an array
			'shift_id' => [
				'required_if:roles,operator',
				function ($attribute, $value, $fail) use ($request) {
					// Validate shift_id only for 'operator'
					$roles = collect($request->roles)->map(function ($role) {
						return strtolower($role);
					});
					if ($roles->contains('operator') && !$value) {
						$fail(__('Shift selection is required for the operator role.'));
					}
				},
			],
			'mobile' => 'required|string|unique:users,mobile|max:15',
			'image' => 'nullable', // Optional, validate image type
			'branches' => 'array',
			'branches.*' => 'exists:branches,id',
			'branch_id' => [
				'nullable',
				function ($attribute, $value, $fail) use ($request) {
					// Validate branch_id only for specific roles
					$roles = collect($request->roles)->map(function ($role) {
						return strtolower($role);
					});
					if ($roles->isNotEmpty() && !$roles->contains('admin') && !$value) {
						$fail(__('Branch selection is required for this role.'));
					}
				},
			],
			'location_id' => [
				'nullable',
				function ($attribute, $value, $fail) use ($request) {
					// Validate location_id only for 'supervisor'
					$roles = collect($request->roles)->map(function ($role) {
						return strtolower($role);
					});
					if ($roles->contains('supervisor') && !$value) {
						$fail(__('Location selection is required for the supervisor role.'));
					}
				},
			],
		];


		$messages = [
			'name.required' => __('default.form.validation.name.required'),
			'name.required' => __('default.form.validation.name.required'),
			'email.required' => __('default.form.validation.email.required'),
			'email.email' => __('default.form.validation.email.email'),
			'email.unique' => __('default.form.validation.email.unique'),
			'password.required' => __('default.form.validation.password.required'),
			'password.same' => __('default.form.validation.password.same'),
			'password.min' => __('default.form.validation.password.min'),
			'roles.required' => __('default.form.validation.roles.required'),
			'mobile.required' => __('default.form.validation.mobile.required'),
			'mobile.unique' => __('default.form.validation.mobile.unique'),
			'mobile.max' => __('default.form.validation.mobile.max'),
			'branches.required' => 'You must select at least one branch.',
			'branches.*.exists' => 'The selected branch is invalid.',
			// 'image.image' => __('default.form.validation.image.image'),
			// 'image.mimes' => __('default.form.validation.image.mimes'),
			// 'image.max' => __('default.form.validation.image.max'),
		];

		$this->validate($request, $rules, $messages);
		$input = request()->all();
		$input['password'] = Hash::make($input['password']);
		$input['created_by'] = Auth::user()->id;
		$is_location_flexible = isset($input['is_location_flexible']) ? (bool) $input['is_location_flexible'] : false;
		$input['is_location_flexible'] = $is_location_flexible;
		try {
			$user = User::create($input);
			if ($request->roles) {
				$user->assignRole($request->input('roles'));
			}
			if (isset($input['branches'])) {
				$user->branches()->attach($input['branches']);
			}
			Toastr::success(__('user.message.store.success'));
			return redirect()->route('users.index');
		} catch (Exception $e) {
			Toastr::error(__('user.message.store.error'));
			return redirect()->route('users.index');
		}
	}

	public function edit($id)
	{
		$user = User::findOrFail($id);

		// Check if the current user is admin and the user being edited has roles 'admin' or 'super admin'
		if (auth()->user()->hasRole('Admin') && $user->hasAnyRole(['Admin', 'Super Admin'])) {
			abort(404); // Return a 404 error
		}

		$roles = Role::when(auth()->user()->hasRole('supervisor'), function ($query) {
			// Exclude Admin, Super Admin, and Supervisor for Supervisors
			$query->whereNotIn('name', ['Admin', 'Super Admin', 'Supervisor']);
		})
			->when(auth()->user()->hasRole('Admin'), function ($query) {
				// Exclude Super Admin for Admins
				$query->whereNotIn('name', ['Super Admin']);
			})
			->when(auth()->user()->hasRole('Super Admin'), function ($query) {
				// Include Admin for Super Admins
				$query->where('name', '!=', 'Super Admin'); // Only exclude Super Admin
			}, function ($query) {
				// Default exclusion for non-Super Admins
				$query->whereNotIn('name', ['Admin', 'Super Admin']);
			})
			->get();

		// If the user is a Supervisor, only return their location
		$locations = auth()->user()->hasRole('supervisor')
			? Location::where('id', auth()->user()->location_id)->get()
			: Location::all();
		$shifts = Shift::where('is_active', 1)->get();

		// Fetch branches, filter by the user's location if assigned
		$branches = Branch::query();
		if ($user->location_id) {
			$branches->where('location_id', $user->location_id);
		}
		$branches = $branches->get();

		return view('admin.users.edit', compact('user', 'roles', 'branches', 'locations', 'shifts'));
	}




	public function update(Request $request, $id)
	{
		$rules = [
			'name' => 'required',
			'password' => 'same:confirm-password',
			'roles' => 'required',
			'image' => 'nullable',
			'branches' => 'array',
			'branches.*' => 'exists:branches,id',
			'shift_id' => [
				'required_if:roles,operator',
				function ($attribute, $value, $fail) use ($request) {
					// Validate shift_id only for 'operator'
					$roles = collect($request->roles)->map(function ($role) {
						return strtolower($role);
					});
					if ($roles->contains('operator') && !$value) {
						$fail(__('Shift selection is required for the operator role.'));
					}
				},
			],
			'branch_id' => [
				'nullable',
				function ($attribute, $value, $fail) use ($request) {
					// Validate branch_id only for specific roles
					$roles = collect($request->roles)->map(function ($role) {
						return strtolower($role);
					});
					if ($roles->isNotEmpty() && !$roles->contains('admin') && !$value) {
						$fail(__('Branch selection is required for this role.'));
					}
				},
			],
			'location_id' => [
				'nullable',
				function ($attribute, $value, $fail) use ($request) {
					// Validate location_id only for 'supervisor'
					$roles = collect($request->roles)->map(function ($role) {
						return strtolower($role);
					});
					if ($roles->contains('supervisor') && !$value) {
						$fail(__('Location selection is required for the supervisor role.'));
					}
				},
			],
		];

		$messages = [
			'name.required' => __('user.form.validation.name.required'),
			'password.required' => __('user.form.validation.password.required'),
			'password.same' => __('user.form.validation.password.same'),
			'roles.required' => __('user.form.validation.roles.required'),
			'branches.required' => 'You must select at least one branch.',
			'branches.*.exists' => 'The selected branch is invalid.',
		];

		$this->validate($request, $rules, $messages);
		$input = $request->all();
		$user = User::find($id);
		$input['updated_by'] = Auth::user()->id;
		$is_location_flexible = isset($input['is_location_flexible']) ? (bool) $input['is_location_flexible'] : false;
		$input['is_location_flexible'] = $is_location_flexible;

		if (empty($input['image'])) {
			$input['image'] = $user->image;
		}

		if (!empty($input['password'])) {
			$input['password'] = Hash::make($input['password']);
		} else {
			$input['password'] = $user->password;
		}

		try {
			$user->update($input);
			$user->branches()->sync($request->branches);
			$user->roles()->detach(); //delete all the roles
			if ($request->roles) {
				$user->assignRole($request->input('roles'));
			}

			Toastr::success(__('user.message.update.success'));
			return redirect()->route('users.index');
		} catch (Exception $e) {
			Toastr::error(__('user.message.update.error'));
			return redirect()->route('users.index');
		}
	}

	public function destroy()
	{
		$id = request()->input('id');
		$all_user = User::all();
		$count_all_user = $all_user->count();

		if ($count_all_user <= 1) {
			Toastr::error(__('user.message.warning_last_user'));
			return redirect()->route('users.index');
		} else {
			$getuser = User::find($id);
			if (!empty($getuser->image)) {
				$image_path = 'storage/' . $getuser->image;
				if (File::exists($image_path)) {
					File::delete($image_path);
				}
			}
			try {
				User::find($id)->delete();
				return back()->with(Toastr::error(__('user.message.destroy.success')));
			} catch (Exception $e) {
				$error_msg = Toastr::error(__('user.message.destroy.error'));
				return redirect()->route('users.index')->with($error_msg);
			}
		}
	}

	public function profile()
	{
		return view('admin.users.profile');
	}

	public function profile_update(Request $request, $id)
	{
		$rules = [
			'password' => 'required|string|min:6|same:confirm-password',
		];

		$messages = [
			'password.required' => __('default.form.validation.password.required'),
			'password.same' => __('default.form.validation.password.same'),
		];

		$this->validate($request, $rules, $messages);
		$input = $request->all();
		$input['password'] = Hash::make($input['password']);

		try {
			$user = User::whereId($id)->update([
				'password' => $input['password']
			]);

			Toastr::success(__('user.message.profile.success'));
			return redirect()->route('profile');
		} catch (Exception $e) {
			Toastr::success(__('user.message.profile.error'));
			return redirect()->route('profile');
		}
	}

	public function status_update(Request $request)
	{
		$user = User::find($request->id)->update(['status' => $request->status, 'updated_by' => Auth::user()->id]);
		if ($request->status == 1) {
			return response()->json(['message' => 'Status activated successfully.']);
		} else {
			return response()->json(['message' => 'Status deactivated successfully.']);
		}
	}
	public function update_shift(Request $request)
	{
		try {
			$user = User::findOrFail($request->user_id);
			$user->update(['shift_id' => $request->shift_id, 'updated_by' => Auth::user()->id]);

			return response()->json(['message' => __('Shift updated successfully.')], 200);
		} catch (\Exception $e) {
			return response()->json(['message' => __('An error occurred while updating the shift.')], 500);
		}
	}
}
