<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Trafik Rule ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'TrafikRule', 'id' => $paper->id]) }}" class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Terdapat beberapa masalah dengan input anda:</p>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                // Helper function for simple boolean choices (e.g., Ada/Tiada, Ya/Tidak)
                function render_boolean_radio($name, $currentValue, $YaLabel = 'Ada / Ya', $TidakLabel = 'Tiada / Tidak') {
                    $effectiveValue = old($name, $currentValue);
                    $html = "<div class='mt-2 flex items-center space-x-6'>";
                    $checkedYa = (($effectiveValue === true || $effectiveValue === 1 || $effectiveValue === '1') ? 'checked' : '');
                    $html .= "<label class='flex items-center cursor-pointer'><input type='radio' name='{$name}' value='1' class='form-radio h-4 w-4 text-indigo-600' {$checkedYa}><span class='ml-2 text-gray-700'>{$YaLabel}</span></label>";
                    $checkedTidak = (($effectiveValue === false || $effectiveValue === 0 || $effectiveValue === '0') && $effectiveValue !== null ? 'checked' : '');
                    $html .= "<label class='flex items-center cursor-pointer'><input type='radio' name='{$name}' value='0' class='form-radio h-4 w-4 text-indigo-600' {$checkedTidak}><span class='ml-2 text-gray-700'>{$TidakLabel}</span></label>";
                    $html .= "</div>";
                    return $html;
                }

                // Helper for status choices that reveal a date input with radio buttons
                function render_status_with_date_radio($id, $statusName, $dateName, $currentStatus, $currentDate, $YaLabel = 'Ada / Cipta', $TidakLabel = 'Tiada / Tidak Cipta') {
                    $effectiveStatus = old($statusName, $currentStatus);
                    $initialStatusForAlpine = (($effectiveStatus === true || $effectiveStatus === 1 || $effectiveStatus === '1') ? '1' : '0');
                    $html = "<div x-data='{ status: \"{$initialStatusForAlpine}\" }'>";
                    $html .= "<div class='mt-2 flex items-center space-x-6'>";
                    $html .= "<label class='flex items-center cursor-pointer'><input type='radio' name='{$statusName}' value='1' x-model='status' class='form-radio h-4 w-4 text-indigo-600'><span class='ml-2 text-gray-700'>{$YaLabel}</span></label>";
                    $html .= "<label class='flex items-center cursor-pointer'><input type='radio' name='{$statusName}' value='0' x-model='status' class='form-radio h-4 w-4 text-indigo-600'><span class='ml-2 text-gray-700'>{$TidakLabel}</span></label>";
                    $html .= "</div>";
                    $html .= "<div x-show='status === \"1\"' x-transition class='mt-2'>";
                    $html .= "<label for='{$dateName}_{$id}' class='text-sm text-gray-600'>Jika Ada, nyatakan tarikh:</label>";
                    $html .= "<input type='date' name='{$dateName}' id='{$dateName}_{$id}' value='" . old($dateName, optional($currentDate)->format('Y-m-d')) . "' class='mt-1 block w-full form-input'>";
                    $html .= "</div></div>";
                    return $html;
                }

                // Helper for JSON fields represented by checkboxes (for multi-select where still needed)
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
                            <label for="no_fail_lmm_t" class="block text-sm font-medium text-gray-700">No. Fail LMM (T)</label>
                            <input type="text" name="no_fail_lmm_t" id="no_fail_lmm_t" value="{{ old('no_fail_lmm_t', $paper->no_fail_lmm_t) }}" class="mt-1 block w-full form-input">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh SIO</label>
                            {!! render_status_with_date_radio('sio', 'arahan_minit_oleh_sio_status', 'arahan_minit_oleh_sio_tarikh', $paper->arahan_minit_oleh_sio_status, $paper->arahan_minit_oleh_sio_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh Ketua Bahagian</label>
                            {!! render_status_with_date_radio('kb', 'arahan_minit_ketua_bahagian_status', 'arahan_minit_ketua_bahagian_tarikh', $paper->arahan_minit_ketua_bahagian_status, $paper->arahan_minit_ketua_bahagian_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh Ketua Jabatan</label>
                            {!! render_status_with_date_radio('kj', 'arahan_minit_ketua_jabatan_status', 'arahan_minit_ketua_jabatan_tarikh', $paper->arahan_minit_ketua_jabatan_status, $paper->arahan_minit_ketua_jabatan_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh YA TPR</label>
                            {!! render_status_with_date_radio('tpr', 'arahan_minit_oleh_ya_tpr_status', 'arahan_minit_oleh_ya_tpr_tarikh', $paper->arahan_minit_oleh_ya_tpr_status, $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}
                        </div>

                        <div class="md:col-span-2">
                            <label for="keputusan_siasatan_oleh_ya_tpr" class="block text-sm font-medium text-gray-700">Keputusan Siasatan Oleh YA TPR</label>
                            <input type="text" name="keputusan_siasatan_oleh_ya_tpr" id="keputusan_siasatan_oleh_ya_tpr" value="{{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) }}" class="mt-1 block w-full form-input">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Adakah Arahan Tuduh Oleh YA TPR Diambil Tindakan</label>
                            <div class="mt-2 space-y-2">
                                @php
                                    $currentArahan = old('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', $paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan);
                                    // If it's an array (from old checkbox data), get the first value
                                    if (is_array($currentArahan)) {
                                        $currentArahan = !empty($currentArahan) ? $currentArahan[0] : '';
                                    }
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan" value="Ya" 
                                        {{ $currentArahan == 'Ya' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan" value="Tidak" 
                                        {{ $currentArahan == 'Tidak' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan" value="Tiada Usaha Oleh IO/AIO" 
                                        {{ $currentArahan == 'Tiada Usaha Oleh IO/AIO' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada Usaha Oleh IO/AIO</span>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="ulasan_keputusan_siasatan_tpr" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Siasatan TPR</label>
                            <textarea name="ulasan_keputusan_siasatan_tpr" id="ulasan_keputusan_siasatan_tpr" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keputusan_siasatan_tpr', $paper->ulasan_keputusan_siasatan_tpr) }}</textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Jika Ada)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa" id="ulasan_keseluruhan_pegawai_pemeriksa" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa', $paper->ulasan_keseluruhan_pegawai_pemeriksa) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 5: Dokumen Siasatan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 5: Dokumen Siasatan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID Siasatan Dikemaskini</label>
                            {!! render_boolean_radio('status_id_siasatan_dikemaskini', $paper->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rajah Kasar Tempat Kejadian</label>
                            {!! render_boolean_radio('status_rajah_kasar_tempat_kejadian', $paper->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Tempat Kejadian</label>
                            {!! render_boolean_radio('status_gambar_tempat_kejadian', $paper->status_gambar_tempat_kejadian, 'Ada', 'Tiada') !!}
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RJ 10B</label>
                                {!! render_status_with_date_radio('rj10b', 'status_rj10b', 'tarikh_rj10b', $paper->status_rj10b, $paper->tarikh_rj10b) !!}
                            </div>
                            <div>
                                <label for="lain_lain_rj_dikesan" class="block text-sm font-medium text-gray-700">Lain-lain RJ Dikesan</label>
                                <input type="text" name="lain_lain_rj_dikesan" id="lain_lain_rj_dikesan" value="{{ old('lain_lain_rj_dikesan', $paper->lain_lain_rj_dikesan) }}" class="mt-1 block w-full form-input">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Saman PDRM (S) 257 --}}
                            <div x-data='{ status: "{{ old('status_saman_pdrm_s_257', $paper->status_saman_pdrm_s_257 ? '1' : '0') }}" }'>
                                <label class="block text-sm font-medium text-gray-700">Saman PDRM (S) 257 Adakah Dicipta</label>
                                <div class="mt-2 flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer"><input type="radio" name="status_saman_pdrm_s_257" value="1" x-model="status" class="form-radio h-4 w-4 text-indigo-600"><span class="ml-2 text-gray-700">Dicipta</span></label>
                                    <label class="flex items-center cursor-pointer"><input type="radio" name="status_saman_pdrm_s_257" value="0" x-model="status" class="form-radio h-4 w-4 text-indigo-600"><span class="ml-2 text-gray-700">Tidak Dicipta</span></label>
                                </div>
                                <div x-show='status === "1"' x-transition class="mt-2">
                                    <label for="no_saman_pdrm_s_257" class="text-sm text-gray-600">Jika Dicipta, nyatakan No Saman:</label>
                                    <input type="text" name="no_saman_pdrm_s_257" id="no_saman_pdrm_s_257" value="{{ old('no_saman_pdrm_s_257', $paper->no_saman_pdrm_s_257) }}" class="mt-1 block w-full form-input">
                                </div>
                            </div>
                            {{-- Saman PDRM (S) 167 --}}
                            <div x-data='{ status: "{{ old('status_saman_pdrm_s_167', $paper->status_saman_pdrm_s_167 ? '1' : '0') }}" }'>
                                <label class="block text-sm font-medium text-gray-700">Saman PDRM (S) 167 Adakah Dicipta</label>
                                <div class="mt-2 flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer"><input type="radio" name="status_saman_pdrm_s_167" value="1" x-model="status" class="form-radio h-4 w-4 text-indigo-600"><span class="ml-2 text-gray-700">Dicipta</span></label>
                                    <label class="flex items-center cursor-pointer"><input type="radio" name="status_saman_pdrm_s_167" value="0" x-model="status" class="form-radio h-4 w-4 text-indigo-600"><span class="ml-2 text-gray-700">Tidak Dicipta</span></label>
                                </div>
                                <div x-show='status === "1"' x-transition class="mt-2">
                                    <label for="no_saman_pdrm_s_167" class="text-sm text-gray-600">Jika Dicipta, nyatakan No Saman:</label>
                                    <input type="text" name="no_saman_pdrm_s_167" id="no_saman_pdrm_s_167" value="{{ old('no_saman_pdrm_s_167', $paper->no_saman_pdrm_s_167) }}" class="mt-1 block w-full form-input">
                                </div>
                            </div>
                        </div>
                         <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_borang" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Borang)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_borang" id="ulasan_keseluruhan_pegawai_pemeriksa_borang" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_borang', $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 7: Laporan Agensi Luar -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7: Laporan Agensi Luar</h3>
                    <div class="space-y-8"> {{-- Changed from space-y-4 to space-y-8 for consistency with TrafikSeksyen's B7 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-md">
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Permohonan Laporan JKR</label>
                                {!! render_status_with_date_radio('jkr_permohonan', 'status_permohonan_laporan_jkr', 'tarikh_permohonan_laporan_jkr', $paper->status_permohonan_laporan_jkr, $paper->tarikh_permohonan_laporan_jkr, 'Ya', 'Tidak') !!}
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Laporan Penuh JKR</label>
                                {!! render_status_with_date_radio('jkr_penuh', 'status_laporan_penuh_jkr', 'tarikh_laporan_penuh_jkr', $paper->status_laporan_penuh_jkr, $paper->tarikh_laporan_penuh_jkr, 'Dilampirkan', 'Tidak') !!}
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-md">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Permohonan Laporan JPJ</label>
                                {!! render_status_with_date_radio('jpj_permohonan', 'status_permohonan_laporan_jpj', 'tarikh_permohonan_laporan_jpj', $paper->status_permohonan_laporan_jpj, $paper->tarikh_permohonan_laporan_jpj, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Laporan Penuh JKJR</label>
                                {!! render_status_with_date_radio('jkjr_penuh', 'status_laporan_penuh_jkjr', 'tarikh_laporan_penuh_jkjr', $paper->status_laporan_penuh_jkjr, $paper->tarikh_laporan_penuh_jkjr, 'Dilampirkan', 'Tidak') !!}
                            </div>
                        </div>
                         <div>
                            <label for="lain_lain_permohonan_laporan" class="block text-sm font-medium text-gray-700">Lain-lain Permohonan Laporan</label>
                            <input type="text" name="lain_lain_permohonan_laporan" id="lain_lain_permohonan_laporan" value="{{ old('lain_lain_permohonan_laporan', $paper->lain_lain_permohonan_laporan) }}" class="mt-1 block w-full form-input">
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 8: Status Fail -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 8: Status Fail</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 Keputusan Kes Dicatat</label>
                                {!! render_boolean_radio('adakah_muka_surat_4_keputusan_kes_dicatat', $paper->adakah_muka_surat_4_keputusan_kes_dicatat, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah KS di KUS/FAIL</label>
                                {!! render_boolean_radio('adakah_ks_kus_fail_selesai', $paper->adakah_ks_kus_fail_selesai, 'Ya', 'Tidak') !!}
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Adakah Fail LMM(T) Telah Ada Keputusan</label>
                                {!! render_boolean_radio('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan', $paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan, 'Ya', 'Tidak') !!}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keputusan Akhir Mahkamah</label>
                             <select name="keputusan_akhir_mahkamah" class="mt-1 block w-full form-select">
                                <option value="">-- Sila Pilih --</option>
                                <option value="Jatuh Hukum" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'Jatuh Hukum' ? 'selected' : '' }}>Jatuh Hukum</option>
                                <option value="NFA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'NFA' ? 'selected' : '' }}>NFA</option>
                                <option value="DNA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'DNA' ? 'selected' : '' }}>DNA</option>
                                <option value="DNAA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'DNAA' ? 'selected' : '' }}>DNAA</option>
                                <option value="KUS/Sementara" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'KUS/Sementara' ? 'selected' : '' }}>KUS/Sementara</option>
                                <option value="Masih Dalam Siasatan OYDS Gagal Dikesan" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'Masih Dalam Siasatan OYDS Gagal Dikesan' ? 'selected' : '' }}>Masih Dalam Siasatan OYDS Gagal Dikesan</option>
                                <option value="Masih Dalam Siasatan Untuk Lengkapkan Dokumentasi" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'Masih Dalam Siasatan Untuk Lengkapkan Dokumentasi' ? 'selected' : '' }}>Masih Dalam Siasatan Untuk Lengkapkan Dokumentasi</option>
                                <option value="Terbengkalai/Tiada Tindakan" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'Terbengkalai/Tiada Tindakan' ? 'selected' : '' }}>Terbengkalai/Tiada Tindakan</option>
                            </select>
                        </div>

                        <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_fail" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_fail" id="ulasan_keseluruhan_pegawai_pemeriksa_fail" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_fail', $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 mt-6 border-t">
                    <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-3">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Kemaskini</button>
                </div>

            </form>
        </div>
    </div>

    <style>
        /* General form element styling for consistency */
        .form-input, .form-select, .form-textarea {
            @apply rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
        }
        .form-radio, .form-checkbox {
            @apply rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500; /* Adjusted form-radio to rounded-full */
        }
    </style>
</x-app-layout>