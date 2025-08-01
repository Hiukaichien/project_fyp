<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: Orang Hilang
            </h2>
            <div>
                <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    ← Kembali ke Projek
                </a>
                <a href="{{ route('kertas_siasatan.edit', ['paperType' => 'OrangHilang', 'id' => $paper->id]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 3: Arahan & Keputusan -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 3: Arahan & Keputusan</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh SIO</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->arahan_minit_oleh_sio_status ? 'Ada' : 'Tiada' }} 
                                @if($paper->arahan_minit_oleh_sio_tarikh)
                                    ({{ optional($paper->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh Ketua Bahagian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->arahan_minit_ketua_bahagian_status ? 'Ada' : 'Tiada' }}
                                @if($paper->arahan_minit_ketua_bahagian_tarikh)
                                    ({{ optional($paper->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh Ketua Jabatan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->arahan_minit_ketua_jabatan_status ? 'Ada' : 'Tiada' }}
                                @if($paper->arahan_minit_ketua_jabatan_tarikh)
                                    ({{ optional($paper->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Minit Oleh YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->arahan_minit_oleh_ya_tpr_status ? 'Ada' : 'Tiada' }}
                                @if($paper->arahan_minit_oleh_ya_tpr_tarikh)
                                    ({{ optional($paper->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Siasatan Oleh YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_siasatan_oleh_ya_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Arahan Tuduh Oleh YA TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->arahan_tuduh_oleh_ya_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keputusan Siasatan TPR</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->ulasan_keputusan_siasatan_tpr ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 4: Barang Kes -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-orange-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 4: Barang Kes</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adakah Barang Kes Didaftarkan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_barang_kes_didaftarkan ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Am</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_barang_kes_am ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">No. Daftar Barang Kes Berharga</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->no_daftar_barang_kes_berharga ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 5: Dokumen Siasatan -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-pink-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 5: Dokumen Siasatan</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ID Siasatan Dikemaskini</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->status_id_siasatan_dikemaskini ? 'Ada' : 'Tiada' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Rajah Kasar Tempat Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->status_rajah_kasar_tempat_kejadian ? 'Ada' : 'Tiada' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Tempat Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->status_gambar_tempat_kejadian ? 'Ada' : 'Tiada' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Am</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->status_gambar_barang_kes_am ? 'Ada' : 'Tiada' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Barang Kes Berharga</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->status_gambar_barang_kes_berharga ? 'Ada' : 'Tiada' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gambar Orang Hilang</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->status_gambar_orang_hilang ? 'Ada' : 'Tiada' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 6: Borang & Semakan -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-purple-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 6: Borang & Semakan</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Borang PEM</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($paper->status_pem)
                                    {{ is_array($paper->status_pem) ? implode(', ', $paper->status_pem) : $paper->status_pem }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">MPS 1</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->status_mps1 ? 'Cipta' : 'Tidak Cipta' }}
                                @if($paper->tarikh_mps1)
                                    ({{ optional($paper->tarikh_mps1)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">MPS 2</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->status_mps2 ? 'Cipta' : 'Tidak Cipta' }}
                                @if($paper->tarikh_mps2)
                                    ({{ optional($paper->tarikh_mps2)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Pemakluman NUR-Alert JSJ (Bawah 18 Tahun)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->pemakluman_nur_alert_jsj_bawah_18_tahun ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Rakaman Percakapan Orang Hilang</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->rakaman_percakapan_orang_hilang ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Laporan Polis Orang Hilang Dijumpai</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->laporan_polis_orang_hilang_dijumpai ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Hebahan Media Massa</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->hebahan_media_massa ? 'Dibuat' : 'Tidak Dibuat' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Orang Hilang Dijumpai (Mati Mengejut Bukan Jenayah)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->orang_hilang_dijumpai_mati_mengejut_bukan_jenayah ? 'Ya' : 'Tidak' }}
                                @if($paper->alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah)
                                    <br><span class="text-sm text-gray-600">Alasan: {{ $paper->alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Orang Hilang Dijumpai (Mati Mengejut Jenayah)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->orang_hilang_dijumpai_mati_mengejut_jenayah ? 'Ya' : 'Tidak' }}
                                @if($paper->alasan_orang_hilang_dijumpai_mati_mengejut_jenayah)
                                    <br><span class="text-sm text-gray-600">Alasan: {{ $paper->alasan_orang_hilang_dijumpai_mati_mengejut_jenayah }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Semboyan Pemakluman ke Kedutaan (Bukan Warganegara)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->semboyan_pemakluman_ke_kedutaan_bukan_warganegara ? 'Dibuat' : 'Tidak Dibuat' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Borang)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_borang ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 7: Permohonan Laporan Agensi Luar -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-indigo-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 7: Permohonan Laporan Agensi Luar</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Permohonan Laporan Imigresen</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->status_permohonan_laporan_imigresen ? 'Ada' : 'Tiada' }}
                                @if($paper->tarikh_permohonan_laporan_imigresen)
                                    ({{ optional($paper->tarikh_permohonan_laporan_imigresen)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Laporan Penuh Imigresen</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $paper->status_laporan_penuh_imigresen ? 'Ada' : 'Tiada' }}
                                @if($paper->tarikh_laporan_penuh_imigresen)
                                    ({{ optional($paper->tarikh_laporan_penuh_imigresen)->format('d/m/Y') }})
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- BAHAGIAN 8: Status Fail -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-red-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">BAHAGIAN 8: Status Fail</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">M/S 4 - Keputusan Kes Dicatat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_muka_surat_4_keputusan_kes_dicatat ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">KS Telah di KUS/FAIL</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->adakah_ks_kus_fail_selesai ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keputusan Akhir Mahkamah</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->keputusan_akhir_mahkamah ?? '-' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan Pegawai Pemeriksa (Fail)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->ulasan_keseluruhan_pegawai_pemeriksa_fail ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Status Terkira -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Status Terkira</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-yellow-50 px-4 py-5 grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-yellow-800">Status Lewat Edaran (>24 Jam)</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">{{ $paper->lewat_edaran_status ?? 'TIDAK DIKIRA' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-800">Status Terbengkalai (>3 Bulan)</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">{{ $paper->terbengkalai_status ?? 'TIDAK DIKIRA' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-800">Status Kemaskini</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">{{ $paper->baru_kemaskini_status ?? 'TIDAK DIKIRA' }}</dd>
                            </div>
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