@props([
    'title' => 'Jadual',
    'collection' => collect(),
    'bgColor' => 'bg-gray-50',
    'pageName' => 'page',
    'issueType' => '' // NEW: To identify which columns to show
])

<div x-data="{ open: new URLSearchParams(window.location.search).get('open_table') === '{{ $pageName }}' }" 
     x-init="$watch('open', value => {
        let url = new URL(window.location.href);
        if (value) {
            url.searchParams.set('open_table', '{{ $pageName }}');
        } else if (url.searchParams.get('open_table') === '{{ $pageName }}') {
            url.searchParams.delete('open_table');
        }
        window.history.replaceState({}, '', url);
     })"
     class="border rounded shadow-sm overflow-hidden">
    
    <button @click="open = !open" class="w-full flex justify-between items-center p-3 text-left {{ $bgColor }} focus:outline-none">
        <span class="text-md font-semibold text-gray-700">{{ $title }} ({{ $collection->total() }})</span>
        <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>

    <div x-show="open" x-transition class="overflow-x-auto bg-white">
        @if($collection->isEmpty())
            <p class="p-4 text-sm text-gray-500 text-center">Tiada rekod.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bil</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO. KERTAS SIASATAN</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IO/AIO</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seksyen</th>
                        
                        {{-- CONDITIONAL COLUMNS BASED ON ISSUE TYPE --}}
                        @if ($issueType === 'lewat')
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Edaran Pertama</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Edaran Kedua</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TEMPOH LEWAT EDARAN DIKESAN </th>
                        @elseif ($issueType === 'terbengkalai')
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Edaran Sebelum Akhir</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Edaran Akhir</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS KERTAS SIASATAN </th>
                        @elseif ($issueType === 'kemaskini')
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Edaran Akhir</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TARIKH SEMBOYAN PEMERIKSAAN</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS KERTAS SIASATAN</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TEMPOH DIKEMASKINI</th>
                        @endif

                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($collection as $index => $item)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $collection->firstItem() + $index }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->no_kertas_siasatan ?? $item->no_ks ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->pegawai_penyiasat ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->seksyen ?? '-' }}</td>
                            
                            {{-- CONDITIONAL DATA BASED ON ISSUE TYPE --}}
                            @if ($issueType === 'lewat')
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ optional($item->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ optional($item->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-red-600">{{ $item->tempoh_lewat_edaran_dikesan }}</td>
                            @elseif ($issueType === 'terbengkalai')
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ optional($item->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ optional($item->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-green-600">
                                    @php
                                        // Create a small array to hold the status parts to display.
                                        $statuses = [];
                                        if ($item->terbengkalai_status_dc === 'TERBENGKALAI MELEBIHI 3 BULAN') {
                                            $statuses[] = 'D-C';
                                        }
                                        if ($item->terbengkalai_status_da === 'TERBENGKALAI MELEBIHI 3 BULAN') {
                                            $statuses[] = 'D-A';
                                        }
                                    @endphp

                                    {{-- Check if any status was found and display them, otherwise show TIDAK --}}
                                    @if (!empty($statuses))
                                        TERBENGKALAI MELEBIHI 3 BULAN ({{ implode(' & ', $statuses) }})
                                    @else
                                        TIDAK
                                    @endif
                                </td>
                            @elseif ($issueType === 'kemaskini')
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ optional($item->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ optional($item->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-green-600">
                                    {{ $item->baru_dikemaskini_status ?? 'TIDAK' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-green-600">{{ $item->tempoh_dikemaskini }}</td>
                            @endif

                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('kertas_siasatan.show', ['paperType' => class_basename($item), 'id' => $item->id]) }}" class="text-indigo-600 hover:text-indigo-900" title="Lihat"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('kertas_siasatan.edit', ['paperType' => class_basename($item), 'id' => $item->id]) }}" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($collection->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $collection->appends(['open_table' => $pageName])->links() }}
                </div>
            @endif
        @endif
    </div>
</div>