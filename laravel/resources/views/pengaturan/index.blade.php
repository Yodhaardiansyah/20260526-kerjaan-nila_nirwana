<x-app-layout>
    <div class="space-y-6 max-w-5xl mx-auto">

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[#0B1727]">
                Pengaturan Sistem
            </h2>

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

                        <select
                            id="mode_select"
                            name="mode"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 outline-none transition"
                        >
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

                    <!-- MANUAL CONTROL -->
                    <div
                        id="manual_control_section"
                        class="md:col-span-2 hidden"
                    >
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">

                            <div class="flex items-start justify-between gap-4">

                                <div>
                                    <h4 class="font-bold text-yellow-800">
                                        Kontrol Manual Relay
                                    </h4>

                                    <p class="text-sm text-yellow-700 mt-1">
                                        Mode manual aktif. Anda dapat mengontrol relay secara langsung.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    id="openRelayModal"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-sm"
                                >
                                    Buka Kontrol
                                </button>

                            </div>

                        </div>
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

                                <div class="absolute top-1/2 left-0 w-full h-2 bg-gray-200 rounded-full -translate-y-1/2">
                                </div>

                                <div id="range_fill"
                                     class="absolute top-1/2 h-2 bg-blue-500 rounded-full -translate-y-1/2">
                                </div>

                                <input
                                    id="ph_min_slider"
                                    type="range"
                                    min="0"
                                    max="14"
                                    step="0.1"
                                    value="{{ $settings->ph_min_limit }}"
                                    class="range-slider"
                                >

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
                    <button
                        type="submit"
                        class="bg-[#0B1727] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-800 transition shadow-sm"
                    >
                        Simpan Pengaturan
                    </button>
                </div>

            </form>
        </div>

        <!-- MAINTENANCE -->
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">

            <div class="p-6 border-b border-red-50 bg-red-50/30">
                <h3 class="text-lg font-bold text-red-800 flex items-center gap-2">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>

                    Tindakan Darurat & Pemeliharaan
                </h3>
            </div>

            <div class="p-6">

                <p class="text-sm text-gray-600 mb-6">
                    Gunakan fitur ini jika sistem otomatisasi mengalami kendala.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">

                    <form action="{{ route('device.maintenance.reset') }}" method="POST" class="flex-1">
                        @csrf

                        <button
                            type="submit"
                            onclick="return confirm('Yakin ingin mereset siklus Node-RED?')"
                            class="w-full bg-white border-2 border-yellow-500 text-yellow-600 px-4 py-3 rounded-xl font-bold hover:bg-yellow-50 transition flex items-center justify-center gap-2"
                        >
                            Reset Siklus (Node-RED)
                        </button>
                    </form>

                    <form action="{{ route('device.maintenance.restart') }}" method="POST" class="flex-1">
                        @csrf

                        <button
                            type="submit"
                            onclick="return confirm('Yakin ingin merestart alat fisik?')"
                            class="w-full bg-red-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-red-700 transition"
                        >
                            Restart Perangkat (ESP32)
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </div>

    <!-- MODAL -->
    <div
        id="relayModal"
        class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4"
    >

        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">

            <!-- HEADER -->
            <div class="flex items-center justify-between p-5 border-b">

                <div>
                    <h3 class="text-lg font-bold text-[#0B1727]">
                        Kontrol Relay Manual
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Kendalikan relay secara langsung.
                    </p>
                </div>

                <button
                    type="button"
                    id="closeRelayModal"
                    class="text-gray-400 hover:text-gray-600 text-xl"
                >
                    ✕
                </button>

            </div>

            <!-- CONTENT -->
            <div class="p-5 space-y-4">

                <!-- RELAY 1 -->
                <div class="border rounded-2xl p-4">

                    <div class="flex items-center justify-between">

                        <div>
                            <h4 class="font-bold text-gray-800">
                                Relay 1
                            </h4>

                            <p class="text-sm text-gray-500">
                                Status:
                                <span
                                    id="relay1Status"
                                    class="font-bold text-red-500"
                                >
                                    OFF
                                </span>
                            </p>
                        </div>

                        <button
                            type="button"
                            onclick="toggleRelay(1)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold"
                        >
                            Toggle
                        </button>

                    </div>

                </div>

                <!-- RELAY 2 -->
                <div class="border rounded-2xl p-4">

                    <div class="flex items-center justify-between">

                        <div>
                            <h4 class="font-bold text-gray-800">
                                Relay 2
                            </h4>

                            <p class="text-sm text-gray-500">
                                Status:
                                <span
                                    id="relay2Status"
                                    class="font-bold text-red-500"
                                >
                                    OFF
                                </span>
                            </p>
                        </div>

                        <button
                            type="button"
                            onclick="toggleRelay(2)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold"
                        >
                            Toggle
                        </button>

                    </div>

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

            // =========================
            // RANGE SLIDER
            // =========================

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

            // =========================
            // MODE MANUAL
            // =========================

            const modeSelect = document.getElementById('mode_select');
            const manualSection = document.getElementById('manual_control_section');

            function checkManualMode(){

                if(modeSelect.value === 'manual'){

                    manualSection.classList.remove('hidden');

                }else{

                    manualSection.classList.add('hidden');
                }
            }

            modeSelect.addEventListener('change', checkManualMode);

            checkManualMode();

            // =========================
            // MODAL
            // =========================

            const relayModal = document.getElementById('relayModal');

            const openRelayModal = document.getElementById('openRelayModal');

            const closeRelayModal = document.getElementById('closeRelayModal');

            openRelayModal.addEventListener('click', () => {

                relayModal.classList.remove('hidden');

                relayModal.classList.add('flex');
            });

            closeRelayModal.addEventListener('click', () => {

                relayModal.classList.add('hidden');

                relayModal.classList.remove('flex');
            });

            relayModal.addEventListener('click', (e) => {

                if(e.target === relayModal){

                    relayModal.classList.add('hidden');

                    relayModal.classList.remove('flex');
                }
            });

        });

        // =========================
        // STATUS RELAY
        // =========================

        let relayState = {
            1:false,
            2:false
        };

        function toggleRelay(relay) {
            // Ubah state lokal dulu agar UI terasa cepat (Optimistic UI update)
            relayState[relay] = !relayState[relay];
            const stateString = relayState[relay] ? 'ON' : 'OFF';

            const statusEl = document.getElementById(`relay${relay}Status`);
            
            // Ubah warna teks
            if(relayState[relay]) {
                statusEl.innerText = 'ON';
                statusEl.classList.remove('text-red-500');
                statusEl.classList.add('text-green-600');
            } else {
                statusEl.innerText = 'OFF';
                statusEl.classList.remove('text-green-600');
                statusEl.classList.add('text-red-500');
            }

            // Tembak API ke server Laravel
            fetch('/relay/control', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    relay: relay,
                    state: stateString
                })
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    // Jika Node-RED / alat gagal merespon, kembalikan posisi tombol seperti semula
                    alert('Gagal: ' + data.message);
                    relayState[relay] = !relayState[relay];
                    
                    statusEl.innerText = relayState[relay] ? 'ON' : 'OFF';
                    if(relayState[relay]) {
                        statusEl.classList.replace('text-red-500', 'text-green-600');
                    } else {
                        statusEl.classList.replace('text-green-600', 'text-red-500');
                    }
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan jaringan saat mengirim perintah.");
            });
        }

    </script>

</x-app-layout>