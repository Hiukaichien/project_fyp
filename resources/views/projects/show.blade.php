<x-app-layout>
    {{-- Add DataTables CSS to the head --}}
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Project Dashboard: ') }} {{ $project->name }}
            </h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                ← {{ __('Back to Projects List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Session Messages and Project Info --}}
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {{ session('success') }}
                </div>
            @endif
             @if (session('error') || $errors->has('excel_errors') || $errors->has('excel_file'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat Import!</strong>
                    @if (session('error'))<span class="block sm:inline">{{ session('error') }}</span>@endif
                    @error('excel_file')<span class="block sm:inline">{{ $message }}</span>@enderror
                    @if ($errors->has('excel_errors'))
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->get('excel_errors') as $failure)
                                <li>Baris {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }} (Nilai: {{ implode(', ', $failure->values()) }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
            @if (session('info'))
                <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-1">
                    <div>
                        <h3 class="text-2xl font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-ks-modal')">
                            <i class="fas fa-file-upload mr-2"></i> {{ __('Import') }}
                        </x-primary-button>
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}"><i class="fas fa-edit fa-lg"></i></a>
                        <a href="{{ route('projects.download_csv', $project) }}" class="text-green-600 dark:text-green-400 hover:text-green-500" title="{{ __('Download Associated Papers CSV') }}"><i class="fas fa-file-csv fa-lg"></i></a>
                    </div>
                </div>
                @if($project->description)
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t pt-4">{{ $project->description }}</p>
                @endif
            </div>

            {{-- Collapsible Stat Tables --}}
            <x-collapsible-table title="KS Lewat Edar (> 24 Jam)" :collection="$ksLewat24Jam" bgColor="bg-red-50 dark:bg-red-900/20" />
            <x-collapsible-table title="KS Terbengkalai (> 3 Bulan)" :collection="$ksTerbengkalai" bgColor="bg-yellow-50 dark:bg-yellow-900/20" />
            <x-collapsible-table title="KS Baru Dikemaskini" :collection="$ksBaruKemaskini" bgColor="bg-green-50 dark:bg-green-900/20" />
            
            <hr class="my-6 border-gray-300 dark:border-gray-700">

            {{-- KERTAS SIASATAN TABLE SECTION --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">          
                <h4 class="font-semibold text-lg mb-4">{{ __('Associated Kertas Siasatan') }}</h4>
                <div class="overflow-x-auto">
                    <table id="kertas-siasatan-datatable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400" style="width:100%">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Tindakan</th>
                                <th scope="col" class="px-4 py-3">No.</th>
                                <th scope="col" class="px-4 py-3">No. KS</th>
                                <th scope="col" class="px-4 py-3">Tarikh KS</th>
                                <th scope="col" class="px-4 py-3">No. Repot</th>
                                <th scope="col" class="px-4 py-3">Jenis Jabatan</th>
                                <th scope="col" class="px-4 py-3">Pegawai Penyiasat</th>
                                <th scope="col" class="px-4 py-3">Status KS</th>
                                <th scope="col" class="px-4 py-3">Status Kes</th>
                                <th scope="col" class="px-4 py-3">Seksyen</th>
                                <th scope="col" class="px-4 py-3">Minit A</th>
                                <th scope="col" class="px-4 py-3">Minit B</th>
                                <th scope="col" class="px-4 py-3">Minit C</th>
                                <th scope="col" class="px-4 py-3">Minit D</th>
                                <th scope="col" class="px-4 py-3">Status Edaran</th>
                                <th scope="col" class="px-4 py-3">Status Terbengkalai</th>
                                <th scope="col" class="px-4 py-3">Status Kemaskini</th>
                                <th scope="col" class="px-4 py-3">Status KS Diperiksa</th>
                                <th scope="col" class="px-4 py-3">Tarikh Status Diperiksa</th>
                                <th scope="col" class="px-4 py-3">Rakam Pengadu</th>
                                <th scope="col" class="px-4 py-3">Rakam Saspek</th>
                                <th scope="col" class="px-4 py-3">Rakam Saksi</th>
                                <th scope="col" class="px-4 py-3">ID Siasatan Dilampirkan</th>
                                <th scope="col" class="px-4 py-3">Tarikh ID Dilampirkan</th>
                                <th scope="col" class="px-4 py-3">BK Didaftar</th>
                                <th scope="col" class="px-4 py-3">No. Daftar AM</th>
                                <th scope="col" class="px-4 py-3">No. Daftar Senjata</th>
                                <th scope="col" class="px-4 py-3">No. Daftar Berharga</th>
                                <th scope="col" class="px-4 py-3">Gambar Rampasan</th>
                                <th scope="col" class="px-4 py-3">Kedudukan BK</th>
                                <th scope="col" class="px-4 py-3">Surat Serah Terima Stor</th>
                                <th scope="col" class="px-4 py-3">Arahan Pelupusan</th>
                                <th scope="col" class="px-4 py-3">Tatacara Pelupusan</th>
                                <th scope="col" class="px-4 py-3">Resit Kew38e</th>
                                <th scope="col" class="px-4 py-3">Sijil Pelupusan</th>
                                <th scope="col" class="px-4 py-3">Gambar Pelupusan</th>
                                <th scope="col" class="px-4 py-3">Surat Serah Terima Penuntut</th>
                                <th scope="col" class="px-4 py-3">Ulasan BK</th>
                                <th scope="col" class="px-4 py-3">Mohon Pakar Judi</th>
                                <th scope="col" class="px-4 py-3">Laporan Pakar Judi</th>
                                <th scope="col" class="px-4 py-3">Keputusan Pakar Judi</th>
                                <th scope="col" class="px-4 py-3">Kategori Judi</th>
                                <th scope="col" class="px-4 py-3">Mohon Forensik</th>
                                <th scope="col" class="px-4 py-3">Laporan Forensik</th>
                                <th scope="col" class="px-4 py-3">Keputusan Forensik</th>
                                <th scope="col" class="px-4 py-3">Surat Jamin Polis</th>
                                <th scope="col" class="px-4 py-3">Lakaran Lokasi</th>
                                <th scope="col" class="px-4 py-3">Gambar Lokasi</th>
                                <th scope="col" class="px-4 py-3">RJ2 Status</th><th scope="col" class="px-4 py-3">RJ2 Tarikh</th>
                                <th scope="col" class="px-4 py-3">RJ9 Status</th><th scope="col" class="px-4 py-3">RJ9 Tarikh</th>
                                <th scope="col" class="px-4 py-3">RJ10A Status</th><th scope="col" class="px-4 py-3">RJ10A Tarikh</th>
                                <th scope="col" class="px-4 py-3">RJ10B Status</th><th scope="col" class="px-4 py-3">RJ10B Tarikh</th>
                                <th scope="col" class="px-4 py-3">RJ99 Status</th><th scope="col" class="px-4 py-3">RJ99 Tarikh</th>
                                <th scope="col" class="px-4 py-3">Semboyan Status</th><th scope="col" class="px-4 py-3">Semboyan Tarikh</th>
                                <th scope="col" class="px-4 py-3">Waran Tangkap Status</th><th scope="col" class="px-4 py-3">Waran Tangkap Tarikh</th>
                                <th scope="col" class="px-4 py-3">Ulasan Isu RJ</th>
                                <th scope="col" class="px-4 py-3">Pem 1</th><th scope="col" class="px-4 py-3">Pem 2</th><th scope="col" class="px-4 py-3">Pem 3</th><th scope="col" class="px-4 py-3">Pem 4</th>
                                <th scope="col" class="px-4 py-3">Isu TPR Tuduh</th>
                                <th scope="col" class="px-4 py-3">Isu KS Lengkap</th>
                                <th scope="col" class="px-4 py-3">Isu TPR Lupus</th>
                                <th scope="col" class="px-4 py-3">Isu TPR Pulang</th>
                                <th scope="col" class="px-4 py-3">Isu Kesan/Tangkap</th>
                                <th scope="col" class="px-4 py-3">Isu Jatuh Hukum</th>
                                <th scope="col" class="px-4 py-3">Isu NFA KBSJD</th>
                                <th scope="col" class="px-4 py-3">Isu Belum KUS/Fail</th>
                                <th scope="col" class="px-4 py-3">Isu Warisan Terbengkalai</th>
                                <th scope="col" class="px-4 py-3">Isu KBSJD Simpan KS</th>
                                <th scope="col" class="px-4 py-3">Isu SIO Simpan KS</th>
                                <th scope="col" class="px-4 py-3">Isu KS Pada TPR</th>
                                <th scope="col" class="px-4 py-3">Hantar TPR Status</th><th scope="col" class="px-4 py-3">Hantar TPR Tarikh</th>
                                <th scope="col" class="px-4 py-3">Hantar KJSJ Status</th><th scope="col" class="px-4 py-3">Hantar KJSJ Tarikh</th>
                                <th scope="col" class="px-4 py-3">Hantar D5 Status</th><th scope="col" class="px-4 py-3">Hantar D5 Tarikh</th>
                                <th scope="col" class="px-4 py-3">Hantar KBSJD Status</th><th scope="col" class="px-4 py-3">Hantar KBSJD Tarikh</th>
                                <th scope="col" class="px-4 py-3">Ulasan Isu Menarik</th>
                                <th scope="col" class="px-4 py-3">Ulasan Keseluruhan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script>
    $(document).ready(function() {
        $('#kertas-siasatan-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('projects.kertas_siasatan_data', $project->id) }}',
            scrollX: true,
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'id', name: 'id' },
                { data: 'no_ks', name: 'no_ks' },
                { data: 'tarikh_ks', name: 'tarikh_ks' },
                { data: 'no_report', name: 'no_report' },
                { data: 'jenis_jabatan_ks', name: 'jenis_jabatan_ks' },
                { data: 'pegawai_penyiasat', name: 'pegawai_penyiasat' },
                { data: 'status_ks', name: 'status_ks' },
                { data: 'status_kes', name: 'status_kes' },
                { data: 'seksyen', name: 'seksyen' },
                { data: 'tarikh_minit_a', name: 'tarikh_minit_a' },
                { data: 'tarikh_minit_b', name: 'tarikh_minit_b' },
                { data: 'tarikh_minit_c', name: 'tarikh_minit_c' },
                { data: 'tarikh_minit_d', name: 'tarikh_minit_d' },
                { data: 'edar_lebih_24_jam_status', name: 'edar_lebih_24_jam_status' },
                { data: 'terbengkalai_3_bulan_status', name: 'terbengkalai_3_bulan_status' },
                { data: 'baru_kemaskini_status', name: 'baru_kemaskini_status' },
                { data: 'status_ks_semasa_diperiksa', name: 'status_ks_semasa_diperiksa' },
                { data: 'tarikh_status_ks_semasa_diperiksa', name: 'tarikh_status_ks_semasa_diperiksa' },
                { data: 'rakaman_pengadu', name: 'rakaman_pengadu' },
                { data: 'rakaman_saspek', name: 'rakaman_saspek' },
                { data: 'rakaman_saksi', name: 'rakaman_saksi' },
                { data: 'id_siasatan_dilampirkan', name: 'id_siasatan_dilampirkan' },
                { data: 'tarikh_id_siasatan_dilampirkan', name: 'tarikh_id_siasatan_dilampirkan' },
                { data: 'barang_kes_am_didaftar', name: 'barang_kes_am_didaftar' },
                { data: 'no_daftar_kes_am', name: 'no_daftar_kes_am' },
                { data: 'no_daftar_kes_senjata_api', name: 'no_daftar_kes_senjata_api' },
                { data: 'no_daftar_kes_berharga', name: 'no_daftar_kes_berharga' },
                { data: 'gambar_rampasan_dilampirkan', name: 'gambar_rampasan_dilampirkan' },
                { data: 'kedudukan_barang_kes', name: 'kedudukan_barang_kes' },
                { data: 'surat_serah_terima_stor', name: 'surat_serah_terima_stor' },
                { data: 'arahan_pelupusan', name: 'arahan_pelupusan' },
                { data: 'tatacara_pelupusan', name: 'tatacara_pelupusan' },
                { data: 'resit_kew38e_dilampirkan', name: 'resit_kew38e_dilampirkan' },
                { data: 'sijil_pelupusan_dilampirkan', name: 'sijil_pelupusan_dilampirkan' },
                { data: 'gambar_pelupusan_dilampirkan', name: 'gambar_pelupusan_dilampirkan' },
                { data: 'surat_serah_terima_penuntut', name: 'surat_serah_terima_penuntut' },
                { data: 'ulasan_barang_kes', name: 'ulasan_barang_kes' },
                { data: 'surat_mohon_pakar_judi', name: 'surat_mohon_pakar_judi' },
                { data: 'laporan_pakar_judi', name: 'laporan_pakar_judi' },
                { data: 'keputusan_pakar_judi', name: 'keputusan_pakar_judi' },
                { data: 'kategori_perjudian', name: 'kategori_perjudian' },
                { data: 'surat_mohon_forensik', name: 'surat_mohon_forensik' },
                { data: 'laporan_forensik', name: 'laporan_forensik' },
                { data: 'keputusan_forensik', name: 'keputusan_forensik' },
                { data: 'surat_jamin_polis', name: 'surat_jamin_polis' },
                { data: 'lakaran_lokasi', name: 'lakaran_lokasi' },
                { data: 'gambar_lokasi', name: 'gambar_lokasi' },
                { data: 'rj2_status', name: 'rj2_status' }, { data: 'rj2_tarikh', name: 'rj2_tarikh' },
                { data: 'rj9_status', name: 'rj9_status' }, { data: 'rj9_tarikh', name: 'rj9_tarikh' },
                { data: 'rj10a_status', name: 'rj10a_status' }, { data: 'rj10a_tarikh', name: 'rj10a_tarikh' },
                { data: 'rj10b_status', name: 'rj10b_status' }, { data: 'rj10b_tarikh', name: 'rj10b_tarikh' },
                { data: 'rj99_status', name: 'rj99_status' }, { data: 'rj99_tarikh', name: 'rj99_tarikh' },
                { data: 'semboyan_kesan_tangkap_status', name: 'semboyan_kesan_tangkap_status' }, { data: 'semboyan_kesan_tangkap_tarikh', name: 'semboyan_kesan_tangkap_tarikh' },
                { data: 'waran_tangkap_status', name: 'waran_tangkap_status' }, { data: 'waran_tangkap_tarikh', name: 'waran_tangkap_tarikh' },
                { data: 'ulasan_isu_rj', name: 'ulasan_isu_rj' },
                { data: 'pem1_status', name: 'pem1_status' }, { data: 'pem2_status', name: 'pem2_status' }, { data: 'pem3_status', name: 'pem3_status' }, { data: 'pem4_status', name: 'pem4_status' },
                { data: 'isu_tpr_tuduh', name: 'isu_tpr_tuduh' },
                { data: 'isu_ks_lengkap_tiada_rujuk_tpr', name: 'isu_ks_lengkap_tiada_rujuk_tpr' },
                { data: 'isu_tpr_arah_lupus_belum_laksana', name: 'isu_tpr_arah_lupus_belum_laksana' },
                { data: 'isu_tpr_arah_pulang_belum_laksana', name: 'isu_tpr_arah_pulang_belum_laksana' },
                { data: 'isu_tpr_arah_kesan_tangkap_tiada_tindakan', name: 'isu_tpr_arah_kesan_tangkap_tiada_tindakan' },
                { data: 'isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus', name: 'isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus' },
                { data: 'isu_nfa_oleh_kbsjd_sahaja', name: 'isu_nfa_oleh_kbsjd_sahaja' },
                { data: 'isu_selesai_jatuh_hukum_belum_kus_fail', name: 'isu_selesai_jatuh_hukum_belum_kus_fail' },
                { data: 'isu_ks_warisan_terbengkalai', name: 'isu_ks_warisan_terbengkalai' },
                { data: 'isu_kbsjd_simpan_ks', name: 'isu_kbsjd_simpan_ks' },
                { data: 'isu_sio_simpan_ks', name: 'isu_sio_simpan_ks' },
                { data: 'isu_ks_pada_tpr', name: 'isu_ks_pada_tpr' },
                { data: 'ks_hantar_tpr_status', name: 'ks_hantar_tpr_status' }, { data: 'ks_hantar_tpr_tarikh', name: 'ks_hantar_tpr_tarikh' },
                { data: 'ks_hantar_kjsj_status', name: 'ks_hantar_kjsj_status' }, { data: 'ks_hantar_kjsj_tarikh', name: 'ks_hantar_kjsj_tarikh' },
                { data: 'ks_hantar_d5_status', name: 'ks_hantar_d5_status' }, { data: 'ks_hantar_d5_tarikh', name: 'ks_hantar_d5_tarikh' },
                { data: 'ks_hantar_kbsjd_status', name: 'ks_hantar_kbsjd_status' }, { data: 'ks_hantar_kbsjd_tarikh', name: 'ks_hantar_kbsjd_tarikh' },
                { data: 'ulasan_isu_menarik', name: 'ulasan_isu_menarik' },
                { data: 'ulasan_keseluruhan', name: 'ulasan_keseluruhan' }
            ],
            order: [[1, 'desc']], // Default order by ID descending
            columnDefs: [
                { "width": "120px", "targets": 0 } // Ensure action column has enough space
            ]
        });
    });
    </script>
    @endpush
</x-app-layout>