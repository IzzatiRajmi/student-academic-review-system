<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in · Student Academic Review System</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
<main class="login-card" id="main">
    <div class="login-logo"><x-icon name="cap" /></div>
    <h1>Student Academic<br>Review System</h1>
    <p class="muted">Log in to access your account</p>

    @foreach (['success', 'error'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type }}" role="status"><span>{{ session($type) }}</span></div>
        @endif
    @endforeach
    @if ($errors->any())
        <div class="alert alert-error" role="alert"><x-icon name="x" /><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" novalidate data-loading-form>
        @csrf
        <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required
                   placeholder="Enter your username" value="{{ old('username') }}" @if (! old('username')) autofocus @endif>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <div class="password-wrap">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       placeholder="Enter your password" @if (old('username')) autofocus @endif>
                <button type="button" class="icon-btn pw-toggle" aria-label="Show password" data-pw-toggle>
                    <span class="show-ic"><x-icon name="eye" /></span><span class="hide-ic"><x-icon name="eye-off" /></span>
                </button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block" data-loading-text="Logging in…">Login</button>
        <button type="button" class="link-btn" data-open-modal="forgot-modal">Forgot password?</button>
    </form>
</main>

<div class="modal" id="forgot-modal" role="dialog" aria-modal="true" aria-labelledby="forgot-title" hidden>
    <div class="modal-box modal-sm">
        <div class="modal-icon"><x-icon name="info" /></div>
        <h2 id="forgot-title">Forgot Password</h2>
        <p>Please contact the faculty administrator to reset your password.</p>
        <div class="modal-actions"><button type="button" class="btn btn-primary" data-close-modal>OK</button></div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
