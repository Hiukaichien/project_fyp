<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Jenayah ({{ $paper->no_ks }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'Jenayah', 'id' => $paper->id]) }}" class="space-y-6 bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                {{-- Non-Editable Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
                    <div><span class="font-semibold">No. Kertas Siasatan:</span> {{ $paper->no_ks }}</div>
                    <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $paper->pegawai_penyiasat }}</div>
                    <div><span class="font-semibold">Seksyen:</span> {{ $paper->seksyen }}</div>
                    <div><span class="font-semibold">Tarikh Laporan Polis:</span> {{ optional($paper->tarikh_laporan_polis)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <hr>

                {{-- Main Details & Dates --}}
                <h2 class="text-lg font-semibold border-b pb-1">Maklumat Utama</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="pegawai_pemeriksa_jips" class="block text-sm font-medium text-gray-700">Pegawai Pemeriksa JIPS</label>
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
                        <label for="terbengkalai_tb" class="block text-sm font-medium text-gray-700">Terbengkalai (TB)</label>
                        <input type="text" name="terbengkalai_tb" id="terbengkalai_tb" value="{{ old('terbengkalai_tb', $paper->terbengkalai_tb) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>

                {{-- Barang Kes --}}
                <h2 class="text-lg font-semibold border-b pb-1">Barang Kes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="no_ext_brg_kes" class="block text-sm font-medium text-gray-700">No Ext Brg Kes</label>
                        <input type="text" name="no_ext_brg_kes" id="no_ext_brg_kes" value="{{ old('no_ext_brg_kes', $paper->no_ext_brg_kes) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="brg_kes_tak_daftar" class="block text-sm font-medium text-gray-700">Brg Kes Tak Daftar</label>
                        <input type="text" name="brg_kes_tak_daftar" id="brg_kes_tak_daftar" value="{{ old('brg_kes_tak_daftar', $paper->brg_kes_tak_daftar) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="gambar_brg_kes" class="block text-sm font-medium text-gray-700">Gambar Brg Kes</label>
                        <input type="text" name="gambar_brg_kes" id="gambar_brg_kes" value="{{ old('gambar_brg_kes', $paper->gambar_brg_kes) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="wang_tunai_lucut_hak_judi" class="block text-sm font-medium text-gray-700">Wang Tunai Lucut Hak Judi (RM)</label>
                        <input type="number" step="0.01" name="wang_tunai_lucut_hak_judi" id="wang_tunai_lucut_hak_judi" value="{{ old('wang_tunai_lucut_hak_judi', $paper->wang_tunai_lucut_hak_judi) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div class="col-span-full">
                        <label for="ulasan_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Barang Kes</label>
                        <textarea name="ulasan_barang_kes" id="ulasan_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_barang_kes', $paper->ulasan_barang_kes) }}</textarea>
                    </div>
                </div>
                <hr>

                {{-- Dokumen & Arahan --}}
                <h2 class="text-lg font-semibold border-b pb-1">Dokumen & Arahan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="pem_1_2_3_4" class="block text-sm font-medium text-gray-700">Pem 1/2/3/4</label>
                        <input type="text" name="pem_1_2_3_4" id="pem_1_2_3_4" value="{{ old('pem_1_2_3_4', $paper->pem_1_2_3_4) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj9" class="block text-sm font-medium text-gray-700">RJ9</label>
                        <input type="text" name="rj9" id="rj9" value="{{ old('rj9', $paper->rj9) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj99" class="block text-sm font-medium text-gray-700">RJ99</label>
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
                        <label for="pdrma43_jamin_polis" class="block text-sm font-medium text-gray-700">PDRM(A)43 Jamin Polis</label>
                        <input type="text" name="pdrma43_jamin_polis" id="pdrma43_jamin_polis" value="{{ old('pdrma43_jamin_polis', $paper->pdrma43_jamin_polis) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="laporan_pakar" class="block text-sm font-medium text-gray-700">Laporan Pakar</label>
                        <input type="text" name="laporan_pakar" id="laporan_pakar" value="{{ old('laporan_pakar', $paper->laporan_pakar) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="arahan_ya_tpr" class="block text-sm font-medium text-gray-700">Arahan YA TPR</label>
                        <input type="text" name="arahan_ya_tpr" id="arahan_ya_tpr" value="{{ old('arahan_ya_tpr', $paper->arahan_ya_tpr) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="arahan_tuduh_ya_tpr" class="block text-sm font-medium text-gray-700">Arahan Tuduh YA TPR</label>
                        <input type="text" name="arahan_tuduh_ya_tpr" id="arahan_tuduh_ya_tpr" value="{{ old('arahan_tuduh_ya_tpr', $paper->arahan_tuduh_ya_tpr) }}" class="mt-1 block w-full form-input">
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