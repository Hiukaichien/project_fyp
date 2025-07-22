<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Komersil ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'Komersil', 'id' => $paper->id]) }}" class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
                @csrf
                @method('PUT')

                @php
                 // Helper function for simple boolean choices (e.g., Ada/Tiada, Ya/Tidak)
                function render_boolean_select($name, $currentValue, $YaLabel = 'Ya', $TidakLabel = 'Tidak') {
                    $options = ['' => '-- Sila Pilih --', '1' => $YaLabel, '0' => $TidakLabel];
                    $html = "<select name='{$name}' id='{$name}' class='mt-1 block w-full form-select'>";
                    foreach ($options as $value => $label) {
                        $selected = (string)old($name, $currentValue) === (string)$value && old($name, $currentValue) !== null ? 'selected' : '';
                        $html .= "<option value='{$value}' {$selected}>{$label}</option>";
                    }
                    $html .= "</select>";
                    return $html;
                }
                
                // Helper for status choices that reveal a date input
                function render_status_with_date($id, $statusName, $dateName, $currentStatus, $currentDate, $YaLabel = 'Ya', $TidakLabel = 'Tidak') {
                    $html = "<div x-data='{ status: " . (int)old($statusName, $currentStatus) . " }'>";
                    $html .= "<select name='{$statusName}' x-model='status' class='mt-1 block w-full form-select'>";
                    $html .= "<option value='0'>{$TidakLabel}</option>";
                    $html .= "<option value='1'>{$YaLabel}</option>";
                    $html .= "</select>";
                    $html .= "<div x-show='status == 1' x-transition class='mt-2'>";
                    $html .= "<label for='{$dateName}_{$id}' class='text-sm text-gray-600'>Jika Ada, nyatakan tarikh:</label>";
                    $html .= "<input type='date' name='{$dateName}' id='{$dateName}_{$id}' value='" . old($dateName, optional($currentDate)->format('Y-m-d')) . "' class='mt-1 block w-full form-input'>";
                    $html .= "</div></div>";
                    return $html;
                }
                function render_json_checkboxes($name, $currentJson, $options) {
                    $currentValues = old($name, $currentJson ?? []);
                    if (!is_array($currentValues)) $currentValues = json_decode($currentValues, true) ?: [];
                    $html = "<div class='mt-2 space-y-2 rounded-md border p-4 bg-gray-50'>";
                    foreach ($options as $optionValue => $optionLabel) {
                        $checked = in_array($optionValue, $currentValues) ? 'checked' : '';
                        $html .= "<label class='flex items-center'><input type='checkbox' name='{$name}[]' value='{$optionValue}' class='form-checkbox h-5 w-5 text-indigo-600' {$checked}><span class='ml-3 text-gray-700'>{$optionLabel}</span></label>";
                    }
                    $html .= "</div>";
                    return $html;
                }
                @endphp

                <!-- BAHAGIAN 1: Maklumat Asas -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 1: Maklumat Asas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">No. Kertas Siasatan</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">{{ $paper->no_kertas_siasatan }}</div>
                        </div>
                        <div>
                            <label for="no_fail_lmm_t" class="block text-sm font-medium text-gray-700">No. Fail LMM (T)</label>
                            <input type="text" name="no_fail_lmm_t" id="no_fail_lmm_t" value="{{ old('no_fail_lmm_t', $paper->no_fail_lmm_t) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="no_report_polis" class="block text-sm font-medium text-gray-700">No. Report Polis</label>
                            <input type="text" name="no_report_polis" id="no_report_polis" value="{{ old('no_report_polis', $paper->no_report_polis) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="pegawai_penyiasat" class="block text-sm font-medium text-gray-700">Pegawai Penyiasat</label>
                            <input type="text" name="pegawai_penyiasat" id="pegawai_penyiasat" value="{{ old('pegawai_penyiasat', $paper->pegawai_penyiasat) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_polis_dibuka" class="block text-sm font-medium text-gray-700">Tarikh Laporan Polis Dibuka</label>
                            <input type="date" name="tarikh_laporan_polis_dibuka" id="tarikh_laporan_polis_dibuka" value="{{ old('tarikh_laporan_polis_dibuka', optional($paper->tarikh_laporan_polis_dibuka)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="seksyen" class="block text-sm font-medium text-gray-700">Seksyen</label>
                            <input type="text" name="seksyen" id="seksyen" value="{{ old('seksyen', $paper->seksyen) }}" class="mt-1 block w-full form-input">
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 2: Pemeriksaan JIPS -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 2: Pemeriksaan JIPS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Pegawai Pemeriksa</label>
                            <input type="text" name="pegawai_pemeriksa" id="pegawai_pemeriksa" value="{{ old('pegawai_pemeriksa', $paper->pegawai_pemeriksa) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_pertama" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Pertama</label>
                            <input type="date" name="tarikh_edaran_minit_ks_pertama" id="tarikh_edaran_minit_ks_pertama" value="{{ old('tarikh_edaran_minit_ks_pertama', optional($paper->tarikh_edaran_minit_ks_pertama)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_kedua" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Kedua</label>
                            <input type="date" name="tarikh_edaran_minit_ks_kedua" id="tarikh_edaran_minit_ks_kedua" value="{{ old('tarikh_edaran_minit_ks_kedua', optional($paper->tarikh_edaran_minit_ks_kedua)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_sebelum_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Sebelum Akhir</label>
                            <input type="date" name="tarikh_edaran_minit_ks_sebelum_akhir" id="tarikh_edaran_minit_ks_sebelum_akhir" value="{{ old('tarikh_edaran_minit_ks_sebelum_akhir', optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Akhir</label>
                            <input type="date" name="tarikh_edaran_minit_ks_akhir" id="tarikh_edaran_minit_ks_akhir" value="{{ old('tarikh_edaran_minit_ks_akhir', optional($paper->tarikh_edaran_minit_ks_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_semboyan_pemeriksaan_jips_ke_daerah" class="block text-sm font-medium text-gray-700">Tarikh Semboyan Pemeriksaan JIPS ke Daerah</label>
                            <input type="date" name="tarikh_semboyan_pemeriksaan_jips_ke_daerah" id="tarikh_semboyan_pemeriksaan_jips_ke_daerah" value="{{ old('tarikh_semboyan_pemeriksaan_jips_ke_daerah', optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 3: Arahan SIO & Ketua -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 3: Arahan SIO & Ketua</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Arahan Minit oleh SIO Status</label>
                            {!! render_boolean_select('arahan_minit_oleh_sio_status', $paper->arahan_minit_oleh_sio_status) !!}
                        </div>
                        <div>
                            <label for="arahan_minit_oleh_sio_tarikh" class="block text-sm font-medium text-gray-700">Arahan Minit oleh SIO Tarikh</label>
                            <input type="date" name="arahan_minit_oleh_sio_tarikh" id="arahan_minit_oleh_sio_tarikh" value="{{ old('arahan_minit_oleh_sio_tarikh', optional($paper->arahan_minit_oleh_sio_tarikh)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Arahan Minit Ketua Bahagian Status</label>
                            {!! render_boolean_select('arahan_minit_ketua_bahagian_status', $paper->arahan_minit_ketua_bahagian_status) !!}
                        </div>
                        <div>
                            <label for="arahan_minit_ketua_bahagian_tarikh" class="block text-sm font-medium text-gray-700">Arahan Minit Ketua Bahagian Tarikh</label>
                            <input type="date" name="arahan_minit_ketua_bahagian_tarikh" id="arahan_minit_ketua_bahagian_tarikh" value="{{ old('arahan_minit_ketua_bahagian_tarikh', optional($paper->arahan_minit_ketua_bahagian_tarikh)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Arahan Minit Ketua Jabatan Status</label>
                            {!! render_boolean_select('arahan_minit_ketua_jabatan_status', $paper->arahan_minit_ketua_jabatan_status) !!}
                        </div>
                        <div>
                            <label for="arahan_minit_ketua_jabatan_tarikh" class="block text-sm font-medium text-gray-700">Arahan Minit Ketua Jabatan Tarikh</label>
                            <input type="date" name="arahan_minit_ketua_jabatan_tarikh" id="arahan_minit_ketua_jabatan_tarikh" value="{{ old('arahan_minit_ketua_jabatan_tarikh', optional($paper->arahan_minit_ketua_jabatan_tarikh)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Arahan Minit oleh YA TPR Status</label>
                            {!! render_boolean_select('arahan_minit_oleh_ya_tpr_status', $paper->arahan_minit_oleh_ya_tpr_status) !!}
                        </div>
                        <div>
                            <label for="arahan_minit_oleh_ya_tpr_tarikh" class="block text-sm font-medium text-gray-700">Arahan Minit oleh YA TPR Tarikh</label>
                            <input type="date" name="arahan_minit_oleh_ya_tpr_tarikh" id="arahan_minit_oleh_ya_tpr_tarikh" value="{{ old('arahan_minit_oleh_ya_tpr_tarikh', optional($paper->arahan_minit_oleh_ya_tpr_tarikh)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="keputusan_siasatan_oleh_ya_tpr" class="block text-sm font-medium text-gray-700">Keputusan Siasatan oleh YA TPR</label>
                            <input type="text" name="keputusan_siasatan_oleh_ya_tpr" id="keputusan_siasatan_oleh_ya_tpr" value="{{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan" class="block text-sm font-medium text-gray-700">Adakah Arahan Tuduh oleh YA TPR Diambil Tindakan</label>
                            <textarea name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan" id="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan" class="mt-1 block w-full form-textarea">{{ old('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', is_array($paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan) ? json_encode($paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan) : $paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan) }}</textarea>
                        </div>
                        <div class="col-span-full">
                            <label for="ulasan_keputusan_siasatan_tpr" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Siasatan TPR</label>
                            <textarea name="ulasan_keputusan_siasatan_tpr" id="ulasan_keputusan_siasatan_tpr" class="mt-1 block w-full form-textarea">{{ old('ulasan_keputusan_siasatan_tpr', $paper->ulasan_keputusan_siasatan_tpr) }}</textarea>
                        </div>
                        <div class="col-span-full">
                            <label for="ulasan_keputusan_pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Pegawai Pemeriksa</label>
                            <textarea name="ulasan_keputusan_pegawai_pemeriksa" id="ulasan_keputusan_pegawai_pemeriksa" class="mt-1 block w-full form-textarea">{{ old('ulasan_keputusan_pegawai_pemeriksa', $paper->ulasan_keputusan_pegawai_pemeriksa) }}</textarea>
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 4: Barang Kes -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 4: Barang Kes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Adakah Barang Kes Didaftarkan</label>
                            {!! render_boolean_select('adakah_barang_kes_didaftarkan', $paper->adakah_barang_kes_didaftarkan) !!}
                        </div>
                        <div>
                            <label for="no_daftar_barang_kes_am" class="block text-sm font-medium text-gray-700">No Daftar Barang Kes (AM)</label>
                            <input type="text" name="no_daftar_barang_kes_am" id="no_daftar_barang_kes_am" value="{{ old('no_daftar_barang_kes_am', $paper->no_daftar_barang_kes_am) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="no_daftar_barang_kes_berharga" class="block text-sm font-medium text-gray-700">No Daftar Barang Kes (Berharga)</label>
                            <input type="text" name="no_daftar_barang_kes_berharga" id="no_daftar_barang_kes_berharga" value="{{ old('no_daftar_barang_kes_berharga', $paper->no_daftar_barang_kes_berharga) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="no_daftar_barang_kes_kenderaan" class="block text-sm font-medium text-gray-700">No Daftar Barang Kes (Kenderaan)</label>
                            <input type="text" name="no_daftar_barang_kes_kenderaan" id="no_daftar_barang_kes_kenderaan" value="{{ old('no_daftar_barang_kes_kenderaan', $paper->no_daftar_barang_kes_kenderaan) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="no_daftar_botol_spesimen_urin" class="block text-sm font-medium text-gray-700">No Daftar Botol Spesimen Urin</label>
                            <input type="text" name="no_daftar_botol_spesimen_urin" id="no_daftar_botol_spesimen_urin" value="{{ old('no_daftar_botol_spesimen_urin', $paper->no_daftar_botol_spesimen_urin) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="jenis_barang_kes_am" class="block text-sm font-medium text-gray-700">Jenis Barang Kes AM</label>
                            <input type="text" name="jenis_barang_kes_am" id="jenis_barang_kes_am" value="{{ old('jenis_barang_kes_am', $paper->jenis_barang_kes_am) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="jenis_barang_kes_berharga" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Berharga</label>
                            <input type="text" name="jenis_barang_kes_berharga" id="jenis_barang_kes_berharga" value="{{ old('jenis_barang_kes_berharga', $paper->jenis_barang_kes_berharga) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="jenis_barang_kes_kenderaan" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Kenderaan</label>
                            <input type="text" name="jenis_barang_kes_kenderaan" id="jenis_barang_kes_kenderaan" value="{{ old('jenis_barang_kes_kenderaan', $paper->jenis_barang_kes_kenderaan) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700">Status Pergerakan Barang Kes</label>
                            {!! render_json_checkboxes('status_pergerakan_barang_kes', $paper->status_pergerakan_barang_kes, [
                                'SIMPANAN STOR EKSHIBIT' => 'Simpanan Stor Ekshibit',
                                'UJIAN MAKMAL' => 'Ujian Makmal (Nyatakan: ...)',
                                'DI MAHKAMAH' => 'Di Mahkamah',
                                'PADA IO/AIO' => 'Pada IO/AIO',
                                'LAIN-LAIN' => 'Lain-lain (Nyatakan ...)'
                            ]) !!}
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700">Status Barang Kes Selesai Siasatan</label>
                            {!! render_json_checkboxes('status_barang_kes_selesai_siasatan', $paper->status_barang_kes_selesai_siasatan, [
                                'DILUPUSKAN KE PERBENDAHARAAN' => 'Dilupuskan ke Perbendaharaan (Nyatakan RM: ...)',
                                'DIKEMBALIKAN KEPADA PEMILIK' => 'Dikembalikan Kepada Pemilik',
                                'DILUPUSKAN' => 'Dilupuskan',
                                'LAIN-LAIN' => 'Lain-lain (Nyatakan ...)'
                            ]) !!}
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700">Kaedah Pelupusan Dilaksanakan</label>
                            {!! render_json_checkboxes('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', $paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan, [
                                'DIBAKAR' => 'Dibakar',
                                'DITANAM' => 'Ditanam',
                                'DIHANCURKAN' => 'Dihancurkan',
                                'DILELONG' => 'Dilelong',
                                'LAIN-LAIN' => 'Lain-lain (Nyatakan ...)'
                            ]) !!}
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700">Adakah Pelupusan Barang Kes Telah Ada Arahan Mahkamah/YA TPR</label>
                            {!! render_json_checkboxes('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', $paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan, [
                                'YA' => 'Ya',
                                'TIDAK' => 'Tidak',
                                'INISIATIF IO/AIO' => 'Inisiatif IO/AIO Sendiri'
                            ]) !!}
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700">Resit KEW.38E Bagi Pelupusan Barang Kes Wang Tunai ke Perbendaharaan</label>
                            {!! render_json_checkboxes('resit_kew_98e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbencaharaan', $paper->resit_kew_98e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbencaharaan, [
                                'ADA DILAMPIRKAN' => 'Ada Dilampirkan',
                                'TIDAK DILAMPIRKAN' => 'Tidak Dilampirkan'
                            ]) !!}
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700">Adakah Borang Serah/Terima Barang Kes Antara Pegawai Tangkapan dan IO/AIO Dilampirkan</label>
                            {!! render_json_checkboxes('adakah_borang_serah_terima_pegawai_tangkapan', $paper->adakah_borang_serah_terima_pegawai_tangkapan, [
                                'ADA DILAMPIRKAN' => 'Ada Dilampirkan',
                                'TIDAK DILAMPIRKAN' => 'Tidak Dilampirkan'
                            ]) !!}
                        </div>
                        <div>
                            <label for="adakah_borang_serah_terima_pemilik_saksi" class="block text-sm font-medium text-gray-700">Adakah Borang Serah/Terima Barang Kes Antara Pegawai Penyiasat, Pemilik dan Saksi Pegawai Kanan Polis Dilampirkan</label>
                            <input type="text" name="adakah_borang_serah_terima_pemilik_saksi" id="adakah_borang_serah_terima_pemilik_saksi" value="{{ old('adakah_borang_serah_terima_pemilik_saksi', $paper->adakah_borang_serah_terima_pemilik_saksi) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Adakah Sijil/Surat Arahan Kebenaran IPD Bagi Pelupusan Dilampirkan</label>
                            {!! render_boolean_select('adakah_sijil_surat_kebenaran_ipo', $paper->adakah_sijil_surat_kebenaran_ipo) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Adakah Gambar Pelupusan Barang Kes Dilampirkan</label>
                            {!! render_boolean_select('adakah_gambar_pelupusan', $paper->adakah_gambar_pelupusan) !!}
                        </div>
                        <div class="col-span-full">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa" id="ulasan_keseluruhan_pegawai_pemeriksa" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa', $paper->ulasan_keseluruhan_pegawai_pemeriksa) }}</textarea>
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 5: Bukti & Rajah -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 5: Bukti & Rajah</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status ID Siasatan Dikemaskini</label>
                            {!! render_boolean_select('status_id_siasatan_dikemaskini', $paper->status_id_siasatan_dikemaskini) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Rajah Kasar Tempat Kejadian</label>
                            {!! render_boolean_select('status_rajah_kasar_tempat_kejadian', $paper->status_rajah_kasar_tempat_kejadian) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Gambar Tempat Kejadian</label>
                            {!! render_boolean_select('status_gambar_tempat_kejadian', $paper->status_gambar_tempat_kejadian) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Gambar Barang Kes AM</label>
                            {!! render_boolean_select('status_gambar_barang_kes_am', $paper->status_gambar_barang_kes_am) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Gambar Barang Kes Berharga</label>
                            {!! render_boolean_select('status_gambar_barang_kes_berharga', $paper->status_gambar_barang_kes_berharga) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Gambar Barang Kes Kenderaan</label>
                            {!! render_boolean_select('status_gambar_barang_kes_kenderaan', $paper->status_gambar_barang_kes_kenderaan) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Gambar Barang Kes Darah</label>
                            {!! render_boolean_select('status_gambar_barang_kes_darah', $paper->status_gambar_barang_kes_darah) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Gambar Barang Kes Kontraban</label>
                            {!! render_boolean_select('status_gambar_barang_kes_kontraban', $paper->status_gambar_barang_kes_kontraban) !!}
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 6: Laporan RJ & Semboyan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 6: Laporan RJ & Semboyan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="col-span-full">
                            <label for="status_pem" class="block text-sm font-medium text-gray-700">Status PEM</label>
                            <textarea name="status_pem" id="status_pem" class="mt-1 block w-full form-textarea">{{ old('status_pem', is_array($paper->status_pem) ? json_encode($paper->status_pem) : $paper->status_pem) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status RJ2</label>
                            {!! render_boolean_select('status_rj2', $paper->status_rj2) !!}
                        </div>
                        <div>
                            <label for="tarikh_rj2" class="block text-sm font-medium text-gray-700">Tarikh RJ2</label>
                            <input type="date" name="tarikh_rj2" id="tarikh_rj2" value="{{ old('tarikh_rj2', optional($paper->tarikh_rj2)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status RJ2B</label>
                            {!! render_boolean_select('status_rj2b', $paper->status_rj2b) !!}
                        </div>
                        <div>
                            <label for="tarikh_rj2b" class="block text-sm font-medium text-gray-700">Tarikh RJ2B</label>
                            <input type="date" name="tarikh_rj2b" id="tarikh_rj2b" value="{{ old('tarikh_rj2b', optional($paper->tarikh_rj2b)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status RJ9</label>
                            {!! render_boolean_select('status_rj9', $paper->status_rj9) !!}
                        </div>
                        <div>
                            <label for="tarikh_rj9" class="block text-sm font-medium text-gray-700">Tarikh RJ9</label>
                            <input type="date" name="tarikh_rj9" id="tarikh_rj9" value="{{ old('tarikh_rj9', optional($paper->tarikh_rj9)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status RJ99</label>
                            {!! render_boolean_select('status_rj99', $paper->status_rj99) !!}
                        </div>
                        <div>
                            <label for="tarikh_rj99" class="block text-sm font-medium text-gray-700">Tarikh RJ99</label>
                            <input type="date" name="tarikh_rj99" id="tarikh_rj99" value="{{ old('tarikh_rj99', optional($paper->tarikh_rj99)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status RJ10A</label>
                            {!! render_boolean_select('status_rj10a', $paper->status_rj10a) !!}
                        </div>
                        <div>
                            <label for="tarikh_rj10a" class="block text-sm font-medium text-gray-700">Tarikh RJ10A</label>
                            <input type="date" name="tarikh_rj10a" id="tarikh_rj10a" value="{{ old('tarikh_rj10a', optional($paper->tarikh_rj10a)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status RJ10B</label>
                            {!! render_boolean_select('status_rj10b', $paper->status_rj10b) !!}
                        </div>
                        <div>
                            <label for="tarikh_rj10b" class="block text-sm font-medium text-gray-700">Tarikh RJ10B</label>
                            <input type="date" name="tarikh_rj10b" id="tarikh_rj10b" value="{{ old('tarikh_rj10b', optional($paper->tarikh_rj10b)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="col-span-full">
                            <label for="lain_lain_rj_dikesan" class="block text-sm font-medium text-gray-700">Lain-lain RJ Dikesan</label>
                            <textarea name="lain_lain_rj_dikesan" id="lain_lain_rj_dikesan" class="mt-1 block w-full form-textarea">{{ old('lain_lain_rj_dikesan', $paper->lain_lain_rj_dikesan) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Semboyan Pertama Wanted Person</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->status_semboyan_pertama_wanted_person ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="tarikh_semboyan_pertama_wanted_person" class="block text-sm font-medium text-gray-700">Tarikh Semboyan Pertama Wanted Person</label>
                            <input type="date" name="tarikh_semboyan_pertama_wanted_person" id="tarikh_semboyan_pertama_wanted_person" value="{{ old('tarikh_semboyan_pertama_wanted_person', optional($paper->tarikh_semboyan_pertama_wanted_person)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Semboyan Kedua Wanted Person</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->status_semboyan_kedua_wanted_person ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="tarikh_semboyan_kedua_wanted_person" class="block text-sm font-medium text-gray-700">Tarikh Semboyan Kedua Wanted Person</label>
                            <input type="date" name="tarikh_semboyan_kedua_wanted_person" id="tarikh_semboyan_kedua_wanted_person" value="{{ old('tarikh_semboyan_kedua_wanted_person', optional($paper->tarikh_semboyan_kedua_wanted_person)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Semboyan Ketiga Wanted Person</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->status_semboyan_ketiga_wanted_person ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="tarikh_semboyan_ketiga_wanted_person" class="block text-sm font-medium text-gray-700">Tarikh Semboyan Ketiga Wanted Person</label>
                            <input type="date" name="tarikh_semboyan_ketiga_wanted_person" id="tarikh_semboyan_ketiga_wanted_person" value="{{ old('tarikh_semboyan_ketiga_wanted_person', optional($paper->tarikh_semboyan_ketiga_wanted_person)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="col-span-full">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_borang" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa Borang</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_borang" id="ulasan_keseluruhan_pegawai_pemeriksa_borang" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_borang', $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Penandaan Kelas Warna</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->status_penandaan_kelas_warna ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 7: Laporan E-FSA, Puspakom, dll -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7: Laporan E-FSA, Puspakom, dll</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- E-FSA (BANK) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Permohonan E-FSA-1 oleh IO/AIO</label>
                            {!! render_boolean_select('status_permohonan_E_FSA_1_oleh_IO_AIO', $paper->status_permohonan_E_FSA_1_oleh_IO_AIO) !!}
                        </div>
                        <div>
                            <label for="nama_bank_permohonan_E_FSA_1" class="block text-sm font-medium text-gray-700">Nama Bank Permohonan E-FSA-1</label>
                            <input type="text" name="nama_bank_permohonan_E_FSA_1" id="nama_bank_permohonan_E_FSA_1" value="{{ old('nama_bank_permohonan_E_FSA_1', $paper->nama_bank_permohonan_E_FSA_1) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Penuh E-FSA-1 oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO" id="tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO" value="{{ old('tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- E-FSA (TELCO) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Laporan Penuh E-FSA-1 Telco oleh IO/AIO</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->{'status_laporan_penuh_E-FSA-1_telco_oleh_IO/AIO'} ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="nama_telco_laporan_E_FSA_1_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Nama Telco Laporan E-FSA-1 oleh IO/AIO</label>
                            <input type="text" name="nama_telco_laporan_E_FSA_1_oleh_IO_AIO" id="nama_telco_laporan_E_FSA_1_oleh_IO_AIO" value="{{ old('nama_telco_laporan_E_FSA_1_oleh_IO_AIO', $paper->nama_telco_laporan_E_FSA_1_oleh_IO_AIO) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Penuh E-FSA-1 Telco oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO" id="tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO" value="{{ old('tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- E-FSA (IMIGRESEN) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Permohonan E-FSA-2 oleh IO/AIO</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->{'status_permohonan_E-FSA-2_oleh_IO/AIO'} ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="nama_imigresen_permohonan_E_FSA_2" class="block text-sm font-medium text-gray-700">Nama Imigresen Permohonan E-FSA-2</label>
                            <input type="text" name="nama_imigresen_permohonan_E_FSA_2" id="nama_imigresen_permohonan_E_FSA_2" value="{{ old('nama_imigresen_permohonan_E_FSA_2', $paper->nama_imigresen_permohonan_E_FSA_2) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Penuh E-FSA-2 oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO" id="tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO" value="{{ old('tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- E-FSA (PUSPAKOM) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Permohonan E-FSA-3 oleh IO/AIO</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->{'status_permohonan_E-FSA-3_oleh_IO/AIO'} ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="nama_puspakom_permohonan_E_FSA_3" class="block text-sm font-medium text-gray-700">Nama Puspakom Permohonan E-FSA-3</label>
                            <input type="text" name="nama_puspakom_permohonan_E_FSA_3" id="nama_puspakom_permohonan_E_FSA_3" value="{{ old('nama_puspakom_permohonan_E_FSA_3', $paper->nama_puspakom_permohonan_E_FSA_3) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Penuh E-FSA-3 oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO" id="tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO" value="{{ old('tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- E-FSA (KASTAM) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Permohonan E-FSA-4 oleh IO/AIO</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->{'status_permohonan_E-FSA-4_oleh_IO/AIO'} ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="nama_kastam_permohonan_E_FSA_4" class="block text-sm font-medium text-gray-700">Nama Kastam Permohonan E-FSA-4</label>
                            <input type="text" name="nama_kastam_permohonan_E_FSA_4" id="nama_kastam_permohonan_E_FSA_4" value="{{ old('nama_kastam_permohonan_E_FSA_4', $paper->nama_kastam_permohonan_E_FSA_4) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Penuh E-FSA-4 oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO" id="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO" value="{{ old('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- E-FSA (FORENSIK) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Permohonan E-FSA-5 oleh IO/AIO</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->{'status_permohonan_E-FSA-5_oleh_IO/AIO'} ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="nama_forensik_permohonan_E_FSA_5" class="block text-sm font-medium text-gray-700">Nama Forensik Permohonan E-FSA-5</label>
                            <input type="text" name="nama_forensik_permohonan_E_FSA_5" id="nama_forensik_permohonan_E_FSA_5" value="{{ old('nama_forensik_permohonan_E_FSA_5', $paper->nama_forensik_permohonan_E_FSA_5) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Penuh E-FSA-5 oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO" id="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO" value="{{ old('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        {{-- Lain-lain --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status Permohonan Lain-lain oleh IO/AIO</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->{'status_permohonan_lain_lain_oleh_IO/AIO'} ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label for="nama_agensi_lain_lain" class="block text-sm font-medium text-gray-700">Nama Agensi Lain-lain</label>
                            <input type="text" name="nama_agensi_lain_lain" id="nama_agensi_lain_lain" value="{{ old('nama_agensi_lain_lain', $paper->nama_agensi_lain_lain) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_lain_lain_oleh_IO_AIO" class="block text-sm font-medium text-gray-700">Tarikh Laporan Lain-lain oleh IO/AIO</label>
                            <input type="date" name="tarikh_laporan_lain_lain_oleh_IO_AIO" id="tarikh_laporan_lain_lain_oleh_IO_AIO" value="{{ old('tarikh_laporan_lain_lain_oleh_IO_AIO', optional($paper->{'tarikh_laporan_lain_lain_oleh_IO/AIO'})->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="col-span-full">
                            <label for="jenis_laporan_lain_lain" class="block text-sm font-medium text-gray-700">Jenis Laporan Lain-lain</label>
                            <textarea name="jenis_laporan_lain_lain" id="jenis_laporan_lain_lain" class="mt-1 block w-full form-textarea">{{ old('jenis_laporan_lain_lain', $paper->jenis_laporan_lain_lain) }}</textarea>
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 8: Penilaian Akhir -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 8: Penilaian Akhir</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar" class="block text-sm font-medium text-gray-700">Status Muka Surat 4 Barang Kes Ditulis Bersama No Daftar</label>
                            <input type="text" name="status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar" id="status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar" value="{{ old('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar', $paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr" class="block text-sm font-medium text-gray-700">Status Muka Surat 4 Barang Kes Ditulis Bersama No Daftar dan Telah Ada Arahan YA TPR</label>
                            <input type="text" name="status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr" id="status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr" value="{{ old('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr', $paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Adakah Muka Surat 4 Keputusan Kes Dicatat</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->adakah_muka_surat_4_keputusan_kes_dicatat ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Adakah Fail LMM/T atau LMM Telah Ada Keputusan</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Adakah KS/KUS/Fail Selesai</label>
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">
                                {{ $paper->adakah_ks_kus_fail_selesai ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>
                        <div class="col-span-full">
                            <label for="keputusan_akhir_mahkamah" class="block text-sm font-medium text-gray-700">Keputusan Akhir Mahkamah</label>
                            <textarea name="keputusan_akhir_mahkamah" id="keputusan_akhir_mahkamah" class="mt-1 block w-full form-textarea">{{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) }}</textarea>
                        </div>
                        <div class="col-span-full">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_fail" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa Fail</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_fail" id="ulasan_keseluruhan_pegawai_pemeriksa_fail" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_fail', $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end pt-4 mt-6 border-t">
                    <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-3">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Kemaskini</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-input, .form-select, .form-textarea {
            @apply rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
        }
        .form-checkbox {
            @apply rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500;
        }
    </style>
</x-app-layout>