<x-app-layout>
    <div class="space-y-6" x-data="logComponent()">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
            <div>
                <h2 class="text-2xl font-bold text-[#0B1727]" x-text="activeTab === 'activities' ? 'Riwayat Data Operasional' : 'Notifikasi & Peringatan Sistem'"></h2>
                <p class="text-sm text-gray-500 mt-1" x-text="activeTab === 'activities' ? 'Catatan seluruh aktivitas relay, pompa, dan sistem otomatisasi.' : 'Daftar peringatan kritis seperti pH abnormal atau alat terputus.'"></p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex p-1 bg-gray-50 rounded-xl border border-gray-100 w-full sm:w-auto">
                <button @click="setTab('activities')" 
                        :class="activeTab === 'activities' ? 'bg-white shadow-sm text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" 
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Riwayat Aktivitas
                    </span>
                </button>
                <button @click="setTab('alerts')" 
                        :class="activeTab === 'alerts' ? 'bg-white shadow-sm text-red-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" 
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Peringatan
                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $alerts->total() }}</span>
                    </span>
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'activities'" x-transition.opacity.duration.300ms class="bg-white p-0 rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-cloak>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Waktu Kejadian</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50 text-sm">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                                    {{ $activity->created_at->setTimezone('Asia/Jakarta')->format('d M Y - H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activity->type == 'success')
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-700 border border-green-200">SELESAI</span>
                                    @elseif($activity->type == 'warning')
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-100 text-blue-700 border border-blue-200">BERJALAN</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 border border-gray-200">INFO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-800">
                                    {{ $activity->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                    <span class="block text-4xl mb-2">📋</span>
                                    Belum ada riwayat aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($activities->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $activities->appends(['alerts_page' => request('alerts_page')])->links() }}
                </div>
            @endif
        </div>

        <div x-show="activeTab === 'alerts'" x-transition.opacity.duration.300ms class="bg-white p-0 rounded-2xl shadow-sm border border-red-100 overflow-hidden" x-cloak>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-red-50">
                    <thead class="bg-red-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-red-800 uppercase tracking-wider w-48">Waktu Terdeteksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-red-800 uppercase tracking-wider w-40">Tipe Masalah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-red-800 uppercase tracking-wider">Detail Peringatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50 text-sm">
                        @forelse($alerts as $alert)
                            <tr class="hover:bg-red-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                                    {{ $alert->created_at->setTimezone('Asia/Jakarta')->format('d M Y - H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-100 text-red-700 border border-red-200">
                                        {{ strtoupper(str_replace('_', ' ', $alert->alert_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-medium">
                                    {{ $alert->message }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                    <span class="block text-4xl mb-2">🎉</span>
                                    Tidak ada peringatan. Sistem berjalan normal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($alerts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $alerts->appends(['activities_page' => request('activities_page')])->links() }}
                </div>
            @endif
        </div>

    </div>

    <script>
        function logComponent() {
            // Mengecek apakah di URL ada kata "#notifikasi" atau parameter pagination peringatan
            const showAlerts = window.location.hash === '#notifikasi' || window.location.search.includes('alerts_page');
            
            return {
                activeTab: showAlerts ? 'alerts' : 'activities',
                
                setTab(tab) {
                    this.activeTab = tab;
                    // Update URL Hash agar kalau di-refresh tab-nya tidak berubah
                    if (tab === 'alerts') {
                        window.history.replaceState(null, null, '#notifikasi');
                    } else {
                        window.history.replaceState(null, null, '#riwayat');
                    }
                }
            }
        }
    </script>
    
    <style> [x-cloak] { display: none !important; } </style>
</x-app-layout>