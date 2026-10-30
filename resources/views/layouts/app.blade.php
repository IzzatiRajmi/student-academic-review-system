@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $active = $active ?? '';
    $nav = $isAdmin
        ? [
            ['dashboard', 'admin.dashboard', 'home', 'Dashboard'],
            ['pending', 'admin.requests.pending', 'clock', 'Pending Requests'],
            ['all', 'admin.requests.index', 'list', 'All Requests'],
        ]
        : [
            ['dashboard', 'student.dashboard', 'home', 'Dashboard'],
            ['my', 'student.requests.index', 'file', 'My Requests'],
            ['add', 'student.requests.create', 'plus', 'Add Request'],
        ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} · Student Academic Review System</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<header class="topbar">
    <button class="icon-btn menu-toggle" type="button" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false" data-menu-toggle>
        <x-icon name="menu" />
    </button>
    <a class="brand" href="{{ route($user->homeRoute()) }}">
        <x-icon name="cap" class="icon brand-icon" />
        <span>Student Academic Review System</span>
    </a>
    <div class="userchip" title="{{ $user->name }}">
        <span class="avatar"><x-icon name="user" /></span>
        <span class="userchip-role">{{ $isAdmin ? 'Administrator' : 'Student' }}</span>
    </div>
</header>

<div class="shell">
    <aside class="sidebar" id="sidebar">
        <nav aria-label="Main">
            @foreach ($nav as [$key, $route, $icon, $label])
                <a class="nav-link {{ $active === $key ? 'active' : '' }}" href="{{ route($route) }}"
                   @if ($active === $key) aria-current="page" @endif>
                    <x-icon :name="$icon" /><span>{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <button type="button" class="nav-link logout-link" data-open-modal="logout-modal">
            <x-icon name="logout" /><span>Logout</span>
        </button>
    </aside>
    <div class="sidebar-backdrop" data-menu-close></div>

    <main class="main" id="main" tabindex="-1">
        @foreach (['success', 'error'] as $type)
            @if (session($type))
                <div class="alert alert-{{ $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
                    <x-icon :name="$type === 'error' ? 'x' : 'check'" />
                    <span>{{ session($type) }}</span>
                    <button type="button" class="alert-close" aria-label="Dismiss" data-dismiss>&times;</button>
                </div>
            @endif
        @endforeach

        @yield('content')
    </main>
</div>

@include('partials.modals')
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
