<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6" x-data="graphComponent()">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
            <div>
                <h2 class="text-2xl font-bold text-[#0B1727]">Grafik & Analisis Data</h2>
                <p class="text-sm text-gray-500 mt-1">Visualisasi data parameter kualitas air secara historis.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-center gap-4">
            
            <div class="flex p-1 bg-gray-50 rounded-xl border border-gray-100 w-full lg:w-auto">
                <button @click="setTab('ph')" 
                        :class="activeTab === 'ph' ? 'bg-white shadow-sm text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" 
                        class="flex-1 lg:flex-none px-6 py-2 rounded-lg text-sm transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <span class="w-2 h-2 rounded-full" :class="activeTab === 'ph' ? 'bg-blue-500' : 'bg-transparent'"></span>
                        pH Air
                    </span>
                </button>
                <button @click="setTab('temp')" 
                        :class="activeTab === 'temp' ? 'bg-white shadow-sm text-red-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" 
                        class="flex-1 lg:flex-none px-6 py-2 rounded-lg text-sm transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <span class="w-2 h-2 rounded-full" :class="activeTab === 'temp' ? 'bg-red-500' : 'bg-transparent'"></span>
                        Suhu Air
                    </span>
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <select x-model="timeRange" @change="handleRangeChange" class="w-full lg:w-auto bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 outline-none transition">
                    <option value="1d">1 Hari Terakhir</option>
                    <option value="hours">Beberapa Jam Terakhir</option>
                    <option value="7d">1 Minggu Terakhir</option>
                    <option value="30d">1 Bulan Terakhir</option>
                    <option value="custom">Waktu Custom</option>
                </select>

                <div x-show="timeRange === 'hours'" class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5" x-cloak>
                    <input type="number" x-model="customHours" @input.debounce.500ms="fetchData" min="1" max="48" class="bg-transparent border-none text-sm w-16 text-center focus:ring-0 p-1 font-bold text-[#0B1727]" placeholder="2">
                    <span class="text-sm text-gray-500 font-medium border-l border-gray-300 pl-3">Jam Terakhir</span>
                </div>

                <div x-show="timeRange === 'custom'" class="flex items-center gap-2" x-cloak>
                    <input type="date" x-model="customStart" class="bg-gray-50 border border-gray-200 rounded-xl text-sm px-3 py-2 text-gray-700 outline-none focus:border-blue-500">
                    <span class="text-gray-400 font-medium">hingga</span>
                    <input type="date" x-model="customEnd" class="bg-gray-50 border border-gray-200 rounded-xl text-sm px-3 py-2 text-gray-700 outline-none focus:border-blue-500">
                    <button @click="fetchData" class="bg-[#0B1727] text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-800 transition shadow-sm">Terapkan</button>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-[#0B1727] mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                <span x-text="activeTab === 'ph' ? 'Visualisasi Pergerakan pH Air' : 'Visualisasi Fluktuasi Suhu Air'"></span>
            </h3>
            <div class="relative h-[22rem] w-full">
                <div x-show="isLoading" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10 rounded-xl transition-all duration-300" x-cloak>
                    <svg class="animate-spin h-8 w-8 text-blue-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm font-semibold text-gray-600 tracking-wide">Menarik Data dari Database...</span>
                </div>
                
                <canvas id="sensorChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-0 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-[#0B1727]">Tabel Riwayat Detail</h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg" x-text="tableData.length + ' Data Terbaca'"></span>
            </div>
            
            <div class="overflow-y-auto max-h-96 custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu Pencatatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Parameter Terukur</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50 text-sm">
                        <template x-for="(row, index) in tableData" :key="index">
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-gray-400 font-medium" x-text="index + 1"></td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-gray-600" x-text="row.time"></td>
                                <td class="px-6 py-3.5 font-bold" :class="activeTab === 'ph' ? 'text-blue-600' : 'text-red-600'" x-text="row.value + ' ' + currentUnit"></td>
                            </tr>
                        </template>
                        
                        <tr x-show="tableData.length === 0 && !isLoading">
                            <td colspan="3" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <span class="text-gray-500 font-medium">Tidak ada data pada rentang waktu ini.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                customHours: 2, 
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
                    if (this.timeRange !== 'custom') {
                        this.fetchData();
                    }
                },

                initChart() {
                    const ctx = document.getElementById('sensorChart').getContext('2d');
                    if (chart) chart.destroy();

                    chart = new Chart(ctx, {
                        type: 'line',
                        data: { 
                            labels: [], 
                            datasets: [{ label: 'Memuat...', data: [], fill: true }] 
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false } // Legend disembunyikan agar lebih bersih (judul sudah ada di HTML)
                            },
                            scales: {
                                y: { 
                                    beginAtZero: false,
                                    grid: { color: '#f3f4f6', drawBorder: false }
                                },
                                x: { 
                                    ticks: { maxTicksLimit: 8 },
                                    grid: { display: false, drawBorder: false }
                                }
                            }
                        }
                    });
                },

                fetchData() {
                    this.isLoading = true;
                    let url = `/api/grafik/data?type=${this.activeTab}&range=${this.timeRange}`;
                    
                    if (this.timeRange === 'custom') {
                        if(!this.customStart || !this.customEnd) {
                            this.isLoading = false;
                            return;
                        }
                        url += `&start=${this.customStart}&end=${this.customEnd}`;
                    } 
                    else if (this.timeRange === 'hours') {
                        if(!this.customHours || this.customHours < 1) {
                            this.isLoading = false;
                            return;
                        }
                        url += `&hours=${this.customHours}`;
                    }

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if(data.error) return;

                            this.tableData = data.tableData || [];
                            this.currentUnit = data.unit || '';
                            
                            const newLabels = Array.isArray(data.labels) ? data.labels : [];
                            const newValues = Array.isArray(data.values) ? data.values : [];
                            
                            chart.data.labels = newLabels;
                            chart.data.datasets = [{
                                label: data.labelName || 'Sensor Data',
                                data: newValues,
                                borderColor: this.activeTab === 'ph' ? '#3b82f6' : '#ef4444',
                                backgroundColor: this.activeTab === 'ph' ? 'rgba(59, 130, 246, 0.15)' : 'rgba(239, 68, 68, 0.15)',
                                borderWidth: 3,
                                pointRadius: 0, // Sembunyikan titik agar garis terlihat sangat mulus
                                pointHoverRadius: 6,
                                fill: true,
                                tension: 0.4 // Membuat garis melengkung (smooth curve)
                            }];
                            
                            chart.update();
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }
        }
    </script>
    
    <style> 
        [x-cloak] { display: none !important; } 
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</x-app-layout>