<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Komersil ({{ $paper->no_ks }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'Komersil', 'id' => $paper->id]) }}" class="space-y-6 bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                {{-- Non-Editable Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
                    <div><span class="font-semibold">No. Kertas Siasatan:</span> {{ $paper->no_ks }}</div>
                    <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $paper->pegawai_penyiasat }}</div>
                    <div><span class="font-semibold">Seksyen:</span> {{ $paper->seksyen }}</div>
                    <div><span class="font-semibold">Tarikh KS:</span> {{ optional($paper->tarikh_ks_dibuka)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <hr>

                {{-- Main Details & Dates --}}
                <h2 class="text-lg font-semibold border-b pb-1">Maklumat Utama & Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
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
                    <div>
                        <label for="edaran_pertama_melebihi_48jam" class="block text-sm font-medium text-gray-700">Edaran Pertama > 48 Jam</label>
                        <input type="text" name="edaran_pertama_melebihi_48jam" id="edaran_pertama_melebihi_48jam" value="{{ old('edaran_pertama_melebihi_48jam', $paper->edaran_pertama_melebihi_48jam) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="terbengkalai_tb" class="block text-sm font-medium text-gray-700">Terbengkalai (TB)</label>
                        <input type="text" name="terbengkalai_tb" id="terbengkalai_tb" value="{{ old('terbengkalai_tb', $paper->terbengkalai_tb) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>
                
                {{-- Barang Kes --}}
                <h2 class="text-lg font-semibold border-b pb-1">Barang Kes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                    <div>
                        <label for="no_ext_brg_kes" class="block text-sm font-medium text-gray-700">No Ext Brg Kes</label>
                        <input type="text" name="no_ext_brg_kes" id="no_ext_brg_kes" value="{{ old('no_ext_brg_kes', $paper->no_ext_brg_kes) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="brg_kes_tak_daftar" class="block text-sm font-medium text-gray-700">Brg Kes Tak Daftar</label>
                        <input type="text" name="brg_kes_tak_daftar" id="brg_kes_tak_daftar" value="{{ old('brg_kes_tak_daftar', $paper->brg_kes_tak_daftar) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div class="col-span-full">
                        <label for="ulasan_isu_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Isu Barang Kes</label>
                        <textarea name="ulasan_isu_barang_kes" id="ulasan_isu_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_isu_barang_kes', $paper->ulasan_isu_barang_kes) }}</textarea>
                    </div>
                </div>
                <hr>

                {{-- Dokumen & Permohonan --}}
                <h2 class="text-lg font-semibold border-b pb-1">Dokumen & Permohonan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="pem_1_2_3_4" class="block text-sm font-medium text-gray-700">Pem 1,2,3,Dan 4</label>
                        <input type="text" name="pem_1_2_3_4" id="pem_1_2_3_4" value="{{ old('pem_1_2_3_4', $paper->pem_1_2_3_4) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj9" class="block text-sm font-medium text-gray-700">RJ9 (Report Tangkapan)</label>
                        <input type="text" name="rj9" id="rj9" value="{{ old('rj9', $paper->rj9) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj99" class="block text-sm font-medium text-gray-700">RJ99 (Keputusan Kes)</label>
                        <input type="text" name="rj99" id="rj99" value="{{ old('rj99', $paper->rj99) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj10a" class="block text-sm font-medium text-gray-700">RJ10A</label>
                        <input type="text" name="rj10a" id="rj10a" value="{{ old('rj10a', $paper->rj10a) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj10b" class="block text-sm font-medium text-gray-700">RJ10B</label>
                        <input type="text" name="rj10b" id="rj10b" value="{{ old('rj10b', $paper->rj10b) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj21" class="block text-sm font-medium text-gray-700">RJ21</label>
                        <input type="text" name="rj21" id="rj21" value="{{ old('rj21', $paper->rj21) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="permohonan_e_fsa" class="block text-sm font-medium text-gray-700">Permohonan e FSA</label>
                        <input type="text" name="permohonan_e_fsa" id="permohonan_e_fsa" value="{{ old('permohonan_e_fsa', $paper->permohonan_e_fsa) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="permohonan_ulangan_e_fsa" class="block text-sm font-medium text-gray-700">Permohonan Ulangan e FSA</label>
                        <input type="text" name="permohonan_ulangan_e_fsa" id="permohonan_ulangan_e_fsa" value="{{ old('permohonan_ulangan_e_fsa', $paper->permohonan_ulangan_e_fsa) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="salinan_pdrm_s_43" class="block text-sm font-medium text-gray-700">Salinan PDRM(S)43</label>
                        <input type="text" name="salinan_pdrm_s_43" id="salinan_pdrm_s_43" value="{{ old('salinan_pdrm_s_43', $paper->salinan_pdrm_s_43) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_permohonan_telco" class="block text-sm font-medium text-gray-700">Tarikh Permohonan Telco</label>
                        <input type="date" name="tarikh_permohonan_telco" id="tarikh_permohonan_telco" value="{{ old('tarikh_permohonan_telco', optional($paper->tarikh_permohonan_telco)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="arahan_tuduh_dilaksanakan_tidak" class="block text-sm font-medium text-gray-700">Arahan Tuduh Dilaksanakan/Tidak</label>
                        <input type="text" name="arahan_tuduh_dilaksanakan_tidak" id="arahan_tuduh_dilaksanakan_tidak" value="{{ old('arahan_tuduh_dilaksanakan_tidak', $paper->arahan_tuduh_dilaksanakan_tidak) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="diari_siasatan_tidak_dikemaskini" class="block text-sm font-medium text-gray-700">Diari Siasatan Tidak Dikemaskini</label>
                        <input type="text" name="diari_siasatan_tidak_dikemaskini" id="diari_siasatan_tidak_dikemaskini" value="{{ old('diari_siasatan_tidak_dikemaskini', $paper->diari_siasatan_tidak_dikemaskini) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="permohonan_waran_tangkap" class="block text-sm font-medium text-gray-700">Permohonan Waran Tangkap</label>
                        <input type="text" name="permohonan_waran_tangkap" id="permohonan_waran_tangkap" value="{{ old('permohonan_waran_tangkap', $paper->permohonan_waran_tangkap) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div class="col-span-full">
                        <label for="ulasan_keseluruhan_ks" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan KS</label>
                        <textarea name="ulasan_keseluruhan_ks" id="ulasan_keseluruhan_ks" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_ks', $paper->ulasan_keseluruhan_ks) }}</textarea>
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
        .form-textarea { @apply resize-vertical; }
    </style>
</x-app-layout>