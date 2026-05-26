<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Monitoring') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error_influx'))
                <div class="p-4 mb-4 text-sm text-amber-800 rounded-lg bg-amber-100 border border-amber-200">
                    ⚠️ <b>Masalah Data Real-time:</b> {{ session('error_influx') }}
                </div>
            @endif
            
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">pH Air</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($realtimeData['ph'], 2) }}</h3>
                        @if($realtimeData['ph'] >= $settings->ph_min_limit && $realtimeData['ph'] <= $settings->ph_max_limit)
                            <span class="text-xs text-green-500 font-semibold mt-2 block">Normal</span>
                        @else
                            <span class="text-xs text-red-500 font-semibold mt-2 block">Di Luar Batas</span>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-500 text-xl font-bold">pH</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Suhu Air</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($realtimeData['temp'], 1) }} &deg;C</h3>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-500 text-xl font-bold">&deg;</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Kondisi Air</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $realtimeData['water'] == 1 ? 'Penuh' : 'Kurang' }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-500 text-xl font-bold">W</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Status Sistem</p>
                        <h3 class="text-2xl font-bold {{ $realtimeData['is_online'] ? 'text-green-600' : 'text-red-600' }} mt-1">
                            {{ $realtimeData['is_online'] ? 'Online' : 'Offline' }}
                        </h3>
                        <span class="text-xs text-gray-500 mt-2 block">{{ $realtimeData['uptime'] }}</span>
                    </div>
                    <div class="w-12 h-12 {{ $realtimeData['is_online'] ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500' }} rounded-full flex items-center justify-center text-xl">
                        {{ $realtimeData['is_online'] ? '✓' : '✗' }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Pengaturan Sistem</h3>
                    
                    <form action="{{ route('device.settings.update') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Mode Operasi</label>
                                <select name="mode" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="auto" {{ $settings->mode == 'auto' ? 'selected' : '' }}>Otomatis (Node-RED)</option>
                                    <option value="manual" {{ $settings->mode == 'manual' ? 'selected' : '' }}>Manual (Web)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Durasi Kuras (Detik)</label>
                                <input type="number" name="drain_duration_seconds" value="{{ $settings->drain_duration_seconds }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Batas pH Minimum</label>
                                <input type="text" name="ph_min_limit" value="{{ $settings->ph_min_limit }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Batas pH Maksimum</label>
                                <input type="text" name="ph_max_limit" value="{{ $settings->ph_max_limit }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md shadow hover:bg-blue-700 transition">Simpan Pengaturan</button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Kontrol Perangkat</h3>
                    
                    @if($settings->mode == 'auto')
                        <div class="p-3 mb-4 text-sm text-yellow-800 bg-yellow-100 rounded">
                            Ubah mode ke <b>Manual</b> untuk mengontrol relay secara langsung.
                        </div>
                    @endif

                    <div class="space-y-4 mt-4">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <div>
                                <p class="font-medium text-gray-700">Solenoid Kuras (R1)</p>
                                <span class="text-xs px-2 py-1 rounded {{ $realtimeData['relay1'] == 'ON' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">Status: {{ $realtimeData['relay1'] }}</span>
                            </div>
                            @if($settings->mode == 'manual')
                            <form action="{{ route('device.relay.control') }}" method="POST">
                                @csrf
                                <input type="hidden" name="relay" value="relay1">
                                <input type="hidden" name="state" value="{{ $realtimeData['relay1'] == 'ON' ? 'OFF' : 'ON' }}">
                                <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded-md text-sm hover:bg-gray-900 transition">
                                    {{ $realtimeData['relay1'] == 'ON' ? 'Matikan' : 'Nyalakan' }}
                                </button>
                            </form>
                            @endif
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b">
                            <div>
                                <p class="font-medium text-gray-700">Pompa Isi (R2)</p>
                                <span class="text-xs px-2 py-1 rounded {{ $realtimeData['relay2'] == 'ON' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">Status: {{ $realtimeData['relay2'] }}</span>
                            </div>
                            @if($settings->mode == 'manual')
                            <form action="{{ route('device.relay.control') }}" method="POST">
                                @csrf
                                <input type="hidden" name="relay" value="relay2">
                                <input type="hidden" name="state" value="{{ $realtimeData['relay2'] == 'ON' ? 'OFF' : 'ON' }}">
                                <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded-md text-sm hover:bg-gray-900 transition">
                                    {{ $realtimeData['relay2'] == 'ON' ? 'Matikan' : 'Nyalakan' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100 mt-6 lg:col-span-3">
                    <h3 class="text-lg font-bold text-red-800 mb-2">Tindakan Darurat & Pemeliharaan</h3>
                    <p class="text-sm text-gray-600 mb-4">Gunakan fitur ini jika sistem otomatisasi mengalami kendala (nyangkut) atau alat tidak merespon.</p>
                    
                    <div class="flex flex-wrap gap-4">
                        <form action="{{ route('device.maintenance.reset') }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin ingin mereset siklus Node-RED? Ini akan mematikan semua pompa dan mereset status logika otomatisasi.')" class="bg-yellow-500 text-white px-4 py-2 rounded-md shadow hover:bg-yellow-600 transition flex items-center gap-2">
                                <span>🔄 Reset Siklus (Node-RED)</span>
                            </button>
                        </form>

                        <form action="{{ route('device.maintenance.restart') }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin ingin merestart alat fisik? Alat akan offline selama proses booting.')" class="bg-red-600 text-white px-4 py-2 rounded-md shadow hover:bg-red-700 transition flex items-center gap-2">
                                <span>🔌 Restart Perangkat (ESP32)</span>
                            </button>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</x-app-layout>