<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Trafik Rule
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'TrafikRule', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Maklumat Rujukan (No. KS: {{ $paper->no_kertas_siasatan }})
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        @php
                            // Helper to display a boolean value as a styled badge
                            function show_boolean_badge($value, $trueText = 'Ya', $falseText = 'Tidak') {
                                if (is_null($value)) {
                                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
                                }
                                return $value ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>" : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                            }
                            
                            // Helper to display a JSON array as a list
                            function show_json_list($json_data) {
                                if (empty($json_data)) {
                                    return '-';
                                }
                                $html = '<ul class="list-disc list-inside space-y-1">';
                                foreach ($json_data as $item) {
                                    $html .= "<li>{$item}</li>";
                                }
                                $html .= '</ul>';
                                return $html;
                            }

                             // Helper to display status and date pairs conditionally
                            function show_status_and_date($label, $status, $date) {
                                $status_html = show_boolean_badge($status, 'Ada / Cipta', 'Tiada / Tidak Cipta');
                                $date_html = $status && $date ? optional($date)->format('d/m/Y') : '-';
                                echo "<div class='py-3 sm:py-4'><dt class='text-sm font-medium text-gray-500'>{$label}</dt><dd class='mt-1 text-sm text-gray-900'>{$status_html} | Tarikh: {$date_html}</dd></div>";
                            }
                        @endphp

                        <!-- BAHAGIAN 1: Maklumat Asas -->
                        <div class="bg-gray-50 px-4 py-3">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 1: Maklumat Asas</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            <div><dt class="text-sm font-medium text-gray-500">No. Fail LMM (T)</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->no_fail_lmm_t ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">No. Repot Polis</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->no_repot_polis ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Pegawai Penyiasat</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->pegawai_penyiasat ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Tarikh Laporan Polis Dibuka</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Seksyen</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->seksyen ?? '-' }}</dd></div>
                        </div>

                        <!-- BAHAGIAN 2: Pemeriksaan & Status -->
                        <div class="bg-gray-50 px-4 py-3"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 2: Pemeriksaan & Status</h4></div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            <div><dt class="text-sm font-medium text-gray-500">Pegawai Pemeriksa</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->pegawai_pemeriksa ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Pertama (A)</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Kedua (B)</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Sebelum Akhir (C)</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Akhir (D)</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Pemeriksaan JIPS (E)</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-' }}</dd></div>
                        </div>

                        <!-- Status Terkira -->
                        <div class="bg-yellow-50 px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                             <div>
                                <dt class="text-sm font-medium text-yellow-800">Status Lewat Edaran (>48 Jam)</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">{{ $paper->lewat_edaran_48_jam_status }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-yellow-800">Status Terbengkalai (>3 Bulan)</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">{{ $paper->terbengkalai_status }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-yellow-800">Status Kemaskini</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">{{ $paper->baru_dikemaskini_status }}</dd>
                            </div>
                        </div>

                        <!-- BAHAGIAN 3: Arahan & Keputusan -->
                        <div class="bg-gray-50 px-4 py-3"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 3: Arahan & Keputusan</h4></div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            {!! show_status_and_date('Arahan Minit Oleh SIO', $paper->getRawOriginal('arahan_minit_oleh_sio_status'), $paper->arahan_minit_oleh_sio_tarikh) !!}
                            {!! show_status_and_date('Arahan Minit Oleh Ketua Bahagian', $paper->getRawOriginal('arahan_minit_ketua_bahagian_status'), $paper->arahan_minit_ketua_bahagian_tarikh) !!}
                            {!! show_status_and_date('Arahan Minit Oleh Ketua Jabatan', $paper->getRawOriginal('arahan_minit_ketua_jabatan_status'), $paper->arahan_minit_ketua_jabatan_tarikh) !!}
                            {!! show_status_and_date('Arahan Minit Oleh YA TPR', $paper->getRawOriginal('arahan_minit_oleh_ya_tpr_status'), $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}
                            <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA TPR</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->keputusan_siasatan_oleh_ya_tpr ?? '-' }}</dd></div>
                            <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Arahan Tuduh Oleh YA TPR Diambil Tindakan</dt><dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan) !!}</dd></div>
                            <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Siasatan TPR</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keputusan_siasatan_tpr ?? '-' }}</dd></div>
                            <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa ?? '-' }}</dd></div>
                        </div>
                        <!-- BAHAGIAN 5: Dokumen Siasatan -->
                        <div class="bg-gray-50 px-4 py-3"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 5: Dokumen Siasatan</h4></div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            <div><dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->getRawOriginal('status_id_siasatan_dikemaskini'), 'Dikemaskini', 'Tidak Dikemaskini') !!}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Rajah Kasar Tempat Kejadian</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->getRawOriginal('status_rajah_kasar_tempat_kejadian'), 'Ada', 'Tiada') !!}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Gambar Tempat Kejadian</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->getRawOriginal('status_gambar_tempat_kejadian'), 'Ada', 'Tiada') !!}</dd></div>
                        </div>

                        <!-- BAHAGIAN 6: Borang & Semakan -->
                        <div class="bg-gray-50 px-4 py-3"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 6: Borang & Semakan</h4></div>
                        <div class="bg-white px-4 py-5 space-y-6">
                            <div><dt class="text-sm font-medium text-gray-500">Status PEM</dt><dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_pem) !!}</dd></div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {!! show_status_and_date('RJ 10B', $paper->getRawOriginal('status_rj10b'), $paper->tarikh_rj10b) !!}
                                <div><dt class="text-sm font-medium text-gray-500">Lain-lain RJ Dikesan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->lain_lain_rj_dikesan ?? '-' }}</dd></div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Saman PDRM (S) 257</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {!! show_boolean_badge($paper->getRawOriginal('status_saman_pdrm_s_257'), 'Dicipta', 'Tidak Dicipta') !!}
                                        @if($paper->no_saman_pdrm_s_257)
                                            | No: <span class="font-semibold">{{ $paper->no_saman_pdrm_s_257 }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Saman PDRM (S) 167</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {!! show_boolean_badge($paper->getRawOriginal('status_saman_pdrm_s_167'), 'Dicipta', 'Tidak Dicipta') !!}
                                        @if($paper->no_saman_pdrm_s_167)
                                            | No: <span class="font-semibold">{{ $paper->no_saman_pdrm_s_167 }}</span>
                                        @endif
                                    </dd>
                                </div>
                            </div>
                            <div><dt class="text-sm font-medium text-gray-500">Ulasan Pegawai Pemeriksa (Borang)</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang ?? '-' }}</dd></div>
                        </div>
                        <!-- BAHAGIAN 7: Laporan Agensi Luar -->
                        <div class="bg-gray-50 px-4 py-3"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 7: Laporan Agensi Luar</h4></div>
                        <div class="bg-white px-4 py-5 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-md">
                                {!! show_status_and_date('Permohonan Laporan JKR', $paper->getRawOriginal('status_permohonan_laporan_jkr'), $paper->tarikh_permohonan_laporan_jkr) !!}
                                {!! show_status_and_date('Laporan Penuh JKR', $paper->getRawOriginal('status_laporan_penuh_jkr'), $paper->tarikh_laporan_penuh_jkr) !!}
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-md">
                                {!! show_status_and_date('Permohonan Laporan JPJ', $paper->getRawOriginal('status_permohonan_laporan_jpj'), $paper->tarikh_permohonan_laporan_jpj) !!}
                                {!! show_status_and_date('Laporan Penuh JKJR', $paper->getRawOriginal('status_laporan_penuh_jkjr'), $paper->tarikh_laporan_penuh_jkjr) !!}
                            </div>
                            <div><dt class="text-sm font-medium text-gray-500">Lain-lain Permohonan Laporan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->lain_lain_permohonan_laporan ?? '-' }}</dd></div>
                        </div>

                        <!-- BAHAGIAN 8: Status Fail -->
                        <div class="bg-gray-50 px-4 py-3"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 8: Status Fail</h4></div>
                        <div class="bg-white px-4 py-5 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><dt class="text-sm font-medium text-gray-500">Adakah Muka Surat 4 Keputusan Kes Dicatat</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_muka_surat_4_keputusan_kes_dicatat ?? '-' }}</dd></div>
                                <div><dt class="text-sm font-medium text-gray-500">Adakah KS di KUS/FAIL</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_ks_kus_fail_selesai ?? '-' }}</dd></div>
                                <div class="md:col-span-2"><dt class="text-sm font-medium text-gray-500">Adakah Fail LMM(T) Telah Ada Keputusan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan ?? '-' }}</dd></div>
                            </div>
                            <div><dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah</dt><dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->keputusan_akhir_mahkamah) !!}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail ?? '-' }}</dd></div>
                        </div>

                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
