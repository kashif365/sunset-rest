<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in — SBE Admin</title>
    <link rel="icon" href="/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto+Condensed:wght@300..700&display=swap" rel="stylesheet">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #69001F, #B51F2A);">
    <main class="card shadow-lg m-3" style="width: 26rem; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div style="font-size: 2.5rem;">🥯</div>
                <h1 class="h4 mt-2" style="font-family: 'Anton', sans-serif; text-transform: uppercase; color: #69001F;">Sunset Bagel Exchange</h1>
                <p class="text-body-secondary mb-0">Sign in to the admin panel</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.login.attempt') }}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email"
                           value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required autocomplete="current-password">
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">Keep me signed in</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Sign In</button>
            </form>
        </div>
    </main>
</body>
</html>
