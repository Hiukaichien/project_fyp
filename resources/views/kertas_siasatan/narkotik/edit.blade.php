<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kemaskini Kertas Siasatan: Narkotik ({{ $paper->no_ks }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sila semak input anda.</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => 'NarkotikPaper', 'id' => $paper->id]) }}"
                  class="space-y-6 bg-white p-6 shadow rounded" novalidate>
                @csrf
                @method('PUT')

                {{-- Display Non-Editable Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
                    <div><span class="font-semibold">No. K/Siasatan:</span> {{ $paper->no_ks }}</div>
                    <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $paper->pegawai_penyiasat }}</div>
                    <div><span class="font-semibold">Seksyen:</span> {{ $paper->seksyen }}</div>
                    <div><span class="font-semibold">Tarikh Laporan Polis:</span> {{ optional($paper->tarikh_laporan_polis)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <hr>

                {{-- Tarikh Edaran Minit --}}
                <h2 class="text-lg font-semibold border-b pb-1">Tarikh Edaran Minit</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
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

                {{-- Maklumat Tambahan --}}
                <h2 class="text-lg font-semibold border-b pb-1">Maklumat Tambahan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="pegawai_pemeriksa" class="block text-sm font-medium text-gray-700">Pegawai Pemeriksa</label>
                        <input type="text" id="pegawai_pemeriksa" name="pegawai_pemeriksa" value="{{ old('pegawai_pemeriksa', $paper->pegawai_pemeriksa) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="terbengkalai_tb" class="block text-sm font-medium text-gray-700">Terbengkalai (TB)</label>
                        <input type="text" id="terbengkalai_tb" name="terbengkalai_tb" value="{{ old('terbengkalai_tb', $paper->terbengkalai_tb) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>
                
                {{-- Barang Kes --}}
                <h2 class="text-lg font-semibold border-b pb-1">Barang Kes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="no_ext_brg_kes" class="block text-sm font-medium text-gray-700">No Ext Brg Kes</label>
                        <input type="text" id="no_ext_brg_kes" name="no_ext_brg_kes" value="{{ old('no_ext_brg_kes', $paper->no_ext_brg_kes) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tidak_daftar_barang_kes" class="block text-sm font-medium text-gray-700">Tidak Daftar Barang Kes (TD)</label>
                        <input type="text" id="tidak_daftar_barang_kes" name="tidak_daftar_barang_kes" value="{{ old('tidak_daftar_barang_kes', $paper->tidak_daftar_barang_kes) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="gambar_barang_kes" class="block text-sm font-medium text-gray-700">Gambar Barang Kes</label>
                        <select id="gambar_barang_kes" name="gambar_barang_kes" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="ADA" {{ old('gambar_barang_kes', $paper->gambar_barang_kes) == 'ADA' ? 'selected' : '' }}>ADA</option>
                            <option value="TIADA" {{ old('gambar_barang_kes', $paper->gambar_barang_kes) == 'TIADA' ? 'selected' : '' }}>TIADA</option>
                        </select>
                    </div>
                    <div class="col-span-full">
                        <label for="ulasan_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Barang Kes</label>
                        <textarea id="ulasan_barang_kes" name="ulasan_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_barang_kes', $paper->ulasan_barang_kes) }}</textarea>
                    </div>
                </div>
                <hr>

                {{-- Urine & Kimia --}}
                <h2 class="text-lg font-semibold border-b pb-1">Ujian Urine & Kimia</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="tarikh_urine_dipungut" class="block text-sm font-medium text-gray-700">Tarikh Urine Dipungut</label>
                        <input type="date" id="tarikh_urine_dipungut" name="tarikh_urine_dipungut" value="{{ old('tarikh_urine_dipungut', optional($paper->tarikh_urine_dipungut)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="tarikh_urine_dihantar_ke_patalogi" class="block text-sm font-medium text-gray-700">Tarikh Urine Dihantar Ke Patalogi</label>
                        <input type="date" id="tarikh_urine_dihantar_ke_patalogi" name="tarikh_urine_dihantar_ke_patalogi" value="{{ old('tarikh_urine_dihantar_ke_patalogi', optional($paper->tarikh_urine_dihantar_ke_patalogi)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="urine_dihantar_lewat" class="block text-sm font-medium text-gray-700">Urine Dihantar Lewat (>48 Jam)</label>
                        <select id="urine_dihantar_lewat" name="urine_dihantar_lewat" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('urine_dihantar_lewat', $paper->urine_dihantar_lewat) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('urine_dihantar_lewat', $paper->urine_dihantar_lewat) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                    <div>
                        <label for="tarikh_laporan_patalogi_diterima" class="block text-sm font-medium text-gray-700">Tarikh Laporan Patalogi Diterima</label>
                        <input type="date" id="tarikh_laporan_patalogi_diterima" name="tarikh_laporan_patalogi_diterima" value="{{ old('tarikh_laporan_patalogi_diterima', optional($paper->tarikh_laporan_patalogi_diterima)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="keputusan_urine" class="block text-sm font-medium text-gray-700">Keputusan Urine</label>
                        <select id="keputusan_urine" name="keputusan_urine" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="POSITIF" {{ old('keputusan_urine', $paper->keputusan_urine) == 'POSITIF' ? 'selected' : '' }}>POSITIF</option>
                            <option value="NEGATIF" {{ old('keputusan_urine', $paper->keputusan_urine) == 'NEGATIF' ? 'selected' : '' }}>NEGATIF</option>
                        </select>
                    </div>
                    <div>
                        <label for="tarikh_brg_kes_dihantar_kimia" class="block text-sm font-medium text-gray-700">Tarikh Brg Kes Dihantar Kimia</label>
                        <input type="date" id="tarikh_brg_kes_dihantar_kimia" name="tarikh_brg_kes_dihantar_kimia" value="{{ old('tarikh_brg_kes_dihantar_kimia', optional($paper->tarikh_brg_kes_dihantar_kimia)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div>
                        <label for="tarikh_laporan_kimia_diterima" class="block text-sm font-medium text-gray-700">Tarikh Laporan Kimia Diterima</label>
                        <input type="date" id="tarikh_laporan_kimia_diterima" name="tarikh_laporan_kimia_diterima" value="{{ old('tarikh_laporan_kimia_diterima', optional($paper->tarikh_laporan_kimia_diterima)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                </div>
                <hr>

                {{-- Tindakan Lanjut --}}
                <h2 class="text-lg font-semibold border-b pb-1">Tindakan Lanjut</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
                    <div>
                        <label for="tarikh_arahan_tuduh_oleh_tpr" class="block text-sm font-medium text-gray-700">Tarikh Arahan Tuduh Oleh TPR</label>
                        <input type="date" id="tarikh_arahan_tuduh_oleh_tpr" name="tarikh_arahan_tuduh_oleh_tpr" value="{{ old('tarikh_arahan_tuduh_oleh_tpr', optional($paper->tarikh_arahan_tuduh_oleh_tpr)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="waran_tangkap_dibuat_atau_tidak" class="block text-sm font-medium text-gray-700">Waran Tangkap Dibuat</label>
                        <select id="waran_tangkap_dibuat_atau_tidak" name="waran_tangkap_dibuat_atau_tidak" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('waran_tangkap_dibuat_atau_tidak', $paper->waran_tangkap_dibuat_atau_tidak) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('waran_tangkap_dibuat_atau_tidak', $paper->waran_tangkap_dibuat_atau_tidak) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                    <div>
                        <label for="rj10a_dijana_atau_tidak" class="block text-sm font-medium text-gray-700">RJ10A Dijana</label>
                        <select id="rj10a_dijana_atau_tidak" name="rj10a_dijana_atau_tidak" class="mt-1 block w-full form-select">
                            <option value="">-- Sila Pilih --</option>
                            <option value="YA" {{ old('rj10a_dijana_atau_tidak', $paper->rj10a_dijana_atau_tidak) == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ old('rj10a_dijana_atau_tidak', $paper->rj10a_dijana_atau_tidak) == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                        </select>
                    </div>
                    <div>
                        <label for="rj9" class="block text-sm font-medium text-gray-700">RJ9</label>
                        <input type="text" id="rj9" name="rj9" value="{{ old('rj9', $paper->rj9) }}" class="mt-1 block w-full form-input">
                    </div>
                    <div>
                        <label for="rj99" class="block text-sm font-medium text-gray-700">RJ99</label>
                        <input type="text" id="rj99" name="rj99" value="{{ old('rj99', $paper->rj99) }}" class="mt-1 block w-full form-input">
                    </div>
                     <div class="col-span-full">
                        <label for="ulasan_keseluruhan_ks" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan KS</label>
                        <textarea id="ulasan_keseluruhan_ks" name="ulasan_keseluruhan_ks" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan_ks', $paper->ulasan_keseluruhan_ks) }}</textarea>
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
            @apply rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm !important;
        }
        .form-radio {
            @apply rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50 !important;
        }
        .form-textarea { 
            @apply resize-vertical !important; 
        }
    </style>
</x-app-layout>