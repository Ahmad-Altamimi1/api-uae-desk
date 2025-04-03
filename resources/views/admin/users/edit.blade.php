@extends('admin.layouts.master')

@section('page_title')
    {{ __('user.edit.title') }}
@endsection

@push('css')
    <style>
        #output {
            height: 300px;
            width: 300px;
            object-fit: cover;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
        }

        .breadcrumb-card {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .equal-height {
            display: flex;
            flex-wrap: wrap;
        }

        .equal-height .card {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
        }

        .save-btn {
            text-align: center;
            margin-top: 30px;
        }
    </style>
@endpush

@section('content')
    <form method="post" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf

        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="card breadcrumb-card">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <h3 class="page-title mb-0">{{ __('user.index.title') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('users.index') }}">{{ __('user.index.title') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('user.edit.title') }} - ({{ $user->name }})</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="card">
            <div class="card-body">
                <div class="row equal-height ">
                    <!-- User Image Section -->
                    <div class="col-md-4 col-sm-12 text-center mb-4 m-auto">
                        <img src="{{ $user->image ?? asset('assets/admin/img/default-user.png') }}" alt="User Image"
                            id="output" class="img-thumbnail rounded-circle mb-3"
                            onerror="this.src='{{ asset('assets/admin/img/default-user.png') }}';">
                        <input type="hidden" id="image1" name="image">
                        <button type="button" class="btn btn-secondary w-100" id="button-image">
                            <i data-feather="image" class="me-1"></i> Change User's Image
                        </button>
                    </div>
                </div>
                <div class="row equal-height mt-4">
                    <!-- Personal Information Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">{{ __('Personal Information') }}</div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="name"
                                        class="form-label required">{{ __('default.form.name') }}:</label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" required
                                        value="{{ $user->name }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="mobile" class="form-label">{{ __('default.form.mobile') }}:</label>
                                    <input type="text" name="mobile" id="mobile"
                                        class="form-control @error('mobile') is-invalid @enderror" disabled
                                        value="{{ $user->mobile }}"
                                        pattern="^((050|051|052|054|055|056|057|058|059)\d{7})$"
                                        title="{{ __('Enter a valid UAE mobile number.') }}">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Authentication Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">{{ __('Authentication') }}</div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">{{ __('default.form.email') }}:</label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ $user->email }}" disabled>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">{{ __('default.form.password') }}:</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password-confirm"
                                        class="form-label">{{ __('default.form.password-confirm') }}:</label>
                                    <input type="password" name="confirm-password" id="password-confirm"
                                        class="form-control @error('confirm-password') is-invalid @enderror">
                                    @error('confirm-password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role, Location, and Branch Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">{{ 'Role' }}</div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="roles" class="form-label required">{{ __('default.form.role') }}</label>
                                    <select name="roles[]" id="roles"
                                        class="form-select @error('roles') is-invalid @enderror" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3 d-none" id="location-section">
                                    <label for="location_id" class="form-label">{{ __('Location') }}</label>
                                    <select name="location_id" id="location_id"
                                        class="form-select @error('location_id') is-invalid @enderror">
                                        <option value="">{{ __('Select Location') }}</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ $user->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3 d-none" id="branch-section">
                                    <label for="branch_id" class="form-label">{{ __('Branch') }}</label>
                                    <select name="branch_id" id="branch_id"
                                        class="form-select @error('branch_id') is-invalid @enderror">
                                        <option value="">{{ __('Select Branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="branches">Select Branches:</label>
                                    <select name="branches[]" id="branches" multiple class="form-control">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ in_array($branch->id, $user->branches->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group form-check">
                                    <input class="form-check-input" type="checkbox" name="is_location_flexible"
                                        id="is_location_flexible" value="1"
                                        {{ $user->is_location_flexible == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_location_flexible">
                                        {{ __('Is Location Flexible?') }}
                                    </label>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="shift_id" class="form-label">{{ __('Shift') }}:</label>
                                    <select name="shift_id" id="shift_id"
                                        class="form-select @error('shift_id') is-invalid @enderror">
                                        <option value="">{{ __('Select Shift') }}</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}"
                                                {{ $user->shift_id == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-header mt-4">
                    <div class="card breadcrumb-card bg-white">
                        <!-- Save Button -->
                        <div class="save-btn mb-4" style="text-align: right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('default.form.update-button') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection


@push('scripts')
    <script>
        var loadFileImageFront = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('button-image').addEventListener('click', (event) => {
                event.preventDefault();
                inputId = 'image1';
                window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
            });
        });

        // input
        let inputId = '';
        let output = 'output';

        // set file link
        function fmSetLink($url) {
            document.getElementById(inputId).value = $url;
            document.getElementById(output).src = $url;
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const rolesDropdown = document.getElementById('roles');
            const locationSection = document.getElementById('location-section');
            const branchSection = document.getElementById('branch-section');
            const locationDropdown = document.getElementById('location_id');
            const branchDropdown = document.getElementById('branch_id');

            // Fetch branches based on location
            function fetchBranchesByLocation(locationId) {
                if (!locationId) {
                    branchDropdown.innerHTML = '<option value="">{{ __('Select Branch') }}</option>';
                    return;
                }

                fetch(`/admin/locations/${locationId}/branches`) // Update this URL according to your route
                    .then(response => response.json())
                    .then(data => {
                        branchDropdown.innerHTML = '<option value="">{{ __('Select Branch') }}</option>';
                        data.forEach(branch => {
                            const option = document.createElement('option');
                            option.value = branch.id;
                            option.textContent = branch.branch_name;
                            branchDropdown.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching branches:', error);
                    });
            }

            // Toggle dropdown visibility based on role selection
            function toggleDropdowns() {
                const selectedRoles = Array.from(rolesDropdown.selectedOptions).map(option => option.value
                    .toLowerCase());

                if (selectedRoles.includes('supervisor')) {
                    locationSection.style.display = 'block';
                    branchSection.style.display = 'none'; // Hide branch dropdown for supervisors
                    // Add 'required' to location dropdown
                    document.getElementById('location_id').setAttribute('required', 'required');

                    // Remove 'required' from branch dropdown
                    document.getElementById('branch_id').removeAttribute('required');

                } else if (selectedRoles.includes('operator') || selectedRoles.includes('expert')) {
                    locationSection.style.display = 'block';
                    branchSection.style.display = 'block'; // Show both for other roles

                    // Add 'required' to both location and branch dropdowns
                    document.getElementById('location_id').setAttribute('required', 'required');
                    document.getElementById('branch_id').setAttribute('required', 'required');

                } else {
                    locationSection.style.display = 'none';
                    branchSection.style.display = 'none'; // Hide both for other roles

                    document.getElementById('location_id').removeAttribute('required');
                    document.getElementById('branch_id').removeAttribute('required');
                }
            }

            // Event listener for roles dropdown
            // rolesDropdown.addEventListener('change', toggleDropdowns);

            // Event listener for location dropdown
            locationDropdown.addEventListener('change', function() {
                const selectedLocation = this.value;
                fetchBranchesByLocation(selectedLocation);
            });

            // Initial check on page load
            //  toggleDropdowns();

            // $(document).ready(function() {
            //     const rolesDropdown = $('#roles');

            //     // Bind change event
            //     rolesDropdown.on('change', toggleDropdowns);

            //     $('#location_id').on('change', function() {
            //         const selectedLocation = $(this).val(); // Get the selected location ID

            //         if (selectedLocation) {
            //             fetchBranchesByLocation(selectedLocation);
            //         } else {
            //             console.log('No location selected');
            //         }
            //     });
            // });
        });
    </script>
@endpush
