<aside class="main-sidebar text-xs sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('index') }}" class="brand-link text-center">
        <img src="{{ asset('assets/images/SKYPLAY_logo.png') }}" alt="AdminLTE Logo" style="width: 140px;">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('index') }}" class="nav-link {{ request()->routeIs('index') ? 'active ' : '' }}">
                        <i class="nav-icon fas fa-home text-sm"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('player.list') }}" class="nav-link {{ request()->routeIs('player.list') ? 'active ' : '' }}">
                        <i class="nav-icon fas fa-users text-sm"></i>
                        <p>Players</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('api.list') }}" class="nav-link {{ request()->routeIs('api.list') ? 'active ' : '' }}">
                        <i class="nav-icon fas fa-cogs text-sm"></i>
                        <p>Api</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
