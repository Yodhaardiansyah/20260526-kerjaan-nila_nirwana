<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nila Nirwana - Welcome</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F4F7FE] text-[#0B1727] min-h-screen flex flex-col justify-between relative overflow-hidden">

    <div class="absolute top-0 right-0 w-[30rem] h-[30rem] bg-gradient-to-br from-blue-100 to-transparent rounded-full -mt-40 -mr-40 z-0 opacity-70"></div>
    <div class="absolute bottom-0 left-0 w-[25rem] h-[25rem] bg-gradient-to-tr from-cyan-100 to-transparent rounded-full -mb-40 -ml-40 z-0 opacity-60"></div>

    <header class="relative z-10 max-w-7xl mx-auto w-full px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0B1727] flex items-center justify-center text-blue-500 shadow-md">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 00-.707.293l-4.5 4.5A5.5 5.5 0 0010 16.5a5.5 5.5 0 005.207-9.707l-4.5-4.5A1 1 0 0010 2zM8.5 7.5a1 1 0 112 0v2.086l1.293 1.293a1 1 0 01-1.414 1.414L9 10.586V7.5z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <span class="text-xl font-black tracking-wide text-[#0B1727]">Nila Nirwana</span>
            </div>
        </div>

        @if (Route::has('login'))
            <nav class="flex gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-bold bg-[#0B1727] text-white px-5 py-2.5 rounded-xl hover:bg-gray-800 transition shadow-sm">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-[#0B1727] hover:text-blue-600 transition px-4 py-2">
                        Masuk
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-bold bg-white border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            Daftar
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <main class="relative z-10 max-w-4xl mx-auto w-full px-6 py-12 flex-1 flex flex-col items-center justify-center text-center">
        <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-widest rounded-full border border-blue-100 mb-6 shadow-sm">
            Sistem Monitoring & Otomatisasi Kolam Digital
        </span>
        
        <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-[#0B1727] leading-none mb-6">
            Kelola Kualitas Air Kolam <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Secara Real-Time & Cerdas</span>
        </h1>
        
        <p class="text-base sm:text-lg text-gray-500 max-w-2xl mb-10 font-medium leading-relaxed">
            Pantau pergerakan kadar pH, suhu air, dan kendalikan sistem pengurasan otomatis kapan saja dan di mana saja langsung dari genggaman Anda.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-[#0B1727] text-white px-8 py-4 rounded-xl font-bold hover:bg-gray-800 transition shadow-md flex items-center justify-center gap-2">
                    <span>Kembali ke Dashboard</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-[#0B1727] text-white px-8 py-4 rounded-xl font-bold hover:bg-gray-800 transition shadow-md flex items-center justify-center gap-2 text-center">
                    <span>Mulai Monitoring Sekarang</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l4-4m0 0l-4-4m4 4H3m14 2a9 9 0 110-18v18z"></path></svg>
                </a>
            @endif
        </div>
    </main>

    <footer class="relative z-10 w-full text-center py-6 text-xs text-gray-400 font-medium border-t border-gray-100 bg-white/40 backdrop-blur-sm">
        &copy; {{ date('Y') }} Nila Nirwana. All rights reserved.
    </footer>

</body>
</html>