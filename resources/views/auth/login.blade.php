<!DOCTYPE html>
<html lang="so">
<head>
<meta charset="UTF-8">
<title>Login - GHTCS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'">
        <h2>Garowe House Tax Collection System</h2>
        <p>Fadlan gal si aad u isticmaasho nidaamka</p>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="text-start">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>

                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Log In</button>
        </form>

        <p class="mt-3" style="font-size:11.5px; color:#94a3b8;">
            Admin: admin / admin123 &nbsp;|&nbsp; Collector: collector / collector123
        </p>
    </div>
</div>
</body>
</html>
