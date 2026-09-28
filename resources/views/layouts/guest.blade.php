<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#eff6ff',
                                500: '#0080ff',
                                600: '#0070e0',
                                700: '#005bb5',
                            },
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.42);
                backdrop-filter: blur(28px) saturate(170%);
                -webkit-backdrop-filter: blur(28px) saturate(170%);
                border: 1.5px solid rgba(255, 255, 255, 0.65);
                box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45), 
                            0 0 25px rgba(255, 255, 255, 0.25) inset,
                            0 10px 20px -5px rgba(15, 23, 42, 0.2);
            }
            .glass-field-container {
                background: rgba(255, 255, 255, 0.25);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border: 1.5px solid rgba(148, 163, 184, 0.55);
                transition: all 0.2s ease-in-out;
            }
            .glass-field-container:focus-within {
                border-color: #2563eb;
                background: rgba(255, 255, 255, 0.45);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            }
            /* Reset all Tailwind Forms and browser input defaults */
            .glass-input {
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
                outline: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .glass-input:focus {
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
                outline: none !important;
                --tw-ring-color: transparent !important;
                --tw-ring-shadow: none !important;
                --tw-ring-offset-shadow: none !important;
            }
            /* Chrome / Edge / Safari Autofill fix - completely transparent background */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus, 
            input:-webkit-autofill:active {
                -webkit-background-clip: text !important;
                -webkit-text-fill-color: #0f172a !important;
                transition: background-color 500000s ease-in-out 0s;
                box-shadow: none !important;
                -webkit-box-shadow: none !important;
            }
        </style>
    </head>
    <body class="h-full w-full antialiased text-slate-800 bg-[#090d16] selection:bg-blue-600 selection:text-white overflow-hidden m-0 p-0">
        <div class="h-screen h-[100dvh] w-full flex items-center justify-center p-4 sm:p-6 bg-cover bg-center bg-no-repeat relative overflow-hidden" style="background-image: url('{{ asset('images/login-bg.jpg') }}');">
            <!-- Subtle ambient glow / vignette -->
            <div class="absolute inset-0 bg-slate-950/20 backdrop-brightness-95 pointer-events-none"></div>

            <div class="relative z-10 w-full flex items-center justify-center">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
