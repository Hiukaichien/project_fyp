@props([
    'title' => 'Jadual',
    'collection' => collect(),
    'bgColor' => 'bg-gray-50',
    'pageName' => 'page' // This prop is now our unique identifier
])

{{-- The x-data logic is updated to handle URL parameters --}}
<div x-data="{
        open: new URLSearchParams(window.location.search).get('open_table') === '{{ $pageName }}',
        toggle() {
            this.open = !this.open;
            let url = new URL(window.location.href);
            if (this.open) {
                url.searchParams.set('open_table', '{{ $pageName }}');
            } else {
                // If we are closing this specific table, remove the parameter
                if (url.searchParams.get('open_table') === '{{ $pageName }}') {
                    url.searchParams.delete('open_table');
                }
            }
            // Update the URL in the browser's history without reloading the page
            window.history.replaceState({}, '', url);
        }
    }" 
    class="border rounded shadow-sm overflow-hidden">
    <button @click="toggle()" class="w-full flex justify-between items-center p-3 text-left {{ $bgColor }} focus:outline-none">
        <span class="text-md font-semibold text-gray-700">{{ $title }} ({{ $collection->total() ?? $collection->count() }})</span>
        <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" x-transition class="overflow-x-auto bg-white">
        @if($collection->isEmpty())
            <p class="p-4 text-sm text-gray-500 text-center">Tiada rekod.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                {{-- Table head and body remain the same --}}
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bil</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Rujukan</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Edaran P</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($collection as $index => $item)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $collection->firstItem() + $index }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->no_sdr_lmm ?? $item->no_ks ?? 'N/A' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $dateValue = $item->tarikh_minit_pertama ?? null;
                                @endphp
                                {{ $dateValue ? \Carbon\Carbon::parse($dateValue)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->pegawai_penyiasat ?? $item->pengawai_penyiasat ?? $item->pengawai_siasatan ?? 'N/A' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium space-x-1">
                                <a href="{{ route('kertas_siasatan.show', ['paperType' => class_basename($item), 'id' => $item->id]) }}" class="text-indigo-600 hover:text-indigo-900" title="Lihat">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('kertas_siasatan.edit', ['paperType' => class_basename($item), 'id' => $item->id]) }}" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($collection->hasPages())
            <div class="p-4 bg-gray-50 border-t border-gray-200 pagination-links">
                {{ $collection->appends(['open_table' => $pageName])->links() }}
            </div>
            @endif
        @endif
    </div>
</div>