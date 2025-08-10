<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Laporan Mati Mengejut
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'LaporanMatiMengejut', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
                        {{-- IPRS Standard Section --}}
            <x-iprs-section :paper="$paper" mode="view" />
            
            @php
                function show_boolean_badge($value, $trueText = 'Ya', $falseText = 'Tidak') {
                    if (is_null($value)) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
                    return $value ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>" : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                }
                function show_status_and_date($status, $date = null, $trueText = 'Ada', $falseText = 'Tiada') {
                    $status_html = show_boolean_badge($status, $trueText, $falseText);
                    $date_html = $status && $date ? (is_string($date) ? $date : optional($date)->format('d/m/Y')) : '-';
                    return "{$status_html} | Tarikh: {$date_html}";
                }
                
                function show_status_with_three_options($status, $date = null, $trueText = 'Ada / Cipta', $falseText = 'Tiada / Tidak Cipta', $notApplicableText = 'Tidak Berkaitan') {
                    if (is_null($status)) {
                        $status_html = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
                    } elseif ($status === 2 || $status === '2') {
                        $status_html = "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800'>{$notApplicableText}</span>";
                    } elseif ($status === true || $status === 1 || $status === '1') {
                        $status_html = "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>";
                    } else {
                        $status_html = "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                    }
                    
                    $date_html = ($status === 1 || $status === '1' || $status === true) && $date ? (is_string($date) ? $date : optional($date)->format('d/m/Y')) : '-';
                    return "{$status_html} | Tarikh: {$date_html}";
                }
                
                function show_status_date_and_keputusan($status, $date = null, $keputusan = null, $trueText = 'Diterima', $falseText = 'Tidak') {
                    $status_html = show_boolean_badge($status, $trueText, $falseText);
                    $date_html = $status && $date ? (is_string($date) ? $date : optional($date)->format('d/m/Y')) : '-';
                    $keputusan_html = $status && $keputusan ? htmlspecialchars($keputusan) : '-';
                    return "{$status_html} | Tarikh: {$date_html}" . ($keputusan_html !== '-' ? " | Keputusan: {$keputusan_html}" : '');
                }
                
                function show_lain_lain_value($main_value, $lain_value = null, $field_name = '') {
                    if (empty($main_value)) return '-';
                    
                    // Define predefined options for specific fields
                    $predefined_options = [
                        'status_pergerakan_barang_kes' => ['Dalam siasatan', 'Diserah kepada pemilik', 'Telah dilupuskan', 'Ujian Makmal'],
                        'status_barang_kes_selesai_siasatan' => ['Diserah kepada pemilik', 'Dilupuskan', 'Disimpan sebagai rujukan', 'Dilupuskan ke Perbendaharaan'],
                        'kaedah_pelupusan_barang_kes' => ['Dibakar', 'Ditanam', 'Dihancurkan', 'Dilelong']
                    ];
                    
                    // For these specific fields, check if the value is predefined
                    if ($field_name && isset($predefined_options[$field_name])) {
                        if (in_array($main_value, $predefined_options[$field_name])) {
                            return htmlspecialchars($main_value);
                        } else {
                            // This is a custom value, show the custom text directly
                            $custom_text = $lain_value ?: $main_value;
                            return htmlspecialchars($custom_text);
                        }
                    }
                    
                    // For other cases, check if there's a separate lain_value field
                    if (!empty($lain_value)) {
                        return htmlspecialchars($lain_value);
                    }
                    
                    // Default case
                    return htmlspecialchars($main_value);
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
                            return $json_data;
                        }
                    } else {
                        return $json_data;
                    }
                    
                    if (!is_array($json_data)) return $json_data;
                    
                    $items = [];
                    foreach ($json_data as $key => $value) {
                        if (!empty($value)) {
                            if (is_string($key) && !is_numeric($key)) {
                                $items[] = htmlspecialchars($key . ': ' . $value);
                            } else {
                                $items[] = htmlspecialchars($value);
                            }
                        }
                    }
                    
                    if (empty($items)) return '-';
                    
                    if (count($items) == 1) {
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
                            <dt class="text-sm font-medium text-gray-500">No. Fail LMM/SDR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_fail_lmm_sdr ?? '-' }}</dd>
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
                        
                        <!-- Maklumat Tambahan FAIL L.M.M -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <dt class="text-sm font-medium text-gray-700 mb-2">Maklumat Tambahan FAIL L.M.M</dt>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ADAKAH M/S 2 L.M.M TELAH DISAHKAN OLEH KPD</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_ms_2_lmm_telah_disahkan_oleh_kpd, 'Ya', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ADAKAH L.M.M TELAH DI RUJUK KEPADA YA KORONER SETELAH ADA ARAHAN OLEH YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_lmm_telah_di_rujuk_kepada_ya_koroner, 'Ya', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">KEPUTUSAN YA KORONER</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_ya_koroner ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 2 -->
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
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Sebelum Akhir (C)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Akhir (D)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Semboyan Pemeriksaan JIPS (E)</dt>
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
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit LMM(T) Pertama</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_pertama)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit LMM(T) Kedua</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_kedua)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit LMM(T) Sebelum Akhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit LMM(T) Akhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ optional($paper->tarikh_edaran_minit_fail_lmm_t_akhir)->format('d/m/Y') ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 3 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-50">
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
                            <dt class="text-sm font-medium text-gray-500">Arahan Tuduh Oleh YA TPR Diambil Tindakan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->arahan_tuduh_oleh_ya_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Siasatan TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keputusan_siasatan_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA Koroner</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_siasatan_oleh_ya_koroner ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Oleh YA Koroner</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->ulasan_keputusan_oleh_ya_koroner ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

<!-- BAHAGIAN 4 -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 bg-orange-50">
        <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 4</h3>
    </div>
    <div class="border-t border-gray-200">
        <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Adakah Barang Kes Didaftarkan</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_barang_kes_didaftarkan) !!}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Am</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_barang_kes_am ?? '-' }}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Berharga</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_barang_kes_berharga ?? '-' }}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Am</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->jenis_barang_kes_am ?? '-' }}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Berharga</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->jenis_barang_kes_berharga ?? '-' }}</dd>
            </div>

            <!-- Status Pergerakan Barang Kes -->
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status Pergerakan Barang Kes</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $paper->status_pergerakan_barang_kes ?? '-' }}

                    @if(!empty($paper->status_pergerakan_barang_kes_lain))
                    <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                        <span class="font-semibold"></span> {{ $paper->status_pergerakan_barang_kes_lain }}
                    </div>
                    @endif
                </dd>
            </div>
            
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Ujian Makmal (jika berkaitan)</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->ujian_makmal_details ?? '-' }}</dd>
            </div>

            <!-- Status Barang Kes Selesai Siasatan -->
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status Barang Kes Selesai Siasatan</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $paper->status_barang_kes_selesai_siasatan ?? '-' }}

                    @if($paper->status_barang_kes_selesai_siasatan === 'Dilupuskan ke Perbendaharaan' && !is_null($paper->dilupuskan_perbendaharaan_amount))
                    <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                        <span class="font-semibold">Jumlah:</span> RM {{ number_format($paper->dilupuskan_perbendaharaan_amount, 2) }}
                    </div>
                    @endif

                    @if(!empty($paper->status_barang_kes_selesai_siasatan_lain))
                    <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                        <span class="font-semibold"></span> {{ $paper->status_barang_kes_selesai_siasatan_lain }}
                    </div>
                    @endif
                </dd>
            </div>
            
            <!-- Kaedah Pelupusan Barang Kes -->
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Kaedah Pelupusan Barang Kes</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $paper->kaedah_pelupusan_barang_kes ?? '-' }}

                    @if(!empty($paper->kaedah_pelupusan_barang_kes_lain))
                    <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                        <span class="font-semibold"></span> {{ $paper->kaedah_pelupusan_barang_kes_lain }}
                    </div>
                    @endif
                </dd>
            </div>
            
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Arahan Pelupusan Barang Kes</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_json_list($paper->arahan_pelupusan_barang_kes) !!}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Borang Serah/Terima (Pegawai Tangkapan dan IO/AIO)</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_borang_serah_terima_pegawai_tangkapan_io) !!}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Borang Serah/Terima (Penyiasat/Pemilik/Saksi)</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_borang_serah_terima_penyiasat_pemilik_saksi) !!}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Sijil/Surat Kebenaran IPD</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_sijil_surat_kebenaran_ipd) !!}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Gambar Pelupusan</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_gambar_pelupusan) !!}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Barang Kes)</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_barang_kes ?? '-' }}</dd>
            </div>
        </dl>
    </div>
</div>

            <!-- BAHAGIAN 5 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-pink-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 5</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_id_siasatan_dikemaskini) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Rajah Kasar Tempat Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_rajah_kasar_tempat_kejadian) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Tempat Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_tempat_kejadian) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Post Mortem Mayat Di Hospital</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_post_mortem_mayat_di_hospital) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Am</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_barang_kes_am) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Berharga</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_barang_kes_berharga) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Darah</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_barang_kes_darah) !!}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 6 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-purple-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 6</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Borang PEM</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_json_list($paper->status_pem) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 2</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_with_three_options($paper->status_rj2, $paper->tarikh_rj2, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 2B</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_with_three_options($paper->status_rj2b, $paper->tarikh_rj2b, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 9</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_with_three_options($paper->status_rj9, $paper->tarikh_rj9, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 99</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_with_three_options($paper->status_rj99, $paper->tarikh_rj99, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 10A</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_with_three_options($paper->status_rj10a, $paper->tarikh_rj10a, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 10B</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_with_three_options($paper->status_rj10b, $paper->tarikh_rj10b, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Lain-lain RJ Dikesan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->lain_lain_rj_dikesan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Semboyan Pemakluman ke Kedutaan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Borang)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>            <!-- BAHAGIAN 7 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-indigo-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 7</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <!-- Post Mortem / Bedah Siasat -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Post Mortem / Bedah Siasat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_post_mortem_mayat, $paper->tarikh_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_date_and_keputusan($paper->status_laporan_penuh_bedah_siasat, $paper->tarikh_laporan_penuh_bedah_siasat, $paper->keputusan_laporan_post_mortem, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Jabatan Kimia -->
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Jabatan Kimia</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_jabatan_kimia, $paper->tarikh_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_date_and_keputusan($paper->status_laporan_penuh_jabatan_kimia, $paper->tarikh_laporan_penuh_jabatan_kimia, $paper->keputusan_laporan_jabatan_kimia, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Jabatan Patalogi -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Jabatan Patalogi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_jabatan_patalogi, $paper->tarikh_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_date_and_keputusan($paper->status_laporan_penuh_jabatan_patalogi, $paper->tarikh_laporan_penuh_jabatan_patalogi, $paper->keputusan_laporan_jabatan_patalogi, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Imigresen -->
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Imigresen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan Pengesahan Masuk / Keluar Malaysia</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->permohonan_laporan_pengesahan_masuk_keluar_malaysia, $paper->tarikh_permohonan_laporan_imigresen, 'Ada / Cipta', 'Tiada / Tidak Cipta') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_imigresen, $paper->tarikh_laporan_penuh_imigresen, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                            
                            <!-- Additional Imigresen Fields -->
                            <div class="mt-4 space-y-3 border-t pt-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan Permit Kerja di Malaysia</dt>
                                    <dd class="text-sm text-gray-900">{!! show_boolean_badge($paper->permohonan_laporan_permit_kerja_di_malaysia, 'Ada', 'Tiada') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan Agensi Pekerjaan di Malaysia</dt>
                                    <dd class="text-sm text-gray-900">{!! show_boolean_badge($paper->permohonan_laporan_agensi_pekerjaan_di_malaysia, 'Ada', 'Tiada') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Status Kewarganegaraan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_boolean_badge($paper->permohonan_status_kewarganegaraan, 'Ada', 'Tiada') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Lain-lain -->
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                            <dt class="text-sm font-medium text-gray-500">Lain-lain Permohonan Laporan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->lain_lain_permohonan_laporan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 8 -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-red-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 8</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">M/S 4 - Barang Kes Ditulis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">M/S 4 - Dengan Arahan TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_barang_kes_arahan_tpr) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">M/S 4 - Keputusan Kes Dicatat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_muka_surat_4_keputusan_kes_dicatat) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Fail LMM Telah Ada Keputusan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">KS Telah di KUS/FAIL</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_ks_kus_fail_selesai ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_json_list($paper->keputusan_akhir_mahkamah) !!}</dd>
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
