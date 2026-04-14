{{-- resources/views/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Kubix</title>

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- PWA -->
    {{-- <link rel="manifest" href="http://localhost:5173/manifest.webmanifest"> --}}
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icons/icon-512x512.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased h-full" style="background:#0f172a;">

    <div id="app">
        {{-- Loader inicial mientras Vue monta --}}
        <div style="
            height:100vh;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            background:#0f172a;
            gap:16px;
        ">
            <div style="
                width:40px;height:40px;
                border:2px solid rgba(59,130,246,0.2);
                border-top-color:#3b82f6;
                border-radius:50%;
                animation:spin 0.8s linear infinite;
            "></div>
            <style>
                @keyframes spin {
                    to {
                        transform: rotate(360deg)
                    }
                }
            </style>
        </div>
    </div>

    @stack('scripts')

</body>

</html>