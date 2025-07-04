<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Laporan Mati Mengejut ({{ $paper->no_lmm }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'LaporanMatiMengejutPaper', 'id' => $paper->id]) }}" class="space-y-6 bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                {{-- Non-Editable Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
                    <div><span class="font-semibold">No SDR/LLM:</span> {{ $paper->no_lmm }}</div>
                    <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $paper->io_aio }}</div>
                    <div><span class="font-semibold">No Laporan Polis:</span> {{ $paper->no_repot_polis }}</div>
                    <div><span class="font-semibold">Tarikh Laporan Polis:</span> {{ optional($paper->tarikh_laporan_polis)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <hr>

                {{-- Main Details --}}
                <h2 class="text-lg font-semibold border-b pb-1">Maklumat Utama</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="pegawai_pemeriksa_jips" class="block text-sm font-medium text-gray-700">Pegawai Pemeriksa (JIPS)</label>
                        <input type="text" name="pegawai_pemeriksa_jips" id="pegawai_pemeriksa_jips" value="{{ old('pegawai_pemeriksa_jips', $paper->pegawai_pemeriksa_jips) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_minit_a" class="block text-sm font-medium text-gray-700">Tarikh Edaran Pertama</label>
                        <input type="date" name="tarikh_minit_a" id="tarikh_minit_a" value="{{ old('tarikh_minit_a', optional($paper->tarikh_minit_a)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_minit_d" class="block text-sm font-medium text-gray-700">Tarikh Edaran Akhir</label>
                        <input type="date" name="tarikh_minit_d" id="tarikh_minit_d" value="{{ old('tarikh_minit_d', optional($paper->tarikh_minit_d)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pem_1_2_3_4" class="block text-sm font-medium text-gray-700">Pem 1/2/3/4</label>
                        <input type="text" name="pem_1_2_3_4" id="pem_1_2_3_4" value="{{ old('pem_1_2_3_4', $paper->pem_1_2_3_4) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="terbengkalai_tb" class="block text-sm font-medium text-gray-700">Terbengkalai (TB)</label>
                        <select name="terbengkalai_tb" id="terbengkalai_tb" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('terbengkalai_tb', $paper->terbengkalai_tb) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('terbengkalai_tb', $paper->terbengkalai_tb) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                </div>
                <hr>

                {{-- Post-Mortem & Status --}}
                <h2 class="text-lg font-semibold border-b pb-1">Post-Mortem & Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="tarikh_permohonan_pm_dipohon" class="block text-sm font-medium text-gray-700">Tarikh Permohonan P/Mortem</label>
                        <input type="date" name="tarikh_permohonan_pm_dipohon" id="tarikh_permohonan_pm_dipohon" value="{{ old('tarikh_permohonan_pm_dipohon', optional($paper->tarikh_permohonan_pm_dipohon)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="laporan_pm_diterima_status" class="block text-sm font-medium text-gray-700">Laporan P/Mortem (Diterima/Tidak Diterima/Follow Up)</label>
                        <input type="text" name="laporan_pm_diterima_status" id="laporan_pm_diterima_status" value="{{ old('laporan_pm_diterima_status', $paper->laporan_pm_diterima_status) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="status_sdr" class="block text-sm font-medium text-gray-700">Status SDR</label>
                        <input type="text" name="status_sdr" id="status_sdr" value="{{ old('status_sdr', $paper->status_sdr) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="gambar_post_mortem" class="block text-sm font-medium text-gray-700">Gambar Post-Mortem</label>
                        <select name="gambar_post_mortem" id="gambar_post_mortem" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="ADA" {{ old('gambar_post_mortem', $paper->gambar_post_mortem) == 'ADA' ? 'selected' : '' }}>ADA</option>
                            <option value="TIADA" {{ old('gambar_post_mortem', $paper->gambar_post_mortem) == 'TIADA' ? 'selected' : '' }}>TIADA</option>
                        </select>
                    </div>
                    <div>
                        <label for="gambar_tempat_kejadian" class="block text-sm font-medium text-gray-700">Gambar Tempat Kejadian</label>
                         <select name="gambar_tempat_kejadian" id="gambar_tempat_kejadian" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="ADA" {{ old('gambar_tempat_kejadian', $paper->gambar_tempat_kejadian) == 'ADA' ? 'selected' : '' }}>ADA</option>
                            <option value="TIADA" {{ old('gambar_tempat_kejadian', $paper->gambar_tempat_kejadian) == 'TIADA' ? 'selected' : '' }}>TIADA</option>
                        </select>
                    </div>
                    <div>
                        <label for="tandatangan_kpd_ms2_sdr" class="block text-sm font-medium text-gray-700">Tandatangan KPD di M/S 2 SDR</label>
                         <select name="tandatangan_kpd_ms2_sdr" id="tandatangan_kpd_ms2_sdr" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="ADA" {{ old('tandatangan_kpd_ms2_sdr', $paper->tandatangan_kpd_ms2_sdr) == 'ADA' ? 'selected' : '' }}>ADA</option>
                            <option value="TIADA" {{ old('tandatangan_kpd_ms2_sdr', $paper->tandatangan_kpd_ms2_sdr) == 'TIADA' ? 'selected' : '' }}>TIADA</option>
                        </select>
                    </div>
                </div>
                <hr>

                {{-- Rujukan & Arahan --}}
                <h2 class="text-lg font-semibold border-b pb-1">Rujukan & Arahan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="tarikh_rujuk_tpr" class="block text-sm font-medium text-gray-700">Tarikh Rujuk TPR</label>
                        <input type="date" name="tarikh_rujuk_tpr" id="tarikh_rujuk_tpr" value="{{ old('tarikh_rujuk_tpr', optional($paper->tarikh_rujuk_tpr)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="arahan_tpr" class="block text-sm font-medium text-gray-700">Arahan TPR</label>
                        <input type="text" name="arahan_tpr" id="arahan_tpr" value="{{ old('arahan_tpr', $paper->arahan_tpr) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_rujuk_koroner" class="block text-sm font-medium text-gray-700">Tarikh Rujuk Koroner</label>
                        <input type="date" name="tarikh_rujuk_koroner" id="tarikh_rujuk_koroner" value="{{ old('tarikh_rujuk_koroner', optional($paper->tarikh_rujuk_koroner)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="arahan_koroner" class="block text-sm font-medium text-gray-700">Arahan Koroner</label>
                        <input type="text" name="arahan_koroner" id="arahan_koroner" value="{{ old('arahan_koroner', $paper->arahan_koroner) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="status_sdr_final" class="block text-sm font-medium text-gray-700">Status SDR Final</label>
                        <input type="text" name="status_sdr_final" id="status_sdr_final" value="{{ old('status_sdr_final', $paper->status_sdr_final) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div class="col-span-full">
                        <label for="ulasan_keseluruhan" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan</label>
                        <textarea name="ulasan_keseluruhan" id="ulasan_keseluruhan" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan', $paper->ulasan_keseluruhan) }}</textarea>
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
    </style>
</x-app-layout>