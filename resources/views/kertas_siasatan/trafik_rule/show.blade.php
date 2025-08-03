<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Trafik Rule ({{ $paper->no_kertas_siasatan }})
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'TrafikRule', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @php
                function show_boolean_badge($value, $trueText = 'Ya', $falseText = 'Tidak') {
                    if (is_null($value)) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
                    // Convert potential 0/1 strings to boolean for consistent display
                    $booleanValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    return $booleanValue ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>" : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                }
                
                function show_status_and_date($status, $date = null, $trueText = 'Ada', $falseText = 'Tiada') {
                    // Convert potential 0/1 strings to boolean for consistent display
                    $booleanStatus = filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    
                    $status_html = show_boolean_badge($booleanStatus, $trueText, $falseText);
                    $date_html = $booleanStatus && $date ? (is_string($date) ? $date : optional($date)->format('d/m/Y')) : '-';
                    return "{$status_html} | Tarikh: {$date_html}";
                }
                
                function show_json_list($json_data) {
                    if (empty($json_data)) return '-';
                    
                    if (is_array($json_data)) {
                        // Already an array, no need to decode
                    } elseif (is_string($json_data)) {
                        $decoded = json_decode($json_data, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $json_data = $decoded;
                        } else {
                            // If it's a string that's not JSON, just display it
                            return htmlspecialchars($json_data);
                        }
                    } else {
                        // Handle other types if necessary, though unlikely for this context
                        return htmlspecialchars((string) $json_data);
                    }
                    
                    if (!is_array($json_data)) return htmlspecialchars((string) $json_data);
                    
                    $items = [];
                    foreach ($json_data as $key => $value) {
                        if (!empty($value) || $value === 0 || $value === false) { // Include 0/false if they are meaningful values
                             if (is_string($key) && !is_numeric($key)) {
                                // If array is associative, show key: value (e.g., 'Lain-Lain': 'specific detail')
                                $items[] = htmlspecialchars($key . ': ' . $value);
                            } else {
                                $items[] = htmlspecialchars($value);
                            }
                        }
                    }
                    
                    if (empty($items)) return '-';
                    
                    if (count($items) == 1 && !str_contains($items[0], ':')) { // Only show as list if more than one item or specific format
                        return $items[0];
                    }
                    
                    $html = '<ul class="list-disc list-inside space-y-1">';
                    foreach ($items as $item) {
                        $html .= "<li>" . $item . "</li>";
                    }
                    $html .= '</ul>';
                    return $html;
                }
            @endphp

                        <!-- BAHAGIAN 1 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-blue-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        BAHAGIAN 1
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Kertas Siasatan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_kertas_siasatan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Fail LMM (T)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_fail_lmm_t ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Repot Polis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_repot_polis ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Pegawai Penyiasat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->pegawai_penyiasat ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Laporan Polis Dibuka</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Seksyen</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->seksyen ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

           <!-- BAHAGIAN 2 (RE-ORDERED) -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-green-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 2</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Pegawai Pemeriksa</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->pegawai_pemeriksa ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Pertama (A)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Kedua (B)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        {{-- Calculated Field 1 --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-yellow-50">
                            <dt class="text-sm font-medium text-yellow-800">Sistem Calculate (B - A): KS Lewat Edaran 24 Jam</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->lewat_edaran_status ?? 'Tidak Terkira' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Sebelum Minit Akhir (C)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Akhir (D)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        {{-- Calculated Field 2 --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-yellow-50">
                            <dt class="text-sm font-medium text-yellow-800">Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->terbengkalai_status_dc ?? 'Tidak Terkira' }}</dd>
                        </div>
                        {{-- Calculated Field 3 --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-yellow-50">
                            <dt class="text-sm font-medium text-yellow-800">Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->baru_dikemaskini_status ?? 'Tidak Terkira' }}</dd>
                        </div>
                        {{-- Calculated Field 4 --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-yellow-50">
                            <dt class="text-sm font-medium text-yellow-800">Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->terbengkalai_status_da ?? 'Tidak Terkira' }}</dd>
                        </div>
                        
                        {{-- L.M.M (T) Section --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t-2 border-green-200">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit Fail L.M.M (T) Pertama</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_pertama)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit Fail L.M.M (T) Kedua</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_kedua)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit Fail L.M.M (T) Sebelum Minit Akhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit Fail L.M.M (T) Akhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Fail L.M.M (T) Muka Surat 2 Disahkan KPD</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->fail_lmm_t_muka_surat_2_disahkan_kpd, 'Telah Disahkan', 'Belum Disahkan') !!}</dd>
                        </div>
                    </dl>
                </div>
            </div>

                        <!-- BAHAGIAN 3 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-orange-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 3</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh SIO</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->arahan_minit_oleh_sio_status, $paper->arahan_minit_oleh_sio_tarikh) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh Ketua Bahagian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->arahan_minit_ketua_bahagian_status, $paper->arahan_minit_ketua_bahagian_tarikh) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh Ketua Jabatan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->arahan_minit_ketua_jabatan_status, $paper->arahan_minit_ketua_jabatan_tarikh) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->arahan_minit_oleh_ya_tpr_status, $paper->arahan_minit_oleh_ya_tpr_tarikh) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_siasatan_oleh_ya_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adakah Arahan Tuduh Oleh YA TPR Diambil Tindakan</dt>
                            {{-- This field is a single string in Trafik Rule, not JSON like TrafikSeksyen --}}
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Siasatan TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keputusan_siasatan_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

                        <!-- BAHAGIAN 5 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-purple-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 5</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Rajah Kasar Tempat Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Tempat Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_tempat_kejadian, 'Ada', 'Tiada') !!}</dd>
                        </div>
                    </dl>
                </div>
            </div>

                        <!-- BAHAGIAN 6 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-indigo-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 6</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Borang PEM</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_json_list($paper->status_pem) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 10B</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_rj10b, $paper->tarikh_rj10b, 'Cipta', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Lain-lain RJ Dikesan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->lain_lain_rj_dikesan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Saman PDRM (S) 257</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {!! show_boolean_badge($paper->status_saman_pdrm_s_257, 'Dicipta', 'Tidak Dicipta') !!}
                                @if($paper->status_saman_pdrm_s_257 && $paper->no_saman_pdrm_s_257)
                                    | No: <span class="font-semibold">{{ $paper->no_saman_pdrm_s_257 }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Saman PDRM (S) 167</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {!! show_boolean_badge($paper->status_saman_pdrm_s_167, 'Dicipta', 'Tidak Dicipta') !!}
                                @if($paper->status_saman_pdrm_s_167 && $paper->no_saman_pdrm_s_167)
                                    | No: <span class="font-semibold">{{ $paper->no_saman_pdrm_s_167 }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Borang)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 7 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-red-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 7</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        {{-- JKR Section --}}
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Laporan JKR</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_jkr, $paper->tarikh_permohonan_laporan_jkr, 'Ada', 'Tiada') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_jkr, $paper->tarikh_laporan_penuh_jkr, 'Dilampirkan', 'Tiada') !!}</dd>
                                </div>
                            </div>
                        </div>
                        
                        {{-- JPJ Section --}}
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Laporan JPJ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Permohonan Laporan JPJ</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_jpj, $paper->tarikh_permohonan_laporan_jpj, 'Ada', 'Tiada') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Laporan Penuh JPJ</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_jpj, $paper->tarikh_laporan_penuh_jpj, 'Dilampirkan', 'Tiada') !!}</dd>
                                </div>
                            </div>
                        </div>

                        {{-- JKJR Section --}}
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Laporan JKJR</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Permohonan Laporan JKJR</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_jkjr, $paper->tarikh_permohonan_laporan_jkjr, 'Ada', 'Tiada') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Laporan Penuh JKJR</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_jkjr, $paper->tarikh_laporan_penuh_jkjr, 'Dilampirkan', 'Tiada') !!}</dd>
                                </div>
                            </div>
                        </div>

                        {{-- Lain-lain Section --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Lain-lain Permohonan Laporan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->lain_lain_permohonan_laporan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

                        <!-- BAHAGIAN 8 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 8</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adakah Muka Surat 4 Keputusan Kes Dicatat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_muka_surat_4_keputusan_kes_dicatat, 'Ya', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adakah KS di KUS/FAIL</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_ks_kus_fail_selesai, 'Ya', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adakah Fail LMM(T) Telah Ada Keputusan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan, 'Ya', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah</dt>
                            {{-- This column is a simple string in Trafik Rule based on your edit blade, not JSON list --}}
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_akhir_mahkamah ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
                        <!-- Maklumat Rekod -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Maklumat Rekod
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Cipta</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ optional($paper->created_at)->format('d/m/Y H:i:s') }}
                                @if($paper->created_at)
                                    {{-- Added ->locale('ms') to translate the output --}}
                                    <span class="text-gray-500 text-xs">({{ $paper->created_at->locale('ms')->diffForHumans() }})</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Kemaskini Terakhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ optional($paper->updated_at)->format('d/m/Y H:i:s') }}
                                @if($paper->updated_at)
                                    {{-- Added ->locale('ms') to translate the output --}}
                                    <span class="text-gray-500 text-xs">({{ $paper->updated_at->locale('ms')->diffForHumans() }})</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
