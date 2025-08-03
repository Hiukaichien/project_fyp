<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Jenayah ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'Jenayah', 'id' => $paper->id]) }}" class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
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

                <!-- BAHAGIAN 1 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 1</h3>
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
                             <select name="keputusan_siasatan_oleh_ya_tpr" id="keputusan_siasatan_oleh_ya_tpr" class="mt-1 block w-full form-select">
                                <option value="">-- Sila Pilih --</option>
                                <option value="Tuduh" {{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) == 'Tuduh' ? 'selected' : '' }}>Tuduh</option>
                                <option value="Tidak Tuduh" {{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) == 'Tidak Tuduh' ? 'selected' : '' }}>Tidak Tuduh</option>
                                <option value="Siasatan Lanjut" {{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) == 'Siasatan Lanjut' ? 'selected' : '' }}>Siasatan Lanjut</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Adakah Arahan Tuduh Oleh YA TPR Diambil Tindakan</label>
                            <div class="mt-2 space-y-2">
                                @php
                                    $currentArahan = old('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', $paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan);
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

                <!-- BAHAGIAN 4 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 4</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Barang Kes Didaftarkan</label>
                            {!! render_boolean_radio('adakah_barang_kes_didaftarkan', $paper->adakah_barang_kes_didaftarkan, 'Ya', 'Tidak') !!}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="no_daftar_barang_kes_am" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Am</label>
                                <input type="text" name="no_daftar_barang_kes_am" id="no_daftar_barang_kes_am" value="{{ old('no_daftar_barang_kes_am', $paper->no_daftar_barang_kes_am) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_barang_kes_berharga" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Berharga</label>
                                <input type="text" name="no_daftar_barang_kes_berharga" id="no_daftar_barang_kes_berharga" value="{{ old('no_daftar_barang_kes_berharga', $paper->no_daftar_barang_kes_berharga) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_barang_kes_kenderaan" class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes Kenderaan</label>
                                <input type="text" name="no_daftar_barang_kes_kenderaan" id="no_daftar_barang_kes_kenderaan" value="{{ old('no_daftar_barang_kes_kenderaan', $paper->no_daftar_barang_kes_kenderaan) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_botol_spesimen_urin" class="block text-sm font-medium text-gray-700">No. Daftar Botol Spesimen Urin</label>
                                <input type="text" name="no_daftar_botol_spesimen_urin" id="no_daftar_botol_spesimen_urin" value="{{ old('no_daftar_botol_spesimen_urin', $paper->no_daftar_botol_spesimen_urin) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_spesimen_darah" class="block text-sm font-medium text-gray-700">No. Daftar Spesimen Darah</label>
                                <input type="text" name="no_daftar_spesimen_darah" id="no_daftar_spesimen_darah" value="{{ old('no_daftar_spesimen_darah', $paper->no_daftar_spesimen_darah) }}" class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_kontraban" class="block text-sm font-medium text-gray-700">No. Daftar Kontraban</label>
                                <input type="text" name="no_daftar_kontraban" id="no_daftar_kontraban" value="{{ old('no_daftar_kontraban', $paper->no_daftar_kontraban) }}" class="mt-1 block w-full form-input">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="jenis_barang_kes_am" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Am</label>
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
                            <div>
                                <label for="jenis_barang_kes_kontraban" class="block text-sm font-medium text-gray-700">Jenis Barang Kes Kontraban</label>
                                <input type="text" name="jenis_barang_kes_kontraban" id="jenis_barang_kes_kontraban" value="{{ old('jenis_barang_kes_kontraban', $paper->jenis_barang_kes_kontraban) }}" class="mt-1 block w-full form-input">
                            </div>
                        </div>

                        <!-- Status Pergerakan Barang Kes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pergerakan Barang Kes</label>
                            <div class="space-y-2 pl-4">
                                @php $currentPergerakan = old('status_pergerakan_barang_kes', $paper->status_pergerakan_barang_kes); @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Simpanan Stor Ekshibit" {{ $currentPergerakan == 'Simpanan Stor Ekshibit' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Simpanan Stor Ekshibit</span>
                                </label>

                                <div class="flex items-start">
                                    <label class="flex items-center mt-2">
                                        <input type="radio" name="status_pergerakan_barang_kes" value="Ujian Makmal" id="radio_pergerakan_makmal_jenayah" {{ $currentPergerakan == 'Ujian Makmal' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Ujian Makmal (Nyatakan):</span>
                                    </label>
                                    <input type="text" name="status_pergerakan_barang_kes_makmal" id="pergerakan_makmal_jenayah" value="{{ old('status_pergerakan_barang_kes_makmal', $paper->status_pergerakan_barang_kes_makmal) }}" class="ml-2 form-input text-sm w-64">
                                </div>
                                
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Di Mahkamah" {{ $currentPergerakan == 'Di Mahkamah' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Di Mahkamah</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Pada IO/AIO" {{ $currentPergerakan == 'Pada IO/AIO' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Pada IO/AIO</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="status_pergerakan_barang_kes" value="Lain-Lain" id="pergerakan_lain_jenayah" {{ $currentPergerakan == 'Lain-Lain' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Lain-lain:</span>
                                    </label>
                                    <input type="text" name="status_pergerakan_barang_kes_lain" id="status_pergerakan_barang_kes_lain_jenayah" value="{{ old('status_pergerakan_barang_kes_lain', $paper->status_pergerakan_barang_kes_lain) }}" class="ml-2 form-input text-sm w-64">
                                </div>
                            </div>
                        </div>

                        <!-- Status Barang Kes Selesai Siasatan -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Barang Kes Selesai Siasatan</label>
                            <div class="space-y-2 pl-4">
                                @php $currentSelesai = old('status_barang_kes_selesai_siasatan', $paper->status_barang_kes_selesai_siasatan); @endphp
                                
                                <div class="flex items-start">
                                    <label class="flex items-center mt-2">
                                        <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dilupuskan ke Perbendaharaan" id="radio_selesai_siasatan_RM_jenayah" {{ $currentSelesai == 'Dilupuskan ke Perbendaharaan' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Dilupuskan ke Perbendaharaan</span>
                                    </label>
                                    <input type="text" name="status_barang_kes_selesai_siasatan_RM" id="selesai_siasatan_RM_jenayah" value="{{ old('status_barang_kes_selesai_siasatan_RM', $paper->status_barang_kes_selesai_siasatan_RM) }}" class="ml-2 form-input text-sm w-64" placeholder="RM">
                                </div>

                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dikembalikan Kepada Pemilik" {{ $currentSelesai == 'Dikembalikan Kepada Pemilik' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dikembalikan Kepada Pemilik</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dilupuskan" {{ $currentSelesai == 'Dilupuskan' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilupuskan</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="status_barang_kes_selesai_siasatan" value="Lain-Lain" id="selesai_lain_jenayah" {{ $currentSelesai == 'Lain-Lain' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Lain-lain:</span>
                                    </label>
                                    <input type="text" name="status_barang_kes_selesai_siasatan_lain" id="status_barang_kes_selesai_siasatan_lain_jenayah" value="{{ old('status_barang_kes_selesai_siasatan_lain', $paper->status_barang_kes_selesai_siasatan_lain) }}" class="ml-2 form-input text-sm w-64">
                                </div>
                            </div>
                        </div>

                        <!-- Kaedah Pelupusan Barang Kes -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sekiranya Barang Kes Dilupuskan, Bagaimana Kaedah Pelupusan Dilaksanakan</label>
                            <div class="space-y-2 pl-4">
                                @php $currentKaedah = old('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', $paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan); @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan" value="Dibakar" {{ $currentKaedah == 'Dibakar' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dibakar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan" value="Ditanam" {{ $currentKaedah == 'Ditanam' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ditanam</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan" value="Dihancurkan" {{ $currentKaedah == 'Dihancurkan' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dihancurkan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan" value="Dilelong" {{ $currentKaedah == 'Dilelong' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilelong</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan" value="Lain-Lain" id="kaedah_lain_jenayah" {{ $currentKaedah == 'Lain-Lain' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Lain-lain</span>
                                    </label>
                                    <input type="text" name="kaedah_pelupusan_barang_kes_lain" id="kaedah_pelupusan_barang_kes_lain_jenayah" value="{{ old('kaedah_pelupusan_barang_kes_lain', $paper->kaedah_pelupusan_barang_kes_lain) }}" class="ml-2 form-input text-sm w-64">
                                </div>
                            </div>
                        </div>

                        <!-- Arahan Pelupusan Barang Kes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adakah Pelupusan Barang Kes Itu Telah Ada Arahan Mahkamah Atau YA TPR</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentArahanPelupusan = old('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', $paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan);
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan" value="Ya" 
                                        {{ $currentArahanPelupusan == 'Ya' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ya (Arahan Mahkamah/TPR)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan" value="Tidak" 
                                        {{ $currentArahanPelupusan == 'Tidak' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan" value="Inisiatif IO/AIO Sendiri" 
                                        {{ $currentArahanPelupusan == 'Inisiatif IO/AIO Sendiri' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Inisiatif IO/AIO Sendiri</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Resit Kew.38e -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resit Kew.38e Bagi Pelupusan Barang Kes Wang Tunai Ke Perbendaharaan</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentResit = old('resit_kew38e_pelupusan_wang_tunai', $paper->resit_kew38e_pelupusan_wang_tunai);
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="resit_kew38e_pelupusan_wang_tunai" value="Ada Dilampirkan" 
                                        {{ $currentResit == 'Ada Dilampirkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ada Dilampirkan</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="resit_kew38e_pelupusan_wang_tunai" value="Tidak Dilampirkan" 
                                        {{ $currentResit == 'Tidak Dilampirkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak Dilampirkan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Borang Serah/Terima Barang Kes Antara Pegawai Tangkapan dan IO/AIO -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adakah Borang Serah/Terima Barang Kes Antara Pegawai Tangkapan dan IO/AIO Dilampirkan</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentBorangTangkapan = old('adakah_borang_serah_terima_pegawai_tangkapan', $paper->adakah_borang_serah_terima_pegawai_tangkapan);
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_borang_serah_terima_pegawai_tangkapan" value="Ada Dilampirkan" 
                                        {{ $currentBorangTangkapan == 'Ada Dilampirkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ada Dilampirkan</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_borang_serah_terima_pegawai_tangkapan" value="Tidak Dilampirkan" 
                                        {{ $currentBorangTangkapan == 'Tidak Dilampirkan' ? 'checked' : '' }} 
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak Dilampirkan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Borang Serah/Terima Barang Kes Antara Pegawai Penyiasat, Pemilik dan Saksi Pegawai Kanan Polis Dilampirkan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Borang Serah/Terima Barang Kes Antara Pegawai Penyiasat, Pemilik dan Saksi Pegawai Kanan Polis Dilampirkan</label>
                            {!! render_boolean_radio('adakah_borang_serah_terima_pemilik_saksi', $paper->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>
                        
                        <!-- Adakah Sijil Atau Surat Arahan Kebenaran Oleh IPD Bagi Melaksanakan Pelupusan Barang Kes Dilampirkan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Sijil Atau Surat Arahan Kebenaran Oleh IPD Bagi Melaksanakan Pelupusan Barang Kes Dilampirkan</label>
                            {!! render_boolean_radio('adakah_sijil_surat_kebenaran_ipo', $paper->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>
                        
                        <!-- Adakah Gambar Pelupusan Barang Kes Dilampirkan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Gambar Pelupusan Barang Kes Dilampirkan</label>
                            {!! render_boolean_radio('adakah_gambar_pelupusan', $paper->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>
                        <div class="md:col-span-3">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Barang Kes)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" id="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes', $paper->ulasan_keseluruhan_pegawai_pemeriksa_barang_kes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 5 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 5</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Post-Mortem Mayat Di Hospital</label>
                            {!! render_boolean_radio('status_gambar_post_mortem_mayat_di_hospital', $paper->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Am</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_am', $paper->status_gambar_barang_kes_am, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Berharga</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_berharga', $paper->status_gambar_barang_kes_berharga, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Kenderaan</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_kenderaan', $paper->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Darah</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_darah', $paper->status_gambar_barang_kes_darah, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Kontraban</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_kontraban', $paper->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada') !!}
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 6 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 6</h3>
                    <div class="space-y-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PEM 1/ PEM 2/ PEM 3/ PEM 4</label>
                            @php
                                $pem_options = [ 'PEM 1' => 'PEM 1', 'PEM 2' => 'PEM 2', 'PEM 3' => 'PEM 3', 'PEM 4' => 'PEM 4' ];
                            @endphp
                            {!! render_json_checkboxes('status_pem', $paper->status_pem, $pem_options) !!}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div><label class="block text-sm font-medium text-gray-700">RJ 2</label>{!! render_status_with_date_radio('rj2', 'status_rj2', 'tarikh_rj2', $paper->status_rj2, $paper->tarikh_rj2) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 2B</label>{!! render_status_with_date_radio('rj2b', 'status_rj2b', 'tarikh_rj2b', $paper->status_rj2b, $paper->tarikh_rj2b) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 9</label>{!! render_status_with_date_radio('rj9', 'status_rj9', 'tarikh_rj9', $paper->status_rj9, $paper->tarikh_rj9) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 99</label>{!! render_status_with_date_radio('rj99', 'status_rj99', 'tarikh_rj99', $paper->status_rj99, $paper->tarikh_rj99) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 10A</label>{!! render_status_with_date_radio('rj10a', 'status_rj10a', 'tarikh_rj10a', $paper->status_rj10a, $paper->tarikh_rj10a) !!}</div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ 10B</label>{!! render_status_with_date_radio('rj10b', 'status_rj10b', 'tarikh_rj10b', $paper->status_rj10b, $paper->tarikh_rj10b) !!}</div>
                        </div>

                        <div>
                            <label for="lain_lain_rj_dikesan" class="block text-sm font-medium text-gray-700">Lain-lain RJ Dikesan</label>
                            <input type="text" name="lain_lain_rj_dikesan" id="lain_lain_rj_dikesan" value="{{ old('lain_lain_rj_dikesan', $paper->lain_lain_rj_dikesan) }}" class="mt-1 block w-full form-input">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Pertama Wanted Person</label>
                                {!! render_status_with_date_radio('wp1', 'status_semboyan_pertama_wanted_person', 'tarikh_semboyan_pertama_wanted_person', $paper->status_semboyan_pertama_wanted_person, $paper->tarikh_semboyan_pertama_wanted_person) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Kedua Wanted Person</label>
                                {!! render_status_with_date_radio('wp2', 'status_semboyan_kedua_wanted_person', 'tarikh_semboyan_kedua_wanted_person', $paper->status_semboyan_kedua_wanted_person, $paper->tarikh_semboyan_kedua_wanted_person) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Ketiga Wanted Person</label>
                                {!! render_status_with_date_radio('wp3', 'status_semboyan_ketiga_wanted_person', 'tarikh_semboyan_ketiga_wanted_person', $paper->status_semboyan_ketiga_wanted_person, $paper->tarikh_semboyan_ketiga_wanted_person) !!}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Penandaan Kelas Warna Pada Kulit Kertas Siasatan Dibuat</label>
                            {!! render_boolean_radio('status_penandaan_kelas_warna', $paper->status_penandaan_kelas_warna, 'Ya', 'Tidak') !!}
                        </div>

                        <div>
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_borang" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa (Borang & Semakan)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_borang" id="ulasan_keseluruhan_pegawai_pemeriksa_borang" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_borang', $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 7 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7</h3>
                    <div class="space-y-8">

                        <!-- Pakar Judi -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Pakar Judi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('pakar_judi_permohonan', 'status_permohonan_laporan_pakar_judi', 'tarikh_permohonan_laporan_pakar_judi', $paper->status_permohonan_laporan_pakar_judi, $paper->tarikh_permohonan_laporan_pakar_judi, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('pakar_judi_penuh', 'status_laporan_penuh_pakar_judi', 'tarikh_laporan_penuh_pakar_judi', $paper->status_laporan_penuh_pakar_judi, $paper->tarikh_laporan_penuh_pakar_judi, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Permohonan Laporan Post Mortem Mayat -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Permohonan Laporan Post Mortem Mayat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status Permohonan</label>
                                    {!! render_status_with_date_radio('post_mortem_mayat_permohonan', 'status_permohonan_laporan_post_mortem_mayat', 'tarikh_permohonan_laporan_post_mortem_mayat', $paper->status_permohonan_laporan_post_mortem_mayat, $paper->tarikh_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Bedah Siasat -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Bedah Siasat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('bedah_siasat_penuh', 'status_laporan_penuh_bedah_siasat', 'tarikh_laporan_penuh_bedah_siasat', $paper->status_laporan_penuh_bedah_siasat, $paper->tarikh_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Jabatan Kimia -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Jabatan Kimia</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('jabatan_kimia_permohonan', 'status_permohonan_laporan_jabatan_kimia', 'tarikh_permohonan_laporan_jabatan_kimia', $paper->status_permohonan_laporan_jabatan_kimia, $paper->tarikh_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('jabatan_kimia_penuh', 'status_laporan_penuh_jabatan_kimia', 'tarikh_laporan_penuh_jabatan_kimia', $paper->status_laporan_penuh_jabatan_kimia, $paper->tarikh_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak') !!}
                                </div>
                                <div class="col-span-full">
                                    <label for="keputusan_laporan_jabatan_kimia" class="block text-sm font-medium text-gray-700">Keputusan Laporan</label>
                                    <input type="text" name="keputusan_laporan_jabatan_kimia" id="keputusan_laporan_jabatan_kimia" value="{{ old('keputusan_laporan_jabatan_kimia', $paper->keputusan_laporan_jabatan_kimia) }}" class="mt-1 block w-full form-input">
                                </div>
                            </div>
                        </div>

                        <!-- Jabatan Patalogi -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Jabatan Patalogi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('jabatan_patalogi_permohonan', 'status_permohonan_laporan_jabatan_patalogi', 'tarikh_permohonan_laporan_jabatan_patalogi', $paper->status_permohonan_laporan_jabatan_patalogi, $paper->tarikh_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('jabatan_patalogi_penuh', 'status_laporan_penuh_jabatan_patalogi', 'tarikh_laporan_penuh_jabatan_patalogi', $paper->status_laporan_penuh_jabatan_patalogi, $paper->tarikh_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak') !!}
                                </div>
                                <div class="col-span-full">
                                    <label for="keputusan_laporan_jabatan_patalogi" class="block text-sm font-medium text-gray-700">Keputusan Laporan</label>
                                    <input type="text" name="keputusan_laporan_jabatan_patalogi" id="keputusan_laporan_jabatan_patalogi" value="{{ old('keputusan_laporan_jabatan_patalogi', $paper->keputusan_laporan_jabatan_patalogi) }}" class="mt-1 block w-full form-input">
                                </div>
                            </div>
                        </div>

                        <!-- PUSPAKOM -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">PUSPAKOM</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('puspakom_permohonan', 'status_permohonan_laporan_puspakom', 'tarikh_permohonan_laporan_puspakom', $paper->status_permohonan_laporan_puspakom, $paper->tarikh_permohonan_laporan_puspakom, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('puspakom_penuh', 'status_laporan_penuh_puspakom', 'tarikh_laporan_penuh_puspakom', $paper->status_laporan_penuh_puspakom, $paper->tarikh_laporan_penuh_puspakom, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>
                            
                        <!-- JPJ -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">JPJ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('jpj_permohonan', 'status_permohonan_laporan_jpj', 'tarikh_permohonan_laporan_jpj', $paper->status_permohonan_laporan_jpj, $paper->tarikh_permohonan_laporan_jpj, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('jpj_penuh', 'status_laporan_penuh_jpj', 'tarikh_laporan_penuh_jpj', $paper->status_laporan_penuh_jpj, $paper->tarikh_laporan_penuh_jpj, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Imigresen -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Imigresen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('imigresen_permohonan', 'status_permohonan_laporan_imigresen', 'tarikh_permohonan_laporan_imigresen', $paper->status_permohonan_laporan_imigresen, $paper->tarikh_permohonan_laporan_imigresen, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('imigresen_penuh', 'status_laporan_penuh_imigresen', 'tarikh_laporan_penuh_imigresen', $paper->status_laporan_penuh_imigresen, $paper->tarikh_laporan_penuh_imigresen, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Kastam -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Kastam</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('kastam_permohonan', 'status_permohonan_laporan_kastam', 'tarikh_permohonan_laporan_kastam', $paper->status_permohonan_laporan_kastam, $paper->tarikh_permohonan_laporan_kastam, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('kastam_penuh', 'status_laporan_penuh_kastam', 'tarikh_laporan_penuh_kastam', $paper->status_laporan_penuh_kastam, $paper->tarikh_laporan_penuh_kastam, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Forensik PDRM -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Forensik PDRM</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan</label>
                                    {!! render_status_with_date_radio('forensik_pdrm_permohonan', 'status_permohonan_laporan_forensik_pdrm', 'tarikh_permohonan_laporan_forensik_pdrm', $paper->status_permohonan_laporan_forensik_pdrm, $paper->tarikh_permohonan_laporan_forensik_pdrm, 'Dibuat', 'Tidak') !!}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh</label>
                                    {!! render_status_with_date_radio('forensik_pdrm_penuh', 'status_laporan_penuh_forensik_pdrm', 'tarikh_laporan_penuh_forensik_pdrm', $paper->status_laporan_penuh_forensik_pdrm, $paper->tarikh_laporan_penuh_forensik_pdrm, 'Diterima', 'Tidak') !!}
                                </div>
                            </div>
                        </div>

                        <!-- Lain-lain Permohonan Laporan -->
                        <div class="p-4 border rounded-md">
                            <h4 class="font-semibold text-md text-gray-700">Lain-lain Permohonan Laporan</h4>
                            <div class="grid grid-cols-1 gap-6 mt-4">
                                <div>
                                    <label for="lain_lain_permohonan_laporan" class="block text-sm font-medium text-gray-700">Catatan</label>
                                    <textarea name="lain_lain_permohonan_laporan" id="lain_lain_permohonan_laporan" rows="3" class="mt-1 block w-full form-textarea">{{ old('lain_lain_permohonan_laporan', $paper->lain_lain_permohonan_laporan) }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div> 
                </div> 

                <!-- BAHAGIAN 8 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 8</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Barang Kes Ditulis Bersama No Daftar Barang Kes</label>
                                {!! render_boolean_radio('muka_surat_4_barang_kes_ditulis', $paper->muka_surat_4_barang_kes_ditulis, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Barang Kes Ditulis Bersama No Daftar Dan Telah Ada Arahan YA TPR Untuk Pelupusan/Serahan Semula</label>
                                {!! render_boolean_radio('muka_surat_4_dengan_arahan_tpr', $paper->muka_surat_4_dengan_arahan_tpr, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Keputusan Kes Dicatat Selengkapnya</label>
                                {!! render_boolean_radio('muka_surat_4_keputusan_kes_dicatat', $paper->muka_surat_4_keputusan_kes_dicatat, 'Ya', 'Tidak') !!}
                            </div>
                                <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Fail L.M.M (T) Atau L.M.M Telah Ada Keputusan Siasatan Oleh YA Koroner</label>
                                {!! render_boolean_radio('fail_lmm_ada_keputusan_koroner', $paper->fail_lmm_ada_keputusan_koroner, 'Ya', 'Tidak') !!}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Kertas Siasatan Telah Di KUS/FAIL Bagi Siasatan Yang Telah Selesai Dan Ada Keputusan Mahkamah</label>
                            {!! render_boolean_radio('status_kus_fail', $paper->status_kus_fail, 'Ya', 'Tidak') !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keputusan Akhir Oleh Mahkamah Sebelum Kertas Siasatan Di KUS/FAIL Atau Disimpan</label>
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
        /* General form element styling for consistency */
        .form-input, .form-select, .form-textarea {
            @apply rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
        }
        .form-radio, .form-checkbox {
            @apply rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500;
        }
    </style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /**
     * A generic helper function to manage the state (enabled/disabled) of a text input
     * based on the selection of its corresponding radio button.
     * The text input's state is toggled based on whether its associated radio button is checked.
     *
     * @param {string} radioGroupName - The 'name' attribute of the radio button group.
     * @param {string} triggerRadioId - The 'id' attribute of the specific radio button that controls the input.
     * @param {string} targetInputId - The 'id' attribute of the text input to enable/disable.
     */
    function setupSpecialInputToggle(radioGroupName, triggerRadioId, targetInputId) {
        const radios = document.querySelectorAll(`input[name="${radioGroupName}"]`);
        const targetInput = document.getElementById(targetInputId);

        if (!radios.length || !targetInput) {
            console.warn(`Toggle setup skipped: Elements not found for group ${radioGroupName} or target ${targetInputId}`);
            return;
        }

        const updateState = () => {
            const selectedRadio = document.querySelector(`input[name="${radioGroupName}"]:checked`);
            
            // Enable the input only if a radio is selected and its ID matches the trigger ID.
            if (selectedRadio && selectedRadio.id === triggerRadioId) {
                targetInput.disabled = false;
            } else {
                // Otherwise, disable it and clear its value to prevent submission of stale data.
                targetInput.disabled = true;
                targetInput.value = '';
            }
        };

        radios.forEach(radio => {
            radio.addEventListener('change', updateState);
        });

        // Set the initial state on page load.
        updateState();
    }

    // --- Initialize All Toggles for the Jenayah Form ---

    // Section 4: Barang Kes
    // Handles the "Ujian Makmal" input for Status Pergerakan
    setupSpecialInputToggle(
        'status_pergerakan_barang_kes',
        'radio_pergerakan_makmal_jenayah',
        'pergerakan_makmal_jenayah'
    );

    // Handles the "Lain-Lain" input for Status Pergerakan
    setupSpecialInputToggle(
        'status_pergerakan_barang_kes',
        'pergerakan_lain_jenayah',
        'status_pergerakan_barang_kes_lain_jenayah'
    );

    // Handles the "Dilupuskan ke Perbendaharaan (RM)" input for Status Selesai Siasatan
    setupSpecialInputToggle(
        'status_barang_kes_selesai_siasatan',
        'radio_selesai_siasatan_RM_jenayah',
        'selesai_siasatan_RM_jenayah'
    );

    // Handles the "Lain-Lain" input for Status Selesai Siasatan
    setupSpecialInputToggle(
        'status_barang_kes_selesai_siasatan',
        'selesai_lain_jenayah',
        'status_barang_kes_selesai_siasatan_lain_jenayah'
    );

    // Handles the "Lain-Lain" input for Kaedah Pelupusan
    setupSpecialInputToggle(
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
        'kaedah_lain_jenayah',
        'kaedah_pelupusan_barang_kes_lain_jenayah'
    );
});
</script>
@endpush
</x-app-layout>
