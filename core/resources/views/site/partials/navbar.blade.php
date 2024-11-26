<nav class="main-header navbar navbar-expand navbar-white navbar-light text-xs">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="{{ asset('assets/dist/img/avatar5.png') }}" class="user-image img-circle elevation-2" alt="User Image">
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-secondary">
                    <img src="{{ asset('assets/dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
                    <p>{{ auth()->user()->name }}
                        <small>{{ auth()->user()->agentcode }}</small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <a href="{{ route('profile') }}" class="btn btn-dark btn-sm"><i class="fas fa-user"></i> Profile</a>
                        <button type="submit" class="btn btn-danger btn-sm float-right"><i class="fas fa-arrow-circle-right"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>
