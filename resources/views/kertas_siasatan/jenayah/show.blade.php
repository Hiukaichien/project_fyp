<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Jenayah
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'Jenayah', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
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
                    $booleanValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    return $booleanValue ? "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>{$trueText}</span>" : "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>{$falseText}</span>";
                }
                
                function show_status_and_date($status, $date = null, $trueText = 'Ada', $falseText = 'Tiada') {
                    $booleanStatus = filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    
                    $status_html = show_boolean_badge($booleanStatus, $trueText, $falseText);
                    $date_html = $booleanStatus && $date ? (is_string($date) ? $date : optional($date)->format('d/m/Y')) : '-';
                    return "{$status_html} | Tarikh: {$date_html}";
                }
                
                function show_json_list($json_data) {
                    if (empty($json_data)) return '-';
                    
                    if (is_array($json_data)) {
                        // Already an array
                    } elseif (is_string($json_data)) {
                        $decoded = json_decode($json_data, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $json_data = $decoded;
                        } else {
                            return htmlspecialchars($json_data);
                        }
                    } else {
                        return htmlspecialchars((string) $json_data);
                    }
                    
                    if (!is_array($json_data)) return htmlspecialchars((string) $json_data);
                    
                    $items = array_filter($json_data, fn($value) => !empty($value) || $value === 0 || $value === false);
                    
                    if (empty($items)) return '-';
                    
                    if (count($items) == 1 && !str_contains(reset($items), ':')) {
                        return reset($items);
                    }
                    
                    $html = '<ul class="list-disc list-inside space-y-1">';
                    foreach ($items as $item) {
                        $html .= "<li>" . htmlspecialchars($item) . "</li>";
                    }
                    $html .= '</ul>';
                    return $html;
                }
            @endphp

            <!-- BAHAGIAN 1: Maklumat Asas -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-blue-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        BAHAGIAN 1: Maklumat Asas (No. KS: {{ $paper->no_kertas_siasatan }})
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
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

            <!-- BAHAGIAN 2: Pemeriksaan & Status -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-green-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 2: Pemeriksaan & Status</h3>
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
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Edaran Minit KS Sebelum Akhir (C)</dt>
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
                    </dl>
                </div>
                <!-- Status Terkira -->
                <div class="px-4 py-5 sm:px-6 bg-yellow-50 border-t border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Status Terkira</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-yellow-800">Status Lewat Edaran (>24 Jam)</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->lewat_edaran_status ?? 'Tidak Terkira' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-yellow-800">Status Terbengkalai (>3 Bulan)</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->terbengkalai_status ?? 'Tidak Terkira' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-yellow-800">Status Kemaskini</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->baru_dikemaskini_status ?? 'Tidak Terkira' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 3: Arahan & Keputusan -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-orange-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 3: Arahan & Keputusan</h3>
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

            <!-- BAHAGIAN 4: Barang Kes -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-pink-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 4: Barang Kes</h3>
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
                            <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Kenderaan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_barang_kes_kenderaan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Daftar Botol Spesimen Urin</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_botol_spesimen_urin ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Daftar Spesimen Darah</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_spesimen_darah ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Daftar Kontraban</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_kontraban ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Am</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->jenis_barang_kes_am ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Berharga</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->jenis_barang_kes_berharga ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Kenderaan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->jenis_barang_kes_kenderaan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Jenis Barang Kes Kontraban</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->jenis_barang_kes_kontraban ?? '-' }}</dd>
                        </div>

                        <!-- Status Pergerakan Barang Kes -->
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Status Pergerakan Barang Kes</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->status_pergerakan_barang_kes ?? '-' }}

                                @if($paper->status_pergerakan_barang_kes === 'Ujian Makmal' && !empty($paper->status_pergerakan_barang_kes_makmal))
                                <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                                    <span class="font-semibold">Nyatakan:</span> {{ $paper->status_pergerakan_barang_kes_makmal }}
                                </div>
                                @elseif($paper->status_pergerakan_barang_kes === 'Lain-Lain' && !empty($paper->status_pergerakan_barang_kes_lain))
                                <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                                    <span class="font-semibold">Nyatakan:</span> {{ $paper->status_pergerakan_barang_kes_lain }}
                                </div>
                                @endif
                            </dd>
                        </div>

                        <!-- Status Barang Kes Selesai Siasatan -->
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Status Barang Kes Selesai Siasatan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->status_barang_kes_selesai_siasatan ?? '-' }}

                                @if($paper->status_barang_kes_selesai_siasatan === 'Dilupuskan ke Perbendaharaan' && !empty($paper->status_barang_kes_selesai_siasatan_RM))
                                <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                                    <span class="font-semibold">RM:</span> {{ $paper->status_barang_kes_selesai_siasatan_RM }}
                                </div>
                                @elseif($paper->status_barang_kes_selesai_siasatan === 'Lain-Lain' && !empty($paper->status_barang_kes_selesai_siasatan_lain))
                                <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                                    <span class="font-semibold">Nyatakan:</span> {{ $paper->status_barang_kes_selesai_siasatan_lain }}
                                </div>
                                @endif
                            </dd>
                        </div>

                        <!-- Kaedah Pelupusan Dilaksanakan -->
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Kaedah Pelupusan Dilaksanakan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan ?? '-' }}
                                @if($paper->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan === 'Lain-Lain' && !empty($paper->kaedah_pelupusan_barang_kes_lain))
                                <div class="text-xs text-gray-600 mt-1 pl-2 border-l-2 border-gray-300">
                                    <span class="font-semibold">Nyatakan:</span> {{ $paper->kaedah_pelupusan_barang_kes_lain }}
                                </div>
                                @endif
                            </dd>
                        </div>

                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Pelupusan Dengan Arahan Mahkamah / YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Resit Kew.38e Bagi Pelupusan Wang Tunai</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Borang Serah/Terima (Pegawai Tangkapan & IO/AIO)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_borang_serah_terima_pegawai_tangkapan ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Borang Serah/Terima (Penyiasat, Pemilik, Saksi)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Sijil / Surat Arahan Pelupusan Oleh IPD</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Pelupusan Dilampirkan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Barang Kes)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_barang_kes ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 5: Dokumen Siasatan -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-purple-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 5: Dokumen Siasatan</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini') !!}</dd>
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
                            <dt class="text-sm font-medium text-gray-500">Gambar Post-Mortem Mayat Di Hospital</dt>
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
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Kenderaan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_barang_kes_kenderaan) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Darah</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_barang_kes_darah) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Kontraban</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_gambar_barang_kes_kontraban) !!}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 6: Borang & Semakan -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-indigo-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 6: Borang & Semakan</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Borang PEM</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_json_list($paper->status_pem) !!}</dd>
                        </div>

                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 2</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_rj2, $paper->tarikh_rj2, 'Cipta', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 2B</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_rj2b, $paper->tarikh_rj2b, 'Cipta', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 9</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_rj9, $paper->tarikh_rj9, 'Cipta', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 99</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_rj99, $paper->tarikh_rj99, 'Cipta', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">RJ 10A</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_rj10a, $paper->tarikh_rj10a, 'Cipta', 'Tidak') !!}</dd>
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
                            <dt class="text-sm font-medium text-gray-500">Semboyan Usaha Pemakluman Pertama Wanted Person</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_semboyan_pertama_wanted_person, $paper->tarikh_semboyan_pertama_wanted_person, 'Dibuat', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Semboyan Usaha Pemakluman Kedua Wanted Person</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_semboyan_kedua_wanted_person, $paper->tarikh_semboyan_kedua_wanted_person, 'Dibuat', 'Tidak') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Semboyan Usaha Pemakluman Ketiga Wanted Person</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_status_and_date($paper->status_semboyan_ketiga_wanted_person, $paper->tarikh_semboyan_ketiga_wanted_person, 'Dibuat', 'Tidak') !!}</dd>
                        </div>
                        
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adakah Penandaan Kelas Warna Pada Kulit Kertas Siasatan Dibuat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_penandaan_kelas_warna) !!}</dd>
                        </div>
                        
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Borang)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 7: Permohonan Laporan Agensi Luar -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-red-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 7: Permohonan Laporan Agensi Luar</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <!-- Pakar Judi -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Pakar Judi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_pakar_judi, $paper->tarikh_permohonan_laporan_pakar_judi, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_pakar_judi, $paper->tarikh_laporan_penuh_pakar_judi, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Permohonan Laporan Post Mortem Mayat -->
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Permohonan Laporan Post Mortem Mayat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Status Permohonan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_post_mortem_mayat, $paper->tarikh_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Bedah Siasat -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Bedah Siasat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_bedah_siasat, $paper->tarikh_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak') !!}</dd>
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
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_jabatan_kimia, $paper->tarikh_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                                <div class="col-span-full">
                                    <dt class="text-sm font-medium text-gray-500">Keputusan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{{ $paper->keputusan_laporan_jabatan_kimia ?? '-' }}</dd>
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
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_jabatan_patalogi, $paper->tarikh_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                                <div class="col-span-full">
                                    <dt class="text-sm font-medium text-gray-500">Keputusan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{{ $paper->keputusan_laporan_jabatan_patalogi ?? '-' }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- PUSPAKOM -->
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">PUSPAKOM</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_puspakom, $paper->tarikh_permohonan_laporan_puspakom, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_puspakom, $paper->tarikh_laporan_penuh_puspakom, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- JPJ -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">JPJ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_jpj, $paper->tarikh_permohonan_laporan_jpj, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_jpj, $paper->tarikh_laporan_penuh_jpj, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Imigresen -->
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Imigresen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_imigresen, $paper->tarikh_permohonan_laporan_imigresen, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_imigresen, $paper->tarikh_laporan_penuh_imigresen, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kastam -->
                        <div class="py-3 sm:py-4 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Kastam</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_kastam, $paper->tarikh_permohonan_laporan_kastam, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_kastam, $paper->tarikh_laporan_penuh_kastam, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Forensik PDRM -->
                        <div class="py-3 sm:py-4 sm:px-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Forensik PDRM</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Permohonan Laporan</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_permohonan_laporan_forensik_pdrm, $paper->tarikh_permohonan_laporan_forensik_pdrm, 'Dibuat', 'Tidak') !!}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Laporan Penuh Diterima</dt>
                                    <dd class="text-sm text-gray-900">{!! show_status_and_date($paper->status_laporan_penuh_forensik_pdrm, $paper->tarikh_laporan_penuh_forensik_pdrm, 'Diterima', 'Tidak') !!}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Lain-lain Permohonan Laporan -->
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                            <dt class="text-sm font-medium text-gray-500">Lain-lain Permohonan Laporan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->lain_lain_permohonan_laporan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 8: Status Fail -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 8: Status Fail</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Muka Surat 4 - Barang Kes Ditulis Bersama No Daftar</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->muka_surat_4_barang_kes_ditulis) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Muka Surat 4 - Dengan Arahan TPR Untuk Pelupusan/Serahan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->muka_surat_4_dengan_arahan_tpr) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Muka Surat 4 - Keputusan Kes Dicatat Selengkapnya</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->muka_surat_4_keputusan_kes_dicatat) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Fail L.M.M Ada Keputusan Siasatan Oleh YA Koroner</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->fail_lmm_ada_keputusan_koroner) !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Status KS di KUS/FAIL</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! show_boolean_badge($paper->status_kus_fail, 'Ada', 'Tiada') !!}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah Sebelum KS di KUS/FAIL</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_akhir_mahkamah ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $paper->ulasan_pegawai_pemeriksa_fail ?? '-' }}</dd>
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
                                    <span class="text-gray-500 text-xs">({{ $paper->created_at->locale('ms')->diffForHumans() }})</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tarikh Kemaskini Terakhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ optional($paper->updated_at)->format('d/m/Y H:i:s') }}
                                @if($paper->updated_at)
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