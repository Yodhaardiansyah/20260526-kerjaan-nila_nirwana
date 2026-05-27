<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-gray-900/50 md:hidden" x-cloak></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-[#0B1727] text-gray-300 transition-transform duration-300 transform md:translate-x-0 md:static md:inset-auto flex flex-col h-full">
    
    <div class="flex items-center px-6 py-6 h-20">
        <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 2a1 1 0 00-.707.293l-4.5 4.5A5.5 5.5 0 0010 16.5a5.5 5.5 0 005.207-9.707l-4.5-4.5A1 1 0 0010 2zM8.5 7.5a1 1 0 112 0v2.086l1.293 1.293a1 1 0 01-1.414 1.414L9 10.586V7.5z" clip-rule="evenodd"></path>
        </svg>
        <div class="ml-3">
            <h1 class="text-white font-bold text-lg tracking-wide leading-tight">Aqua Monitor</h1>
            <p class="text-xs text-blue-400 font-medium">Monitoring Kolam</p>
        </div>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto custom-scrollbar">
        
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="font-medium text-sm">Dashboard</span>
        </a>

        <a href="{{ route('grafik.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('grafik.index') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
            <span class="font-medium text-sm">Grafik Data</span>
        </a>

        <a href="{{ route('logs.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('logs.index') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="font-medium text-sm">Riwayat Data</span>
        </a>

        <a href="{{ route('status.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('status.index') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            <span class="font-medium text-sm">Status Sistem</span>
        </a>

        <a href="{{ route('pengaturan.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('pengaturan.index') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="font-medium text-sm">Pengaturan</span>
        </a>

    </nav>

    <div class="px-6 py-6 border-t border-gray-800 bg-[#08121f]">
        
        <div class="flex items-center space-x-3 mb-6">
            <div class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </div>
            <div>
                <p class="text-xs font-semibold text-white">Sistem Online</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Terhubung</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full text-left text-sm text-gray-400 hover:text-red-400 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar ({{ Auth::user()->name }})
            </button>
        </form>
    </div>
</aside>