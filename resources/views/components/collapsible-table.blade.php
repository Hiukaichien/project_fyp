@props([
    'title' => 'Table',
    'collection' => collect(),
    'bgColor' => 'bg-gray-50'
])

<div x-data="{ open: false }" class="border rounded shadow-sm overflow-hidden">
    <button @click="open = !open" class="w-full flex justify-between items-center p-3 text-left {{ $bgColor }} hover:bg-gray-100 focus:outline-none">
        <span class="text-md font-semibold text-gray-700">{{ $title }} ({{ $collection->count() }})</span>
        <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" x-transition class="overflow-x-auto bg-white">
        @if($collection->isEmpty())
            <p class="p-4 text-sm text-gray-500 text-center">Tiada rekod.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bil</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Rujukan</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($collection as $index => $item)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            
                            {{-- Dynamically get the identifier --}}
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->no_ks ?? $item->no_kst ?? $item->no_lmm ?? $item->no_ks_oh ?? 'N/A' }}
                            </td>

                            {{-- Dynamically get the date --}}
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    // Check for various possible date columns
                                    $dateValue = $item->tarikh_ks ?? $item->tarikh_ks_dibuka ?? $item->tarikh_laporan_polis ?? $item->tarikh_daftar ?? null;
                                @endphp
                                {{ $dateValue ? \Carbon\Carbon::parse($dateValue)->format('d/m/Y') : '-' }}
                            </td>

                            {{-- Dynamically get the officer --}}
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->io_aio ?? $item->pegawai_penyiasat ?? 'N/A' }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium space-x-1">
                                {{-- Dynamically build the routes with the correct paperType --}}
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
        @endif
    </div>
</div>