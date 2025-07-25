<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Laporan Mati Mengejut ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'LaporanMatiMengejut', 'id' => $paper->id]) }}" class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
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
                            <label for="no_fail_lmm_sdr" class="block text-sm font-medium text-gray-700">No. Fail LMM/SDR</label>
                            <input type="text" name="no_fail_lmm_sdr" id="no_fail_lmm_sdr" value="{{ old('no_fail_lmm_sdr', $paper->no_fail_lmm_sdr) }}" class="mt-1 block w-full form-input">
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
                        
                        <!-- LMM(T) Dates -->
                        <div class="md:col-span-3 lg:col-span-3 my-4 border-t pt-4">
                            <h4 class="font-semibold text-sm text-gray-600">Edaran Fail LMM(T)</h4>
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_fail_lmm_t_pertama" class="block text-sm font-medium text-gray-700">Tarikh Edaran Pertama</label>
                            <input type="date" name="tarikh_edaran_minit_fail_lmm_t_pertama" id="tarikh_edaran_minit_fail_lmm_t_pertama" value="{{ old('tarikh_edaran_minit_fail_lmm_t_pertama', optional($paper->tarikh_edaran_minit_fail_lmm_t_pertama)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_fail_lmm_t_kedua" class="block text-sm font-medium text-gray-700">Tarikh Edaran Kedua</label>
                            <input type="date" name="tarikh_edaran_minit_fail_lmm_t_kedua" id="tarikh_edaran_minit_fail_lmm_t_kedua" value="{{ old('tarikh_edaran_minit_fail_lmm_t_kedua', optional($paper->tarikh_edaran_minit_fail_lmm_t_kedua)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Sebelum Akhir</label>
                            <input type="date" name="tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir" id="tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir" value="{{ old('tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir', optional($paper->tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_fail_lmm_t_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Akhir</label>
                            <input type="date" name="tarikh_edaran_minit_fail_lmm_t_akhir" id="tarikh_edaran_minit_fail_lmm_t_akhir" value="{{ old('tarikh_edaran_minit_fail_lmm_t_akhir', optional($paper->tarikh_edaran_minit_fail_lmm_t_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fail LMM Bhg. Pengurusan M/S 2</label>
                            {!! render_boolean_radio('fail_lmm_bahagian_pengurusan_pada_muka_surat_2', $paper->fail_lmm_bahagian_pengurusan_pada_muka_surat_2) !!}
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
                                    <input type="radio" name="arahan_tuduh_oleh_ya_tpr" value="Ya" 
                                        {{ $currentArahan == 'Ya' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="arahan_tuduh_oleh_ya_tpr" value="Tidak" 
                                        {{ $currentArahan == 'Tidak' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="arahan_tuduh_oleh_ya_tpr" value="Tiada Usaha Oleh IO/AIO" 
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
                            <label for="keputusan_siasatan_oleh_ya_koroner" class="block text-sm font-medium text-gray-700">Keputusan Siasatan Oleh YA Koroner</label>
                            <input type="text" name="keputusan_siasatan_oleh_ya_koroner" id="keputusan_siasatan_oleh_ya_koroner" value="{{ old('keputusan_siasatan_oleh_ya_koroner', $paper->keputusan_siasatan_oleh_ya_koroner) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="md:col-span-2">
                            <label for="ulasan_keputusan_oleh_ya_koroner" class="block text-sm font-medium text-gray-700">Ulasan Keputusan Oleh YA Koroner</label>
                            <input type="text" name="ulasan_keputusan_oleh_ya_koroner" id="ulasan_keputusan_oleh_ya_koroner" value="{{ old('ulasan_keputusan_oleh_ya_koroner', $paper->ulasan_keputusan_oleh_ya_koroner) }}" class="mt-1 block w-full form-input">
                        </div>
                        <div class="md:col-span-2">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa" id="ulasan_keseluruhan_pegawai_pemeriksa" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa', $paper->ulasan_keseluruhan_pegawai_pemeriksa) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 4: Barang Kes -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 4: Barang Kes</h3>
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
                            <div>
                                <label for="jenis_barang_kes_am" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Am</label>
                                <input type="text" name="jenis_barang_kes_am" id="jenis_barang_kes_am" value="{{ old('jenis_barang_kes_am', $paper->jenis_barang_kes_am) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="jenis_barang_kes_berharga" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Berharga</label>
                                <input type="text" name="jenis_barang_kes_berharga" id="jenis_barang_kes_berharga" value="{{ old('jenis_barang_kes_berharga', $paper->jenis_barang_kes_berharga) }}" class="mt-1 block w-full form-input">
                            </div>
                        </div>
                        
                        <!-- Status Pergerakan Barang Kes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pergerakan Barang Kes</label>
                            <div class="space-y-2 pl-4">
                                @php
                                    $currentValue = $paper->status_pergerakan_barang_kes ?? '';
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Simpanan Stor Ekshibit" 
                                        {{ $currentValue == 'Simpanan Stor Ekshibit' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Simpanan Stor Ekshibit</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Ujian Makmal" 
                                        {{ $currentValue == 'Ujian Makmal' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ujian Makmal</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Di Mahkamah" 
                                        {{ $currentValue == 'Di Mahkamah' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Di Mahkamah</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Pada IO/AIO" 
                                        {{ $currentValue == 'Pada IO/AIO' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Pada IO/AIO</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="status_pergerakan_barang_kes" value="Lain-lain" 
                                            {{ $currentValue == 'Lain-lain' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-blue-600" id="status_pergerakan_lain">
                                        <span class="ml-2 text-gray-700">Lain-lain</span>
                                    </label>
                                    <input type="text" name="status_pergerakan_barang_kes_lain" id="status_pergerakan_barang_kes_lain" 
                                        value="{{ old('status_pergerakan_barang_kes_lain', '') }}" 
                                        class="ml-2 form-input text-sm w-64" 
                                        {{ $currentValue != 'Lain-lain' ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Status Barang Kes Selesai Siasatan -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Barang Kes Selesai Siasatan</label>
                            <div class="space-y-2 pl-4">
                                @php
                                    $currentValueSelesai = $paper->status_barang_kes_selesai_siasatan ?? '';
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dilupuskan ke Perbendaharaan" 
                                        {{ $currentValueSelesai == 'Dilupuskan ke Perbendaharaan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilupuskan ke Perbendaharaan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dikembalikan kepada Pemilik" 
                                        {{ $currentValueSelesai == 'Dikembalikan kepada Pemilik' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dikembalikan kepada Pemilik</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dilupuskan" 
                                        {{ $currentValueSelesai == 'Dilupuskan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilupuskan</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="status_barang_kes_selesai_siasatan" value="Lain-lain" 
                                            {{ $currentValueSelesai == 'Lain-lain' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-blue-600" id="status_selesai_lain">
                                        <span class="ml-2 text-gray-700">Lain-lain</span>
                                    </label>
                                    <input type="text" name="status_barang_kes_selesai_siasatan_lain" id="status_barang_kes_selesai_siasatan_lain" 
                                        value="{{ old('status_barang_kes_selesai_siasatan_lain', '') }}" 
                                        class="ml-2 form-input text-sm w-64" 
                                        {{ $currentValueSelesai != 'Lain-lain' ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Kaedah Pelupusan Barang Kes -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kaedah Pelupusan Barang Kes</label>
                            <div class="space-y-2 pl-4">
                                @php
                                    $currentValueKaedah = $paper->kaedah_pelupusan_barang_kes ?? '';
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="kaedah_pelupusan_barang_kes" value="Dibakar" 
                                        {{ $currentValueKaedah == 'Dibakar' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dibakar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kaedah_pelupusan_barang_kes" value="Ditanam" 
                                        {{ $currentValueKaedah == 'Ditanam' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ditanam</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kaedah_pelupusan_barang_kes" value="Dihancurkan" 
                                        {{ $currentValueKaedah == 'Dihancurkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dihancurkan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kaedah_pelupusan_barang_kes" value="Dilelong" 
                                        {{ $currentValueKaedah == 'Dilelong' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilelong</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="kaedah_pelupusan_barang_kes" value="Lain-lain" 
                                            {{ $currentValueKaedah == 'Lain-lain' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-blue-600" id="kaedah_pelupusan_lain">
                                        <span class="ml-2 text-gray-700">Lain-lain</span>
                                    </label>
                                    <input type="text" name="kaedah_pelupusan_barang_kes_lain" id="kaedah_pelupusan_barang_kes_lain" 
                                        value="{{ old('kaedah_pelupusan_barang_kes_lain', '') }}" 
                                        class="ml-2 form-input text-sm w-64" 
                                        {{ $currentValueKaedah != 'Lain-lain' ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Arahan Pelupusan Barang Kes (Radio buttons - mutually exclusive) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arahan Pelupusan Barang Kes</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentValueArahan = $paper->arahan_pelupusan_barang_kes ?? '';
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="arahan_pelupusan_barang_kes" value="Ya" 
                                        {{ $currentValueArahan == 'Ya' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ya (Arahan Mahkamah/TPR)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="arahan_pelupusan_barang_kes" value="Tidak" 
                                        {{ $currentValueArahan == 'Tidak' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="arahan_pelupusan_barang_kes" value="Inisiatif IO/AIO Sendiri" 
                                        {{ $currentValueArahan == 'Inisiatif IO/AIO Sendiri' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Inisiatif IO/AIO Sendiri</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borang Serah/Terima Antara Pegawai Tangkapan dan IO/AIO</label>
                            {!! render_boolean_radio('adakah_borang_serah_terima_pegawai_tangkapan_io', $paper->adakah_borang_serah_terima_pegawai_tangkapan_io) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borang Serah/Terima (Penyiasat/Pemilik/Saksi)</label>
                            {!! render_boolean_radio('adakah_borang_serah_terima_penyiasat_pemilik_saksi', $paper->adakah_borang_serah_terima_penyiasat_pemilik_saksi) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sijil/Surat Kebenaran IPD</label>
                            {!! render_boolean_radio('adakah_sijil_surat_kebenaran_ipd', $paper->adakah_sijil_surat_kebenaran_ipd) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Pelupusan</label>
                            {!! render_boolean_radio('adakah_gambar_pelupusan', $paper->adakah_gambar_pelupusan) !!}
                        </div>
                        <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Barang Kes)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" id="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes', $paper->ulasan_keseluruhan_pegawai_pemeriksa_barang_kes) }}</textarea>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Enable/disable lain-lain input fields based on checkbox state
                    document.getElementById('status_pergerakan_lain').addEventListener('change', function() {
                        document.getElementById('status_pergerakan_barang_kes_lain').disabled = !this.checked;
                        if (!this.checked) {
                            document.getElementById('status_pergerakan_barang_kes_lain').value = '';
                        }
                    });
                    
                    document.getElementById('status_selesai_lain').addEventListener('change', function() {
                        document.getElementById('status_barang_kes_selesai_siasatan_lain').disabled = !this.checked;
                        if (!this.checked) {
                            document.getElementById('status_barang_kes_selesai_siasatan_lain').value = '';
                        }
                    });
                    
                    document.getElementById('kaedah_pelupusan_lain').addEventListener('change', function() {
                        document.getElementById('kaedah_pelupusan_barang_kes_lain').disabled = !this.checked;
                        if (!this.checked) {
                            document.getElementById('kaedah_pelupusan_barang_kes_lain').value = '';
                        }
                    });
                });
                </script>

                <!-- BAHAGIAN 5: Dokumen Siasatan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 5: Dokumen Siasatan</h3>
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
                            <label class="block text-sm font-medium text-gray-700">Gambar Post Mortem</label>
                            {!! render_boolean_radio('status_gambar_post_mortem_mayat_di_hospital', $paper->status_gambar_post_mortem_mayat_di_hospital) !!}
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
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Darah</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_darah', $paper->status_gambar_barang_kes_darah) !!}
                        </div>
                    </div>
                </div>
                
                <!-- BAHAGIAN 6: Borang & Semakan -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 6: Borang & Semakan</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Borang PEM</label>
                            {!! render_json_checkboxes('status_pem', $paper->status_pem, ['PEM 1' => 'PEM 1', 'PEM 2' => 'PEM 2', 'PEM 3' => 'PEM 3', 'PEM 4' => 'PEM 4']) !!}
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RJ2</label>
                                {!! render_status_with_date_radio('rj2', 'status_rj2', 'tarikh_rj2', $paper->status_rj2, $paper->tarikh_rj2) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RJ2B</label>
                                {!! render_status_with_date_radio('rj2b', 'status_rj2b', 'tarikh_rj2b', $paper->status_rj2b, $paper->tarikh_rj2b) !!}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Semboyan Pemakluman ke Kedutaan (Mati Mengejut Bukan Warganegara)</label>
                            {!! render_boolean_radio('status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati', $paper->status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati) !!}
                        </div>
                        <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_borang" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan (Borang)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_borang" id="ulasan_keseluruhan_pegawai_pemeriksa_borang" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_borang', $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 7: Permohonan Laporan Agensi Luar -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7: Permohonan Laporan Agensi Luar</h3>
                    <div class="space-y-8">
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Post Mortem / Bedah Siasat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('pm_permohonan', 'status_permohonan_laporan_post_mortem_mayat', 'tarikh_permohonan_laporan_post_mortem_mayat', $paper->status_permohonan_laporan_post_mortem_mayat, $paper->tarikh_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh Diterima</label>
                                    {!! render_status_with_date_radio('pm_penuh', 'status_laporan_penuh_bedah_siasat', 'tarikh_laporan_penuh_bedah_siasat', $paper->status_laporan_penuh_bedah_siasat, $paper->tarikh_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Jabatan Kimia</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('kimia_permohonan', 'status_permohonan_laporan_jabatan_kimia', 'tarikh_permohonan_laporan_jabatan_kimia', $paper->status_permohonan_laporan_jabatan_kimia, $paper->tarikh_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh Diterima</label>
                                    {!! render_status_with_date_radio('kimia_penuh', 'status_laporan_penuh_jabatan_kimia', 'tarikh_laporan_penuh_jabatan_kimia', $paper->status_laporan_penuh_jabatan_kimia, $paper->tarikh_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Jabatan Patalogi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('patalogi_permohonan', 'status_permohonan_laporan_jabatan_patalogi', 'tarikh_permohonan_laporan_jabatan_patalogi', $paper->status_permohonan_laporan_jabatan_patalogi, $paper->tarikh_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh Diterima</label>
                                    {!! render_status_with_date_radio('patalogi_penuh', 'status_laporan_penuh_jabatan_patalogi', 'tarikh_laporan_penuh_jabatan_patalogi', $paper->status_laporan_penuh_jabatan_patalogi, $paper->tarikh_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Imigresen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('imigresen_permohonan', 'status_permohonan_laporan_imigresen', 'tarikh_permohonan_laporan_imigresen', $paper->status_permohonan_laporan_imigresen, $paper->tarikh_permohonan_laporan_imigresen, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh Diterima</label>
                                    {!! render_status_with_date_radio('imigresen_penuh', 'status_laporan_penuh_imigresen', 'tarikh_laporan_penuh_imigresen', $paper->status_laporan_penuh_imigresen, $paper->tarikh_laporan_penuh_imigresen, 'Diterima', 'Tidak') !!}
                                </div>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700">M/S 4 - Barang Kes Ditulis Bersama No Daftar</label>
                            {!! render_boolean_radio('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar', $paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">M/S 4 - Barang Kes Ditulis Bersama No Daftar & Arahan TPR</label>
                            {!! render_boolean_radio('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr', $paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">M/S 4 - Keputusan Kes Dicatat</label>
                            {!! render_boolean_radio('adakah_muka_surat_4_keputusan_kes_dicatat', $paper->adakah_muka_surat_4_keputusan_kes_dicatat) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fail LMM(T) Telah Ada Keputusan</label>
                            {!! render_boolean_radio('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan', $paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">KS Telah di KUS/FAIL</label>
                            {!! render_boolean_radio('adakah_ks_kus_fail_selesai', $paper->adakah_ks_kus_fail_selesai) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keputusan Akhir Mahkamah</label>
                            <select name="keputusan_akhir_mahkamah" class="mt-1 block w-full form-select">
                                <option value="">-- Sila Pilih --</option>
                                <option value="Jatuh Hukum" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'Jatuh Hukum' ? 'selected' : '' }}>Jatuh Hukum</option>
                                <option value="NFA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'NFA' ? 'selected' : '' }}>NFA</option>
                                <option value="DNA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'DNA' ? 'selected' : '' }}>DNA</option>
                                <option value="DNAA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'DNAA' ? 'selected' : '' }}>DNAA</option>
                                <option value="KUS/SEMENTARA" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'KUS/SEMENTARA' ? 'selected' : '' }}>KUS/SEMENTARA</option>
                                <option value="MASIH DALAM SIASATAN OYDS GAGAL DIKESAN" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'MASIH DALAM SIASATAN OYDS GAGAL DIKESAN' ? 'selected' : '' }}>MASIH DALAM SIASATAN OYDS GAGAL DIKESAN</option>
                                <option value="MASIH DALAM SIASATAN UNTUK LENGKAPKAN DOKUMENTASI" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'MASIH DALAM SIASATAN UNTUK LENGKAPKAN DOKUMENTASI' ? 'selected' : '' }}>MASIH DALAM SIASATAN UNTUK LENGKAPKAN DOKUMENTASI</option>
                                <option value="TERBENGKALAI/ TIADA TINDAKAN" {{ old('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah) == 'TERBENGKALAI/ TIADA TINDAKAN' ? 'selected' : '' }}>TERBENGKALAI/ TIADA TINDAKAN</option>
                            </select>
                        </div>
                        <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_fail" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan (Fail)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_fail" id="ulasan_keseluruhan_pegawai_pemeriksa_fail" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_fail', $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Hidden Fields --}}

                {{-- Submit Button --}}
                <div class="flex justify-end pt-4 mt-6 border-t">
                    <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-3">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Kemaskini</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Status Pergerakan Barang Kes
            const statusPergerakanRadios = document.querySelectorAll('input[name="status_pergerakan_barang_kes"]');
            const statusPergerakanLainInput = document.getElementById('status_pergerakan_barang_kes_lain');
            
            statusPergerakanRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'Lain-lain') {
                        statusPergerakanLainInput.disabled = false;
                        statusPergerakanLainInput.focus();
                    } else {
                        statusPergerakanLainInput.disabled = true;
                        statusPergerakanLainInput.value = '';
                    }
                });
            });

            // Handle Status Barang Kes Selesai Siasatan
            const statusSelesaiRadios = document.querySelectorAll('input[name="status_barang_kes_selesai_siasatan"]');
            const statusSelesaiLainInput = document.getElementById('status_barang_kes_selesai_siasatan_lain');
            
            statusSelesaiRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'Lain-lain') {
                        statusSelesaiLainInput.disabled = false;
                        statusSelesaiLainInput.focus();
                    } else {
                        statusSelesaiLainInput.disabled = true;
                        statusSelesaiLainInput.value = '';
                    }
                });
            });

            // Handle Kaedah Pelupusan Barang Kes
            const kaedahPelupusanRadios = document.querySelectorAll('input[name="kaedah_pelupusan_barang_kes"]');
            const kaedahPelupusanLainInput = document.getElementById('kaedah_pelupusan_barang_kes_lain');
            
            kaedahPelupusanRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'Lain-lain') {
                        kaedahPelupusanLainInput.disabled = false;
                        kaedahPelupusanLainInput.focus();
                    } else {
                        kaedahPelupusanLainInput.disabled = true;
                        kaedahPelupusanLainInput.value = '';
                    }
                });
            });
        });
    </script>
</x-app-layout>