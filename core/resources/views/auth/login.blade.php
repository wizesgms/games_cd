<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SKYPLAY - Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdn.g11329.net/assets/iziToast/dist/css/iziToast.min.css">
</head>

<body class="hold-transition text-sm login-page" style="background: url('assets/images/login-background.jpg')">
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('assets/images/SKYPLAY_logo.png') }}" alt="" width="235">
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Login</p>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="agentcode" value="{{ old('agentcode') }}" class="form-control"
                            placeholder="agentcode" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="number" name="captcha" class="form-control" placeholder="Validation" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 text-center">
                        <img src="{{ url('/captcha') }}" >
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Log In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.g11329.net/assets/iziToast/dist/js/iziToast.min.js" type="text/javascript">
    </script>

    @if (session('error'))
    <script>
        iziToast.error({
            title: 'Error',
            position: 'topRight',
            message: '{{ session('error') }}',
        });
    </script>
    @endif

    @error('agentcode')
    <script>
        iziToast.error({
            title: 'Error',
            position: 'topRight',
            message: '{{ $message }}',
        });
    </script>
    @enderror

    @error('password')
    <script>
        iziToast.error({
            title: 'Error',
            position: 'topRight',
            message: '{{ $message }}',
        });
    </script>
    @enderror
</body>

</html>
