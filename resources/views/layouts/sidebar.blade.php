<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('home') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="60">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('home') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.webp') }}" alt="" height="60">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @can('home')
                    <li class="menu-title"><span>@lang('translation.menu') </span></li>
                @endcan
                @if (Auth::user()->hasRole('customer'))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->get('status') == 'active' ? 'active' : '' }} "
                            href="{{ route('contracts.status', ['status' => 'active']) }}" aria-expanded="false">
                            <i class="ri-award-line"></i> <span>All Active Contracts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->get('status') == 'expired' ? 'active' : '' }}"
                            href="{{ route('contracts.status', ['status' => 'expired']) }}" aria-expanded="false">
                            <i class="bx bx-file-blank"></i> <span>Expired Contracts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->get('status') == 'expiring-soon' ? 'active' : '' }}"
                            href="{{ route('contracts.status', ['status' => 'expiring-soon']) }}"
                            aria-expanded="false">
                            <i class="bx bx-hourglass"></i> <span>Expiring Soon</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('customer*') ? 'active' : '' }}"
                            href="{{ route('contract.index') }}/create" aria-expanded="false">
                            <i class="bx bx-plus"></i> <span>Add Contract</span>
                        </a>
                    </li>
                @endif

                @can('renewal.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('renewal') ? 'active' : '' }}"
                            href="{{ route('contract.index') }}" aria-expanded="false">
                            <i class="ri-file-list-2-line"></i> <span>All Contracts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('renewal') ? 'active' : '' }}"
                            href="{{ route('renewal.index') }}" aria-expanded="false">
                            <i class="ri-refresh-line"></i> <span>All Renewals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('renewal/create') ? 'active' : '' }}"
                            href="{{ route('renewal.index') }}/create" aria-expanded="false">
                            <i class="ri-add-line"></i> <span>New Renewal</span>
                        </a>
                    </li>
                @endcan
                @can('type.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('type*') ? 'active' : '' }}"
                            href="{{ route('type.index') }}" aria-expanded="false">
                            <i class="ri-folder-settings-line"></i> <span>Renewals Types</span>
                        </a>
                    </li>
                @endcan
                @can('manufacturer.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('manufacturer*') ? 'active' : '' }}"
                            href="{{ route('manufacturer.index') }}" aria-expanded="false">
                            <i class="ri-archive-line"></i> <span>Manufacturers</span>
                        </a>
                    </li>
                @endcan

                @can('distributor.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('distributor*') ? 'active' : '' }}"
                            href="{{ route('distributor.index') }}" aria-expanded="false">
                            <i class="ri-compass-fill"></i> <span>Distributors</span>
                        </a>
                    </li>
                @endcan
                @can('term.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('term*') ? 'active' : '' }}"
                            href="{{ route('term.index') }}" aria-expanded="false">
                            <i class=" ri-terminal-box-line"></i> <span>Contract Terms</span>
                        </a>
                    </li>
                @endcan
                @can('customer.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('customer*') ? 'active' : '' }}"
                            href="{{ route('customer.index') }}" aria-expanded="false">
                            <i class="bx bx-user"></i> <span>Customers</span>
                        </a>
                    </li>
                @endcan

                {{-- @can('contract.index')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('contract*') ? 'active' : '' }}"
                            href="{{ route('contract.index') }}" aria-expanded="false">
                            <i class="bx bx-poll"></i> <span>All Contracts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->get('status') == 'active' ? 'active' : '' }} "
                            href="{{ route('contracts.status', ['status' => 'active']) }}" aria-expanded="false">
                            <i class=" ri-award-line"></i> <span>Active Contracts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->get('status') == 'expired' ? 'active' : '' }} "
                            href="{{ route('contracts.status', ['status' => 'expired']) }}" aria-expanded="false">
                            <i class="ri-medal-fill"></i> <span>Expired Contracts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->get('status') == 'expiring-soon' ? 'active' : '' }}"
                            href="{{ route('contracts.status', ['status' => 'expiring-soon']) }}" aria-expanded="false">
                            <i class="ri-medal-2-line"></i> <span>Expiring Soon</span>
                            <a class="nav-link menu-link {{ request()->is('contract*') ? 'active' : '' }}"
                                href="{{ route('contract.index') }}" aria-expanded="false">
                                <i class="bx bx-poll"></i> <span>Contract</span>
                            </a>
                    </li>
                @endcan --}}
                @can('home')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('home') ? 'active' : '' }}"
                            href="{{ route('home') }}" aria-expanded="false">
                            <i class="mdi mdi-speedometer"></i> <span>@lang('translation.dashboards')</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
