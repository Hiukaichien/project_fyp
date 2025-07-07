{{-- resources/views/projects/_associated_kertas_siasatan_table_rows.blade.php --}}
@forelse ($kertasSiasatans as $index => $ks)
    <tr>
        <td class="sticky left-0 bg-white dark:bg-gray-800 px-4 py-3 whitespace-nowrap text-sm font-medium space-x-2">
            {{-- Action Buttons --}}
            <a href="{{ route('kertas_siasatan.show', $ks->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200" title="Lihat"><i class="fas fa-eye"></i></a>
            <a href="{{ route('kertas_siasatan.edit', $ks->id) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-200" title="Audit/Kemaskini"><i class="fas fa-edit"></i></a>
            @if(isset($project))
            <form action="{{ route('projects.disassociate_paper', ['project' => $project->id, 'paperType' => 'KertasSiasatan', 'paperId' => $ks->id]) }}" method="POST" class="inline" onsubmit="return confirm('Anda pasti ingin mengeluarkan Kertas Siasatan {{ $ks->no_ks }} daripada projek ini?');">
                @csrf
                <button type="submit" class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-200" title="Keluarkan dari Projek"><i class="fas fa-unlink"></i></button>
            </form>
            @endif
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            @if(method_exists($kertasSiasatans, 'firstItem')){{ $kertasSiasatans->firstItem() + $index }}@else{{ $index + 1 }}@endif
        </td>
        
        {{-- Maklumat Asas --}}
        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ks->no_ks }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_ks)->format('d/m/Y') ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->no_report ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->jenis_jabatan_ks ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->pegawai_penyiasat ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ks->status_ks == 'Fail' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $ks->status_ks ?? '-' }}</span></td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ks->status_kes == 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $ks->status_kes ?? '-' }}</span></td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->seksyen ?? '-' }}</td>
        
        {{-- Minit & Status --}}
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_minit_a)->format('d/m/Y') ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_minit_b)->format('d/m/Y') ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_minit_c)->format('d/m/Y') ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_minit_d)->format('d/m/Y') ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->edar_lebih_24_jam_status ?? 'Tiada' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->terbengkalai_3_bulan_status ?? 'Tiada' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->baru_kemaskini_status ?? 'Tiada' }}</td>

        {{-- Status Semasa Diperiksa --}}
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->status_ks_semasa_diperiksa ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($ks->tarikh_status_ks_semasa_diperiksa)->format('d/m/Y') ?? '-' }}</td>

        {{-- Rakaman Percakapan --}}
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->rakaman_pengadu ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->rakaman_saspek ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->rakaman_saksi ?? '-' }}</td>

        {{-- Barang Kes --}}
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->barang_kes_am_didaftar ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->no_daftar_kes_am ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->no_daftar_kes_senjata_api ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->no_daftar_kes_berharga ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->gambar_rampasan_dilampirkan ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->kedudukan_barang_kes ?? '-' }}</td>
        
        {{-- Isu Isu --}}
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->isu_tpr_tuduh ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->isu_ks_lengkap_tiada_rujuk_tpr ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->isu_tpr_arah_lupus_belum_laksana ?? '-' }}</td>
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ks->isu_tpr_arah_pulang_belum_laksana ?? '-' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="35" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
            Tiada rekod kertas siasatan yang dikaitkan dengan projek ini.
        </td>
    </tr>
@endforelse