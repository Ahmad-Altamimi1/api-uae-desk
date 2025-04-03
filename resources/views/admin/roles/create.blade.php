@extends('admin.layouts.master')

@section('page_title')
    {{ __('role.create.title') }}
@endsection

@section('content')
    <form action="{{ route('roles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf()

        <div class="page-header">
            <div class="card breadcrumb-card">
                <div class="row justify-content-between align-content-between" style="height: 100%;">
                    <div class="col-md-6">
                        <h3 class="page-title">{{ __('role.index.title') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('roles.index') }}">{{ __('role.index.title') }}</a>
                            </li>
                            <li class="breadcrumb-item active-breadcrumb">
                                <a href="{{ route('roles.create') }}">{{ __('role.create.title') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <div class="create-btn pull-right">
                            <button type="submit"
                                class="btn custom-create-btn">{{ __('default.form.save-button') }}</button>
                        </div>
                    </div>
                </div>
            </div><!-- /card finish -->
        </div><!-- /Page Header -->

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">Roles Information</h4>
                    </div>

                    <div class="card-body">
                        <!-- Role Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <!-- Role Name -->
                                <div class="form-group">
                                    <label for="name" class="form-label required">Role Name</label>
                                    <input type="text" class="form-control border-primary" name="name" id="name"
                                        placeholder="Enter role name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Role Code -->
                                <div class="form-group">
                                    <label for="code" class="form-label required">Role Code</label>
                                    <input type="text" class="form-control border-secondary" name="code"
                                        id="code" placeholder="Enter role code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="form-group">
                            <label for="permission" class="form-label">
                                <h5>Permissions</h5>
                            </label>
                            @error('permission')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror

                            <!-- Select All -->
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="checkPermissionAll" value="1">
                                <label class="form-check-label" for="checkPermissionAll">Select All Permissions</label>
                            </div>
                            <hr>

                            <!-- Grouped Permissions -->
                            <div class="row">
                                @foreach ($groupedPermissions as $groupName => $permissions)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-light shadow-sm">
                                            <div class="card-header bg-light">
                                                <strong>{{ ucfirst($groupName) }}</strong>
                                                <div class="form-check float-end">
                                                    <input type="checkbox" class="form-check-input group-check"
                                                        id="group-{{ $groupName }}">
                                                    <label class="form-check-label small"
                                                        for="group-{{ $groupName }}">Select All</label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @foreach ($permissions as $permission)
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox" class="form-check-input permission-check"
                                                            name="permission[]" value="{{ $permission->id }}">
                                                        <label
                                                            class="form-check-label">{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col-md-12 -->
        </div> <!-- end row -->



    </form>
@endsection

@push('scripts')
    <script>
        $("#checkPermissionAll").click(function() {
            if ($(this).is(':checked')) {
                $('input[type=checkbox]').prop('checked', true)
            } else {
                $('input[type=checkbox]').prop('checked', false)
            }
        })

        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkPermissionAll');
            const groupChecks = document.querySelectorAll('.group-check');
            const permissionChecks = document.querySelectorAll('.permission-check');

            // Global Select All
            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                permissionChecks.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                groupChecks.forEach(group => {
                    group.checked = isChecked;
                });
            });

            // Group Select All
            groupChecks.forEach(group => {
                group.addEventListener('change', function() {
                    const groupCard = this.closest('.card');
                    const groupPermissions = groupCard.querySelectorAll('.permission-check');
                    groupPermissions.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            });
        });
    </script>
@endpush
