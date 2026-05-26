<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grafik & Riwayat Sensor') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12 bg-gray-50" x-data="graphComponent()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    
                    <div class="flex space-x-2 border-b border-gray-200 w-full md:w-auto">
                        <button @click="setTab('ph')" :class="activeTab === 'ph' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-4 py-2 border-b-2 font-medium text-sm transition">
                            Grafik pH Air
                        </button>
                        <button @click="setTab('temp')" :class="activeTab === 'temp' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-4 py-2 border-b-2 font-medium text-sm transition">
                            Grafik Suhu Air
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <select x-model="timeRange" @change="handleRangeChange" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="1d">1 Hari Terakhir</option>
                            <option value="hours">Beberapa Jam Terakhir</option> <option value="7d">1 Minggu Terakhir</option>
                            <option value="30d">1 Bulan Terakhir</option>
                            <option value="custom">Waktu Custom Tanggal</option>
                        </select>

                        <div x-show="timeRange === 'hours'" class="flex items-center gap-2" x-cloak>
                            <input type="number" x-model="customHours" min="1" max="48" class="rounded-md border-gray-300 shadow-sm text-sm w-20 text-center" placeholder="Jam">
                            <span class="text-sm text-gray-500 font-medium">Jam Terakhir</span>
                            <button @click="fetchData" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700 transition">Terapkan</button>
                        </div>

                        <div x-show="timeRange === 'custom'" class="flex items-center gap-2" x-cloak>
                            <input type="date" x-model="customStart" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <span class="text-gray-500">-</span>
                            <input type="date" x-model="customEnd" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <button @click="fetchData" class="bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm hover:bg-gray-700 transition">Terapkan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="relative h-72 md:h-96 w-full">
                    <div x-show="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10" x-cloak>
                        <span class="text-gray-500 font-medium">Memuat Data...</span>
                    </div>
                    
                    <canvas id="sensorChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4" x-text="'Tabel Data Detail ' + (activeTab === 'ph' ? 'pH' : 'Suhu')"></h3>
                
                <div class="overflow-y-auto max-h-96 border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pencatatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Terukur</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <template x-for="(row, index) in tableData" :key="index">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 whitespace-nowrap text-gray-500" x-text="row.time"></td>
                                    <td class="px-6 py-3 font-medium text-gray-800" x-text="row.value + ' ' + currentUnit"></td>
                                </tr>
                            </template>
                            
                            <tr x-show="tableData.length === 0 && !isLoading">
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada data pada rentang waktu ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function graphComponent() {
            let chart = null; 

            return {
                activeTab: 'ph',
                timeRange: '1d',
                customStart: '',
                customEnd: '',
                customHours: 2, // Default kolom jam custom diisi angka 2
                isLoading: false,
                tableData: [],
                currentUnit: '',

                init() {
                    this.initChart();
                    this.fetchData();
                },

                setTab(tab) {
                    if (this.activeTab !== tab) {
                        this.activeTab = tab;
                        this.fetchData();
                    }
                },

                handleRangeChange() {
                    // Jika memilih 'custom' (tanggal), sistem akan diam menunggu tombol Terapkan ditekan
                    if (this.timeRange === 'custom') {
                        return;
                    }
                    
                    // Untuk pilihan 1d, 7d, 30d, DAN 'hours', langsung update grafik secara otomatis
                    this.fetchData();
                },
                
                initChart() {
                    const ctx = document.getElementById('sensorChart').getContext('2d');
                    
                    if (chart) {
                        chart.destroy();
                    }

                    chart = new Chart(ctx, {
                        type: 'line',
                        data: { 
                            labels: [], 
                            datasets: [{
                                label: 'Memuat data...',
                                data: [],
                                borderColor: '#9ca3af',
                                backgroundColor: 'rgba(156, 163, 175, 0.1)',
                                borderWidth: 2,
                                fill: true,
                            }] 
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: true, position: 'top' }
                            },
                            scales: {
                                y: { beginAtZero: false },
                                x: { ticks: { maxTicksLimit: 10 } }
                            }
                        }
                    });
                },

                fetchData() {
                    this.isLoading = true;
                    
                    let url = `/api/grafik/data?type=${this.activeTab}&range=${this.timeRange}`;
                    
                    // Kondisi penambahan parameter jika memilih custom tanggal
                    if (this.timeRange === 'custom') {
                        if(!this.customStart || !this.customEnd) {
                            alert("Pilih tanggal awal dan akhir terlebih dahulu.");
                            this.isLoading = false;
                            return;
                        }
                        url += `&start=${this.customStart}&end=${this.customEnd}`;
                    } 
                    // Kondisi penambahan parameter jika memilih custom jam
                    else if (this.timeRange === 'hours') {
                        if(!this.customHours || this.customHours < 1) {
                            alert("Masukkan jumlah jam yang valid (minimal 1 jam).");
                            this.isLoading = false;
                            return;
                        }
                        url += `&hours=${this.customHours}`;
                    }

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if(data.error) {
                                console.error("InfluxDB Error:", data.error);
                                alert("Gagal mengambil data dari database.");
                                return;
                            }

                            this.tableData = data.tableData || [];
                            this.currentUnit = data.unit || '';
                            
                            const newLabels = Array.isArray(data.labels) ? data.labels : [];
                            const newValues = Array.isArray(data.values) ? data.values : [];
                            
                            chart.data.labels = newLabels;
                            chart.data.datasets = [{
                                label: data.labelName || 'Sensor Data',
                                data: newValues,
                                borderColor: this.activeTab === 'ph' ? '#3b82f6' : '#ef4444',
                                backgroundColor: this.activeTab === 'ph' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 2,
                                pointRadius: 2,
                                pointHoverRadius: 5,
                                fill: true,
                                tension: 0.3
                            }];
                            
                            chart.update();
                        })
                        .catch(err => {
                            console.error("Fetch Error:", err);
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }
        }
    </script>
    
    <style> [x-cloak] { display: none !important; } </style>
</x-app-layout>