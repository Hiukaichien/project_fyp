<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: {{ $kertasSiasatan->no_ks }}
            </h2>
            <div>
                @if($kertasSiasatan->project)
                    <a href="{{ route('projects.show', $kertasSiasatan->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                        Kembali ke Projek: {{ $kertasSiasatan->project->name }}
                    </a>
                @else
                    {{-- Fallback if the paper somehow has no project --}}
                    <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                        Kembali ke Senarai Projek
                    </a>
                @endif
                <a href="{{ route('kertas_siasatan.edit', $kertasSiasatan->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                @php
                    function display_field($label, $value, $isDate = false) {
                        $displayValue = $value ?? '<span class="text-gray-400 italic">Tiada Data</span>';
                        if ($isDate && $value instanceof \Carbon\Carbon) {
                            $displayValue = $value->format('d/m/Y');
                        } elseif ($isDate && !empty($value)) {
                            try {
                                $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y');
                            } catch (\Exception $e) {
                                $displayValue = e($value); // Show original if parsing fails
                            }
                        } elseif (is_string($value) && !empty($value)) {
                            $displayValue = e($value);
                        } elseif (empty($value)) {
                             $displayValue = '<span class="text-gray-400 italic">Tiada Data</span>';
                        }
                        
                        echo '<div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">';
                        echo '<dt class="text-sm font-medium text-gray-500">' . e($label) . '</dt>';
                        echo '<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">' . $displayValue . '</dd>';
                        echo '</div>';
                    }
                    function display_textarea($label, $value) {
                         $displayValue = !empty($value) ? nl2br(e($value)) : '<span class="text-gray-400 italic">Tiada Data</span>';
                         echo '<div class="py-3 sm:py-4 sm:px-6">';
                         echo '<dt class="text-sm font-medium text-gray-500">' . e($label) . '</dt>';
                         echo '<dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">' . $displayValue . '</dd>';
                         echo '</div>';
                    }
                @endphp

                {{-- Maklumat Asas --}}
                <div class="px-4 py-5 sm:px-6"><h3 class="text-lg leading-6 font-medium text-gray-900">Maklumat Asas</h3><p class="mt-1 max-w-2xl text-sm text-gray-500">Maklumat awal kertas siasatan.</p></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('No. Kertas Siasatan', $kertasSiasatan->no_ks) !!}
                    {!! display_field('Tarikh KS', $kertasSiasatan->tarikh_ks, true) !!}
                    {!! display_field('No. Repot', $kertasSiasatan->no_report) !!}
                    {!! display_field('Jenis Jabatan / KS', $kertasSiasatan->jenis_jabatan_ks) !!}
                    {!! display_field('Pegawai Penyiasat', $kertasSiasatan->pegawai_penyiasat) !!}
                    {!! display_field('Status KS', $kertasSiasatan->status_ks) !!}
                    {!! display_field('Status Kes', $kertasSiasatan->status_kes) !!}
                    {!! display_field('Seksyen', $kertasSiasatan->seksyen) !!}
                </dl></div>

                {{-- Minit Edaran & Status --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Minit Edaran & Status</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                   {!! display_field('Tarikh Edaran Minit Pertama (A)', $kertasSiasatan->tarikh_minit_a, true) !!}
                   {!! display_field('Tarikh Edaran Minit Kedua (B)', $kertasSiasatan->tarikh_minit_b, true) !!}
                   {!! display_field('Tarikh Edaran Sebelum Minit Terakhir (C)', $kertasSiasatan->tarikh_minit_c, true) !!}
                   {!! display_field('Tarikh Edaran Minit Terakhir (D)', $kertasSiasatan->tarikh_minit_d, true) !!}
                   {!! display_field('Status: Edaran > 24 Jam', $kertasSiasatan->edar_lebih_24_jam_status) !!}
                   {!! display_field('Status: Terbengkalai 3 Bulan', $kertasSiasatan->terbengkalai_3_bulan_status) !!}
                   {!! display_field('Status: Baru Kemaskini', $kertasSiasatan->baru_kemaskini_status) !!}
                </dl></div>

                {{-- Status Semasa Diperiksa --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Status Semasa Diperiksa</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                   {!! display_field('Status Dipilih', $kertasSiasatan->status_ks_semasa_diperiksa) !!}
                   {!! display_field('Tarikh Status', $kertasSiasatan->tarikh_status_ks_semasa_diperiksa, true) !!}
                </dl></div>

                {{-- Rakaman Percakapan --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Rakaman Percakapan</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Rakaman Pengadu', $kertasSiasatan->rakaman_pengadu) !!}
                    {!! display_field('Rakaman Saspek', $kertasSiasatan->rakaman_saspek) !!}
                    {!! display_field('Rakaman Saksi', $kertasSiasatan->rakaman_saksi) !!}
                </dl></div>

                {{-- ID Siasatan --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">ID Siasatan</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('ID Siasatan Dilampirkan', $kertasSiasatan->id_siasatan_dilampirkan) !!}
                    {!! display_field('Tarikh ID Dilampirkan', $kertasSiasatan->tarikh_id_siasatan_dilampirkan, true) !!}
                </dl></div>

                {{-- Barang Kes --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Barang Kes</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Barang Kes AM Didaftar', $kertasSiasatan->barang_kes_am_didaftar) !!}
                    {!! display_field('No. Daftar Kes AM', $kertasSiasatan->no_daftar_kes_am) !!}
                    {!! display_field('No. Daftar Kes Senjata Api', $kertasSiasatan->no_daftar_kes_senjata_api) !!}
                    {!! display_field('No. Daftar Kes Berharga', $kertasSiasatan->no_daftar_kes_berharga) !!}
                    {!! display_field('Gambar Rampasan Dilampirkan', $kertasSiasatan->gambar_rampasan_dilampirkan) !!}
                    {!! display_field('Kedudukan Barang Kes', $kertasSiasatan->kedudukan_barang_kes) !!}
                    {!! display_field('Surat Serah Terima Stor', $kertasSiasatan->surat_serah_terima_stor) !!}
                    {!! display_field('Arahan Pelupusan', $kertasSiasatan->arahan_pelupusan) !!}
                    {!! display_field('Tatacara Pelupusan', $kertasSiasatan->tatacara_pelupusan) !!}
                    {!! display_field('Resit Kew.38E Dilampirkan', $kertasSiasatan->resit_kew38e_dilampirkan) !!}
                    {!! display_field('Sijil Pelupusan Dilampirkan', $kertasSiasatan->sijil_pelupusan_dilampirkan) !!}
                    {!! display_field('Gambar Pelupusan Dilampirkan', $kertasSiasatan->gambar_pelupusan_dilampirkan) !!}
                    {!! display_field('Surat Serah Terima Penuntut', $kertasSiasatan->surat_serah_terima_penuntut) !!}
                    {!! display_textarea('Ulasan Barang Kes', $kertasSiasatan->ulasan_barang_kes) !!}
                </dl></div>

                {{-- Pakar Judi / Forensik --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Pakar Judi / Forensik</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Surat Mohon Pakar Judi', $kertasSiasatan->surat_mohon_pakar_judi) !!}
                    {!! display_field('Laporan Pakar Judi', $kertasSiasatan->laporan_pakar_judi) !!}
                    {!! display_field('Keputusan Pakar Judi', $kertasSiasatan->keputusan_pakar_judi) !!}
                    {!! display_field('Kategori Perjudian', $kertasSiasatan->kategori_perjudian) !!}
                    {!! display_field('Surat Mohon Forensik', $kertasSiasatan->surat_mohon_forensik) !!}
                    {!! display_field('Laporan Forensik', $kertasSiasatan->laporan_forensik) !!}
                    {!! display_field('Keputusan Forensik', $kertasSiasatan->keputusan_forensik) !!}
                </dl></div>
                
                {{-- Dokumen Lain --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Dokumen Lain</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Surat Jamin Polis', $kertasSiasatan->surat_jamin_polis) !!}
                    {!! display_field('Lakaran Lokasi', $kertasSiasatan->lakaran_lokasi) !!}
                    {!! display_field('Gambar Lokasi', $kertasSiasatan->gambar_lokasi) !!}
                </dl></div>

                {{-- RJ Forms --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Borang Rekod Jenayah (RJ)</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Status RJ2', $kertasSiasatan->rj2_status) !!} {!! display_field('Tarikh RJ2', $kertasSiasatan->rj2_tarikh, true) !!}
                    {!! display_field('Status RJ9', $kertasSiasatan->rj9_status) !!} {!! display_field('Tarikh RJ9', $kertasSiasatan->rj9_tarikh, true) !!}
                    {!! display_field('Status RJ10A', $kertasSiasatan->rj10a_status) !!} {!! display_field('Tarikh RJ10A', $kertasSiasatan->rj10a_tarikh, true) !!}
                    {!! display_field('Status RJ10B', $kertasSiasatan->rj10b_status) !!} {!! display_field('Tarikh RJ10B', $kertasSiasatan->rj10b_tarikh, true) !!}
                    {!! display_field('Status RJ99', $kertasSiasatan->rj99_status) !!} {!! display_field('Tarikh RJ99', $kertasSiasatan->rj99_tarikh, true) !!}
                    {!! display_field('Status Semboyan Kesan/Tangkap', $kertasSiasatan->semboyan_kesan_tangkap_status) !!} {!! display_field('Tarikh Semboyan', $kertasSiasatan->semboyan_kesan_tangkap_tarikh, true) !!}
                    {!! display_field('Status Waran Tangkap', $kertasSiasatan->waran_tangkap_status) !!} {!! display_field('Tarikh Waran Tangkap', $kertasSiasatan->waran_tangkap_tarikh, true) !!}
                    {!! display_textarea('Ulasan Isu RJ', $kertasSiasatan->ulasan_isu_rj) !!}
                </dl></div>
                
                {{-- Surat Pemberitahuan --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Surat Pemberitahuan (Pem)</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Status Pem 1', $kertasSiasatan->pem1_status) !!}
                    {!! display_field('Status Pem 2', $kertasSiasatan->pem2_status) !!}
                    {!! display_field('Status Pem 3', $kertasSiasatan->pem3_status) !!}
                    {!! display_field('Status Pem 4', $kertasSiasatan->pem4_status) !!}
                </dl></div>

                {{-- Isu-Isu Pemeriksaan --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Isu-Isu Pemeriksaan</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Isu: Arahan TPR Tuduh Tidak Dilaksana', $kertasSiasatan->isu_tpr_tuduh) !!}
                    {!! display_field('Isu: KS Lengkap Tidak Rujuk TPR', $kertasSiasatan->isu_ks_lengkap_tiada_rujuk_tpr) !!}
                    {!! display_field('Isu: Arahan Lupus Belum Laksana', $kertasSiasatan->isu_tpr_arah_lupus_belum_laksana) !!}
                    {!! display_field('Isu: Arahan Pulang Belum Laksana', $kertasSiasatan->isu_tpr_arah_pulang_belum_laksana) !!}
                    {!! display_field('Isu: Arahan Kesan/Tangkap Tiada Tindakan', $kertasSiasatan->isu_tpr_arah_kesan_tangkap_tiada_tindakan) !!}
                    {!! display_field('Isu: Jatuh Hukum, BK Tidak Rujuk Lupus', $kertasSiasatan->isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus) !!}
                    {!! display_field('Isu: NFA Oleh KBSJD Sahaja', $kertasSiasatan->isu_nfa_oleh_kbsjd_sahaja) !!}
                    {!! display_field('Isu: Selesai Jatuh Hukum, Belum KUS/Fail', $kertasSiasatan->isu_selesai_jatuh_hukum_belum_kus_fail) !!}
                    {!! display_field('Isu: KS Warisan Terbengkalai', $kertasSiasatan->isu_ks_warisan_terbengkalai) !!}
                    {!! display_field('Isu: KBSJD Simpan KS', $kertasSiasatan->isu_kbsjd_simpan_ks) !!}
                    {!! display_field('Isu: SIO Simpan KS', $kertasSiasatan->isu_sio_simpan_ks) !!}
                    {!! display_field('Isu: KS Masih Pada TPR', $kertasSiasatan->isu_ks_pada_tpr) !!}
                </dl></div>
                
                {{-- Status Penghantaran KS --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Status Penghantaran KS</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_field('Hantar ke TPR', $kertasSiasatan->ks_hantar_tpr_status) !!} {!! display_field('Tarikh Hantar', $kertasSiasatan->ks_hantar_tpr_tarikh, true) !!}
                    {!! display_field('Hantar ke KJSJ', $kertasSiasatan->ks_hantar_kjsj_status) !!} {!! display_field('Tarikh Hantar', $kertasSiasatan->ks_hantar_kjsj_tarikh, true) !!}
                    {!! display_field('Hantar ke D5', $kertasSiasatan->ks_hantar_d5_status) !!} {!! display_field('Tarikh Hantar', $kertasSiasatan->ks_hantar_d5_tarikh, true) !!}
                    {!! display_field('Hantar ke KBSJD', $kertasSiasatan->ks_hantar_kbsjd_status) !!} {!! display_field('Tarikh Hantar', $kertasSiasatan->ks_hantar_kbsjd_tarikh, true) !!}
                </dl></div>

                {{-- Ulasan Pemeriksa --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200"><h3 class="text-lg leading-6 font-medium text-gray-900">Ulasan Pemeriksa</h3></div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200">
                    {!! display_textarea('Ulasan Isu Menarik', $kertasSiasatan->ulasan_isu_menarik) !!}
                    {!! display_textarea('Ulasan Keseluruhan', $kertasSiasatan->ulasan_keseluruhan) !!}
                </dl></div>

            </div> {{-- Close bg-white div --}}
        </div> {{-- Close container div --}}
    </div> {{-- Close padding div --}}
</x-app-layout>