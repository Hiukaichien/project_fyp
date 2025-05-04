{{-- resources/views/kertas_siasatan/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Butiran Kertas Siasatan: {{ $kertasSiasatan->no_ks }}
            </h2>
            <div>
                <a href="{{ route('kertas_siasatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm mr-2">
                    Kembali ke Senarai
                </a>
                <a href="{{ route('kertas_siasatan.edit', $kertasSiasatan->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Audit / Kemaskini
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12"> {{-- Add padding --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> {{-- Add container --}}
            {{-- Remove the old header section as it's now in the slot --}}
            {{-- <div class="flex justify-between items-center mb-6"> ... </div> --}}

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Maklumat Asas</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Maklumat awal kertas siasatan.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        {{-- Helper function to display data --}}
                        @php
                            function display_field($label, $value, $isDate = false) {
                                $displayValue = $value ?? '<span class="text-gray-400 italic">Tiada Data</span>';
                                if ($isDate && $value instanceof \Carbon\Carbon) {
                                    $displayValue = $value->format('d/m/Y');
                                } elseif ($isDate && !empty($value) && !$value instanceof \Carbon\Carbon) {
                                     // Attempt to parse if it's a date string but not Carbon object already
                                    try {
                                        $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y');
                                    } catch (\Exception $e) {
                                        $displayValue = $value; // Show original if parsing fails
                                    }
                                } elseif (empty($value)) {
                                    $displayValue = '<span class="text-gray-400 italic">Tiada Data</span>';
                                }

                                echo '<div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">';
                                echo '<dt class="text-sm font-medium text-gray-500">' . e($label) . '</dt>';
                                echo '<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">' . $displayValue . '</dd>';
                                echo '</div>';
                            }
                        @endphp

                        {!! display_field('No. Kertas Siasatan', $kertasSiasatan->no_ks) !!}
                        {!! display_field('Tarikh KS', $kertasSiasatan->tarikh_ks, true) !!}
                        {!! display_field('No. Repot', $kertasSiasatan->no_report) !!}
                        {!! display_field('Jenis Jabatan / KS', $kertasSiasatan->jenis_jabatan_ks) !!}
                        {!! display_field('Pegawai Penyiasat', $kertasSiasatan->pegawai_penyiasat) !!}
                        {!! display_field('Status KS', $kertasSiasatan->status_ks) !!}
                        {!! display_field('Status Kes', $kertasSiasatan->status_kes) !!}
                        {!! display_field('Seksyen', $kertasSiasatan->seksyen) !!}
                    </dl>
                </div>

                {{-- Minit Edaran --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Minit Edaran & Status</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                     <dl class="sm:divide-y sm:divide-gray-200">
                        {!! display_field('Tarikh Edaran Minit Pertama (A)', $kertasSiasatan->tarikh_minit_a, true) !!}
                        {!! display_field('Tarikh Edaran Minit Kedua (B)', $kertasSiasatan->tarikh_minit_b, true) !!}
                        {!! display_field('Tarikh Edaran Sebelum Minit Terakhir (C)', $kertasSiasatan->tarikh_minit_c, true) !!}
                        {!! display_field('Tarikh Edaran Minit Terakhir (D)', $kertasSiasatan->tarikh_minit_d, true) !!}
                        {!! display_field('Status: Edaran > 24 Jam', $kertasSiasatan->edar_lebih_24_jam_status) !!}
                        {!! display_field('Status: Terbengkalai 3 Bulan', $kertasSiasatan->terbengkalai_3_bulan_status) !!}
                        {!! display_field('Status: Baru Kemaskini', $kertasSiasatan->baru_kemaskini_status) !!}
                     </dl>
                </div>

                {{-- Status Semasa Diperiksa --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Status Semasa Diperiksa</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                     <dl class="sm:divide-y sm:divide-gray-200">
                        {!! display_field('Status Dipilih', $kertasSiasatan->status_ks_semasa_diperiksa) !!}
                        {!! display_field('Tarikh Status', $kertasSiasatan->tarikh_status_ks_semasa_diperiksa, true) !!}
                     </dl>
                </div>

                {{-- Add similar sections for ALL other groups of fields from your CSV: --}}
                {{-- Rakaman Percakapan --}}
                {{-- ID Siasatan Lampiran --}}
                {{-- Barang Kes --}}
                {{-- Pakar Judi / Forensik --}}
                {{-- Dokumen Lain --}}
                {{-- RJ Forms --}}
                {{-- Surat Pemberitahuan --}}
                {{-- Isu-Isu --}}
                {{-- KS Telah Dihantar Ke --}}
                {{-- Ulasan Pemeriksa --}}

                {{-- Example for Ulasan Pemeriksa --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Ulasan Pemeriksa</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                     <dl class="sm:divide-y sm:divide-gray-200">
                         <div class="py-3 sm:py-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Isu Menarik</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $kertasSiasatan->ulasan_isu_menarik ?? '-' }}</dd>
                         </div>
                          <div class="py-3 sm:py-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ulasan Keseluruhan</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $kertasSiasatan->ulasan_keseluruhan ?? '-' }}</dd>
                         </div>
                     </dl>
                </div>

            </div> {{-- Close bg-white div --}}
        </div> {{-- Close container div --}}
    </div> {{-- Close padding div --}}
</x-app-layout>