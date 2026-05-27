<x-app-layout>
    <div class="space-y-6">
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[#0B1727]">Status Koneksi & Perangkat</h2>
            <p class="text-sm text-gray-500 mt-1">Pemantauan *uptime* dan kesehatan perangkat keras IoT.</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border {{ $status['is_online'] ? 'border-green-200' : 'border-red-200' }} flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br {{ $status['is_online'] ? 'from-green-50 to-transparent' : 'from-red-50 to-transparent' }} rounded-full -mt-20 -mr-20 z-0"></div>

            <div class="relative z-10 flex-shrink-0">
                @if($status['is_online'])
                    <div class="w-32 h-32 rounded-full bg-green-100 flex items-center justify-center border-4 border-green-50 shadow-inner">
                        <div class="relative flex h-16 w-16">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-16 w-16 bg-green-500 flex items-center justify-center text-white">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                    </div>
                @else
                    <div class="w-32 h-32 rounded-full bg-red-100 flex items-center justify-center border-4 border-red-50 shadow-inner">
                        <div class="h-16 w-16 rounded-full bg-red-500 flex items-center justify-center text-white shadow-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                    </div>
                @endif
            </div>

            <div class="relative z-10 text-center md:text-left">
                <span class="px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full {{ $status['is_online'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} mb-3 inline-block">
                    Kondisi Sistem Keseluruhan
                </span>
                <h3 class="text-4xl font-black text-gray-900 mb-2">
                    {{ $status['is_online'] ? 'SISTEM ONLINE' : 'SISTEM OFFLINE' }}
                </h3>
                <p class="text-gray-600 font-medium max-w-lg">{{ $status['message'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Terakhir Mengirim Data</h4>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $status['last_seen_human'] }}</p>
                        <p class="text-sm text-gray-400 mt-1">Tepatnya pada: {{ $status['last_seen'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-4">Pemeriksaan Sub-Sistem</h4>
                <div class="space-y-3">
                    
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-gray-700 font-medium">Mikrokontroler ESP32</span>
                        <span class="font-bold {{ $status['is_online'] ? 'text-green-500' : 'text-red-500' }}">{{ $status['is_online'] ? 'Online' : 'Terputus' }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-gray-700 font-medium">Node-RED Automasi</span>
                        <span class="font-bold text-green-500">Aktif</span> </div>

                    <div class="flex justify-between items-center pb-2">
                        <span class="text-gray-700 font-medium">Database InfluxDB</span>
                        <span class="font-bold {{ $status['last_seen'] != '-' ? 'text-green-500' : 'text-red-500' }}">{{ $status['last_seen'] != '-' ? 'Aktif' : 'Error' }}</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>