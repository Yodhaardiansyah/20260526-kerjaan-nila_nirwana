<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - Nila Nirwana</title>
    
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F4F7FE] text-[#0B1727] min-h-screen flex items-center justify-center relative overflow-hidden p-4">

    <div class="absolute top-0 left-0 w-[30rem] h-[30rem] bg-gradient-to-br from-blue-100 to-transparent rounded-full -mt-40 -ml-40 z-0 opacity-70"></div>
    <div class="absolute bottom-0 right-0 w-[25rem] h-[25rem] bg-gradient-to-tr from-cyan-100 to-transparent rounded-full -mb-40 -mr-40 z-0 opacity-60"></div>

    <div class="relative z-10 w-full max-w-md px-8 py-10 bg-white shadow-xl shadow-blue-900/5 rounded-[2rem] border border-white/50 backdrop-blur-sm">
        
        <div class="flex flex-col items-center justify-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-[#0B1727] flex items-center justify-center text-blue-500 shadow-lg mb-5">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 00-.707.293l-4.5 4.5A5.5 5.5 0 0010 16.5a5.5 5.5 0 005.207-9.707l-4.5-4.5A1 1 0 0010 2zM8.5 7.5a1 1 0 112 0v2.086l1.293 1.293a1 1 0 01-1.414 1.414L9 10.586V7.5z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black tracking-wide text-[#0B1727]">Selamat Datang</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">Masuk ke sistem pemantauan kolam.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 font-medium text-sm text-green-700 bg-green-50 p-4 rounded-xl border border-green-200 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email Akses</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="pl-11 w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 outline-none transition" placeholder="admin@nilanirwana.com">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-sm font-bold text-gray-700">Kata Sandi</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">Lupa Sandi?</a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="pl-11 w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 outline-none transition" placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center pt-1">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-gray-50 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                <label for="remember_me" class="ml-2 text-sm font-medium text-gray-600 cursor-pointer">Ingat sesi saya</label>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full bg-[#0B1727] text-white px-5 py-4 rounded-xl text-sm font-bold hover:bg-gray-800 transition shadow-md flex justify-center items-center gap-2">
                    Masuk ke Sistem
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
            
            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>
</body>
</html>