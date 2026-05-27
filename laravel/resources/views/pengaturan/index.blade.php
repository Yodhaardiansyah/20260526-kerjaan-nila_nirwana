<x-app-layout>
    <div class="space-y-6 max-w-5xl mx-auto">

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[#0B1727]">Pengaturan Sistem</h2>
            <p class="text-sm text-gray-500 mt-1">
                Konfigurasi batas sensor, mode operasi, dan pemeliharaan alat.
            </p>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="p-6 border-b border-gray-50">
                <h3 class="text-lg font-bold text-[#0B1727] flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Parameter Otomatisasi (Node-RED)
                </h3>
            </div>

            <form action="{{ route('device.settings.update') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <!-- MODE -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mode Operasi
                        </label>

                        <select name="mode"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 outline-none transition">
                            <option value="auto" {{ $settings->mode == 'auto' ? 'selected' : '' }}>
                                Otomatis (Dikontrol oleh Node-RED)
                            </option>
                            <option value="manual" {{ $settings->mode == 'manual' ? 'selected' : '' }}>
                                Manual (Hanya dikontrol pengguna)
                            </option>
                        </select>

                        <p class="text-xs text-gray-500 mt-1.5">
                            Pilih "Manual" jika ingin menghentikan sementara aktivitas otomatis.
                        </p>
                    </div>

                    <!-- PH RANGE -->
                    <div class="md:col-span-2">

                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Rentang pH Normal
                        </label>

                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5">

                            <!-- INPUT -->
                            <div class="grid grid-cols-2 gap-4 mb-6">

                                <div>
                                    <label class="text-xs text-gray-500">
                                        pH Minimum
                                    </label>

                                    <div class="relative mt-1">
                                        <input
                                            id="ph_min_input"
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            max="14"
                                            name="ph_min_limit"
                                            value="{{ $settings->ph_min_limit }}"
                                            class="w-full bg-white border border-gray-200 rounded-xl p-3 pr-10 text-sm"
                                        >
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                            pH
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs text-gray-500">
                                        pH Maksimum
                                    </label>

                                    <div class="relative mt-1">
                                        <input
                                            id="ph_max_input"
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            max="14"
                                            name="ph_max_limit"
                                            value="{{ $settings->ph_max_limit }}"
                                            class="w-full bg-white border border-gray-200 rounded-xl p-3 pr-10 text-sm"
                                        >
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                            pH
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <!-- SLIDER -->
                            <div class="relative h-10">

                                <!-- TRACK -->
                                <div class="absolute top-1/2 left-0 w-full h-2 bg-gray-200 rounded-full -translate-y-1/2">
                                </div>

                                <!-- FILL -->
                                <div id="range_fill"
                                     class="absolute top-1/2 h-2 bg-blue-500 rounded-full -translate-y-1/2">
                                </div>

                                <!-- MIN -->
                                <input
                                    id="ph_min_slider"
                                    type="range"
                                    min="0"
                                    max="14"
                                    step="0.1"
                                    value="{{ $settings->ph_min_limit }}"
                                    class="range-slider"
                                >

                                <!-- MAX -->
                                <input
                                    id="ph_max_slider"
                                    type="range"
                                    min="0"
                                    max="14"
                                    step="0.1"
                                    value="{{ $settings->ph_max_limit }}"
                                    class="range-slider"
                                >
                            </div>

                            <div class="flex justify-between text-xs text-gray-400 mt-2">
                                <span>0</span>
                                <span>14</span>
                            </div>

                            <p class="text-xs text-gray-500 mt-3">
                                Geser kiri dan kanan untuk menentukan rentang pH normal.
                            </p>

                        </div>
                    </div>

                    <!-- DURASI -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Durasi Solenoid Kuras Air
                        </label>

                        <div class="relative">
                            <input
                                type="number"
                                name="drain_duration_seconds"
                                value="{{ $settings->drain_duration_seconds }}"
                                class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 pr-16"
                            >

                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 text-sm font-bold">
                                Detik
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-1.5">
                            Lama waktu Solenoid Valve menyala saat pH tidak normal.
                        </p>
                    </div>

                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="bg-[#0B1727] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-800 transition shadow-sm">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="p-6 border-b border-red-50 bg-red-50/30">
                <h3 class="text-lg font-bold text-red-800 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Tindakan Darurat & Pemeliharaan
                </h3>
            </div>
            
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-6">Gunakan fitur ini jika sistem otomatisasi mengalami kendala (nyangkut), sensor membaca error, atau alat tidak merespon perintah.</p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <form action="{{ route('device.maintenance.reset') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" onclick="return confirm('Yakin ingin mereset siklus Node-RED? Ini akan mematikan semua pompa dan mereset status logika otomatisasi.')" class="w-full bg-white border-2 border-yellow-500 text-yellow-600 px-4 py-3 rounded-xl font-bold hover:bg-yellow-50 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset Siklus (Node-RED)
                        </button>
                    </form>

                    <form action="{{ route('device.maintenance.restart') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" onclick="return confirm('Yakin ingin merestart alat fisik? Alat akan offline selama proses booting.')" class="w-full bg-red-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-red-700 transition flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Restart Perangkat (ESP32)
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- STYLE -->
    <style>
        .range-slider{
            position:absolute;
            width:100%;
            height:22px;
            appearance:none;
            background:transparent;
            pointer-events:none;
        }

        .range-slider::-webkit-slider-thumb{
            appearance:none;
            width:22px;
            height:22px;
            border-radius:999px;
            background:#2563eb;
            border:3px solid white;
            box-shadow:0 2px 6px rgba(0,0,0,.25);
            cursor:pointer;
            pointer-events:auto;
        }

        .range-slider::-moz-range-thumb{
            width:22px;
            height:22px;
            border:none;
            border-radius:999px;
            background:#2563eb;
            cursor:pointer;
            pointer-events:auto;
        }
    </style>

    <!-- SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function(){

            const minSlider=document.getElementById('ph_min_slider');
            const maxSlider=document.getElementById('ph_max_slider');

            const minInput=document.getElementById('ph_min_input');
            const maxInput=document.getElementById('ph_max_input');

            const fill=document.getElementById('range_fill');

            const maxPH=14;

            function updateFill(){
                let min=parseFloat(minSlider.value);
                let max=parseFloat(maxSlider.value);

                let left=(min/maxPH)*100;
                let width=((max-min)/maxPH)*100;

                fill.style.left=left+'%';
                fill.style.width=width+'%';
            }

            function sliderUpdate(){

                let min=parseFloat(minSlider.value);
                let max=parseFloat(maxSlider.value);

                if(min>max){
                    min=max;
                    minSlider.value=min;
                }

                if(max<min){
                    max=min;
                    maxSlider.value=max;
                }

                minInput.value=min;
                maxInput.value=max;

                updateFill();
            }

            function inputUpdate(){

                let min=parseFloat(minInput.value);
                let max=parseFloat(maxInput.value);

                if(min>max){
                    min=max;
                    minInput.value=min;
                }

                if(max<min){
                    max=min;
                    maxInput.value=max;
                }

                minSlider.value=min;
                maxSlider.value=max;

                updateFill();
            }

            minSlider.addEventListener('input', sliderUpdate);
            maxSlider.addEventListener('input', sliderUpdate);

            minInput.addEventListener('input', inputUpdate);
            maxInput.addEventListener('input', inputUpdate);

            updateFill();
        });
    </script>
</x-app-layout>