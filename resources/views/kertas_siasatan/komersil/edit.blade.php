<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Komersil ({{ $paper->no_kertas_siasatan }})
        </h2>
    </x-slot>

    <!-- Add meta tag for Malaysian locale -->
    @push('head')
        <meta name="locale" content="ms-MY">
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced date format conversion with visual cues and validation
            function setupDateFormatConversion() {
                const dateInputs = document.querySelectorAll('input[type="date"]');
                
                dateInputs.forEach(function(input) {
                    // Set Malaysian locale and data attributes
                    input.setAttribute('lang', 'ms-MY');
                    input.setAttribute('data-format', 'DD/MM/YYYY');
                    input.setAttribute('title', 'Format: DD/MM/YYYY (contoh: 30/05/2025)');
                    input.style.direction = 'ltr';
                    
                    // Add format validation and visual feedback
                    input.addEventListener('change', function() {
                        if (this.value) {
                            // Convert from YYYY-MM-DD to DD/MM/YYYY for display purposes
                            const dateValue = new Date(this.value);
                            if (!isNaN(dateValue.getTime())) {
                                // Add success indicator
                                this.style.borderColor = '#10B981';
                                this.style.boxShadow = '0 0 0 1px #10B981';
                            }
                        }
                    });
                    
                    // Add focus styling
                    input.addEventListener('focus', function() {
                        this.style.borderColor = '#6366F1';
                        this.style.boxShadow = '0 0 0 1px #6366F1';
                    });
                    
                    input.addEventListener('blur', function() {
                        if (!this.value) {
                            this.style.borderColor = '';
                            this.style.boxShadow = '';
                        }
                    });
                });
            }
            
            // Initialize date format conversion
            setupDateFormatConversion();
            
            // Set document locale
            document.documentElement.setAttribute('lang', 'ms-MY');
        });
    </script>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST"
                action="{{ route('kertas_siasatan.update', ['paperType' => 'Komersil', 'id' => $paper->id]) }}"
                class="space-y-10 bg-white p-8 shadow-lg rounded-lg">
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
                    function render_boolean_radio($name, $currentValue, $YaLabel = 'Ada / Ya', $TidakLabel = 'Tiada / Tidak')
                    {
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
                    function render_status_with_date_radio($id, $statusName, $dateName, $currentStatus, $currentDate, $YaLabel = 'Ada / Cipta', $TidakLabel = 'Tiada / Tidak Cipta')
                    {
                        $effectiveStatus = old($statusName, $currentStatus);
                        $initialStatusForAlpine = (($effectiveStatus === true || $effectiveStatus === 1 || $effectiveStatus === '1') ? '1' : '0');
                        $html = "<div x-data='{ status: \"{$initialStatusForAlpine}\" }'>";
                        $html .= "<div class='mt-2 flex items-center space-x-6'>";
                        $html .= "<label class='flex items-center cursor-pointer'><input type='radio' name='{$statusName}' value='1' x-model='status' class='form-radio h-4 w-4 text-indigo-600'><span class='ml-2 text-gray-700'>{$YaLabel}</span></label>";
                        $html .= "<label class='flex items-center cursor-pointer'><input type='radio' name='{$statusName}' value='0' x-model='status' class='form-radio h-4 w-4 text-indigo-600'><span class='ml-2 text-gray-700'>{$TidakLabel}</span></label>";
                        $html .= "</div>";
                        $html .= "<div x-show='status === \"1\"' x-transition class='mt-2'>";
                        $html .= "<label for='{$dateName}_{$id}' class='text-sm text-gray-600'>Jika Ada, nyatakan tarikh (DD/MM/YYYY):</label>";
                        $html .= "<input type='date' name='{$dateName}' id='{$dateName}_{$id}' value='" . old($dateName, optional($currentDate)->format('Y-m-d')) . "' class='mt-1 block w-full form-input'>";
                        $html .= "</div></div>";
                        return $html;
                    }

                    // Helper for JSON fields represented by checkboxes (for multi-select where still needed)
                    function render_json_checkboxes($name, $currentJson, $options)
                    {
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
                            <div class="mt-1 p-2 bg-gray-100 rounded-md font-mono">{{ $paper->no_kertas_siasatan }}
                            </div>
                        </div>
                        <div>
                            <label for="no_repot_polis" class="block text-sm font-medium text-gray-700">No. Repot
                                Polis</label>
                            <input type="text" name="no_repot_polis" id="no_repot_polis"
                                value="{{ old('no_repot_polis', $paper->no_repot_polis) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="pegawai_penyiasat" class="block text-sm font-medium text-gray-700">Pegawai
                                Penyiasat</label>
                            <input type="text" name="pegawai_penyiasat" id="pegawai_penyiasat"
                                value="{{ old('pegawai_penyiasat', $paper->pegawai_penyiasat) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_laporan_polis_dibuka"
                                class="block text-sm font-medium text-gray-700">Tarikh Laporan Polis Dibuka (DD/MM/YYYY)</label>
                            <input type="date" name="tarikh_laporan_polis_dibuka" id="tarikh_laporan_polis_dibuka"
                                value="{{ old('tarikh_laporan_polis_dibuka', optional($paper->tarikh_laporan_polis_dibuka)->format('Y-m-d')) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="seksyen" class="block text-sm font-medium text-gray-700">Seksyen</label>
                            <input type="text" name="seksyen" id="seksyen" value="{{ old('seksyen', $paper->seksyen) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                    </div>
                </div>

                <!-- BAHAGIAN 2 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 2</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Pegawai
                                Pemeriksa</label>
                            <input type="text" name="pegawai_pemeriksa" id="pegawai_pemeriksa"
                                value="{{ old('pegawai_pemeriksa', $paper->pegawai_pemeriksa) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_pertama"
                                class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Pertama
                                (A) - (DD/MM/YYYY)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_pertama" id="tarikh_edaran_minit_ks_pertama"
                                value="{{ old('tarikh_edaran_minit_ks_pertama', optional($paper->tarikh_edaran_minit_ks_pertama)->format('Y-m-d')) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_kedua"
                                class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Kedua (B) - (DD/MM/YYYY)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_kedua" id="tarikh_edaran_minit_ks_kedua"
                                value="{{ old('tarikh_edaran_minit_ks_kedua', optional($paper->tarikh_edaran_minit_ks_kedua)->format('Y-m-d')) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_sebelum_akhir"
                                class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Sebelum Minit
                                Akhir (C) - (DD/MM/YYYY)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_sebelum_akhir"
                                id="tarikh_edaran_minit_ks_sebelum_akhir"
                                value="{{ old('tarikh_edaran_minit_ks_sebelum_akhir', optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('Y-m-d')) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_edaran_minit_ks_akhir"
                                class="block text-sm font-medium text-gray-700">Tarikh Edaran Minit KS Akhir (D) - (DD/MM/YYYY)</label>
                            <input type="date" name="tarikh_edaran_minit_ks_akhir" id="tarikh_edaran_minit_ks_akhir"
                                value="{{ old('tarikh_edaran_minit_ks_akhir', optional($paper->tarikh_edaran_minit_ks_akhir)->format('Y-m-d')) }}"
                                class="mt-1 block w-full form-input">
                        </div>
                        <div>
                            <label for="tarikh_semboyan_pemeriksaan_jips_ke_daerah"
                                class="block text-sm font-medium text-gray-700">Tarikh Semboyan Pemeriksaan JIPS ke
                                Daerah (E) - (DD/MM/YYYY)</label>
                            <input type="date" name="tarikh_semboyan_pemeriksaan_jips_ke_daerah"
                                id="tarikh_semboyan_pemeriksaan_jips_ke_daerah"
                                value="{{ old('tarikh_semboyan_pemeriksaan_jips_ke_daerah', optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('Y-m-d')) }}"
                                class="mt-1 block w-full form-input">
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
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh Ketua
                                Bahagian</label>
                            {!! render_status_with_date_radio('kb', 'arahan_minit_ketua_bahagian_status', 'arahan_minit_ketua_bahagian_tarikh', $paper->arahan_minit_ketua_bahagian_status, $paper->arahan_minit_ketua_bahagian_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh Ketua
                                Jabatan</label>
                            {!! render_status_with_date_radio('kj', 'arahan_minit_ketua_jabatan_status', 'arahan_minit_ketua_jabatan_tarikh', $paper->arahan_minit_ketua_jabatan_status, $paper->arahan_minit_ketua_jabatan_tarikh) !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arahan Minit Oleh YA TPR</label>
                            {!! render_status_with_date_radio('tpr', 'arahan_minit_oleh_ya_tpr_status', 'arahan_minit_oleh_ya_tpr_tarikh', $paper->arahan_minit_oleh_ya_tpr_status, $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}
                        </div>

                        <div class="md:col-span-2">
                            <label for="keputusan_siasatan_oleh_ya_tpr"
                                class="block text-sm font-medium text-gray-700">Keputusan Siasatan Oleh YA TPR</label>
                            <input type="text" name="keputusan_siasatan_oleh_ya_tpr" id="keputusan_siasatan_oleh_ya_tpr"
                                value="{{ old('keputusan_siasatan_oleh_ya_tpr', $paper->keputusan_siasatan_oleh_ya_tpr) }}"
                                class="mt-1 block w-full form-input">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Adakah Arahan Tuduh Oleh YA TPR
                                Diambil Tindakan</label>
                            <div class="mt-2 space-y-2">
                                @php
                                    $currentArahan = old('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', $paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan);
                                    // If it's an array (from old checkbox data), get the first value
                                    if (is_array($currentArahan)) {
                                        $currentArahan = !empty($currentArahan) ? $currentArahan[0] : '';
                                    }
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan"
                                        value="Ya" {{ $currentArahan == 'Ya' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan"
                                        value="Tidak" {{ $currentArahan == 'Tidak' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tidak</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan"
                                        value="Tiada Usaha Oleh IO/AIO" {{ $currentArahan == 'Tiada Usaha Oleh IO/AIO' ? 'checked' : '' }} class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada Usaha Oleh IO/AIO</span>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="ulasan_keputusan_siasatan_tpr"
                                class="block text-sm font-medium text-gray-700">Ulasan Keputusan Siasatan TPR</label>
                            <textarea name="ulasan_keputusan_siasatan_tpr" id="ulasan_keputusan_siasatan_tpr" rows="3"
                                class="mt-1 block w-full form-textarea">{{ old('ulasan_keputusan_siasatan_tpr', $paper->ulasan_keputusan_siasatan_tpr) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa"
                                class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa
                                (Jika Ada)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa"
                                id="ulasan_keseluruhan_pegawai_pemeriksa" rows="4"
                                class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa', $paper->ulasan_keseluruhan_pegawai_pemeriksa) }}</textarea>
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
                                <label for="no_daftar_barang_kes_am" class="block text-sm font-medium text-gray-700">No.
                                    Daftar Barang Kes Am</label>
                                <input type="text" name="no_daftar_barang_kes_am" id="no_daftar_barang_kes_am"
                                    value="{{ old('no_daftar_barang_kes_am', $paper->no_daftar_barang_kes_am) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_barang_kes_berharga"
                                    class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes
                                    Berharga</label>
                                <input type="text" name="no_daftar_barang_kes_berharga"
                                    id="no_daftar_barang_kes_berharga"
                                    value="{{ old('no_daftar_barang_kes_berharga', $paper->no_daftar_barang_kes_berharga) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_barang_kes_kenderaan"
                                    class="block text-sm font-medium text-gray-700">No. Daftar Barang Kes
                                    Kenderaan</label>
                                <input type="text" name="no_daftar_barang_kes_kenderaan"
                                    id="no_daftar_barang_kes_kenderaan"
                                    value="{{ old('no_daftar_barang_kes_kenderaan', $paper->no_daftar_barang_kes_kenderaan) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="no_daftar_botol_spesimen_urin"
                                    class="block text-sm font-medium text-gray-700">No. Daftar Botol Spesimen
                                    Urin</label>
                                <input type="text" name="no_daftar_botol_spesimen_urin"
                                    id="no_daftar_botol_spesimen_urin"
                                    value="{{ old('no_daftar_botol_spesimen_urin', $paper->no_daftar_botol_spesimen_urin) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="jenis_barang_kes_am" class="block text-sm font-medium text-gray-700">Jenis
                                    Barang Kes Am</label>
                                <input type="text" name="jenis_barang_kes_am" id="jenis_barang_kes_am"
                                    value="{{ old('jenis_barang_kes_am', $paper->jenis_barang_kes_am) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="jenis_barang_kes_berharga"
                                    class="block text-sm font-medium text-gray-700">Jenis Barang Kes Berharga</label>
                                <input type="text" name="jenis_barang_kes_berharga" id="jenis_barang_kes_berharga"
                                    value="{{ old('jenis_barang_kes_berharga', $paper->jenis_barang_kes_berharga) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                            <div>
                                <label for="jenis_barang_kes_kenderaan"
                                    class="block text-sm font-medium text-gray-700">Jenis Barang Kes Kenderaan</label>
                                <input type="text" name="jenis_barang_kes_kenderaan" id="jenis_barang_kes_kenderaan"
                                    value="{{ old('jenis_barang_kes_kenderaan', $paper->jenis_barang_kes_kenderaan) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>

                        <!-- Status Pergerakan Barang Kes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pergerakan Barang
                                Kes</label>
                            <div class="space-y-2 pl-4">
                                @php $currentPergerakan = old('status_pergerakan_barang_kes', $paper->status_pergerakan_barang_kes); @endphp
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes"
                                        value="Simpanan Stor Ekshibit" {{ $currentPergerakan == 'Simpanan Stor Ekshibit' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Simpanan Stor Ekshibit</span>
                                </label>

                                <div class="flex items-start">
                                    <label class="flex items-center mt-2">
                                        <input type="radio" name="status_pergerakan_barang_kes" value="Ujian Makmal"
                                            id="radio_pergerakan_makmal_komersil" {{ $currentPergerakan == 'Ujian Makmal' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Ujian Makmal (Nyatakan):</span>
                                    </label>
                                    <input type="text" name="status_pergerakan_barang_kes_makmal"
                                        id="pergerakan_makmal_komersil"
                                        value="{{ old('status_pergerakan_barang_kes_makmal', $paper->status_pergerakan_barang_kes_makmal) }}"
                                        class="ml-2 form-input text-sm w-64" {{ $currentPergerakan != 'Ujian Makmal' ? 'disabled' : '' }}>
                                </div>

                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Di Mahkamah" {{ $currentPergerakan == 'Di Mahkamah' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Di Mahkamah</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_pergerakan_barang_kes" value="Pada IO/AIO" {{ $currentPergerakan == 'Pada IO/AIO' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Pada IO/AIO</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="status_pergerakan_barang_kes" value="Lain-Lain"
                                            id="pergerakan_lain_komersil" {{ $currentPergerakan == 'Lain-Lain' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Lain-lain:</span>
                                    </label>
                                    <input type="text" name="status_pergerakan_barang_kes_lain"
                                        id="status_pergerakan_barang_kes_lain_komersil"
                                        value="{{ old('status_pergerakan_barang_kes_lain', $paper->status_pergerakan_barang_kes_lain) }}"
                                        class="ml-2 form-input text-sm w-64" {{ $currentPergerakan != 'Lain-Lain' ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Status Barang Kes Selesai Siasatan -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Barang Kes Selesai
                                Siasatan</label>
                            <div class="space-y-2 pl-4">
                                @php $currentSelesai = old('status_barang_kes_selesai_siasatan', $paper->status_barang_kes_selesai_siasatan); @endphp
                                <div class="flex items-start">
                                    <label class="flex items-center mt-2">
                                        <input type="radio" name="status_barang_kes_selesai_siasatan"
                                            value="Dilupuskan ke Perbendaharaan" id="radio_selesai_siasatan_RM_komersil"
                                            {{ $currentSelesai == 'Dilupuskan ke Perbendaharaan' ? 'checked' : '' }}
                                            class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Dilupuskan ke Perbendaharaan</span>
                                    </label>
                                    <input type="text" name="status_barang_kes_selesai_siasatan_RM"
                                        id="selesai_siasatan_RM_komersil"
                                        value="{{ old('status_barang_kes_selesai_siasatan_RM', $paper->status_barang_kes_selesai_siasatan_RM) }}"
                                        class="ml-2 form-input text-sm w-64" placeholder="RM" {{ $currentSelesai != 'Dilupuskan ke Perbendaharaan' ? 'disabled' : '' }}>
                                </div>

                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan"
                                        value="Dikembalikan Kepada Pemilik" {{ $currentSelesai == 'Dikembalikan Kepada Pemilik' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dikembalikan Kepada Pemilik</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status_barang_kes_selesai_siasatan" value="Dilupuskan" {{ $currentSelesai == 'Dilupuskan' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilupuskan</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio" name="status_barang_kes_selesai_siasatan" value="Lain-Lain"
                                            id="selesai_lain_komersil" {{ $currentSelesai == 'Lain-Lain' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                        <span class="ml-2 text-gray-700">Lain-lain:</span>
                                    </label>
                                    <input type="text" name="status_barang_kes_selesai_siasatan_lain"
                                        id="status_barang_kes_selesai_siasatan_lain_komersil"
                                        value="{{ old('status_barang_kes_selesai_siasatan_lain', $paper->status_barang_kes_selesai_siasatan_lain) }}"
                                        class="ml-2 form-input text-sm w-64" {{ $currentSelesai != 'Lain-Lain' ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Kaedah Pelupusan Barang Kes -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sekiranya Barang Kes Dilupuskan,
                                Bagaimana Kaedah Pelupusan Dilaksanakan</label>
                            <div class="space-y-2 pl-4">
                                @php
                                    $currentKaedah = old('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', is_array($paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan) ? ($paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan[0] ?? '') : $paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan);
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio"
                                        name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan"
                                        value="Dibakar" {{ $currentKaedah == 'Dibakar' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dibakar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                        name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan"
                                        value="Ditanam" {{ $currentKaedah == 'Ditanam' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ditanam</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                        name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan"
                                        value="Dihancurkan" {{ $currentKaedah == 'Dihancurkan' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dihancurkan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                        name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan"
                                        value="Dilelong" {{ $currentKaedah == 'Dilelong' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Dilelong</span>
                                </label>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="radio"
                                            name="barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan"
                                            value="Lain-Lain" {{ $currentKaedah == 'Lain-Lain' ? 'checked' : '' }}
                                            class="form-radio h-4 w-4 text-blue-600" id="kaedah_lain_komersil">
                                        <span class="ml-2 text-gray-700">Lain-lain</span>
                                    </label>
                                    <input type="text" name="kaedah_pelupusan_lain"
                                        id="kaedah_pelupusan_barang_kes_lain_komersil"
                                        value="{{ old('kaedah_pelupusan_lain', $paper->kaedah_pelupusan_lain) }}"
                                        class="ml-2 form-input text-sm w-64" {{ $currentKaedah != 'Lain-Lain' ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Arahan Pelupusan Barang Kes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adakah Pelupusan Barang Kes Itu
                                Telah Ada Arahan Mahkamah Atau YA TPR</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentArahanPelupusan = old('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', is_array($paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan) ? ($paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan[0] ?? '') : $paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan);
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan"
                                        value="Ya" {{ $currentArahanPelupusan == 'Ya' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ya (Arahan Mahkamah/TPR)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan"
                                        value="Tidak" {{ $currentArahanPelupusan == 'Tidak' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan"
                                        value="Inisiatif IO/AIO Sendiri" {{ $currentArahanPelupusan == 'Inisiatif IO/AIO Sendiri' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Inisiatif IO/AIO Sendiri</span>
                                </label>
                            </div>
                        </div>

                        <!-- Resit Kew.38e -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resit Kew.38e Bagi Pelupusan
                                Barang Kes Wang Tunai Ke Perbendaharaan</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentResit = old('resit_kew_38e_bagi_pelupusan', is_array($paper->resit_kew_38e_bagi_pelupusan) ? ($paper->resit_kew_38e_bagi_pelupusan[0] ?? '') : $paper->resit_kew_38e_bagi_pelupusan);
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="resit_kew_38e_bagi_pelupusan" value="Ada Dilampirkan" {{ $currentResit == 'Ada Dilampirkan' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ada Dilampirkan</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="resit_kew_38e_bagi_pelupusan" value="Tidak Dilampirkan" {{ $currentResit == 'Tidak Dilampirkan' ? 'checked' : '' }}
                                        class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak Dilampirkan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Borang Serah/Terima Barang Kes Antara Pegawai Tangkapan dan IO/AIO -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adakah Borang Serah/Terima
                                Barang Kes Antara Pegawai Tangkapan dan IO/AIO Dilampirkan</label>
                            <div class="flex flex-col space-y-2 pl-4">
                                @php
                                    $currentBorangTangkapan = old('adakah_borang_serah_terima_pegawai_tangkapan', is_array($paper->adakah_borang_serah_terima_pegawai_tangkapan) ? ($paper->adakah_borang_serah_terima_pegawai_tangkapan[0] ?? '') : $paper->adakah_borang_serah_terima_pegawai_tangkapan);
                                @endphp
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_borang_serah_terima_pegawai_tangkapan"
                                        value="Ada Dilampirkan" {{ $currentBorangTangkapan == 'Ada Dilampirkan' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Ada Dilampirkan</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="adakah_borang_serah_terima_pegawai_tangkapan"
                                        value="Tidak Dilampirkan" {{ $currentBorangTangkapan == 'Tidak Dilampirkan' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-gray-700">Tidak Dilampirkan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Borang Serah/Terima Barang Kes Antara Pegawai Penyiasat, Pemilik dan Saksi Pegawai Kanan Polis Dilampirkan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Borang Serah/Terima Barang Kes
                                Antara Pegawai Penyiasat, Pemilik dan Saksi Pegawai Kanan Polis Dilampirkan</label>
                            {!! render_boolean_radio('adakah_borang_serah_terima_pemilik_saksi', $paper->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>

                        <!-- Adakah Sijil Atau Surat Arahan Kebenaran Oleh IPD Bagi Melaksanakan Pelupusan Barang Kes Dilampirkan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Sijil Atau Surat Arahan
                                Kebenaran Oleh IPD Bagi Melaksanakan Pelupusan Barang Kes Dilampirkan</label>
                            {!! render_boolean_radio('adakah_sijil_surat_kebenaran_ipo', $paper->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>

                        <!-- Adakah Gambar Pelupusan Barang Kes Dilampirkan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Gambar Pelupusan Barang Kes
                                Dilampirkan</label>
                            {!! render_boolean_radio('adakah_gambar_pelupusan', $paper->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}
                        </div>
                        <div class="md:col-span-3">
                            <label for="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes"
                                class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan Pegawai Pemeriksa
                                (Barang Kes)</label>
                            <textarea name="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes"
                                id="ulasan_keseluruhan_pegawai_pemeriksa_barang_kes" rows="3"
                                class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes', $paper->ulasan_keseluruhan_pegawai_pemeriksa_barang_kes) }}</textarea>
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
                            <label class="block text-sm font-medium text-gray-700">Gambar Botol Spesimen Urin 3 Dimensi
                                Dan Berseal Merah</label>
                            {!! render_boolean_radio('gambar_botol_urin_3d_berseal', $paper->gambar_botol_urin_3d_berseal, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Pembalut Botol Spesimen Urin
                                Bernombor Siri Dan Test Strip Dadah Positif</label>
                            {!! render_boolean_radio('gambar_pembalut_urin_dan_test_strip', $paper->gambar_pembalut_urin_dan_test_strip, 'Ada', 'Tiada') !!}
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
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Dadah</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_dadah', $paper->status_gambar_barang_kes_dadah, 'Ada', 'Tiada') !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Barang Kes Ketum</label>
                            {!! render_boolean_radio('status_gambar_barang_kes_ketum', $paper->status_gambar_barang_kes_ketum, 'Ada', 'Tiada') !!}
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
                    <h3 class="text-lg mt-5 font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 6
                    </h3>
                    <div class="space-y-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">PEM 1/ PEM 2/ PEM 3/ PEM 4</label>
                            @php
                                $pem_options = ['PEM 1' => 'PEM 1', 'PEM 2' => 'PEM 2', 'PEM 3' => 'PEM 3', 'PEM 4' => 'PEM 4'];
                            @endphp
                            {!! render_json_checkboxes('status_pem', $paper->status_pem, $pem_options) !!}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div><label class="block text-sm font-medium text-gray-700">RJ
                                    2</label>{!! render_status_with_date_radio('rj2', 'status_rj2', 'tarikh_rj2', $paper->status_rj2, $paper->tarikh_rj2) !!}
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ
                                    2B</label>{!! render_status_with_date_radio('rj2b', 'status_rj2b', 'tarikh_rj2b', $paper->status_rj2b, $paper->tarikh_rj2b) !!}
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ
                                    9</label>{!! render_status_with_date_radio('rj9', 'status_rj9', 'tarikh_rj9', $paper->status_rj9, $paper->tarikh_rj9) !!}
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ
                                    99</label>{!! render_status_with_date_radio('rj99', 'status_rj99', 'tarikh_rj99', $paper->status_rj99, $paper->tarikh_rj99) !!}
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ
                                    10A</label>{!! render_status_with_date_radio('rj10a', 'status_rj10a', 'tarikh_rj10a', $paper->status_rj10a, $paper->tarikh_rj10a) !!}
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700">RJ
                                    10B</label>{!! render_status_with_date_radio('rj10b', 'status_rj10b', 'tarikh_rj10b', $paper->status_rj10b, $paper->tarikh_rj10b) !!}
                            </div>
                        </div>

                        <div>
                            <label for="lain_lain_rj_dikesan" class="block text-sm font-medium text-gray-700">Lain-lain
                                RJ Dikesan</label>
                            <input type="text" name="lain_lain_rj_dikesan" id="lain_lain_rj_dikesan"
                                value="{{ old('lain_lain_rj_dikesan', $paper->lain_lain_rj_dikesan) }}"
                                class="mt-1 block w-full form-input">
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Pertama
                                    Wanted Person</label>
                                {!! render_status_with_date_radio('wp1', 'status_semboyan_pertama_wanted_person', 'tarikh_semboyan_pertama_wanted_person', $paper->status_semboyan_pertama_wanted_person, $paper->tarikh_semboyan_pertama_wanted_person) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Kedua
                                    Wanted Person</label>
                                {!! render_status_with_date_radio('wp2', 'status_semboyan_kedua_wanted_person', 'tarikh_semboyan_kedua_wanted_person', $paper->status_semboyan_kedua_wanted_person, $paper->tarikh_semboyan_kedua_wanted_person) !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semboyan Usaha Pemakluman Ketiga
                                    Wanted Person</label>
                                {!! render_status_with_date_radio('wp3', 'status_semboyan_ketiga_wanted_person', 'tarikh_semboyan_ketiga_wanted_person', $paper->status_semboyan_ketiga_wanted_person, $paper->tarikh_semboyan_ketiga_wanted_person) !!}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Penandaan Kelas Warna Pada
                                Kulit Kertas Siasatan Dibuat</label>
                            {!! render_boolean_radio('status_penandaan_kelas_warna', $paper->status_penandaan_kelas_warna, 'Ya', 'Tidak') !!}
                        </div>
                    </div>
                </div>
                <!-- BAHAGIAN 7 -->
                <div>
                    <h3 class="text-lg mt-5 font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 7
                        Agensi Luar</h3>


                    <!-- Permohonan Laporan Post Mortem Mayat -->
                    <!-- E-FSA BANK SECTION -->
                    <h4 class="font-semibold text-md text-gray-700 mt-6 mb-4">Permohonan & Laporan E-FSA (BANK)</h4>

                    <div class="space-y-8">
                        <!-- E-FSA BANK 1 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (BANK) - 1</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA BANK 1 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_1_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (BANK) - 1
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_1_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_1_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_bank_permohonan_E_FSA_1"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan bank:</label>
                                        <input type="text" name="nama_bank_permohonan_E_FSA_1"
                                            id="nama_bank_permohonan_E_FSA_1"
                                            value="{{ old('nama_bank_permohonan_E_FSA_1', $paper->nama_bank_permohonan_E_FSA_1) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA BANK 1 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_1_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (BANK) -
                                        1 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_1_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_1_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_bank_laporan_E_FSA_1_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    bank:</label>
                                                <input type="text" name="nama_bank_laporan_E_FSA_1_oleh_IO_AIO"
                                                    id="nama_bank_laporan_E_FSA_1_oleh_IO_AIO"
                                                    value="{{ old('nama_bank_laporan_E_FSA_1_oleh_IO_AIO', $paper->nama_bank_laporan_E_FSA_1_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh (DD/MM/YYYY):</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-FSA BANK 2 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (BANK) - 2</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA BANK 2 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_2_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (BANK) - 2
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_2_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_2_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_bank_permohonan_E_FSA_2_BANK"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan bank:</label>
                                        <input type="text" name="nama_bank_permohonan_E_FSA_2_BANK"
                                            id="nama_bank_permohonan_E_FSA_2_BANK"
                                            value="{{ old('nama_bank_permohonan_E_FSA_2_BANK', $paper->nama_bank_permohonan_E_FSA_2_BANK) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA BANK 2 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_2_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (BANK) -
                                        2 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_bank_laporan_E_FSA_2_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    bank:</label>
                                                <input type="text" name="nama_bank_laporan_E_FSA_2_oleh_IO_AIO"
                                                    id="nama_bank_laporan_E_FSA_2_oleh_IO_AIO"
                                                    value="{{ old('nama_bank_laporan_E_FSA_2_oleh_IO_AIO', $paper->nama_bank_laporan_E_FSA_2_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-FSA BANK 3 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (BANK) - 3</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA BANK 3 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_3_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (BANK) - 3
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_3_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_3_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_bank_permohonan_E_FSA_3_BANK"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan bank:</label>
                                        <input type="text" name="nama_bank_permohonan_E_FSA_3_BANK"
                                            id="nama_bank_permohonan_E_FSA_3_BANK"
                                            value="{{ old('nama_bank_permohonan_E_FSA_3_BANK', $paper->nama_bank_permohonan_E_FSA_3_BANK) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA BANK 3 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_3_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (BANK) -
                                        3 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_bank_laporan_E_FSA_3_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    bank:</label>
                                                <input type="text" name="nama_bank_laporan_E_FSA_3_oleh_IO_AIO"
                                                    id="nama_bank_laporan_E_FSA_3_oleh_IO_AIO"
                                                    value="{{ old('nama_bank_laporan_E_FSA_3_oleh_IO_AIO', $paper->nama_bank_laporan_E_FSA_3_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- E-FSA BANK 4 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (BANK) - 4</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA BANK 4 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_4_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (BANK) - 4
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_4_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_4_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_bank_permohonan_E_FSA_4_BANK"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan bank:</label>
                                        <input type="text" name="nama_bank_permohonan_E_FSA_4_BANK"
                                            id="nama_bank_permohonan_E_FSA_4_BANK"
                                            value="{{ old('nama_bank_permohonan_E_FSA_4_BANK', $paper->nama_bank_permohonan_E_FSA_4_BANK) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA BANK 4 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_4_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (BANK) -
                                        4 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_bank_laporan_E_FSA_4_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    bank:</label>
                                                <input type="text" name="nama_bank_laporan_E_FSA_4_oleh_IO_AIO"
                                                    id="nama_bank_laporan_E_FSA_4_oleh_IO_AIO"
                                                    value="{{ old('nama_bank_laporan_E_FSA_4_oleh_IO_AIO', $paper->nama_bank_laporan_E_FSA_4_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- E-FSA BANK 5 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (BANK) - 5</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA BANK 5 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_5_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (BANK) - 5
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_5_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_5_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_bank_permohonan_E_FSA_5_BANK"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan bank:</label>
                                        <input type="text" name="nama_bank_permohonan_E_FSA_5_BANK"
                                            id="nama_bank_permohonan_E_FSA_5_BANK"
                                            value="{{ old('nama_bank_permohonan_E_FSA_5_BANK', $paper->nama_bank_permohonan_E_FSA_5_BANK) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA BANK 5 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_5_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (BANK) -
                                        5 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_bank_laporan_E_FSA_5_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    bank:</label>
                                                <input type="text" name="nama_bank_laporan_E_FSA_5_oleh_IO_AIO"
                                                    id="nama_bank_laporan_E_FSA_5_oleh_IO_AIO"
                                                    value="{{ old('nama_bank_laporan_E_FSA_5_oleh_IO_AIO', $paper->nama_bank_laporan_E_FSA_5_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-FSA TELCO SECTION -->
                        <h4 class="font-semibold text-md text-gray-700 mt-6 mb-4">Permohonan & Laporan E-FSA (TELCO)
                        </h4>

                        <!-- E-FSA TELCO 1 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (TELCO) - 1</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA TELCO 1 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_1_telco_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (TELCO) - 1
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_1_telco_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_1_telco_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_telco_permohonan_E_FSA_1_oleh_IO_AIO"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan telco:</label>
                                        <input type="text" name="nama_telco_permohonan_E_FSA_1_oleh_IO_AIO"
                                            id="nama_telco_permohonan_E_FSA_1_oleh_IO_AIO"
                                            value="{{ old('nama_telco_permohonan_E_FSA_1_oleh_IO_AIO', $paper->nama_telco_permohonan_E_FSA_1_oleh_IO_AIO) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA TELCO 1 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (TELCO) -
                                        1 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_telco_laporan_E_FSA_1_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    telco:</label>
                                                <input type="text" name="nama_telco_laporan_E_FSA_1_oleh_IO_AIO"
                                                    id="nama_telco_laporan_E_FSA_1_oleh_IO_AIO"
                                                    value="{{ old('nama_telco_laporan_E_FSA_1_oleh_IO_AIO', $paper->nama_telco_laporan_E_FSA_1_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- E-FSA TELCO 2 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (TELCO) - 2</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA TELCO 2 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_2_telco_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (TELCO) - 2
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_2_telco_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_2_telco_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_telco_permohonan_E_FSA_2_oleh_IO_AIO"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan telco:</label>
                                        <input type="text" name="nama_telco_permohonan_E_FSA_2_oleh_IO_AIO"
                                            id="nama_telco_permohonan_E_FSA_2_oleh_IO_AIO"
                                            value="{{ old('nama_telco_permohonan_E_FSA_2_oleh_IO_AIO', $paper->nama_telco_permohonan_E_FSA_2_oleh_IO_AIO) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA TELCO 2 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (TELCO) -
                                        2 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_2_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_telco_laporan_E_FSA_2_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    telco:</label>
                                                <input type="text" name="nama_telco_laporan_E_FSA_2_oleh_IO_AIO"
                                                    id="nama_telco_laporan_E_FSA_2_oleh_IO_AIO"
                                                    value="{{ old('nama_telco_laporan_E_FSA_2_oleh_IO_AIO', $paper->nama_telco_laporan_E_FSA_2_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- E-FSA TELCO 3 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (TELCO) - 3</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA TELCO 3 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_3_telco_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (TELCO) - 3
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_3_telco_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_3_telco_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_telco_permohonan_E_FSA_3_oleh_IO_AIO"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan telco:</label>
                                        <input type="text" name="nama_telco_permohonan_E_FSA_3_oleh_IO_AIO"
                                            id="nama_telco_permohonan_E_FSA_3_oleh_IO_AIO"
                                            value="{{ old('nama_telco_permohonan_E_FSA_3_oleh_IO_AIO', $paper->nama_telco_permohonan_E_FSA_3_oleh_IO_AIO) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA TELCO 3 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (TELCO) -
                                        3 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_3_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_telco_laporan_E_FSA_3_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    telco:</label>
                                                <input type="text" name="nama_telco_laporan_E_FSA_3_oleh_IO_AIO"
                                                    id="nama_telco_laporan_E_FSA_3_oleh_IO_AIO"
                                                    value="{{ old('nama_telco_laporan_E_FSA_3_oleh_IO_AIO', $paper->nama_telco_laporan_E_FSA_3_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- E-FSA TELCO 4 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (TELCO) - 4</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA TELCO 4 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_4_telco_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (TELCO) - 4
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_4_telco_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_4_telco_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_telco_permohonan_E_FSA_4_oleh_IO_AIO"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan telco:</label>
                                        <input type="text" name="nama_telco_permohonan_E_FSA_4_oleh_IO_AIO"
                                            id="nama_telco_permohonan_E_FSA_4_oleh_IO_AIO"
                                            value="{{ old('nama_telco_permohonan_E_FSA_4_oleh_IO_AIO', $paper->nama_telco_permohonan_E_FSA_4_oleh_IO_AIO) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA TELCO 4 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (TELCO) -
                                        4 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_telco_laporan_E_FSA_4_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    telco:</label>
                                                <input type="text" name="nama_telco_laporan_E_FSA_4_oleh_IO_AIO"
                                                    id="nama_telco_laporan_E_FSA_4_oleh_IO_AIO"
                                                    value="{{ old('nama_telco_laporan_E_FSA_4_oleh_IO_AIO', $paper->nama_telco_laporan_E_FSA_4_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- E-FSA TELCO 5 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">E-FSA (TELCO) - 5</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan E-FSA TELCO 5 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_permohonan_E_FSA_5_telco_oleh_IO_AIO == 'Permohonan Dibuat' ? 'Permohonan Dibuat' : 'Tiada Permohonan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan E-FSA (TELCO) - 5
                                        oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_5_telco_oleh_IO_AIO"
                                                value="Permohonan Dibuat" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_E_FSA_5_telco_oleh_IO_AIO"
                                                value="Tiada Permohonan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="nama_telco_permohonan_E_FSA_5_oleh_IO_AIO"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan telco:</label>
                                        <input type="text" name="nama_telco_permohonan_E_FSA_5_oleh_IO_AIO"
                                            id="nama_telco_permohonan_E_FSA_5_oleh_IO_AIO"
                                            value="{{ old('nama_telco_permohonan_E_FSA_5_oleh_IO_AIO', $paper->nama_telco_permohonan_E_FSA_5_oleh_IO_AIO) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh E-FSA TELCO 5 -->
                                <div
                                    x-data="{ status: '{{ $paper->status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO == 'Dilampirkan' ? 'Dilampirkan' : 'Tidak Dilampirkan' }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh E-FSA (TELCO) -
                                        5 oleh IO/AIO</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                value="Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                value="Tidak Dilampirkan" x-model="status"
                                                class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Dilampirkan'" x-transition class="mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nama_telco_laporan_E_FSA_5_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                                    telco:</label>
                                                <input type="text" name="nama_telco_laporan_E_FSA_5_oleh_IO_AIO"
                                                    id="nama_telco_laporan_E_FSA_5_oleh_IO_AIO"
                                                    value="{{ old('nama_telco_laporan_E_FSA_5_oleh_IO_AIO', $paper->nama_telco_laporan_E_FSA_5_oleh_IO_AIO) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                            <div>
                                                <label for="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                    class="block text-sm text-gray-600">Tarikh:</label>
                                                <input type="date" name="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                    id="tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO"
                                                    value="{{ old('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO', optional($paper->tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PUSPAKOM Reports Section -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-3">Laporan PUSPAKOM</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan Laporan PUSPAKOM -->
                                <div x-data="{ status: '{{ $paper->status_permohonan_laporan_puspakom ? 1 : 0 }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan
                                        PUSPAKOM</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_laporan_puspakom" value="1"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_laporan_puspakom" value="0"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="tarikh_permohonan_laporan_puspakom"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan tarikh (DD/MM/YYYY):</label>
                                        <input type="date" name="tarikh_permohonan_laporan_puspakom"
                                            id="tarikh_permohonan_laporan_puspakom"
                                            value="{{ old('tarikh_permohonan_laporan_puspakom', optional($paper->tarikh_permohonan_laporan_puspakom)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh PUSPAKOM -->
                                <div x-data="{ status: '{{ $paper->status_laporan_penuh_puspakom ? 1 : 0 }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh
                                        PUSPAKOM</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_puspakom" value="1"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_puspakom" value="0"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="tarikh_laporan_penuh_puspakom"
                                            class="block text-sm text-gray-600">Jika Dilampirkan, nyatakan
                                            tarikh (DD/MM/YYYY):</label>
                                        <input type="date" name="tarikh_laporan_penuh_puspakom"
                                            id="tarikh_laporan_penuh_puspakom"
                                            value="{{ old('tarikh_laporan_penuh_puspakom', optional($paper->tarikh_laporan_penuh_puspakom)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- JPJ Reports Section -->
                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <h5 class="font-medium text-gray-700 mb-3">Laporan JPJ</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Permohonan Laporan JPJ -->
                                <div x-data="{ status: '{{ $paper->status_permohonan_laporan_jpj ? 1 : 0 }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan
                                        JPJ</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_laporan_jpj" value="1"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_laporan_jpj" value="0"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="tarikh_permohonan_laporan_jpj"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan tarikh:</label>
                                        <input type="date" name="tarikh_permohonan_laporan_jpj"
                                            id="tarikh_permohonan_laporan_jpj"
                                            value="{{ old('tarikh_permohonan_laporan_jpj', optional($paper->tarikh_permohonan_laporan_jpj)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>

                                <!-- Laporan Penuh JPJ (corrected from JKR) -->
                                <div x-data="{ status: '{{ $paper->status_laporan_penuh_jpj ? 1 : 0 }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh JPJ</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_jpj" value="1"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_jpj" value="0"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="tarikh_laporan_penuh_jpj" class="block text-sm text-gray-600">Jika
                                            Dilampirkan, nyatakan tarikh:</label>
                                        <input type="date" name="tarikh_laporan_penuh_jpj" id="tarikh_laporan_penuh_jpj"
                                            value="{{ old('tarikh_laporan_penuh_jpj', optional($paper->tarikh_laporan_penuh_jpj)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 
                        
                        JKR Reports Section

                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <h5 class="font-medium text-gray-700 mb-3">Laporan JKR</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                Permohonan Laporan JKR 

                                <div x-data="{ status: '{{ $paper->status_permohonan_laporan_jkr ? 1 : 0 }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Permohonan Laporan
                                        JKR</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_laporan_jkr" value="1"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_permohonan_laporan_jkr" value="0"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="tarikh_permohonan_laporan_jkr"
                                            class="block text-sm text-gray-600">Jika Ada, nyatakan tarikh:</label>
                                        <input type="date" name="tarikh_permohonan_laporan_jkr"
                                            id="tarikh_permohonan_laporan_jkr"
                                            value="{{ old('tarikh_permohonan_laporan_jkr', optional($paper->tarikh_permohonan_laporan_jkr)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div> 
                                
                                -->

                        <!-- 
                                
                                Laporan Penuh JKR 
                                <div x-data="{ status: '{{ $paper->status_laporan_penuh_jkr ? 1 : 0 }}' }">
                                    <label class="block text-sm font-medium text-gray-700">Laporan Penuh JKR</label>
                                    <div class="mt-2 flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_jkr" value="1"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Dilampirkan</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_laporan_penuh_jkr" value="0"
                                                x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                            <span class="ml-2 text-gray-700">Tiada</span>
                                        </label>
                                    </div>
                                    <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                        <label for="tarikh_laporan_penuh_jkr" class="block text-sm text-gray-600">Jika
                                            Dilampirkan, nyatakan tarikh:</label>
                                        <input type="date" name="tarikh_laporan_penuh_jkr" id="tarikh_laporan_penuh_jkr"
                                            value="{{ old('tarikh_laporan_penuh_jkr', optional($paper->tarikh_laporan_penuh_jkr)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full form-input">
                                    </div>
                                </div> 
                                
                                 -->
                    </div>
                </div>

                <!-- IMIGRESEN Reports Section -->
                <div class="bg-gray-50 p-4 rounded-lg mt-4">
                    <h5 class="font-medium text-gray-700 mb-3">Laporan IMIGRESEN</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Permohonan Laporan IMIGRESEN -->
                        <div x-data="{ status: '{{ $paper->status_permohonan_laporan_imigresen ? 1 : 0 }}' }">
                            <label class="block text-sm font-medium text-gray-700">Permohonan Laporan
                                IMIGRESEN</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_permohonan_laporan_imigresen" value="1"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ada</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_permohonan_laporan_imigresen" value="0"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                            </div>
                            <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                <label for="tarikh_permohonan_laporan_imigresen"
                                    class="block text-sm text-gray-600">Jika Ada, nyatakan tarikh:</label>
                                <input type="date" name="tarikh_permohonan_laporan_imigresen"
                                    id="tarikh_permohonan_laporan_imigresen"
                                    value="{{ old('tarikh_permohonan_laporan_imigresen', optional($paper->tarikh_permohonan_laporan_imigresen)->format('Y-m-d')) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>

                        <!-- Laporan Penuh IMIGRESEN -->
                        <div x-data="{ status: '{{ $paper->status_laporan_penuh_imigresen ? 1 : 0 }}' }">
                            <label class="block text-sm font-medium text-gray-700">Laporan Penuh IMIGRESEN</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_laporan_penuh_imigresen" value="1" x-model="status"
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Dilampirkan</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_laporan_penuh_imigresen" value="0" x-model="status"
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                            </div>
                            <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                <label for="tarikh_laporan_penuh_imigresen" class="block text-sm text-gray-600">Jika
                                    Dilampirkan, nyatakan tarikh:</label>
                                <input type="date" name="tarikh_laporan_penuh_imigresen"
                                    id="tarikh_laporan_penuh_imigresen"
                                    value="{{ old('tarikh_laporan_penuh_imigresen', optional($paper->tarikh_laporan_penuh_imigresen)->format('Y-m-d')) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KASTAM Reports Section -->
                <div class="bg-gray-50 p-4 rounded-lg mt-4">
                    <h5 class="font-medium text-gray-700 mb-3">Laporan KASTAM</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Permohonan Laporan KASTAM -->
                        <div x-data="{ status: '{{ $paper->status_permohonan_laporan_kastam ? 1 : 0 }}' }">
                            <label class="block text-sm font-medium text-gray-700">Permohonan Laporan
                                KASTAM</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_permohonan_laporan_kastam" value="1"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ada</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_permohonan_laporan_kastam" value="0"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                            </div>
                            <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                <label for="tarikh_permohonan_laporan_kastam" class="block text-sm text-gray-600">Jika
                                    Ada, nyatakan tarikh:</label>
                                <input type="date" name="tarikh_permohonan_laporan_kastam"
                                    id="tarikh_permohonan_laporan_kastam"
                                    value="{{ old('tarikh_permohonan_laporan_kastam', optional($paper->tarikh_permohonan_laporan_kastam)->format('Y-m-d')) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>

                        <!-- Laporan Penuh KASTAM -->
                        <div x-data="{ status: '{{ $paper->status_laporan_penuh_kastam ? 1 : 0 }}' }">
                            <label class="block text-sm font-medium text-gray-700">Laporan Penuh KASTAM</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_laporan_penuh_kastam" value="1" x-model="status"
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Dilampirkan</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_laporan_penuh_kastam" value="0" x-model="status"
                                        class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                            </div>
                            <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                <label for="tarikh_laporan_penuh_kastam" class="block text-sm text-gray-600">Jika
                                    Dilampirkan, nyatakan tarikh:</label>
                                <input type="date" name="tarikh_laporan_penuh_kastam" id="tarikh_laporan_penuh_kastam"
                                    value="{{ old('tarikh_laporan_penuh_kastam', optional($paper->tarikh_laporan_penuh_kastam)->format('Y-m-d')) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forensik PDRM Reports Section -->
                <div class="bg-gray-50 p-4 rounded-lg mt-4">
                    <h5 class="font-medium text-gray-700 mb-3">Laporan Forensik PDRM</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Permohonan Laporan Forensik PDRM -->
                        <div x-data="{ status: '{{ $paper->status_permohonan_laporan_forensik_pdrm ? 1 : 0 }}' }">
                            <label class="block text-sm font-medium text-gray-700">Permohonan Laporan Forensik
                                PDRM</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_permohonan_laporan_forensik_pdrm" value="1"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Ada</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_permohonan_laporan_forensik_pdrm" value="0"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                            </div>
                            <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                <div>
                                    <label for="tarikh_permohonan_laporan_forensik_pdrm"
                                        class="block text-sm text-gray-600">Jika Ada, nyatakan tarikh:</label>
                                    <input type="date" name="tarikh_permohonan_laporan_forensik_pdrm"
                                        id="tarikh_permohonan_laporan_forensik_pdrm"
                                        value="{{ old('tarikh_permohonan_laporan_forensik_pdrm', optional($paper->tarikh_permohonan_laporan_forensik_pdrm)->format('Y-m-d')) }}"
                                        class="mt-1 block w-full form-input">
                                </div>
                                <div class="mt-2">
                                    <label for="jenis_barang_kes_forensik" class="block text-sm text-gray-600">Jenis
                                        Barang Kes Di Hantar:</label>
                                    <input type="text" name="jenis_barang_kes_forensik" id="jenis_barang_kes_forensik"
                                        value="{{ old('jenis_barang_kes_forensik', $paper->jenis_barang_kes_forensik) }}"
                                        class="mt-1 block w-full form-input">
                                </div>
                            </div>
                        </div>

                        <!-- Laporan Penuh Forensik PDRM -->
                        <div x-data="{ status: '{{ $paper->status_laporan_penuh_forensik_pdrm ? 1 : 0 }}' }">
                            <label class="block text-sm font-medium text-gray-700">Laporan Penuh Forensik
                                PDRM</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_laporan_penuh_forensik_pdrm" value="1"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Dilampirkan</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_laporan_penuh_forensik_pdrm" value="0"
                                        x-model="status" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2 text-gray-700">Tiada</span>
                                </label>
                            </div>
                            <div x-show="status == 'Permohonan Dibuat'" x-transition class="mt-2">
                                <label for="tarikh_laporan_penuh_forensik_pdrm" class="block text-sm text-gray-600">Jika
                                    Dilampirkan, nyatakan
                                    tarikh:</label>
                                <input type="date" name="tarikh_laporan_penuh_forensik_pdrm"
                                    id="tarikh_laporan_penuh_forensik_pdrm"
                                    value="{{ old('tarikh_laporan_penuh_forensik_pdrm', optional($paper->tarikh_laporan_penuh_forensik_pdrm)->format('Y-m-d')) }}"
                                    class="mt-1 block w-full form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Reports (Lain-lain) -->
                <div class="bg-gray-50 p-4 rounded-lg mt-4">
                    <div>
                        <label for="lain_lain_permohonan_laporan"
                            class="block text-sm font-medium text-gray-700">Lain-lain Permohonan Laporan</label>
                        <input type="text" name="lain_lain_permohonan_laporan" id="lain_lain_permohonan_laporan"
                            value="{{ old('lain_lain_permohonan_laporan', $paper->lain_lain_permohonan_laporan) }}"
                            class="mt-1 block w-full form-input">
                    </div>
                </div>
                <!-- BAHAGIAN 8 -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">BAHAGIAN 8</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Barang Kes
                                    Ditulis Bersama No Daftar Barang Kes</label>
                                {!! render_boolean_radio('muka_surat_4_barang_kes_ditulis', $paper->muka_surat_4_barang_kes_ditulis, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Barang Kes
                                    Ditulis Bersama No Daftar Dan Telah Ada Arahan YA TPR Untuk Pelupusan/Serahan
                                    Semula</label>
                                {!! render_boolean_radio('muka_surat_4_dengan_arahan_tpr', $paper->muka_surat_4_dengan_arahan_tpr, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Muka Surat 4 - Keputusan
                                    Kes Dicatat Selengkapnya</label>
                                {!! render_boolean_radio('muka_surat_4_keputusan_kes_dicatat', $paper->muka_surat_4_keputusan_kes_dicatat, 'Ya', 'Tidak') !!}
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adakah Fail L.M.M (T) Atau L.M.M
                                    Telah Ada Keputusan Siasatan Oleh YA Koroner</label>
                                {!! render_boolean_radio('fail_lmm_ada_keputusan_koroner', $paper->fail_lmm_ada_keputusan_koroner, 'Ya', 'Tidak') !!}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adakah Kertas Siasatan Telah Di
                                KUS/FAIL Bagi Siasatan Yang Telah Selesai Dan Ada Keputusan Mahkamah</label>
                            {!! render_boolean_radio('status_kus_fail', $paper->status_kus_fail, 'Ya', 'Tidak') !!}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keputusan Akhir Mahkamah Sebelum KS
                                di KUS/FAIL Atau Disimpan</label>
                            @php
                                $mahkamah_options = ['JATUH HUKUM' => 'JATUH HUKUM', 'NFA' => 'NFA', 'DNA' => 'DNA', 'DNAA' => 'DNAA', 'KUS/SEMENTARA' => 'KUS/SEMENTARA', 'MASIH DALAM SIASATAN/OYDS GAGAL DIKESAN' => 'MASIH DALAM SIASATAN/OYDS GAGAL DIKESAN', 'MASIH DALAM SIASATAN/LENGKAPKAN DOKUMEN SIASATAN' => 'MASIH DALAM SIASATAN/LENGKAPKAN DOKUMEN SIASATAN', 'TERBENGKALAI/TIADA TINDAKAN' => 'TERBENGKALAI/TIADA TINDAKAN', 'KUS/FAIL' => 'KUS/FAIL'];
                            @endphp
                            {!! render_json_checkboxes('keputusan_akhir_mahkamah', $paper->keputusan_akhir_mahkamah, $mahkamah_options) !!}
                        </div>
                        <div>
                            <label for="ulasan_pegawai_pemeriksa_fail"
                                class="block text-sm font-medium text-gray-700">Ulasan
                                Keseluruhan Pegawai Pemeriksa
                                (Jika Ada)</label>
                            <textarea name="ulasan_pegawai_pemeriksa_fail" id="ulasan_pegawai_pemeriksa_fail" rows="4"
                                class="mt-1 block w-full form-textarea">{{ old('ulasan_pegawai_pemeriksa_fail', $paper->ulasan_pegawai_pemeriksa_fail) }}</textarea>
                        </div>
                    </div>
                </div>
                    {{-- Submit Button --}}
    <div class="flex justify-end pt-4 mt-6 border-t">
        <a href="{{ route('projects.show', $paper->project_id) }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-3">Batal</a>
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Kemaskini</button>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Function to handle enabling/disabling of "Lain-lain" text input
                function setupOtherInputToggle(radioName, otherInputId) {
                    const radios = document.querySelectorAll(`input[name="${radioName}"]`);
                    const otherInput = document.getElementById(otherInputId);

                    radios.forEach(radio => {
                        radio.addEventListener('change', function () {
                            if (this.value === 'Lain-Lain') {
                                otherInput.disabled = false;
                                otherInput.focus();
                            } else {
                                otherInput.disabled = true;
                                otherInput.value = ''; // Clear value when not 'Lain-lain'
                            }
                        });
                    });

                    // Initial state on page load
                    const currentChecked = document.querySelector(`input[name="${radioName}"]:checked`);
                    if (currentChecked && currentChecked.value === 'Lain-Lain') {
                        otherInput.disabled = false;
                    } else {
                        otherInput.disabled = true;
                    }
                }

                // Apply to Barang Kes "Status Pergerakan"
                setupOtherInputToggle('status_pergerakan_barang_kes', 'status_pergerakan_barang_kes_lain_komersil');

                // Apply to Barang Kes "Status Selesai Siasatan"
                setupOtherInputToggle('status_barang_kes_selesai_siasatan', 'status_barang_kes_selesai_siasatan_lain_komersil');

                // Apply to Barang Kes "Kaedah Pelupusan"
                setupOtherInputToggle('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', 'kaedah_pelupusan_barang_kes_lain_komersil');
            });
        </script>

        </form>
    </div>
        </div>

    </div>




    </div>

    <style>
        /* General form element styling for consistency */
        .form-input,
        .form-select,
        .form-textarea {
            @apply rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
        }

        .form-radio,
        .form-checkbox {
            @apply rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500;
            /* Adjusted form-radio to rounded-full */
        }

        /* Enhanced date input styling with DD/MM/YYYY format hints */
        input[type="date"] {
            position: relative;
        }
        
        input[type="date"]::-webkit-datetime-edit-day-field {
            background-color: transparent;
        }
        
        input[type="date"]::-webkit-datetime-edit-month-field {
            background-color: transparent;
        }
        
        input[type="date"]::-webkit-datetime-edit-year-field {
            background-color: transparent;
        }
        
        /* Enhanced format hint with calendar icon */
        input[type="date"]:before {
            content: attr(data-format) " 📅";
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            pointer-events: none;
            font-size: 0.875rem;
            background: white;
            padding: 0 4px;
            z-index: 1;
        }
        
        input[type="date"]:focus:before,
        input[type="date"]:valid:before {
            display: none;
        }
        
        /* Add visual indicator for DD/MM/YYYY format */
        input[type="date"]:after {
            content: "DD/MM/YYYY";
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            color: #D1D5DB;
            pointer-events: none;
            font-size: 0.75rem;
            opacity: 0.7;
        }
        
        input[type="date"]:focus:after,
        input[type="date"]:valid:after {
            opacity: 0;
        }
        
        /* Hover effects for better UX */
        input[type="date"]:hover {
            border-color: #6366F1;
            box-shadow: 0 0 0 1px #6366F1;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                /**
                 * A generic helper function to manage the state (enabled/disabled) of a text input
                 * based on the selection of its corresponding radio button.
                 * The input field will always be visible.
                 *
                 * @param {string} radioGroupName - The 'name' attribute of the radio button group.
                 * @param {string} triggerRadioId - The 'id' attribute of the specific radio button that triggers the input.
                 * @param {string} targetInputId - The 'id' attribute of the text input to enable/disable.
                 */
                function setupSpecialInputToggle(radioGroupName, triggerRadioId, targetInputId) {
                    const radios = document.querySelectorAll(`input[name="${radioGroupName}"]`);
                    const targetInput = document.getElementById(targetInputId);

                    // If the elements don't exist on the page, do nothing.
                    if (!radios.length || !targetInput) {
                        return;
                    }

                    // This function updates the state of the target input field.
                    const updateState = () => {
                        const selectedRadio = document.querySelector(`input[name="${radioGroupName}"]:checked`);

                        // Check if a radio is selected AND if its ID matches the trigger ID.
                        if (selectedRadio && selectedRadio.id === triggerRadioId) {
                            // ENABLE the input if its radio button is selected.
                            targetInput.disabled = false;
                        } else {
                            // DISABLE the input if its radio button is NOT selected.
                            targetInput.disabled = true;
                            // Also, clear its value to prevent submitting old data.
                            targetInput.value = '';
                        }
                    };

                    // Add a 'change' event listener to every radio button in the group.
                    radios.forEach(radio => {
                        radio.addEventListener('change', updateState);
                    });

                    // Run the function once on page load to set the initial correct state.
                    updateState();
                }

                // --- Initialize All Toggles for the komersil Seksyen Form ---

                // Section 1: Status Pergerakan Barang Kes
                // Handles the "Ujian Makmal (Nyatakan)" input
                setupSpecialInputToggle(
                    'status_pergerakan_barang_kes',
                    'radio_pergerakan_makmal_komersil',
                    'pergerakan_makmal_komersil'
                );
                // Handles the "Lain-Lain" input in the same section
                setupSpecialInputToggle(
                    'status_pergerakan_barang_kes',
                    'pergerakan_lain_komersil',
                    'status_pergerakan_barang_kes_lain_komersil'
                );


                // Section 2: Status Barang Kes Selesai Siasatan
                // Handles the "Dilupuskan ke Perbendaharaan (RM)" input
                setupSpecialInputToggle(
                    'status_barang_kes_selesai_siasatan',
                    'radio_selesai_siasatan_RM_komersil',
                    'selesai_siasatan_RM_komersil'
                );
                // Handles the "Lain-Lain" input in the same section
                setupSpecialInputToggle(
                    'status_barang_kes_selesai_siasatan',
                    'selesai_lain_komersil',
                    'status_barang_kes_selesai_siasatan_lain_komersil'
                );


                // Section 3: Kaedah Pelupusan Barang Kes
                // Handles the "Lain-Lain" input
                setupSpecialInputToggle(
                    'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                    'kaedah_lain_komersil',
                    'kaedah_pelupusan_barang_kes_lain_komersil'
                );
            });
        </script>
    @endpush
</x-app-layout>

</div>