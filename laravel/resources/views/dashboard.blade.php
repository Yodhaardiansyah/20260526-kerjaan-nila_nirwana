<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6" x-data="dashboardComponent()">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[#0B1727]">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}</p>
            </div>
            <div class="flex items-center gap-6 text-sm font-medium text-gray-600 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="currentTime"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center font-bold {{ $realtimeData['is_online'] ? 'bg-blue-50 border-blue-100 text-blue-500' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                    pH
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">pH Air</p>
                    <h3 class="text-3xl font-bold {{ $realtimeData['is_online'] ? 'text-gray-900' : 'text-gray-400' }} mt-0.5">
                        {{ $realtimeData['is_online'] ? number_format($realtimeData['ph'], 2) : '--' }}
                    </h3>
                    <p class="text-sm font-medium mt-1 
                        @if(!$realtimeData['is_online']) text-gray-400 
                        @elseif($realtimeData['ph'] >= $settings->ph_min_limit && $realtimeData['ph'] <= $settings->ph_max_limit) text-green-500 
                        @else text-red-500 @endif">
                        {{ !$realtimeData['is_online'] ? 'Offline' : (($realtimeData['ph'] >= $settings->ph_min_limit && $realtimeData['ph'] <= $settings->ph_max_limit) ? 'Normal' : 'Tidak Normal') }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center {{ $realtimeData['is_online'] ? 'bg-cyan-50 border-cyan-100 text-cyan-500' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Suhu Air</p>
                    <h3 class="text-3xl font-bold {{ $realtimeData['is_online'] ? 'text-gray-900' : 'text-gray-400' }} mt-0.5">
                        @if($realtimeData['is_online'])
                            {{ number_format($realtimeData['temp'], 1) }} <span class="text-lg text-gray-400">°C</span>
                        @else
                            --
                        @endif
                    </h3>
                    <p class="text-sm font-medium {{ $realtimeData['is_online'] ? 'text-green-500' : 'text-gray-400' }} mt-1">
                        {{ $realtimeData['is_online'] ? 'Normal' : 'Offline' }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center {{ $realtimeData['is_online'] ? 'bg-blue-50 border-blue-100 text-blue-500' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kondisi Air</p>
                    <h3 class="text-2xl font-bold {{ $realtimeData['is_online'] ? 'text-gray-900' : 'text-gray-400' }} mt-0.5">
                        @if(!$realtimeData['is_online'])
                            --
                        @else
                            {{ $realtimeData['water'] == 1 ? 'Penuh' : 'Kurang' }}
                        @endif
                    </h3>
                    <p class="text-sm font-medium mt-1 
                        @if(!$realtimeData['is_online']) text-gray-400 
                        @elseif($realtimeData['water'] == 1) text-green-500 
                        @else text-yellow-500 @endif">
                        {{ !$realtimeData['is_online'] ? 'Offline' : ($realtimeData['water'] == 1 ? 'Normal' : 'Mengisi...') }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center {{ $realtimeData['is_online'] ? 'bg-green-50 border-green-100 text-green-500' : 'bg-red-50 border-red-100 text-red-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.906 14.142 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Status Sistem</p>
                    <h3 class="text-2xl font-bold {{ $realtimeData['is_online'] ? 'text-gray-900' : 'text-red-600' }} mt-0.5">
                        {{ $realtimeData['is_online'] ? 'Online' : 'Offline' }}
                    </h3>
                    <p class="text-sm font-medium {{ $realtimeData['is_online'] ? 'text-green-500' : 'text-red-500' }} mt-1">
                        {{ $realtimeData['is_online'] ? 'Terhubung' : 'Terputus' }}
                    </p>
                </div>
            </div>
            
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-[#0B1727]">Grafik Real-time</h3>
                        <p class="text-xs font-medium mt-1 {{ $realtimeData['is_online'] ? 'text-gray-500' : 'text-red-500' }}">
                            Update terakhir: {{ $realtimeData['last_seen'] ? $realtimeData['last_seen']->format('H:i:s') : 'Belum ada data' }}
                        </p>
                    </div>
                    <span class="text-sm px-3 py-1 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 font-medium">1 Jam Terakhir</span>
                </div>
                
                <div class="relative h-64 w-full">
                    @if(count($chartData['labels']) > 0)
                        <canvas id="miniDashboardChart"></canvas>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <span class="text-3xl mb-2 text-gray-300">📉</span>
                            <span class="text-sm font-semibold text-gray-500">Grafik Kosong</span>
                            <span class="text-xs text-gray-400 mt-1">Tidak ada data masuk dalam 1 jam terakhir.</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-[#0B1727] mb-4">Status Perangkat</h3>
                <div class="space-y-4">
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="{{ $realtimeData['is_online'] ? 'text-green-600' : 'text-gray-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                            </span>
                            <span class="font-semibold text-gray-700 text-sm">ESP32</span>
                        </div>
                        <span class="px-2.5 py-1 {{ $realtimeData['is_online'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-xs font-bold rounded-lg">
                            {{ $realtimeData['is_online'] ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="text-base">{{ $realtimeData['is_online'] ? '🌡️' : '⚪' }}</span>
                            <span class="font-semibold text-gray-700 text-sm">Sensor Suhu & pH</span>
                        </div>
                        <span class="px-2.5 py-1 {{ $realtimeData['is_online'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }} text-xs font-bold rounded-lg">
                            {{ $realtimeData['is_online'] ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="text-base">{{ $realtimeData['is_online'] ? '💧' : '⚪' }}</span>
                            <span class="font-semibold text-gray-700 text-sm">Sensor Water</span>
                        </div>
                        <span class="px-2.5 py-1 {{ $realtimeData['is_online'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }} text-xs font-bold rounded-lg">
                            {{ $realtimeData['is_online'] ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="text-base">{{ $realtimeData['is_online'] ? '⚙️' : '⚪' }}</span>
                            <span class="font-semibold text-gray-700 text-sm">Pompa Air (R2)</span>
                        </div>
                        <span class="px-2.5 py-1 {{ !$realtimeData['is_online'] ? 'bg-gray-200 text-gray-600' : ($realtimeData['relay2'] == 'ON' ? 'bg-blue-100 text-blue-700' : 'bg-green-50 text-green-700') }} text-xs font-bold rounded-lg">
                            {{ !$realtimeData['is_online'] ? 'Matikan' : ($realtimeData['relay2'] == 'ON' ? 'Menyala' : 'Otomatis') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="text-base">{{ $realtimeData['is_online'] ? '⏱️' : '⚪' }}</span>
                            <span class="font-semibold text-gray-700 text-sm">Solenoid Valve (R1)</span>
                        </div>
                        <span class="px-2.5 py-1 {{ !$realtimeData['is_online'] ? 'bg-gray-200 text-gray-600' : ($realtimeData['relay1'] == 'ON' ? 'bg-blue-100 text-blue-700' : 'bg-green-50 text-green-700') }} text-xs font-bold rounded-lg">
                            {{ !$realtimeData['is_online'] ? 'Matikan' : ($realtimeData['relay1'] == 'ON' ? 'Menyala' : 'Otomatis') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
                <h3 class="text-lg font-bold text-[#0B1727] mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    @forelse($activities as $activity)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-3">
                                @if($activity->type == 'success')
                                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-600">✓</div>
                                @elseif($activity->type == 'warning')
                                    <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">!</div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">i</div>
                                @endif
                                <span class="text-sm text-gray-700 font-medium">{{ $activity->description }}</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ $activity->created_at->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas hari ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-[#0B1727] mb-4">Ringkasan Hari Ini</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="p-4 border border-gray-100 rounded-xl text-center">
                        <p class="text-xs text-gray-500 font-semibold mb-1">Rata-rata pH</p>
                        <p class="text-xl font-bold text-green-600">
                            {{ $todaySummary['avg_ph'] !== null 
                                ? number_format($todaySummary['avg_ph'], 2) 
                                : 'Tidak ada data' }}
                        </p>
                    </div>
                    <div class="p-4 border border-gray-100 rounded-xl text-center">
                        <p class="text-xs text-gray-500 font-semibold mb-1">Rata-rata Suhu</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ $todaySummary['avg_temp'] !== null 
                                ? number_format($todaySummary['avg_temp'], 1) . ' °C' 
                                : 'Tidak ada data' }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-between items-center p-4 border border-gray-100 rounded-xl bg-gray-50">
                    <span class="text-sm font-semibold text-gray-600">Status Online</span>
                    <span class="text-sm font-bold text-gray-900">{{ $realtimeData['uptime'] }}</span>
                </div>
            </div>
        </div>

    </div>

    <script>
        function dashboardComponent() {
            return {
                currentTime: '',
                chartData: @json($chartData), // Menerima data asli dari controller
                
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    
                    // Render grafik hanya jika data tersedia
                    if (this.chartData.labels.length > 0) {
                        this.renderChart();
                    }
                },
                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                },
                renderChart() {
                    const ctx = document.getElementById('miniDashboardChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.chartData.labels,
                            datasets: [
                                { 
                                    label: 'pH Air', 
                                    data: this.chartData.ph, 
                                    borderColor: '#3b82f6', 
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 0,
                                    pointHoverRadius: 6
                                },
                                { 
                                    label: 'Suhu Air', 
                                    data: this.chartData.temp, 
                                    borderColor: '#ef4444', 
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 0,
                                    pointHoverRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: true, 
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: { legend: { position: 'top' } },
                            scales: {
                                y: { grid: { color: '#f3f4f6' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>