<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Not found · Student Academic Review System</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
<main class="login-card">
    <h1>Request not found</h1>
    <p class="muted">The page or request does not exist, or you do not have access to it.</p>
    <p style="margin-top:20px">
        <a class="btn btn-primary" href="{{ auth()->check() ? route(auth()->user()->homeRoute()) : route('login') }}">Back to Dashboard</a>
    </p>
</main>
</body>
</html>
