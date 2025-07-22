<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Komersil
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'Komersil', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Maklumat Rujukan (No. KS: {{ $paper->no_kertas_siasatan }})
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-0">
                    @php
                        function show_boolean_badge($value, $trueText = 'Ya', $falseText = 'Tidak') {
                            if (is_null($value)) {
                                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
                            }
                            return $value ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>" : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                        }
                        function show_json_list($json_data) {
                            if (empty($json_data)) return '-';
                            if (is_string($json_data)) $json_data = json_decode($json_data, true);
                            if (!is_array($json_data)) return '-';
                            $html = '<ul class="list-disc list-inside space-y-1">';
                            foreach ($json_data as $item) {
                                $html .= "<li>{$item}</li>";
                            }
                            $html .= '</ul>';
                            return $html;
                        }
                        function show_status_and_date($label, $status, $date) {
                            $status_html = show_boolean_badge($status, 'Ada / Cipta', 'Tiada / Tidak Cipta');
                            $date_html = $status && $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '-';
                            return "<div class='py-3 sm:py-4'><dt class='text-sm font-medium text-gray-500'>{$label}</dt><dd class='mt-1 text-sm text-gray-900'>{$status_html} | Tarikh: {$date_html}</dd></div>";
                        }
                    @endphp

                    <!-- BAHAGIAN 1: Maklumat Asas -->
                    <div class="bg-gray-50 px-4 py-3 mb-2">
                        <h4 class="text-md font-semibold text-gray-700">BAHAGIAN 1: Maklumat Asas</h4>
                    </div>
                    <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6 mb-8">
                        <div><dt class="text-sm font-medium text-gray-500">No. Kertas Siasatan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->no_kertas_siasatan ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">No. Report Polis</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->no_report_polis ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Pegawai Penyiasat</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->pegawai_penyiasat ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Laporan Polis Dibuka</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Seksyen</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->seksyen ?? '-' }}</dd></div>
                    </div>

                    <!-- BAHAGIAN 2: Pemeriksaan JIPS -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 2: Pemeriksaan JIPS</h4></div>
                    <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6 mb-8">
                        <div><dt class="text-sm font-medium text-gray-500">Pegawai Pemeriksa</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->pegawai_pemeriksa ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Pertama</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Kedua</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Sebelum Akhir</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Akhir</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Pemeriksaan JIPS ke Daerah</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-' }}</dd></div>
                    </div>

                    <!-- BAHAGIAN 3: Arahan SIO & Ketua -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 3: Arahan SIO & Ketua</h4></div>
                    <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-8">
                        {!! show_status_and_date('Arahan Minit Oleh SIO', $paper->arahan_minit_oleh_sio_status, $paper->arahan_minit_oleh_sio_tarikh) !!}
                        {!! show_status_and_date('Arahan Minit Ketua Bahagian', $paper->arahan_minit_ketua_bahagian_status, $paper->arahan_minit_ketua_bahagian_tarikh) !!}
                        {!! show_status_and_date('Arahan Minit Ketua Jabatan', $paper->arahan_minit_ketua_jabatan_status, $paper->arahan_minit_ketua_jabatan_tarikh) !!}
                        {!! show_status_and_date('Arahan Minit Oleh YA TPR', $paper->arahan_minit_oleh_ya_tpr_status, $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}
                        <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA TPR</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->keputusan_siasatan_oleh_ya_tpr ?? '-' }}</dd></div>
                        <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Arahan Tuduh Oleh YA TPR Diambil Tindakan</dt><dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan) !!}</dd></div>
                        <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Siasatan TPR</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keputusan_siasatan_tpr ?? '-' }}</dd></div>
                        <div class="md:col-span-2 py-3 sm:py-4"><dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Pegawai Pemeriksa</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keputusan_pegawai_pemeriksa ?? '-' }}</dd></div>
                    </div>

                    <!-- BAHAGIAN 4: Barang Kes -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 4: Barang Kes</h4></div>
                    <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-8">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adakah Barang Kes Didaftarkan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->adakah_barang_kes_didaftarkan) !!}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">No Daftar Barang Kes (AM)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_barang_kes_am ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">No Daftar Barang Kes (Berharga)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_barang_kes_berharga ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">No Daftar Barang Kes (Kenderaan)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paper->no_daftar_barang_kes_kenderaan ?? '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Status Pergerakan Barang Kes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_pergerakan_barang_kes) !!}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Status Barang Kes Selesai Siasatan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_barang_kes_selesai_siasatan) !!}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Barang Kes Dilupusan Bagaimana Kaedah Pelupusan Dilaksanakan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan) !!}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Adakah Pelupusan Barang Kes Wang Tunai ke Perbendaharaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan) !!}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Resit KEW 98E Bagi Pelupusan Barang Kes Wang Tunai ke Perbendaharaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->resit_kew_98e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbencaharaan) !!}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Adakah Borang Serah Terima Pegawai Tangkapan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->adakah_borang_serah_terima_pegawai_tangkapan) !!}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adakah Borang Serah Terima Pemilik Saksi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_borang_serah_terima_pemilik_saksi ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adakah Sijil Surat Kebenaran IPO</dt>
                            <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->adakah_sijil_surat_kebenaran_ipo) !!}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adakah Gambar Pelupusan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_gambar_pelupusan ?? '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa ?? '-' }}</dd>
                        </div>
                    </div>

                    <!-- BAHAGIAN 5: Bukti & Rajah -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 5: Bukti & Rajah</h4></div>
                    <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6 mb-8">
                        <div><dt class="text-sm font-medium text-gray-500">Status ID Siasatan Dikemaskini</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_id_siasatan_dikemaskini) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Rajah Kasar Tempat Kejadian</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rajah_kasar_tempat_kejadian) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Gambar Tempat Kejadian</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_tempat_kejadian) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Gambar Barang Kes AM</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_am) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Gambar Barang Kes Berharga</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_berharga) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Gambar Barang Kes Kenderaan</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_kenderaan) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Gambar Barang Kes Darah</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_darah) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Gambar Barang Kes Kontraban</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_gambar_barang_kes_kontraban) !!}</dd></div>
                    </div>

                    <!-- BAHAGIAN 6: Laporan RJ & Semboyan -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 6: Laporan RJ & Semboyan</h4></div>
                    <div class="bg-white px-4 py-5 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-8">
                        <div class="md:col-span-2"><dt class="text-sm font-medium text-gray-500">Status PEM</dt><dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->status_pem) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status RJ2</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rj2) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh RJ2</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_rj2)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status RJ2B</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rj2b) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh RJ2B</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_rj2b)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status RJ9</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rj9) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh RJ9</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_rj9)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status RJ99</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rj99) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh RJ99</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_rj99)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status RJ10A</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rj10a) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh RJ10A</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_rj10a)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status RJ10B</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_rj10b) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh RJ10B</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_rj10b)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-sm font-medium text-gray-500">Lain-lain RJ Dikesan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->lain_lain_rj_dikesan ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Semboyan Pertama Wanted Person</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_semboyan_pertama_wanted_person) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Pertama Wanted Person</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_semboyan_pertama_wanted_person)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Semboyan Kedua Wanted Person</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_semboyan_kedua_wanted_person) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Kedua Wanted Person</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_semboyan_kedua_wanted_person)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Semboyan Ketiga Wanted Person</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_semboyan_ketiga_wanted_person) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Ketiga Wanted Person</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_semboyan_ketiga_wanted_person)->format('d/m/Y') ?? '-' }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa Borang</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang ?? '-' }}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Status Penandaan Kelas Warna</dt><dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_penandaan_kelas_warna) !!}</dd></div>
                    </div>

                    <!-- BAHAGIAN 7: Laporan E-FSA, Puspakom, dll -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 7: Laporan E-FSA, Puspakom, dll</h4></div>
                    <div class="bg-white px-4 py-5 space-y-6 mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-md">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Permohonan E-FSA-1 oleh IO/AIO</dt>
                                <dd class="mt-1 text-sm text-gray-900">{!! show_boolean_badge($paper->status_permohonan_E_FSA_1_oleh_IO_AIO) !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tarikh Laporan Penuh E-FSA-1 oleh IO/AIO</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional($paper->tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO)->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            {{-- Repeat for all E-FSA, Telco, Imigresen, Puspakom, Kastam, Forensik, etc. fields as needed --}}
                        </div>
                        {{-- Add more grids for other agencies as needed --}}
                        <div><dt class="text-sm font-medium text-gray-500">Lain-lain Permohonan Laporan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->lain_lain_permohonan_laporan ?? '-' }}</dd></div>
                    </div>

                    <!-- BAHAGIAN 8: Penilaian Akhir -->
                    <div class="bg-gray-50 px-4 py-3 mb-2"><h4 class="text-md font-semibold text-gray-700">BAHAGIAN 8: Penilaian Akhir</h4></div>
                    <div class="bg-white px-4 py-5 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><dt class="text-sm font-medium text-gray-500">Status Muka Surat 4 Barang Kes Ditulis Bersama No Daftar</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Status Muka Surat 4 Barang Kes Ditulis Bersama No Daftar dan Telah Ada Arahan YA TPR</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Adakah Muka Surat 4 Keputusan Kes Dicatat</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_muka_surat_4_keputusan_kes_dicatat ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Adakah Fail LMM/T atau LMM Telah Ada Keputusan</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan ?? '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-gray-500">Adakah KS/KUS/Fail Selesai</dt><dd class="mt-1 text-sm text-gray-900">{{ $paper->adakah_ks_kus_fail_selesai ?? '-' }}</dd></div>
                        </div>
                        <div><dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah</dt><dd class="mt-1 text-sm text-gray-900">{!! show_json_list($paper->keputusan_akhir_mahkamah) !!}</dd></div>
                        <div><dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa Fail</dt><dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail ?? '-' }}</dd></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>