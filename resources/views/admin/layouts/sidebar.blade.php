<!-- Sidebar -->
<?php
use App\Models\Location;
$locations = Location::with('getAllBranches')->get();

?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>

                <li class="menu-title">
                    <span>Main</span>
                </li>

                <!-- Dashboard -->
                @if (auth()->user()->can('dashboard'))
                    <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false">
                            <i data-feather="book-open"></i>
                            <span>
                                {{ __('sidebar.dashboard') }}
                            </span>
                        </a>
                    </li>
                @endif

                <!-- /Dashboard -->
                <!-- Customers -->
                @php
                    //  dd(auth()->user()->hasRole('supervisor') );
                    //  dd((auth()->user()->can('customers-list') || auth()->user()->hasRole('operator'))  )
                @endphp @if (
                    (auth()->user()->can('customers-list') || auth()->user()->hasRole('operator')) &&
                        !auth()->user()->hasRole('supervisor'))
                    <li class="submenu">
                        <a class="" href="javascript:void(0)" aria-expanded="false">
                            <i data-feather="user-plus"></i>
                            <span class="hide-menu">{{ __('sidebar.customers') }} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="display: none;">
                            @if (auth()->user()->can('customers-list') || !auth()->user()->hasRole('supervisor'))
                                <li>
                                    <a href="{{ route('customers.index') }}" title="{{ __('sidebar.customers') }}"
                                        class="sidebar-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.customers') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                <!-- /Customers -->
                <!-- CMS -->
                {{-- @if (auth()->user()->can('cmspage-list') || auth()->user()->can('cmscategory-list'))
                    <li class="submenu">
                        <a class="" href="javascript:void(0)" aria-expanded="false">
                            <i data-feather="file-text"></i>
                            <span class="hide-menu">{{__('sidebar.cms')}} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="display: none;">
                            @can('cmscategory-list')
                                <li>
                                    <a href="{{ route('cmscategories.index') }}" title="{{__('sidebar.category')}}" class="sidebar-link {{ (request()->is('admin/cmscategories*')) ? 'active' : '' }}">
                                        <span class="hide-menu">{{__('sidebar.category')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('cmspage-list')
                                <li>
                                    <a href="{{ route('cmspages.index') }}" title="{{__('sidebar.cms-pages')}}" class="sidebar-link {{ (request()->is('admin/cmspage*')) ? 'active' : '' }}">
                                        <span class="hide-menu">{{__('sidebar.cms-pages')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif --}}
                <!-- /CMS -->

                <!-- Users -->
                @if (auth()->user()->can('user-list') ||
                        auth()->user()->can('role-list') ||
                        auth()->user()->can('permission-list') ||
                        auth()->user()->can('user-activity'))
                    <li class="submenu">
                        <a class="" href="javascript:void(0)" aria-expanded="false">
                            <i data-feather="users"></i>
                            <span class="hide-menu">{{ __('sidebar.user') }} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="display: none;">
                            @can('user-list')
                                <li>
                                    <a href="{{ route('users.index') }}" title="{{ __('sidebar.user') }}"
                                        class="sidebar-link {{ request()->is('admin/user*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.user') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('role-list')
                                <li>
                                    <a href="{{ route('roles.index') }}" title="{{ __('sidebar.roles') }}"
                                        class="sidebar-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.roles') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('permission-list')
                                <li>
                                    <a href="{{ route('permissions.index') }}" title="{{ __('sidebar.permissions') }}"
                                        class="sidebar-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.permission') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('user-activity')
                                <li>
                                    <a href="/admin/user-activity" title="{{ __('sidebar.user-activity') }}"
                                        class="sidebar-link {{ request()->is('admin/setting/useractivity*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.user-activity') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                <!-- /Users -->

                <!-- Settings -->
                @if (auth()->user()->can('file-manager') ||
                        auth()->user()->can('currency-list') ||
                        auth()->user()->can('websetting-edit') ||
                        auth()->user()->can('log-view') ||
                        auth()->user()->can('branches-list'))
                    <li class="submenu">
                        <a class="" href="javascript:void(0)" aria-expanded="false">
                            <i data-feather="settings"></i>
                            <span class="hide-menu">{{ __('sidebar.settings') }} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="display: none;">
                            {{-- @can('currency-list')
                                <li>
                                    <a href="{{ route('currencies.index') }}" title="{{__('sidebar.currencies')}}" class="sidebar-link {{ (request()->is('admin/currencies*')) ? 'active' : '' }}">
                                        <span class="hide-menu">{{__('sidebar.currency')}}</span>
                                    </a>
                                </li>
                            @endcan --}}
                            @can('websetting-edit')
                                <li>
                                    <a href="{{ route('website-setting.edit') }}"
                                        title="{{ __('sidebar.website-setting') }}"
                                        class="sidebar-link {{ request()->is('admin/setting/website-setting*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.website-setting') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('file-manager')
                                <li>
                                    <a href="{{ route('filemanager.index') }}" title="{{ __('sidebar.file-manager') }}"
                                        class="sidebar-link {{ request()->is('admin/setting/file-manager*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.file-manager') }}</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- @can('log-view')
                                <li>
                                    <a href="/admin/log-reader" title="{{__('sidebar.read-logs')}}" class="sidebar-link {{ (request()->is('admin/setting/log*')) ? 'active' : '' }}">
                                        <span class="hide-menu">{{__('sidebar.read-logs')}}</span>
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endif
                <!-- /Settings -->
                {{-- System start --}}
                @if (auth()->user()->can('services-list') || auth()->user()->can('branches-list'))
                    <li class="submenu">
                        <a class="" href="javascript:void(0)" aria-expanded="false">
                            <i data-feather="package"></i>
                            <span class="hide-menu">{{ __('sidebar.system') }} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="display: none;">
                            @can('services-list')
                                <li>
                                    <a href="{{ route('services.index') }}" title="{{ __('sidebar.services') }}"
                                        class="sidebar-link {{ request()->is('admin/services*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.services') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('branches-list')
                                <li>
                                    <a href="{{ route('branches.index') }}" title="{{ __('sidebar.branches') }}"
                                        class="sidebar-link {{ request()->is('admin/branches/*') ? 'active' : '' }}">
                                        <span class="hide-menu">{{ __('sidebar.branches') }}</span>
                                    </a>
                                </li>
                            @endcan




                        </ul>
                    </li>
                @endif
                {{-- System end --}}
                {{-- All Customers Start --}}
                @if (auth()->user()->can('can-see-all-branches-data'))
                    <li
                        class="{{ request()->routeIs('branches.allBranchesData') && request('branch') == '' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('branches.allBranchesData') }}" aria-expanded="false">
                            <i data-feather="git-pull-request"></i>
                            <span>
                                {{ __('All Customers') }}
                            </span>
                        </a>
                    </li>
                    {{-- @foreach ($locations as $location)
                    <li class="submenu">
                        <a class="" href="javascript:void(0)" aria-expanded="false">
                            <i data-feather="map-pin"></i>
                            <span class="hide-menu">{{ $location->name }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                    
                        @if ($location->getAllBranches->isNotEmpty())
                            <ul style="display: none;">
                                
                                @foreach ($location->getAllBranches as $branch)
                                    <li>
                                        <a href="{{ route('branches.allBranchesData', ['branch' => $branch->id]) }}" 
                                        title="{{ __('sidebar.customers') }}" 
                                        class="sidebar-link {{ request()->routeIs('branches.allBranchesData') && request('branch') == $branch->id ? 'active' : '' }}">
                                            <span class="hide-menu">- {{ $branch->branch_name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach --}}
                @endif
                {{-- All Customers End --}}

                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                    <li class="{{ request()->routeIs('shifts.index') && request('shift') == '' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('shifts.index') }}" aria-expanded="false">
                            <i data-feather="clock"></i>
                            <span>
                                {{ __('Shifts') }}
                            </span>
                        </a>
                    </li>
                @endif

                <li
                    class="{{ request()->routeIs('attendances.index') && request('attendances') == '' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('attendances.index') }}" aria-expanded="false">
                        <i data-feather="clock"></i>
                        <span>
                            {{ __('Attendances') }}
                        </span>
                    </a>
                </li>

                @if (auth()->user()->hasRole('super Admin') || auth()->user()->hasRole('Admin'))
                    <li
                        class="{{ request()->routeIs('admin.financial_report') && request('financial') == '' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.financial_report') }}" aria-expanded="false">
                            <i data-feather="file-text"></i>
                            <span>
                                {{ __('Financial') }}
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div> <!-- /Sidebar-Menu -->
    </div> <!-- /Sidebar-inner -->
</div><!-- /Sidebar -->
