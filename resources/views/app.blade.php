<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="app_url" content="{{ config('app.url') }}">
        <meta name="app_name" content="{{ config('app.name') }}">
        <meta name="app_version" content="{{ Inertia\Inertia::getVersion() }}">
        <meta name="company_name" content="{{ config('app.name') }}">
        <meta name="company_acronym" content="{{ preg_replace('/\b(\w)|./', '$1', config('app.name')) }}">
        <meta property="og:locale" content="es_ES"/>
        <meta property="og:url" content="{{ config('app.url') }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="{{ config('app.name') }}"/>
        <meta property="og:description" content="{{ config('app.description') }}"/>
        <meta name="description" content="{{ config('app.description') }}">
        <meta name="theme-color" content="#b91c1c">
        <meta property="og:image" content="{{ url('images/logo.png') }}">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="512">
        <meta property="og:image:height" content="512">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
        <link rel="icon" href="{{ asset('images/favicon.ico') }}" sizes="32x32"><!-- 16, 32 and 48 -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}"><!-- 180×180 -->
        <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

        <title inertia>{{ config('app.name', 'RedstonePanel') }}</title>

        <!-- Fonts: served from public/fonts (see resources/css/fonts.css). The icons are preloaded, as every page uses them -->
        <link rel="preload" href="{{ asset('fonts/material-symbols-outlined.woff2') }}" as="font" type="font/woff2" crossorigin>

        <!-- The theme (the one the user chose, or the one of the system), before anything is painted: see the theme store -->
        <script>
            (() => {
                let mode = 'system'
                try { mode = localStorage.getItem('theme') || mode } catch (e) {}
                const dark = mode === 'dark' || (mode !== 'light' && matchMedia('(prefers-color-scheme: dark)').matches)
                document.documentElement.classList.toggle('app-dark', dark)
            })()
        </script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
