<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Trafik Seksyen
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'TrafikSeksyen', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
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
                        <!-- BAHAGIAN 1: Maklumat Asas -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 1: Maklumat Asas</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Repot Polis</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_repot_polis ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pegawai Penyiasat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->pegawai_penyiasat ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Laporan Polis Dibuka</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Seksyen</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->seksyen ?? '-' }}</dd>
                            </div>
                        </div>

                                                <!-- BAHAGIAN 2: Pemeriksaan & Status -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 2: Pemeriksaan & Status</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pegawai Pemeriksa</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->pegawai_pemeriksa ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Pertama (A)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Kedua (B)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Sebelum Akhir (C)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Akhir (D)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
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
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 3: Arahan & Keputusan</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                            @php
                                // Helper to display status and date pairs
                                function show_status_and_date($label, $status, $date) {
                                    $status_html = $status ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ada</span>' : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tiada</span>';
                                    $date_html = $status && $date ? optional($date)->format('d/m/Y') : '-';
                                    echo "<div><dt class='text-sm font-medium text-gray-500'>{$label}</dt><dd class='mt-1 text-sm text-gray-900'>{$status_html} | Tarikh: {$date_html}</dd></div>";
                                }
                            @endphp

                            {!! show_status_and_date('Arahan Minit Oleh SIO', $paper->arahan_minit_oleh_sio_status, $paper->arahan_minit_oleh_sio_tarikh) !!}
                            {!! show_status_and_date('Arahan Minit Oleh Ketua Bahagian', $paper->arahan_minit_ketua_bahagian_status, $paper->arahan_minit_ketua_bahagian_tarikh) !!}
                            {!! show_status_and_date('Arahan Minit Oleh Ketua Jabatan', $paper->arahan_minit_ketua_jabatan_status, $paper->arahan_minit_ketua_jabatan_tarikh) !!}
                            {!! show_status_and_date('Arahan Minit Oleh YA TPR', $paper->arahan_minit_oleh_ya_tpr_status, $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}
                            
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA TPR</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->keputusan_siasatan_oleh_ya_tpr ?? '-' }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Adakah Arahan Tuduh Oleh YA TPR Diambil Tindakan</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(!empty($paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan))
                                        <ul class="list-disc list-inside">
                                            @foreach($paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Siasatan TPR</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keputusan_siasatan_tpr ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA Koroner</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->keputusan_siasatan_oleh_ya_koroner ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Oleh YA Koroner</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->ulasan_keputusan_oleh_ya_koroner ?? '-' }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa ?? '-' }}</dd>
                            </div>
                        </div>
                                            <!-- BAHAGIAN 4: Barang Kes -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 4: Barang Kes</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            @php
                                // Helper to display a boolean value as a styled badge
                                function show_boolean_badge($value, $trueText = 'Ya', $falseText = 'Tidak') {
                                    if (is_null($value)) return '-';
                                    return $value ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>" : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                                }
                                
                                // Helper to display a JSON array as a list
                                function show_json_list($json_data) {
                                    if (empty($json_data)) return '-';
                                    $html = '<ul class="list-disc list-inside space-y-1">';
                                    foreach ($json_data as $item) {
                                        $html .= "<li>{$item}</li>";
                                    }
                                    $html .= '</ul>';
                                    return $html;
                                }
                            @endphp

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Adakah Barang Kes Didaftarkan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->adakah_barang_kes_didaftarkan) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Am</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_barang_kes_am ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Berharga</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->status_gambar_barang_kes_berharga_text }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Kenderaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_barang_kes_kenderaan ?? '-' }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500">No. Daftar Botol Spesimen Urin</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_botol_spesimen_urin ?? '-' }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500">No. Daftar Spesimen Darah</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_spesimen_darah ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Am</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->jenis_barang_kes_am ?? '-' }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Berharga</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->jenis_barang_kes_berharga ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Kenderaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->jenis_barang_kes_kenderaan ?? '-' }}</dd>
                            </div>

                            <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Status Pergerakan Barang Kes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_pergerakan_barang_kes) !!}</dd>
                            </div>
                             <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Status Barang Kes Selesai Siasatan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_barang_kes_selesai_siasatan) !!}</dd>
                            </div>
                            <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Kaedah Pelupusan Dilaksanakan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan) !!}</dd>
                            </div>
                            <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Pelupusan Dengan Arahan Mahkamah / YA TPR</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan) !!}</dd>
                            </div>
                             <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Resit Kew.38e Bagi Pelupusan Wang Tunai</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan) !!}</dd>
                            </div>
                             <div class="md:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Borang Serah/Terima (Pegawai Tangkapan & IO/AIO)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->adakah_borang_serah_terima_pegawai_tangkapan) !!}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Borang Serah/Terima (Penyiasat, Pemilik, Saksi)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_borang_serah_terima_pemilik_saksi ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sijil / Surat Arahan Pelupusan Oleh IPD</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Pelupusan Dilampirkan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_gambar_pelupusan ?? '-' }}</dd>
                            </div>
                        </div>
                                            <!-- BAHAGIAN 5: Dokumen Siasatan -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 5: Dokumen Siasatan</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini') !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rajah Kasar Tempat Kejadian</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rajah_kasar_tempat_kejadian) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Tempat Kejadian</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_tempat_kejadian) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Post-Mortem Mayat Di Hospital</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_post_mortem_mayat_di_hospital) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Am</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_am) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Berharga</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_berharga) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Kenderaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_kenderaan) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Darah</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_darah) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Kontraban</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_kontraban) !!}</dd>
                            </div>
                        </div>                        <!-- BAHAGIAN 5: Dokumen Siasatan -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 5: Dokumen Siasatan</h4>
                        </div>
                        <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini') !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rajah Kasar Tempat Kejadian</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rajah_kasar_tempat_kejadian) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Tempat Kejadian</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_tempat_kejadian) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Post-Mortem Mayat Di Hospital</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_post_mortem_mayat_di_hospital) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Am</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_am) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Kenderaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_kenderaan) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Darah</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_darah) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Kontraban</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_kontraban) !!}</dd>
                            </div>
                        </div>
                                            <!-- BAHAGIAN 6: Borang & Semakan -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 6: Borang & Semakan</h4>
                        </div>
                        <div class="bg-white px-4 py-5 space-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status PEM</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_pem) !!}</dd>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                                {!! show_status_and_date('RJ 2', $paper->status_rj2, $paper->tarikh_rj2) !!}
                                {!! show_status_and_date('RJ 2B', $paper->status_rj2b, $paper->tarikh_rj2b) !!}
                                {!! show_status_and_date('RJ 9', $paper->status_rj9, $paper->tarikh_rj9) !!}
                                {!! show_status_and_date('RJ 99', $paper->status_rj99, $paper->tarikh_rj99) !!}
                                {!! show_status_and_date('RJ 10A', $paper->status_rj10a, $paper->tarikh_rj10a) !!}
                                {!! show_status_and_date('RJ 10B', $paper->status_rj10b, $paper->tarikh_rj10b) !!}
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Lain-lain RJ Dikesan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->lain_lain_rj_dikesan ?? '-' }}</dd>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Saman PDRM (S) 257</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {!! show_boolean_badge($paper->status_saman_pdrm_s_257, 'Dicipta', 'Tidak Dicipta') !!}
                                        @if($paper->status_saman_pdrm_s_257 && $paper->no_saman_pdrm_s_257)
                                            | No: <span class="font-semibold">{{ $paper->no_saman_pdrm_s_257 }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Saman PDRM (S) 167</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {!! show_boolean_badge($paper->status_saman_pdrm_s_167, 'Dicipta', 'Tidak Dicipta') !!}
                                        @if($paper->status_saman_pdrm_s_167 && $paper->no_saman_pdrm_s_167)
                                            | No: <span class="font-semibold">{{ $paper->no_saman_pdrm_s_167 }}</span>
                                        @endif
                                    </dd>
                                </div>
                            </div>

                             <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                                {!! show_status_and_date('Semboyan Pertama Wanted Person', $paper->status_semboyan_pertama_wanted_person, $paper->tarikh_semboyan_pertama_wanted_person) !!}
                                {!! show_status_and_date('Semboyan Kedua Wanted Person', $paper->status_semboyan_kedua_wanted_person, $paper->tarikh_semboyan_kedua_wanted_person) !!}
                                {!! show_status_and_date('Semboyan Ketiga Wanted Person', $paper->status_semboyan_ketiga_wanted_person, $paper->tarikh_semboyan_ketiga_wanted_person) !!}
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Adakah Penandaan Kelas Warna Pada Kulit Kertas Siasatan Dibuat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_penandaan_kelas_warna) !!}</dd>
                            </div>
                        </div>
                            <!-- BAHAGIAN 7: Permohonan Laporan Agensi Luar -->
                            <div class="bg-gray-50 px-4 py-5">
                                <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 7: Permohonan Laporan Agensi Luar</h4>
                            </div>
                            <div class="bg-white px-4 py-5 space-y-8">
                                <!-- Permohonan Laporan Post Mortem Mayat -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">Permohonan Laporan Post Mortem Mayat</h5></div>
                                    {!! show_status_and_date('Status Permohonan', $paper->status_permohonan_laporan_post_mortem_mayat, $paper->tarikh_permohonan_laporan_post_mortem_mayat) !!}
                                </div>

                                <!-- Bedah Siasat -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">Bedah Siasat</h5></div>
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_bedah_siasat, $paper->tarikh_laporan_penuh_bedah_siasat) !!}
                                </div>

                                <!-- Jabatan Kimia -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">Jabatan Kimia</h5></div>
                                    {!! show_status_and_date('Permohonan Laporan', $paper->status_permohonan_laporan_jabatan_kimia, $paper->tarikh_permohonan_laporan_jabatan_kimia) !!}
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_jabatan_kimia, $paper->tarikh_laporan_penuh_jabatan_kimia) !!}
                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Keputusan Laporan</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $paper->keputusan_laporan_jabatan_kimia ?? '-' }}</dd>
                                    </div>
                                </div>

                                <!-- Jabatan Patalogi -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">Jabatan Patalogi</h5></div>
                                    {!! show_status_and_date('Permohonan Laporan', $paper->status_permohonan_laporan_jabatan_patalogi, $paper->tarikh_permohonan_laporan_jabatan_patalogi) !!}
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_jabatan_patalogi, $paper->tarikh_laporan_penuh_jabatan_patalogi) !!}
                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Keputusan Laporan</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $paper->keputusan_laporan_jabatan_patalogi ?? '-' }}</dd>
                                    </div>
                                </div>

                                <!-- PUSPAKOM -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">PUSPAKOM</h5></div>
                                    {!! show_status_and_date('Permohonan Laporan', $paper->status_permohonan_laporan_puspakom, $paper->tarikh_permohonan_laporan_puspakom) !!}
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_puspakom, $paper->tarikh_laporan_penuh_puspakom) !!}
                                </div>

                                <!-- JKR -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">JKR</h5></div>
                                    {!! show_status_and_date('Permohonan Laporan', $paper->status_permohonan_laporan_jkr, $paper->tarikh_permohonan_laporan_jkr) !!}
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_jkr, $paper->tarikh_laporan_penuh_jkr) !!}
                                </div>

                                <!-- JPJ -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">JPJ</h5></div>
                                    {!! show_status_and_date('Permohonan Laporan', $paper->status_permohonan_laporan_jpj, $paper->tarikh_permohonan_laporan_jpj) !!}
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_jpj, $paper->tarikh_laporan_penuh_jpj) !!}
                                </div>

                                <!-- Imigresen -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">Imigresen</h5></div>
                                    {!! show_status_and_date('Permohonan Laporan', $paper->status_permohonan_laporan_imigresen, $paper->tarikh_permohonan_laporan_imigresen) !!}
                                    {!! show_status_and_date('Laporan Penuh', $paper->status_laporan_penuh_imigresen, $paper->tarikh_laporan_penuh_imigresen) !!}
                                </div>

                                <!-- Lain-lain Permohonan Laporan -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 p-4 border rounded-md">
                                    <div class="md:col-span-2"><h5 class="font-semibold text-gray-800">Lain-lain Permohonan Laporan</h5></div>
                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $paper->lain_lain_permohonan_laporan ?? '-' }}</dd>
                                    </div>
                                </div>
                            </div>
                                            <!-- BAHAGIAN 8: Status Fail -->
                        <div class="bg-gray-50 px-4 py-5">
                            <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 8: Status Fail</h4>
                        </div>
                        <div class="bg-white px-4 py-5 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Muka Surat 4 - Barang Kes Ditulis Bersama No Daftar</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->muka_surat_4_barang_kes_ditulis) !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Muka Surat 4 - Dengan Arahan TPR Untuk Pelupusan/Serahan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->muka_surat_4_dengan_arahan_tpr) !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Muka Surat 4 - Keputusan Kes Dicatat Selengkapnya</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->muka_surat_4_keputusan_kes_dicatat) !!}</dd>
                                </div>
                                 <div>
                                    <dt class="text-sm font-medium text-gray-500">Fail L.M.M Ada Keputusan Siasatan Oleh YA Koroner</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->fail_lmm_ada_keputusan_koroner) !!}</dd>
                                </div>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status KS di KUS/FAIL</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paper->status_kus_fail ?? '-' }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah Sebelum KS di KUS/FAIL</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->keputusan_akhir_mahkamah) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_pegawai_pemeriksa_fail ?? '-' }}</dd>
                            </div>
                        </div>        
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>