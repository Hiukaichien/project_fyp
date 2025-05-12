{{-- resources/views/projects/_associated_kertas_siasatan_table_rows.blade.php --}}
@forelse ($kertasSiasatans as $index => $ks)
    <tr>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            {{-- Ensure $kertasSiasatans is a Paginator instance for firstItem() to work --}}
            @if(method_exists($kertasSiasatans, 'firstItem'))
                {{ $kertasSiasatans->firstItem() + $index }}
            @else
                {{ $index + 1 }} {{-- Fallback for simple arrays/collections --}}
            @endif
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 0;" title="{{ $ks->no_ks }}">
            {{ $ks->no_ks }}
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_ks)->format('d/m/Y') ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 0;" title="{{ $ks->no_report ?? '-' }}">
            {{ $ks->no_report ?? '-' }}
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 0;" title="{{ $ks->pegawai_penyiasat ?? '-' }}">
            {{ $ks->pegawai_penyiasat ?? '-' }}
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ks->status_ks == 'Fail' ? 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' : 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' }}">
                {{ $ks->status_ks ?? '-' }}
            </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ks->status_kes == 'Selesai' ? 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                {{ $ks->status_kes ?? '-' }}
            </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium space-x-2">
            {{-- View Button --}}
            <a href="{{ route('kertas_siasatan.show', $ks->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200" title="Lihat">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </a>
            {{-- Edit Button - Still links to kertas_siasatan.edit, assuming this is desired --}}
            <a href="{{ route('kertas_siasatan.edit', $ks->id) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-200" title="Audit/Kemaskini">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </a>
            {{-- Remove Association Button --}}
            @if(isset($project))
            <form action="{{ route('projects.disassociate_paper', ['project' => $project->id, 'paperType' => 'KertasSiasatan', 'paperId' => $ks->id]) }}" method="POST" class="inline" onsubmit="return confirm('Anda pasti ingin mengeluarkan Kertas Siasatan {{ $ks->no_ks }} daripada projek ini?');">
                @csrf
                <button type="submit" class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-200" title="Keluarkan dari Projek">
                    <svg class="w-5 h-5 inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 22v-2"></path>
                        <path d="M9 15l6 -6"></path>
                        <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"></path>
                        <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"></path>
                        <path d="M20 17h2"></path>
                        <path d="M2 7h2"></path>
                        <path d="M7 2v2"></path>
                    </svg>
                </button>
            </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">Tiada rekod kertas siasatan yang dikaitkan dengan projek ini.</td>
    </tr>
@endforelse