<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Trafik (Seksyen) ({{ $paper->no_kst }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'TrafikSeksyenPaper', 'id' => $paper->id]) }}" class="space-y-6 bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                {{-- Non-Editable Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
                    <div><span class="font-semibold">No. KS:</span> {{ $paper->no_ks }}</div>
                    <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $paper->pegawai_penyiasat }}</div>
                    <div><span class="font-semibold">Seksyen:</span> {{ $paper->seksyen }}</div>
                    <div><span class="font-semibold">Tarikh Daftar:</span> {{ optional($paper->tarikh_daftar)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <hr>

                {{-- Main Details --}}
                <h2 class="text-lg font-semibold border-b pb-1">Maklumat Utama</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="no_saman" class="block text-sm font-medium text-gray-700">No Saman</label>
                        <input type="text" name="no_saman" id="no_saman" value="{{ old('no_saman', $paper->no_saman) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pegawai_pemeriksa_jips" class="block text-sm font-medium text-gray-700">Pegawai Pemeriksa (JIPS)</label>
                        <input type="text" name="pegawai_pemeriksa_jips" id="pegawai_pemeriksa_jips" value="{{ old('pegawai_pemeriksa_jips', $paper->pegawai_pemeriksa_jips) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_minit_pertama" class="block text-sm font-medium text-gray-700">Tarikh Edaran Pertama</label>
                        <input type="date" name="tarikh_minit_pertama" id="tarikh_minit_pertama" value="{{ old('tarikh_minit_pertama', optional($paper->tarikh_minit_pertama)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_minit_akhir" class="block text-sm font-medium text-gray-700">Tarikh Edaran Akhir</label>
                        <input type="date" name="tarikh_minit_akhir" id="tarikh_minit_akhir" value="{{ old('tarikh_minit_akhir', optional($paper->tarikh_minit_akhir)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>

                {{-- Status & Findings --}}
                <h2 class="text-lg font-semibold border-b pb-1">Status & Penemuan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="lewat_edaran_pertama_48_jam" class="block text-sm font-medium text-gray-700">Lewat Edaran Pertama (>48 Jam)</label>
                        <select name="lewat_edaran_pertama_48_jam" id="lewat_edaran_pertama_48_jam" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('lewat_edaran_pertama_48_jam', $paper->lewat_edaran_pertama_48_jam) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('lewat_edaran_pertama_48_jam', $paper->lewat_edaran_pertama_48_jam) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                    <div>
                        <label for="tiada_gambar_tempat_kejadian" class="block text-sm font-medium text-gray-700">Tiada Gambar Tempat Kejadian</label>
                        <select name="tiada_gambar_tempat_kejadian" id="tiada_gambar_tempat_kejadian" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('tiada_gambar_tempat_kejadian', $paper->tiada_gambar_tempat_kejadian) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('tiada_gambar_tempat_kejadian', $paper->tiada_gambar_tempat_kejadian) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                    <div>
                        <label for="keputusan_kes_rule" class="block text-sm font-medium text-gray-700">Keputusan Kes (Rule)</label>
                        <input type="text" name="keputusan_kes_rule" id="keputusan_kes_rule" value="{{ old('keputusan_kes_rule', $paper->keputusan_kes_rule) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="no_sdr_sek_41" class="block text-sm font-medium text-gray-700">No SDR (Sek 41(1) APJ)</label>
                        <input type="text" name="no_sdr_sek_41" id="no_sdr_sek_41" value="{{ old('no_sdr_sek_41', $paper->no_sdr_sek_41) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="ms_2_fail_lmm" class="block text-sm font-medium text-gray-700">MS 2 Fail LMM (T.T KPD)</label>
                        <select name="ms_2_fail_lmm" id="ms_2_fail_lmm" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="ADA" {{ old('ms_2_fail_lmm', $paper->ms_2_fail_lmm) == 'ADA' ? 'selected' : '' }}>ADA</option>
                            <option value="TIADA" {{ old('ms_2_fail_lmm', $paper->ms_2_fail_lmm) == 'TIADA' ? 'selected' : '' }}>TIADA</option>
                        </select>
                    </div>
                    <div>
                        <label for="rajah_kasar" class="block text-sm font-medium text-gray-700">Rajah Kasar</label>
                        <select name="rajah_kasar" id="rajah_kasar" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="ADA" {{ old('rajah_kasar', $paper->rajah_kasar) == 'ADA' ? 'selected' : '' }}>ADA</option>
                            <option value="TIADA" {{ old('rajah_kasar', $paper->rajah_kasar) == 'TIADA' ? 'selected' : '' }}>TIADA</option>
                        </select>
                    </div>
                    <div>
                        <label for="terbengkalai_tb" class="block text-sm font-medium text-gray-700">Terbengkalai (TB)</label>
                        <select name="terbengkalai_tb" id="terbengkalai_tb" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="TERBENGKALAI" {{ old('terbengkalai_tb', $paper->terbengkalai_tb) == 'TERBENGKALAI' ? 'selected' : '' }}>TERBENGKALAI</option>
                            <option value="TIDAK" {{ old('terbengkalai_tb', $paper->terbengkalai_tb) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                </div>
                <hr>

                {{-- Laporan Pakar --}}
                <h2 class="text-lg font-semibold border-b pb-1">Laporan Pakar</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="tarikh_hantar_puspakom" class="block text-sm font-medium text-gray-700">Tarikh Hantar PUSPAKOM</label>
                        <input type="date" name="tarikh_hantar_puspakom" id="tarikh_hantar_puspakom" value="{{ old('tarikh_hantar_puspakom', optional($paper->tarikh_hantar_puspakom)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_hantar_patalogi" class="block text-sm font-medium text-gray-700">Tarikh Hantar Patalogi</label>
                        <input type="date" name="tarikh_hantar_patalogi" id="tarikh_hantar_patalogi" value="{{ old('tarikh_hantar_patalogi', optional($paper->tarikh_hantar_patalogi)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_hantar_kimia" class="block text-sm font-medium text-gray-700">Tarikh Hantar Kimia</label>
                        <input type="date" name="tarikh_hantar_kimia" id="tarikh_hantar_kimia" value="{{ old('tarikh_hantar_kimia', optional($paper->tarikh_hantar_kimia)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_terima_laporan_pakar" class="block text-sm font-medium text-gray-700">Tarikh Terima Laporan Pakar</label>
                        <input type="date" name="tarikh_terima_laporan_pakar" id="tarikh_terima_laporan_pakar" value="{{ old('tarikh_terima_laporan_pakar', optional($paper->tarikh_terima_laporan_pakar)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="slip_eba_sek_45a" class="block text-sm font-medium text-gray-700">Slip EBA (Sek 45A)</label>
                        <input type="text" name="slip_eba_sek_45a" id="slip_eba_sek_45a" value="{{ old('slip_eba_sek_45a', $paper->slip_eba_sek_45a) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>

                {{-- Arahan & Ulasan --}}
                <h2 class="text-lg font-semibold border-b pb-1">Arahan & Ulasan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                    <div>
                        <label for="arahan_tpr" class="block text-sm font-medium text-gray-700">Arahan TPR</label>
                        <input type="text" name="arahan_tpr" id="arahan_tpr" value="{{ old('arahan_tpr', $paper->arahan_tpr) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="kst_rujuk_tpr" class="block text-sm font-medium text-gray-700">KST Rujuk TPR</label>
                        <select name="kst_rujuk_tpr" id="kst_rujuk_tpr" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('kst_rujuk_tpr', $paper->kst_rujuk_tpr) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('kst_rujuk_tpr', $paper->kst_rujuk_tpr) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                    <div class="col-span-full">
                        <label for="ulasan_pemeriksa" class="block text-sm font-medium text-gray-700">Ulasan Pemeriksa</label>
                        <textarea name="ulasan_pemeriksa" id="ulasan_pemeriksa" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_pemeriksa', $paper->ulasan_pemeriksa) }}</textarea>
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
            @apply rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
        }
        .form-radio {
            @apply rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50;
        }
        .form-textarea { @apply resize-vertical; }
    </style>
</x-app-layout>