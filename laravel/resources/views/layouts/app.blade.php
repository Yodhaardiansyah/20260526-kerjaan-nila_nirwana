<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Nila Nirwana - Welcome</title>
        <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-[#F4F7FE]" x-data="{ sidebarOpen: false }">
        
        <div class="flex h-screen overflow-hidden">
            
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden relative">
                
                <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 md:hidden z-10">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <span class="ml-3 font-semibold text-lg text-[#0B1727]">Aqua Monitor</span>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
                    {{ $slot }}
                </main>
                
            </div>
        </div>
    </body>
</html>