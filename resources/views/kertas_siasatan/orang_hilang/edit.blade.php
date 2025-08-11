<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Orang Hilang ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'OrangHilang', 'id' => $paper->id]) }}" class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
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
                function render_json_checkboxes($name, $currentJson, $options) {
                    $currentValues = old($name, $currentJson ?? []);
                    
                    // Handle JSON strings from database
                    if (is_string($currentValues)) {
                        $decoded = json_decode($currentValues, true);
                        $currentValues = is_array($decoded) ? $decoded : [];
                    } elseif (!is_array($currentValues)) {
                        $currentValues = [];
                    }
                    
                    $html = "<div class='mt-2 space-y-2 rounded-md border p-4 bg-gray-50'>";
                    foreach ($options as $optionValue => $optionLabel) {
                        $checked = in_array($optionValue, $currentValues) ? 'checked' : '';
                        $html .= "<label class='flex items-center'><input type='checkbox' name='{$name}[]' value='{$optionValue}' class='form-checkbox h-5 w-5 text-indigo-600' {$checked}><span class='ml-3 text-gray-700'>{$optionLabel}</span></label>";
                    }
                    $html .= "</div>";
                    return $html;
                }
                @endphp

                {{-- IPRS Standard Section --}}
                 <x-iprs-section :paper="$paper" mode="view" />

                <!-- BAHAGIAN 1 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 1</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">No. Kertas Siasatan</label>
                                                            <input type="text" name="no_kertas_siasatan" id="no_kertas_siasatan" value="{{ old('no_kertas_siasatan', $paper->no_kertas_siasatan) }}" class="mt-1 block w-full form-input">
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

                <!-- BAHAGIAN 2 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 2</h3>
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
                            <label for="tarikh_edaran_minit_ks_sebelum_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Sebelum Akhir (C)</label>
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
                    </div>
                </div>

                <!-- BAHAGIAN 3 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 3</h3>
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
                        <!-- Arahan Tuduh Oleh YA TPR - Single Choice Radio Buttons -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Arahan Tuduh Oleh YA TPR Diambil Tindakan</label>
                            <div class="mt-2 space-y-2">
                                @php
                                    $currentArahan = old('arahan_tuduh_oleh_ya_tpr', $paper->arahan_tuduh_oleh_ya_tpr);
                                    // If it's an array (from old checkbox data), get the first value
                                    if (is_array($currentArahan)) {
                                        $currentArahan = !empty($currentArahan) ? $currentArahan[0] : '';
                                    }
                                @endphp
                                <label class="flex items-center">
                                    <input type='radio' name='arahan_tuduh_oleh_ya_tpr' value='Ya' 
                                        {{ $currentArahan == 'Ya' ? 'checked' : '' }}
                                        class='form-radio h-4 w-4 text-indigo-600'>
                                    <span class='ml-2 text-gray-700'>Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type='radio' name='arahan_tuduh_oleh_ya_tpr' value='Tidak' 
                                        {{ $currentArahan == 'Tidak' ? 'checked' : '' }}
                                        class='form-radio h-4 w-4 text-indigo-600'>
                                    <span class='ml-2 text-gray-700'>Tidak</span>
                                </label>
                                <label class="flex items-center">
                                    <input type='radio' name='arahan_tuduh_oleh_ya_tpr' value='Tiada Usaha Oleh IO/AIO' 
                                        {{ $currentArahan == 'Tiada Usaha Oleh IO/AIO' ? 'checked' : '' }}
                                        class='form-radio h-4 w-4 text-indigo-600'>
                                    <span class='ml-2 text-gray-700'>Tiada Usaha Oleh IO/AIO</span>
                                </label>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label for="ulasan_keputusan_siasatan_tpr" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Siasatan TPR</label>
                            <textarea name="ulasan_keputusan_siasatan_tpr" id="ulasan_keputusan_siasatan_tpr" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keputusan_siasatan_tpr', $paper->ulasan_keputusan_siasatan_tpr) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa" id="ulasan_keseluruhan_pegawai_pemeriksa" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa', $paper->ulasan_keseluruhan_pegawai_pemeriksa) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 4 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 4</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Barang Kes Didaftarkan</label>
                            {!! render_boolean_radio('adakah_barang_kes_didaftarkan', $paper->adakah_barang_kes_didaftarkan) !!}
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="no_daftar_barang_kes_am" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Am</label>
                                <input type="text" name="no_daftar_barang_kes_am" id="no_daftar_barang_kes_am" value="{{ old('no_daftar_barang_kes_am', $paper->no_daftar_barang_kes_am) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_barang_kes_berharga" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Berharga</label>
                                <input type="text" name="no_daftar_barang_kes_berharga" id="no_daftar_barang_kes_berharga" value="{{ old('no_daftar_barang_kes_berharga', $paper->no_daftar_barang_kes_berharga) }}" class="mt-1 block w-full form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 5 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 5</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID Siasatan Dikemaskini</label>
                            {!! render_boolean_radio('status_id_siasatan_dikemaskini', $paper->status_id_siasatan_dikemaskini) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rajah Kasar Tempat Kejadian</label>
                            {!! render_boolean_radio('status_rajah_kasar_tempat_kejadian', $paper->status_rajah_kasar_tempat_kejadian) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Tempat Kejadian</label>
                            {!! render_boolean_radio('status_gambar_tempat_kejadian', $paper->status_gambar_tempat_kejadian) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Am</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_am', $paper->status_gambar_barang_kes_am) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Berharga</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_berharga', $paper->status_gambar_barang_kes_berharga) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Orang Hilang</label>
                            {!! render_boolean_radio('status_gambar_orang_hilang', $paper->status_gambar_orang_hilang) !!}
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 6 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 6</h3>
                    <div class="space-y-6">
                        <!-- Borang PEM (Multiple Selection) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borang PEM</label>
                            {!! render_json_checkboxes('status_pem', $paper->status_pem, ['PEM 1' => 'PEM 1', 'PEM 2' => 'PEM 2', 'PEM 3' => 'PEM 3', 'PEM 4' => 'PEM 4']) !!}
                        </div>

                        <!-- MPS 1 & MPS 2 with date input -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">MPS 1</label>
                                {!! render_status_with_date_radio('mps1', 'status_mps1', 'tarikh_mps1', $paper->status_mps1, $paper->tarikh_mps1, 'Cipta', 'Tidak Cipta') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">MPS 2</label>
                                {!! render_status_with_date_radio('mps2', 'status_mps2', 'tarikh_mps2', $paper->status_mps2, $paper->tarikh_mps2, 'Cipta', 'Tidak Cipta') !!}
                            </div>
                        </div>

                        <!-- NUR-Alert JSJ (Multiple Options) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pemakluman NUR-Alert JSJ (Bawah 18 Tahun)</label>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="pemakluman_nur_alert_jsj_bawah_18_tahun" value="Ada" 
                                        {{ old('pemakluman_nur_alert_jsj_bawah_18_tahun', $paper->pemakluman_nur_alert_jsj_bawah_18_tahun) == 'Ada' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ada</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="pemakluman_nur_alert_jsj_bawah_18_tahun" value="Tiada" 
                                        {{ old('pemakluman_nur_alert_jsj_bawah_18_tahun', $paper->pemakluman_nur_alert_jsj_bawah_18_tahun) == 'Tiada' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="pemakluman_nur_alert_jsj_bawah_18_tahun" value="Tidak Pasti" 
                                        {{ old('pemakluman_nur_alert_jsj_bawah_18_tahun', $paper->pemakluman_nur_alert_jsj_bawah_18_tahun) == 'Tidak Pasti' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak Pasti</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rakaman Percakapan (Multiple Options) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rakaman Percakapan Orang Hilang (OH) Dijumpai Semula</label>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="rakaman_percakapan_orang_hilang" value="Telah Diambil" 
                                        {{ old('rakaman_percakapan_orang_hilang', $paper->rakaman_percakapan_orang_hilang) == 'Telah Diambil' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Telah Diambil</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="rakaman_percakapan_orang_hilang" value="Belum Diambil" 
                                        {{ old('rakaman_percakapan_orang_hilang', $paper->rakaman_percakapan_orang_hilang) == 'Belum Diambil' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Belum Diambil</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="rakaman_percakapan_orang_hilang" value="Tidak Pasti" 
                                        {{ old('rakaman_percakapan_orang_hilang', $paper->rakaman_percakapan_orang_hilang) == 'Tidak Pasti' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak Pasti</span>
                                </label>
                            </div>
                        </div>

                        <!-- Laporan Polis (Multiple Options) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Laporan Polis Orang Hilang (OH) Dijumpai Semula</label>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="laporan_polis_orang_hilang_dijumpai" value="Ada Dilampirkan" 
                                        {{ old('laporan_polis_orang_hilang_dijumpai', $paper->laporan_polis_orang_hilang_dijumpai) == 'Ada Dilampirkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ada Dilampirkan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="laporan_polis_orang_hilang_dijumpai" value="Tidak Dilampirkan" 
                                        {{ old('laporan_polis_orang_hilang_dijumpai', $paper->laporan_polis_orang_hilang_dijumpai) == 'Tidak Dilampirkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak Dilampirkan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="laporan_polis_orang_hilang_dijumpai" value="Tidak Pasti" 
                                        {{ old('laporan_polis_orang_hilang_dijumpai', $paper->laporan_polis_orang_hilang_dijumpai) == 'Tidak Pasti' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak Pasti</span>
                                </label>
                            </div>
                        </div>

                        <!-- Hebahan Media Massa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hebahan Media Massa</label>
                            {!! render_boolean_radio('hebahan_media_massa', $paper->hebahan_media_massa, 'Dibuat', 'Tidak Dibuat') !!}
                        </div>

                        <div x-data="{
                            bukanJenayah: '{{ old('orang_hilang_dijumpai_mati_mengejut_bukan_jenayah', $paper->orang_hilang_dijumpai_mati_mengejut_bukan_jenayah) }}',
                            jenayah: '{{ old('orang_hilang_dijumpai_mati_mengejut_jenayah', $paper->orang_hilang_dijumpai_mati_mengejut_jenayah) }}'
                        }">
                            <!-- Orang Hilang Dijumpai (Mati Mengejut Bukan Jenayah) with reason -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Orang Hilang Dijumpai (Mati Mengejut Bukan Jenayah)</label>
                                <div class="mt-2 flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="orang_hilang_dijumpai_mati_mengejut_bukan_jenayah" value="1" 
                                            x-model="bukanJenayah" 
                                            {{ old('orang_hilang_dijumpai_mati_mengejut_bukan_jenayah', $paper->orang_hilang_dijumpai_mati_mengejut_bukan_jenayah) == '1' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-indigo-600">
                                        <span class="ml-2 text-gray-700">Ya</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="orang_hilang_dijumpai_mati_mengejut_bukan_jenayah" value="0" 
                                            x-model="bukanJenayah" 
                                            {{ old('orang_hilang_dijumpai_mati_mengejut_bukan_jenayah', $paper->orang_hilang_dijumpai_mati_mengejut_bukan_jenayah) == '0' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-indigo-600">
                                        <span class="ml-2 text-gray-700">Tidak</span>
                                    </label>
                                </div>
                                <div x-show="bukanJenayah === '0'" x-transition class="mt-2">
                                    <label for="alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah" class="text-sm text-gray-600">Alasan/Sebab:</label>
                                    <textarea name="alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah" id="alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah" rows="2" class="mt-1 block w-full form-textarea">{{ old('alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah', $paper->alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah) }}</textarea>
                                </div>
                            </div>

                            <!-- Orang Hilang Dijumpai (Mati Mengejut Jenayah) with reason -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Orang Hilang Dijumpai (Mati Mengejut Jenayah)</label>
                                <div class="mt-2 flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="orang_hilang_dijumpai_mati_mengejut_jenayah" value="1" 
                                            x-model="jenayah" 
                                            {{ old('orang_hilang_dijumpai_mati_mengejut_jenayah', $paper->orang_hilang_dijumpai_mati_mengejut_jenayah) == '1' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-indigo-600">
                                        <span class="ml-2 text-gray-700">Ya</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="orang_hilang_dijumpai_mati_mengejut_jenayah" value="0" 
                                            x-model="jenayah" 
                                            {{ old('orang_hilang_dijumpai_mati_mengejut_jenayah', $paper->orang_hilang_dijumpai_mati_mengejut_jenayah) == '0' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-indigo-600">
                                        <span class="ml-2 text-gray-700">Tidak</span>
                                    </label>
                                </div>
                                <div x-show="jenayah === '0'" x-transition class="mt-2">
                                    <label for="alasan_orang_hilang_dijumpai_mati_mengejut_jenayah" class="text-sm text-gray-600">Alasan/Sebab:</label>
                                    <textarea name="alasan_orang_hilang_dijumpai_mati_mengejut_jenayah" id="alasan_orang_hilang_dijumpai_mati_mengejut_jenayah" rows="2" class="mt-1 block w-full form-textarea">{{ old('alasan_orang_hilang_dijumpai_mati_mengejut_jenayah', $paper->alasan_orang_hilang_dijumpai_mati_mengejut_jenayah) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Semboyan Pemakluman ke Kedutaan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Semboyan Pemakluman ke Kedutaan (Bukan Warganegara)</label>
                            {!! render_boolean_radio('semboyan_pemakluman_ke_kedutaan_bukan_warganegara', $paper->semboyan_pemakluman_ke_kedutaan_bukan_warganegara, 'Dibuat', 'Tidak Dibuat') !!}
                        </div>

                        <!-- Ulasan -->
                        <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_borang" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Borang)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_borang" id="ulasan_keseluruhan_pegawai_pemeriksa_borang" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_borang', $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 7 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7</h3>
                    <div class="space-y-8">
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Imigresen</h4>
                            <div class="grid grid-cols-1 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan Pengesahan Masuk / Keluar Malaysia</label>
                                    {!! render_status_with_date_radio('imigresen_permohonan', 'status_permohonan_laporan_imigresen', 'tarikh_permohonan_laporan_imigresen', $paper->status_permohonan_laporan_imigresen, $paper->tarikh_permohonan_laporan_imigresen) !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan Permit Kerja di Malaysia</label>
                                    {!! render_boolean_radio('permohonan_laporan_permit_kerja', $paper->permohonan_laporan_permit_kerja) !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan Agensi Pekerjaan di Malaysia</label>
                                    {!! render_boolean_radio('permohonan_laporan_agensi_pekerjaan', $paper->permohonan_laporan_agensi_pekerjaan) !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Status Kewarganegaraan</label>
                                    {!! render_boolean_radio('permohonan_status_kewarganegaraan', $paper->permohonan_status_kewarganegaraan) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<!-- BAHAGIAN 8 -->
<div>
    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 8</h3>
    <div class="space-y-6">
        <!-- 1. M/S 4 - Keputusan Kes Dicatat (Excel 标记为 Y) -->
        <div>
            <label class="block text-sm font-medium text-gray-700">M/S 4 - Keputusan Kes Dicatat</label>
            {!! render_boolean_radio('adakah_muka_surat_4_keputusan_kes_dicatat', $paper->adakah_muka_surat_4_keputusan_kes_dicatat) !!}
        </div>

        <!-- 2. KS Telah di KUS/FAIL (Excel 标记为 Y) -->
        <div x-data="{
            kusFailStatus: '{{ old('adakah_ks_kus_fail_selesai', $paper->adakah_ks_kus_fail_selesai) }}'
        }">
            <label class="block text-sm font-medium text-gray-700">KS Telah di KUS/FAIL</label>
            <select name="adakah_ks_kus_fail_selesai" x-model="kusFailStatus" class="mt-1 block w-full form-select">
                <option value="">-- Sila Pilih --</option>
                <option value="KUS" {{ old('adakah_ks_kus_fail_selesai', $paper->adakah_ks_kus_fail_selesai) == 'KUS' ? 'selected' : '' }}>KUS</option>
                <option value="FAIL" {{ old('adakah_ks_kus_fail_selesai', $paper->adakah_ks_kus_fail_selesai) == 'FAIL' ? 'selected' : '' }}>FAIL</option>
            </select>
        </div>

        <!-- 3. Keputusan Akhir Mahkamah (Excel 标记为 Y) -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Keputusan Akhir Mahkamah (boleh pilih lebih dari satu)</label>
            {!! render_json_checkboxes('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah, [
                'Jatuh Hukum' => 'Jatuh Hukum',
                'NFA' => 'NFA',
                'DNA' => 'DNA',
                'DNAA' => 'DNAA',
                'KUS/SEMENTARA' => 'KUS/SEMENTARA',
                'MASIH DALAM SIASATAN / OYDS GAGAL DIKESAN' => 'MASIH DALAM SIASATAN / OYDS GAGAL DIKESAN',
                'MASIH DALAM SIASATAN / LENGKAPKAN DOKUMEN SIASATAN' => 'MASIH DALAM SIASATAN / LENGKAPKAN DOKUMEN SIASATAN',
                'TERBENGKALAI/ TIADA TINDAKAN' => 'TERBENGKALAI/ TIADA TINDAKAN'
            ]) !!}
        </div>

        <!-- 4. Ulasan Keseluruhan Pegawai Pemeriksa (Excel 标记为 Y) -->
        <div>
            <label for="ulasan_keseluruhan_pegawai_pemeriksa_fail" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</label>
            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_fail" id="ulasan_keseluruhan_pegawai_pemeriksa_fail" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_fail', $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail) }}</textarea>
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
    </style>
</x-app-layout>
