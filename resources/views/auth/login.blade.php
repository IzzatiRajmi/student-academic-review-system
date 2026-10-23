<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log in | Student Academic Review System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-white text-[#1A2233] antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">

        {{-- LEFT: brand panel --}}
        <aside class="flex flex-col justify-between gap-10 bg-[#1F3A7A] p-8 text-white lg:w-[42%] lg:p-14">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 9l9-5 9 5-9 5-9-5z"/>
                        <path d="M7 11.5V16c0 1.5 2.2 3 5 3s5-1.5 5-3v-4.5"/>
                    </svg>
                </div>
                
                <span class="text-[15px] font-semibold">[FAKULTI KOMPUTERAN DAN META-TEKNOLOGI]</span>
            </div>

            <div class="space-y-5">
                <h1 class="text-3xl font-bold leading-tight lg:text-[44px]">Student Academic Review System</h1>
                <p class="max-w-sm text-[17px] leading-relaxed text-[#DCE4F5]">
                    Submit a review request for an academic record or decision, and follow its status until the faculty decides.
                </p>
            </div>

            <p class="text-sm text-[#C3CFEA]">DES3083 · Group 3</p>
        </aside>

        {{-- RIGHT: login form --}}
        <main class="flex flex-1 items-center justify-center p-8 lg:p-14">
            <div class="w-full max-w-[400px]">

                <div class="mb-6 space-y-2">
                    <h2 class="text-3xl font-bold">Log in</h2>
                    <p class="text-[15px] text-[#4D5870]">Use the account given by the faculty.</p>
                </div>

                {{-- Status message (e.g. after password reset) --}}
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="you@siswa.upsi.edu.my"
                               class="h-12 w-full rounded-[10px] border border-[#C9D0DD] px-3.5 text-[15px] focus:border-[#1F3A7A] focus:ring-[#1F3A7A]">
                        @error('email')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-2">
                        <div class="flex items-baseline justify-between">
                            <label for="password" class="block text-sm font-semibold">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[13px] text-[#2557C7] hover:text-[#173C8F]">Forgot password?</a>
                            @endif
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="h-12 w-full rounded-[10px] border border-[#C9D0DD] px-3.5 text-[15px] focus:border-[#1F3A7A] focus:ring-[#1F3A7A]">
                        @error('password')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2.5">
                        <input id="remember_me" name="remember" type="checkbox"
                               class="h-[18px] w-[18px] rounded border-[#C9D0DD] text-[#1F3A7A] focus:ring-[#1F3A7A]">
                        <label for="remember_me" class="text-sm text-[#4D5870]">Remember me</label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="h-[50px] w-full rounded-[10px] bg-[#1F3A7A] text-base font-semibold text-white hover:bg-[#173066] focus:outline-none focus:ring-2 focus:ring-[#1F3A7A] focus:ring-offset-2">
                        Log in
                    </button>

                    <p class="text-center text-[13px] text-[#5B6478]">No account? Contact the faculty administrator.</p>
                </form>
            </div>
        </main>
    </div>
</body>
</html>