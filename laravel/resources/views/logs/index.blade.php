<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Log Sistem & Perangkat') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100">
                <h3 class="text-lg font-bold text-red-800 mb-4">⚠️ Log Peringatan Kritis (Alert Logs)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Peringatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Kejadian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($alerts as $alert)
                                <tr class="hover:bg-red-50/30">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ $alert->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ strtoupper(str_replace('_', ' ', $alert->alert_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $alert->message }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada data peringatan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $alerts->appends(['activities_page' => $activities->currentPage()])->links() }}
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📋 Log Aktivitas Operasional (Activity Logs)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status/Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Aktivitas Alat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ $activity->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($activity->type == 'success')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">SUCCESS</span>
                                        @elif($activity->type == 'warning')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">WARNING</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">INFO</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $activity->description }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada aktivitas tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $activities->appends(['alerts_page' => $alerts->currentPage()])->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>