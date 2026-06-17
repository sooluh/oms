<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', config('app.name', 'Oh My School')) — {{ config('app.name', 'Oh My School') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-canvas font-sans antialiased">
    <main>
        @yield('content')
    </main>

    <footer class="mx-auto max-w-2xl px-4 pb-8 pt-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs text-muted-soft">
            Powered by
            <a href="https://oms.my.id" target="_blank" rel="noopener noreferrer"
                class="underline underline-offset-2 hover:text-muted">Oh My School</a>
            &middot;
            <a href="https://github.com/sooluh/oms" target="_blank" rel="noopener noreferrer"
                class="underline underline-offset-2 hover:text-muted">GitHub</a>
        </p>
    </footer>
</body>

</html>
