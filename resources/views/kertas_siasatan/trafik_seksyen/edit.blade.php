<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Trafik ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'TrafikSeksyen', 'id' => $paper->id]) }}" class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
                @csrf
                @method('PUT')

                @php
                // Helper function for simple boolean choices (e.g., Ada/Tiada, Ya/Tidak)
                function render_boolean_select($name, $currentValue, $YaLabel = 'Ada / Ya', $TidakLabel = 'Tiada / Tidak') {
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
                function render_status_with_date($id, $statusName, $dateName, $currentStatus, $currentDate) {
                    $html = "<div x-data='{ status: " . (old($statusName, $currentStatus) ? 'Ya' : 'Tidak') . " }'>";
                    $html .= "<select name='{$statusName}' x-model='status' class='mt-1 block w-full form-select'>";
                    $html .= "<option value='0'>Tiada / Tidak Cipta</option>";
                    $html .= "<option value='1'>Ada / Cipta</option>";
                    $html .= "</select>";
                    $html .= "<div x-show='status' x-transition class='mt-2'>";
                    $html .= "<label for='{$dateName}_{$id}' class='text-sm text-gray-600'>Jika Ada, nyatakan tarikh:</label>";
                    $html .= "<input type='date' name='{$dateName}' id='{$dateName}_{$id}' value='" . old($dateName, optional($currentDate)->format('Y-m-d')) . "' class='mt-1 block w-full form-input'>";
                    $html .= "</div></div>";
                    return $html;
                }

                // Helper for JSON fields represented by checkboxes
                function render_json_checkboxes($name, $currentJson, $options) {
                    $currentValues = old($name, $currentJson ?? []);
                    if (!is_array($currentValues)) $currentValues = [];
                    
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
                            <label for="no_repot_polis" class="block text-sm font-medium text-gray-700">No. Repot Polis</label>
                            <input type="text" name="no_repot_polis" id="no_repot_polis" value="{{ old('no_repot_polis', $paper->no_repot_polis) }}" class="mt-1 block w-full form-input">
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

                <!-- BAHAGIAN 2: Pemeriksaan & Status -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 2: Pemeriksaan & Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Pegawai Pemeriksa</label>
                            <input type="text" name="pegawai_pemeriksa" id="pegawai_pemeriksa" value="{{ old('pegawai_pemeriksa', $paper->pegawai_pemeriksa) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_pertama" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Pertama (A)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_pertama" id="tarikh_edaran_minit_ks_pertama" value="{{ old('tarikh_edaran_minit_ks_pertama', optional($paper->tarikh_edaran_minit_ks_pertama)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_kedua" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Kedua (B)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_kedua" id="tarikh_edaran_minit_ks_kedua" value="{{ old('tarikh_edaran_minit_ks_kedua', optional($paper->tarikh_edaran_minit_ks_kedua)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_sebelum_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Sebelum Minit Akhir (C)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_sebelum_akhir" id="tarikh_edaran_minit_ks_sebelum_akhir" value="{{ old('tarikh_edaran_minit_ks_sebelum_akhir', optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Akhir (D)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_akhir" id="tarikh_edaran_minit_ks_akhir" value="{{ old('tarikh_edaran_minit_ks_akhir', optional($paper->tarikh_edaran_minit_ks_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_semboyan_pemeriksaan_jips_ke_daerah" class="block text-sm font-medium text-gray-700">Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)</label>
                            <input type="date" name="tarikh_semboyan_pemeriksaan_jips_ke_daerah" id="tarikh_semboyan_pemeriksaan_jips_ke_daerah" value="{{ old('tarikh_semboyan_pemeriksaan_jips_ke_daerah', optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="md:col-span-2 lg:col-span-3 mt-4 p-3 bg-gray-100 rounded-md">
                            <h4 class="font-semibold text-sm text-gray-600">Sistem Kalkulasi Status</h4>
                            <div class="mt-2 text-sm text-gray-800 space-y-1">
                                <p><span class="font-medium">KS Lewat Edaran 48 Jam (B-A):</span> Status akan dikira secara automatik semasa simpan.</p>
                                <p><span class="font-medium">Terbengkalai Melebihi 3 Bulan (D-C) atau (D-A):</span> Status akan dikira secara automatik semasa simpan.</p>
                                <p><span class="font-medium">Terbengkalai / Baru Dikemaskini (E-D):</span> Status akan dikira secara automatik semasa simpan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                                <!-- BAHAGIAN 3: Arahan & Keputusan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 3: Arahan & Keputusan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh SIO</label>
                            {!! render_status_with_date('sio', 'arahan_minit_oleh_sio_status', 'arahan_minit_oleh_sio_tarikh', $paper->arahan_minit_oleh_sio_status, $paper->arahan_minit_oleh_sio_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh Ketua Bahagian</label>
                            {!! render_status_with_date('kb', 'arahan_minit_ketua_bahagian_status', 'arahan_minit_ketua_bahagian_tarikh', $paper->arahan_minit_ketua_bahagian_status, $paper->arahan_minit_ketua_bahagian_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh Ketua Jabatan</label>
                            {!! render_status_with_date('kj', 'arahan_minit_ketua_jabatan_status', 'arahan_minit_ketua_jabatan_tarikh', $paper->arahan_minit_ketua_jabatan_status, $paper->arahan_minit_ketua_jabatan_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh YA TPR</label>
                            {!! render_status_with_date('tpr', 'arahan_minit_oleh_ya_tpr_status', 'arahan_minit_oleh_ya_tpr_tarikh', $paper->arahan_minit_oleh_ya_tpr_status, $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}
                        </div>

                        <div class="md:col-span-2">
                            <label for="keputusan_siasatan_oleh_ya_tpr" class="block text-sm font-medium text-gray-700">Keputusan Siasatan Oleh YA TPR</label>
                            <input type="text" name="keputusan_siasatan_oleh_ya_tpr" id="keputusan_siasatan_oleh_ya_tpr" value="{{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) }}" class="mt-1 block w-full form-input">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Adakah Arahan Tuduh Oleh YA TPR Diambil Tindakan</label>
                            @php
                                $tuduh_options = [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                    'Tiada Usaha Oleh IO/AIO' => 'Tiada Usaha Oleh IO/AIO'
                                ];
                            @endphp
                            {!! render_json_checkboxes('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', $paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan, $tuduh_options) !!}
                        </div>

                        <div class="md:col-span-2">
                            <label for="ulasan_keputusan_siasatan_tpr" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Siasatan TPR</label>
                            <textarea name="ulasan_keputusan_siasatan_tpr" id="ulasan_keputusan_siasatan_tpr" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keputusan_siasatan_tpr', $paper->ulasan_keputusan_siasatan_tpr) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="keputusan_siasatan_oleh_ya_koroner" class="block text-sm font-medium text-gray-700">Keputusan Siasatan Oleh YA Koroner</label>
                            <input type="text" name="keputusan_siasatan_oleh_ya_koroner" id="keputusan_siasatan_oleh_ya_koroner" value="{{ old('keputusan_siasatan_oleh_ya_koroner', $paper->keputusan_siasatan_oleh_ya_koroner) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="ulasan_keputusan_oleh_ya_koroner" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Oleh YA Koroner</label>
                            <input type="text" name="ulasan_keputusan_oleh_ya_koroner" id="ulasan_keputusan_oleh_ya_koroner" value="{{ old('ulasan_keputusan_oleh_ya_koroner', $paper->ulasan_keputusan_oleh_ya_koroner) }}" class="mt-1 block w-full form-input">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Jika Ada)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa" id="ulasan_keseluruhan_pegawai_pemeriksa" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa', $paper->ulasan_keseluruhan_pegawai_pemeriksa) }}</textarea>
                        </div>
                    </div>
                </div>

                                <!-- BAHAGIAN 4: Barang Kes -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 4: Barang Kes</h3>
                    <div class="space-y-6">
                        <div>
                            <label for="adakah_barang_kes_didaftarkan" class="block text-sm font-medium text-gray-700">Adakah Barang Kes Didaftarkan</label>
                            {!! render_boolean_select('adakah_barang_kes_didaftarkan', $paper->adakah_barang_kes_didaftarkan, 'Ya', 'Tidak') !!}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div><label for="no_daftar_barang_kes_am" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Am</label><input type="text" name="no_daftar_barang_kes_am" id="no_daftar_barang_kes_am" value="{{ old('no_daftar_barang_kes_am', $paper->no_daftar_barang_kes_am) }}" class="mt-1 block w-full form-input"></div>
                            <div><label for="no_daftar_barang_kes_berharga" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Berharga</label><input type="text" name="no_daftar_barang_kes_berharga" id="no_daftar_barang_kes_berharga" value="{{ old('no_daftar_barang_kes_berharga', $paper->no_daftar_barang_kes_berharga) }}" class="mt-1 block w-full form-input"></div>
                            <div><label for="no_daftar_barang_kes_kenderaan" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Kenderaan</label><input type="text" name="no_daftar_barang_kes_kenderaan" id="no_daftar_barang_kes_kenderaan" value="{{ old('no_daftar_barang_kes_kenderaan', $paper->no_daftar_barang_kes_kenderaan) }}" class="mt-1 block w-full form-input"></div>
                            <div><label for="no_daftar_botol_spesimen_urin" class="block text-sm font-medium text-gray-700">No. Daftar Botol Spesimen Urin</label><input type="text" name="no_daftar_botol_spesimen_urin" id="no_daftar_botol_spesimen_urin" value="{{ old('no_daftar_botol_spesimen_urin', $paper->no_daftar_botol_spesimen_urin) }}" class="mt-1 block w-full form-input"></div>
                            <div><label for="no_daftar_spesimen_darah" class="block text-sm font-medium text-gray-700">No. Daftar Spesimen Darah</label><input type="text" name="no_daftar_spesimen_darah" id="no_daftar_spesimen_darah" value="{{ old('no_daftar_spesimen_darah', $paper->no_daftar_spesimen_darah) }}" class="mt-1 block w-full form-input"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div><label for="jenis_barang_kes_am" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Am</label><input type="text" name="jenis_barang_kes_am" id="jenis_barang_kes_am" value="{{ old('jenis_barang_kes_am', $paper->jenis_barang_kes_am) }}" class="mt-1 block w-full form-input"></div>
                            <div><label for="jenis_barang_kes_berharga" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Berharga</label><input type="text" name="jenis_barang_kes_berharga" id="jenis_barang_kes_berharga" value="{{ old('jenis_barang_kes_berharga', $paper->jenis_barang_kes_berharga) }}" class="mt-1 block w-full form-input"></div>
                            <div><label for="jenis_barang_kes_kenderaan" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Kenderaan</label><input type="text" name="jenis_barang_kes_kenderaan" id="jenis_barang_kes_kenderaan" value="{{ old('jenis_barang_kes_kenderaan', $paper->jenis_barang_kes_kenderaan) }}" class="mt-1 block w-full form-input"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Pergerakan Barang Kes</label>
                            @php
                                $pergerakan_options = [
                                    'Simpanan Stor Ekshibit' => 'Simpanan Stor Ekshibit',
                                    'Ujian Makmal' => 'Ujian Makmal',
                                    'Di Mahkamah' => 'Di Mahkamah',
                                    'Pada IO/AIO' => 'Pada IO/AIO',
                                    'Lain-Lain' => 'Lain-Lain'
                                ];
                            @endphp
                            {!! render_json_checkboxes('status_pergerakan_barang_kes', $paper->status_pergerakan_barang_kes, $pergerakan_options) !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Barang Kes Selesai Siasatan</label>
                            @php
                                $selesai_options = [
                                    'Dilupuskan ke Perbendaharaan' => 'Dilupuskan ke Perbendaharaan',
                                    'Dikembalikan Kepada Pemilik' => 'Dikembalikan Kepada Pemilik',
                                    'Dilupuskan' => 'Dilupuskan',
                                    'Lain-Lain' => 'Lain-Lain'
                                ];
                            @endphp
                            {!! render_json_checkboxes('status_barang_kes_selesai_siasatan', $paper->status_barang_kes_selesai_siasatan, $selesai_options) !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sekiranya Barang Kes Dilupuskan, Bagaimana Kaedah Pelupusan Dilaksanakan</label>
                            @php
                                $kaedah_options = [
                                    'Dibakar' => 'Dibakar',
                                    'Ditanam' => 'Ditanam',
                                    'Dihancurkan' => 'Dihancurkan',
                                    'Dilelong' => 'Dilelong',
                                    'Lain-Lain' => 'Lain-Lain'
                                ];
                            @endphp
                            {!! render_json_checkboxes('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', $paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan, $kaedah_options) !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Pelupusan Barang Kes Itu Telah Ada Arahan Mahkamah Atau YA TPR</label>
                            @php
                                $arahan_options = [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                    'Inisiatif IO/AIO Sendiri' => 'Inisiatif IO/AIO Sendiri'
                                ];
                            @endphp
                            {!! render_json_checkboxes('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', $paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan, $arahan_options) !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Resit Kew.38e Bagi Pelupusan Barang Kes Wang Tunai Ke Perbendaharaan</label>
                            @php
                                $resit_options = ['Ada Dilampirkan' => 'Ada Dilampirkan', 'Tidak Dilampirkan' => 'Tidak Dilampirkan'];
                            @endphp
                            {!! render_json_checkboxes('resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', $paper->resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan, $resit_options) !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Borang Serah/Terima Barang Kes Antara Pegawai Tangkapan dan IO/AIO Dilampirkan</label>
                            {!! render_json_checkboxes('adakah_borang_serah_terima_pegawai_tangkapan', $paper->adakah_borang_serah_terima_pegawai_tangkapan, $resit_options) !!}
                        </div>
                        
                        <div>
                            <label for="adakah_borang_serah_terima_pemilik_saksi" class="block text-sm font-medium text-gray-700">Adakah Borang Serah/Terima Barang Kes Antara Pegawai Penyiasat, Pemilik dan Saksi Pegawai Kanan Polis Dilampirkan</label>
                            <input type="text" name="adakah_borang_serah_terima_pemilik_saksi" id="adakah_borang_serah_terima_pemilik_saksi" value="{{ old('adakah_borang_serah_terima_pemilik_saksi', $paper->adakah_borang_serah_terima_pemilik_saksi) }}" class="mt-1 block w-full form-input">
                        </div>
                        
                        <div>
                            <label for="adakah_sijil_surat_kebenaran_ipo" class="block text-sm font-medium text-gray-700">Adakah Sijil Atau Surat Arahan Kebenaran Oleh IPD Bagi Melaksanakan Pelupusan Barang Kes Dilampirkan</label>
                            {!! render_boolean_select('adakah_sijil_surat_kebenaran_ipo', $paper->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>
                        
                        <div>
                            <label for="adakah_gambar_pelupusan" class="block text-sm font-medium text-gray-700">Adakah Gambar Pelupusan Barang Kes Dilampirkan</label>
                            <input type="text" name="adakah_gambar_pelupusan" id="adakah_gambar_pelupusan" value="{{ old('adakah_gambar_pelupusan', $paper->adakah_gambar_pelupusan) }}" class="mt-1 block w-full form-input">
                        </div>

                    </div>
                </div>

                                <!-- BAHAGIAN 5: Dokumen Siasatan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 5: Dokumen Siasatan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <label for="status_id_siasatan_dikemaskini" class="block text-sm font-medium text-gray-700">ID Siasatan Dikemaskini</label>
                            {!! render_boolean_select('status_id_siasatan_dikemaskini', $paper->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini') !!}
                        </div>
                        <div>
                            <label for="status_rajah_kasar_tempat_kejadian" class="block text-sm font-medium text-gray-700">Rajah Kasar Tempat Kejadian</label>
                            {!! render_boolean_select('status_rajah_kasar_tempat_kejadian', $paper->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label for="status_gambar_tempat_kejadian" class="block text-sm font-medium text-gray-700">Gambar Tempat Kejadian</label>
                            {!! render_boolean_select('status_gambar_tempat_kejadian', $paper->status_gambar_tempat_kejadian, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label for="status_gambar_post_mortem_mayat_di_hospital" class="block text-sm font-medium text-gray-700">Gambar Post-Mortem Mayat Di Hospital</label>
                            {!! render_boolean_select('status_gambar_post_mortem_mayat_di_hospital', $paper->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label for="status_gambar_barang_kes_am" class="block text-sm font-medium text-gray-700">Gambar Barang Kes Am</label>
                            {!! render_boolean_select('status_gambar_barang_kes_am', $paper->status_gambar_barang_kes_am, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label for="status_gambar_barang_kes_kenderaan" class="block text-sm font-medium text-gray-700">Gambar Barang Kes Kenderaan</label>
                            {!! render_boolean_select('status_gambar_barang_kes_kenderaan', $paper->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label for="status_gambar_barang_kes_darah" class="block text-sm font-medium text-gray-700">Gambar Barang Kes Darah</label>
                            {!! render_boolean_select('status_gambar_barang_kes_darah', $paper->status_gambar_barang_kes_darah, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label for="status_gambar_barang_kes_kontraban" class="block text-sm font-medium text-gray-700">Gambar Barang Kes Kontraban</label>
                            {!! render_boolean_select('status_gambar_barang_kes_kontraban', $paper->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada') !!}
                        </div>
                    </div>
                </div>

                                <!-- BAHAGIAN 6: Borang & Semakan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 6: Borang & Semakan</h3>
                    <div class="space-y-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PEM 1/ PEM 2/ PEM 3/ PEM 4</label>
                            @php
                                $pem_options = [ 'PEM 1' => 'PEM 1', 'PEM 2' => 'PEM 2', 'PEM 3' => 'PEM 3', 'PEM 4' => 'PEM 4' ];
                            @endphp
                            {!! render_json_checkboxes('status_pem', $paper->status_pem, $pem_options) !!}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div><label class="block text-sm font-medium text-gray-700">RJ 2</label>{!! render_status_with_date('rj2', 'status_rj2', 'tarikh_rj2', $paper->status_rj2, $paper->tarikh_rj2) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 2B</label>{!! render_status_with_date('rj2b', 'status_rj2b', 'tarikh_rj2b', $paper->status_rj2b, $paper->tarikh_rj2b) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 9</label>{!! render_status_with_date('rj9', 'status_rj9', 'tarikh_rj9', $paper->status_rj9, $paper->tarikh_rj9) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 99</label>{!! render_status_with_date('rj99', 'status_rj99', 'tarikh_rj99', $paper->status_rj99, $paper->tarikh_rj99) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 10A</label>{!! render_status_with_date('rj10a', 'status_rj10a', 'tarikh_rj10a', $paper->status_rj10a, $paper->tarikh_rj10a) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 10B</label>{!! render_status_with_date('rj10b', 'status_rj10b', 'tarikh_rj10b', $paper->status_rj10b, $paper->tarikh_rj10b) !!}</div>
                        </div>

                        <div>
                            <label for="lain_lain_rj_dikesan" class="block text-sm font-medium text-gray-700">Lain-lain RJ Dikesan</label>
                            <input type="text" name="lain_lain_rj_dikesan" id="lain_lain_rj_dikesan" value="{{ old('lain_lain_rj_dikesan', $paper->lain_lain_rj_dikesan) }}" class="mt-1 block w-full form-input">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div x-data="{ status: {{ old('status_saman_pdrm_s_257', $paper->status_saman_pdrm_s_257) ? 'Ya' : 'Tidak' }} }">
                                <label for="status_saman_pdrm_s_257" class="block text-sm font-medium text-gray-700">Saman PDRM (S) 257 Adakah Dicipta</label>
                                <select name="status_saman_pdrm_s_257" x-model="status" class="mt-1 block w-full form-select">
                                    <option value="0">Tidak Dicipta</option>
                                    <option value="1">Ada Dicipta</option>
                                </select>
                                <div x-show="status" x-transition class="mt-2">
                                    <label for="no_saman_pdrm_s_257" class="text-sm text-gray-600">Jika Ada, nyatakan No Saman:</label>
                                    <input type="text" name="no_saman_pdrm_s_257" id="no_saman_pdrm_s_257" value="{{ old('no_saman_pdrm_s_257', $paper->no_saman_pdrm_s_257) }}" class="mt-1 block w-full form-input">
                                </div>
                            </div>
                            <div x-data="{ status: {{ old('status_saman_pdrm_s_167', $paper->status_saman_pdrm_s_167) ? 'Ya' : 'Tidak' }} }">
                                <label for="status_saman_pdrm_s_167" class="block text-sm font-medium text-gray-700">Saman PDRM (S) 167 Adakah Dicipta</label>
                                <select name="status_saman_pdrm_s_167" x-model="status" class="mt-1 block w-full form-select">
                                    <option value="0">Tidak Dicipta</option>
                                    <option value="1">Ada Dicipta</option>
                                </select>
                                <div x-show="status" x-transition class="mt-2">
                                    <label for="no_saman_pdrm_s_167" class="text-sm text-gray-600">Jika Ada, nyatakan No Saman:</label>
                                    <input type="text" name="no_saman_pdrm_s_167" id="no_saman_pdrm_s_167" value="{{ old('no_saman_pdrm_s_167', $paper->no_saman_pdrm_s_167) }}" class="mt-1 block w-full form-input">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Pertama Wanted Person</label>
                                {!! render_status_with_date('wp1', 'status_semboyan_pertama_wanted_person', 'tarikh_semboyan_pertama_wanted_person', $paper->status_semboyan_pertama_wanted_person, $paper->tarikh_semboyan_pertama_wanted_person) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Kedua Wanted Person</label>
                                {!! render_status_with_date('wp2', 'status_semboyan_kedua_wanted_person', 'tarikh_semboyan_kedua_wanted_person', $paper->status_semboyan_kedua_wanted_person, $paper->tarikh_semboyan_kedua_wanted_person) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Ketiga Wanted Person</label>
                                {!! render_status_with_date('wp3', 'status_semboyan_ketiga_wanted_person', 'tarikh_semboyan_ketiga_wanted_person', $paper->status_semboyan_ketiga_wanted_person, $paper->tarikh_semboyan_ketiga_wanted_person) !!}
                            </div>
                        </div>

                        <div>
                            <label for="status_penandaan_kelas_warna" class="block text-sm font-medium text-gray-700">Adakah Penandaan Kelas Warna Pada Kulit Kertas Siasatan Dibuat</label>
                            {!! render_boolean_select('status_penandaan_kelas_warna', $paper->status_penandaan_kelas_warna, 'Ya', 'Tidak') !!}
                        </div>
                    </div>
                </div>
                                <!-- BAHAGIAN 7: Permohonan Laporan Agensi Luar -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7: Permohonan Laporan Agensi Luar</h3>
                    <div class="space-y-8">

                        <!-- PUSPAKOM -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">PUSPAKOM</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div><label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>{!! render_status_with_date('puspakom_permohonan', 'status_permohonan_laporan_puspakom', 'tarikh_permohonan_laporan_puspakom', $paper->status_permohonan_laporan_puspakom, $paper->tarikh_permohonan_laporan_puspakom) !!}</div>
                                <div><label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>{!! render_status_with_date('puspakom_penuh', 'status_laporan_penuh_puspakom', 'tarikh_laporan_penuh_puspakom', $paper->status_laporan_penuh_puspakom, $paper->tarikh_laporan_penuh_puspakom) !!}</div>
                            </div>
                        </div>
                        
                        <!-- JKR -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">JKR</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div><label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>{!! render_status_with_date('jkr_permohonan', 'status_permohonan_laporan_jkr', 'tarikh_permohonan_laporan_jkr', $paper->status_permohonan_laporan_jkr, $paper->tarikh_permohonan_laporan_jkr) !!}</div>
                                <div><label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>{!! render_status_with_date('jkr_penuh', 'status_laporan_penuh_jkr', 'tarikh_laporan_penuh_jkr', $paper->status_laporan_penuh_jkr, $paper->tarikh_laporan_penuh_jkr) !!}</div>
                            </div>
                        </div>

                        <!-- JPJ -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">JPJ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div><label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>{!! render_status_with_date('jpj_permohonan', 'status_permohonan_laporan_jpj', 'tarikh_permohonan_laporan_jpj', $paper->status_permohonan_laporan_jpj, $paper->tarikh_permohonan_laporan_jpj) !!}</div>
                                <div><label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>{!! render_status_with_date('jpj_penuh', 'status_laporan_penuh_jpj', 'tarikh_laporan_penuh_jpj', $paper->status_laporan_penuh_jpj, $paper->tarikh_laporan_penuh_jpj) !!}</div>
                            </div>
                        </div>

                <!-- BAHAGIAN 8: Status Fail -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 8: Status Fail</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="muka_surat_4_barang_kes_ditulis" class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Barang Kes Ditulis Bersama No Daftar Barang Kes</label>
                                {!! render_boolean_select('muka_surat_4_barang_kes_ditulis', $paper->muka_surat_4_barang_kes_ditulis, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label for="muka_surat_4_dengan_arahan_tpr" class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Barang Kes Ditulis Bersama No Daftar Dan Telah Ada Arahan YA TPR Untuk Pelupusan/Serahan Semula</label>
                                {!! render_boolean_select('muka_surat_4_dengan_arahan_tpr', $paper->muka_surat_4_dengan_arahan_tpr, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label for="muka_surat_4_keputusan_kes_dicatat" class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Keputusan Kes Dicatat Selengkapnya</label>
                                {!! render_boolean_select('muka_surat_4_keputusan_kes_dicatat', $paper->muka_surat_4_keputusan_kes_dicatat, 'Ya', 'Tidak') !!}
                            </div>
                             <div>
                                <label for="fail_lmm_ada_keputusan_koroner" class="block text-sm font-medium text-gray-700">Adakah Fail L.M.M (T) Atau L.M.M Telah Ada Keputusan Siasatan Oleh YA Koroner</label>
                                {!! render_boolean_select('fail_lmm_ada_keputusan_koroner', $paper->fail_lmm_ada_keputusan_koroner, 'Ya', 'Tidak') !!}
                            </div>
                        </div>

                        <div>
                            <label for="status_kus_fail" class="block text-sm font-medium text-gray-700">Adakah Kertas Siasatan Telah Di KUS/FAIL Bagi Siasatan Yang Telah Selesai Dan Ada Keputusan Mahkamah</label>
                            <input type="text" name="status_kus_fail" id="status_kus_fail" value="{{ old('status_kus_fail', $paper->status_kus_fail) }}" class="mt-1 block w-full form-input">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keputusan Akhir Oleh Mahkamah Sebelum Kertas Siasatan Di KUS/FAIL Atau Disimpan</label>
                             @php
                                $mahkamah_options = [
                                    'Jatuh Hukum' => 'Jatuh Hukum',
                                    'NFA' => 'NFA',
                                    'DNA' => 'DNA',
                                    'DNAA' => 'DNAA',
                                    'KUS/Sementara' => 'KUS/Sementara',
                                    'Masih Dalam Siasatan OYDS Gagal Dikesan' => 'Masih Dalam Siasatan OYDS Gagal Dikesan',
                                    'Masih Dalam Siasatan Untuk Lengkapkan Dokumentasi' => 'Masih Dalam Siasatan Untuk Lengkapkan Dokumentasi',
                                    'Terbengkalai/Tiada Tindakan' => 'Terbengkalai/Tiada Tindakan',
                                ];
                            @endphp
                            {!! render_json_checkboxes('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah, $mahkamah_options) !!}
                        </div>

                        <div>
                            <label for="ulasan_pegawai_pemeriksa_fail" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Jika Ada)</label>
                            <textarea name="ulasan_pegawai_pemeriksa_fail" id="ulasan_pegawai_pemeriksa_fail" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_pegawai_pemeriksa_fail', $paper->ulasan_pegawai_pemeriksa_fail) }}</textarea>
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