<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Orang Hilang ({{ $paper->no_ks_oh }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'OrangHilang', 'id' => $paper->id]) }}" class="space-y-6 bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                {{-- Non-Editable Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
                    <div><span class="font-semibold">No. KS:</span> {{ $paper->no_ks }}</div>
                    <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $paper->pegawai_penyiasat }}</div>
                    <div><span class="font-semibold">Tarikh Laporan Polis:</span> {{ optional($paper->tarikh_laporan_polis_system)->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-semibold">Tarikh KS:</span> {{ optional($paper->tarikh_ks)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <hr>

                {{-- Main Details --}}
                <h2 class="text-lg font-semibold border-b pb-1">Maklumat Utama & Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
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
                    <div>
                        <label for="status_oh" class="block text-sm font-medium text-gray-700">Status Orang Hilang</label>
                        <input type="text" name="status_oh" id="status_oh" value="{{ old('status_oh', $paper->status_oh) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>

                {{-- MPS & Butiran --}}
                <h2 class="text-lg font-semibold border-b pb-1">MPS & Butiran Orang Hilang</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="mps1_butiran_oh" class="block text-sm font-medium text-gray-700">MPS 1 (Butiran OH)</label>
                        <input type="text" name="mps1_butiran_oh" id="mps1_butiran_oh" value="{{ old('mps1_butiran_oh', $paper->mps1_butiran_oh) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="mps2_oh_dijumpai" class="block text-sm font-medium text-gray-700">MPS 2 (OH Dijumpai)</label>
                        <input type="text" name="mps2_oh_dijumpai" id="mps2_oh_dijumpai" value="{{ old('mps2_oh_dijumpai', $paper->mps2_oh_dijumpai) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="percakapan_mangsa_dijumpai" class="block text-sm font-medium text-gray-700">Percakapan Mangsa Dijumpai</label>
                        <input type="text" name="percakapan_mangsa_dijumpai" id="percakapan_mangsa_dijumpai" value="{{ old('percakapan_mangsa_dijumpai', $paper->percakapan_mangsa_dijumpai) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="kategori_umur_oh" class="block text-sm font-medium text-gray-700">Kategori Umur OH</label>
                        <input type="text" name="kategori_umur_oh" id="kategori_umur_oh" value="{{ old('kategori_umur_oh', $paper->kategori_umur_oh) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="jantina_oh" class="block text-sm font-medium text-gray-700">Jantina OH</label>
                        <input type="text" name="jantina_oh" id="jantina_oh" value="{{ old('jantina_oh', $paper->jantina_oh) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="kewarganegaraan" class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                        <input type="text" name="kewarganegaraan" id="kewarganegaraan" value="{{ old('kewarganegaraan', $paper->kewarganegaraan) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="kedutaan" class="block text-sm font-medium text-gray-700">Kedutaan</label>
                        <input type="text" name="kedutaan" id="kedutaan" value="{{ old('kedutaan', $paper->kedutaan) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>

                {{-- Pemakluman & Hebahan --}}
                <h2 class="text-lg font-semibold border-b pb-1">Pemakluman & Hebahan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="pem1_status" class="block text-sm font-medium text-gray-700">Pem 1 Status</label>
                        <input type="text" name="pem1_status" id="pem1_status" value="{{ old('pem1_status', $paper->pem1_status) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pem2_status" class="block text-sm font-medium text-gray-700">Pem 2 Status</label>
                        <input type="text" name="pem2_status" id="pem2_status" value="{{ old('pem2_status', $paper->pem2_status) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pem3_status" class="block text-sm font-medium text-gray-700">Pem 3 Status</label>
                        <input type="text" name="pem3_status" id="pem3_status" value="{{ old('pem3_status', $paper->pem3_status) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pem4_status" class="block text-sm font-medium text-gray-700">Pem 4 Status</label>
                        <input type="text" name="pem4_status" id="pem4_status" value="{{ old('pem4_status', $paper->pem4_status) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="gambar_orang_hilang" class="block text-sm font-medium text-gray-700">Gambar Orang Hilang</label>
                        <input type="text" name="gambar_orang_hilang" id="gambar_orang_hilang" value="{{ old('gambar_orang_hilang', $paper->gambar_orang_hilang) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="hebahan_oh" class="block text-sm font-medium text-gray-700">Hebahan OH</label>
                        <input type="text" name="hebahan_oh" id="hebahan_oh" value="{{ old('hebahan_oh', $paper->hebahan_oh) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pemakluman_ciq_jsj" class="block text-sm font-medium text-gray-700">Pemakluman CIQ JSJ</label>
                        <input type="text" name="pemakluman_ciq_jsj" id="pemakluman_ciq_jsj" value="{{ old('pemakluman_ciq_jsj', $paper->pemakluman_ciq_jsj) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="pemakluman_ke_nur_kasih" class="block text-sm font-medium text-gray-700">Pemakluman Ke Nur Kasih</label>
                        <input type="text" name="pemakluman_ke_nur_kasih" id="pemakluman_ke_nur_kasih" value="{{ old('pemakluman_ke_nur_kasih', $paper->pemakluman_ke_nur_kasih) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="kep_dibuka_ks_jenayah" class="block text-sm font-medium text-gray-700">KEP Dibuka KS Jenayah</label>
                        <input type="text" name="kep_dibuka_ks_jenayah" id="kep_dibuka_ks_jenayah" value="{{ old('kep_dibuka_ks_jenayah', $paper->kep_dibuka_ks_jenayah) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="sitrep_warga_asing" class="block text-sm font-medium text-gray-700">Sitrep (Warga Asing)</label>
                        <input type="text" name="sitrep_warga_asing" id="sitrep_warga_asing" value="{{ old('sitrep_warga_asing', $paper->sitrep_warga_asing) }}" class="mt-1 block w-full form-input">
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