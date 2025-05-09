@csrf

{{-- === Fields requiring User Input / Audit === --}}

{{-- Tarikh Edaran Minit --}}
<h2 class="text-lg font-semibold border-b pb-1 mt-4">Tarikh Edaran Minit</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
    <div>
        <label for="tarikh_minit_a" class="block text-sm font-medium text-gray-700">Minit Pertama (A)</label>
        <input type="date" id="tarikh_minit_a" name="tarikh_minit_a" value="{{ old('tarikh_minit_a', optional($kertasSiasatan->tarikh_minit_a)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
    </div>
    <div>
        <label for="tarikh_minit_b" class="block text-sm font-medium text-gray-700">Minit Kedua (B)</label>
        <input type="date" id="tarikh_minit_b" name="tarikh_minit_b" value="{{ old('tarikh_minit_b', optional($kertasSiasatan->tarikh_minit_b)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
    </div>
    <div>
        <label for="tarikh_minit_c" class="block text-sm font-medium text-gray-700">Sebelum Minit Terakhir (C)</label>
        <input type="date" id="tarikh_minit_c" name="tarikh_minit_c" value="{{ old('tarikh_minit_c', optional($kertasSiasatan->tarikh_minit_c)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
    </div>
     <div>
        <label for="tarikh_minit_d" class="block text-sm font-medium text-gray-700">Minit Terakhir (D)</label>
        <input type="date" id="tarikh_minit_d" name="tarikh_minit_d" value="{{ old('tarikh_minit_d', optional($kertasSiasatan->tarikh_minit_d)->format('Y-m-d')) }}" class="mt-1 block w-full form-input">
    </div>
</div>

{{-- Display Calculated Statuses (Read Only) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 p-2 bg-gray-100 rounded">
    <div><span class="font-semibold">Edaran > 24 Jam:</span> {{ $kertasSiasatan->edar_lebih_24_jam_status ?? 'N/A' }}</div>
    <div><span class="font-semibold">Terbengkalai 3 Bulan:</span> {{ $kertasSiasatan->terbengkalai_3_bulan_status ?? 'N/A' }}</div>
    <div><span class="font-semibold">Baru Kemaskini:</span> {{ $kertasSiasatan->baru_kemaskini_status ?? 'N/A' }}</div>
</div>
<hr class="my-4">


{{-- Status KS Semasa Diperiksa --}}
<h2 class="text-lg font-semibold border-b pb-1">Status KS Semasa Diperiksa</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
    <div>
        <label for="status_ks_semasa_diperiksa" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="status_ks_semasa_diperiksa" name="status_ks_semasa_diperiksa"
                x-model="status_ks_semasa_diperiksa"
                class="mt-1 block w-full form-select">
            <option value="">-- Sila Pilih --</option>
            @foreach(['Siasatan Aktif', 'Rujuk TPR', 'Rujuk PPN', 'Rujuk KJSJ', 'Rujuk KBSJD', 'KUS/Sementara', 'Jatuh Hukum', 'KUS/Fail'] as $status)
                <option value="{{ $status }}"> {{ $status }} </option>
            @endforeach
        </select>
    </div>
    {{-- Conditional Date Field --}}
    <div x-show="status_ks_semasa_diperiksa">
        <label for="tarikh_status_ks_semasa_diperiksa" class="block text-sm font-medium text-gray-700">Tarikh Status</label>
        <input type="date" id="tarikh_status_ks_semasa_diperiksa" name="tarikh_status_ks_semasa_diperiksa"
               value="{{ old('tarikh_status_ks_semasa_diperiksa', optional($kertasSiasatan->tarikh_status_ks_semasa_diperiksa)->format('Y-m-d')) }}"
               :required="status_ks_semasa_diperiksa"
               class="mt-1 block w-full form-input">
    </div>
</div>
<hr class="my-4">

{{-- Rakaman Percakapan --}}
<h2 class="text-lg font-semibold border-b pb-1">Rakaman Percakapan (112 KPJ)</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
     {{-- Pengadu --}}
     <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pengadu</label>
        <div class="flex space-x-4">
             <label class="inline-flex items-center">
                <input type="radio" name="rakaman_pengadu" value="YA" class="form-radio" {{ old('rakaman_pengadu', $kertasSiasatan->rakaman_pengadu ?? 'TIADA') == 'YA' ? 'checked' : '' }}>
                <span class="ml-2">YA, Dirakam</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="rakaman_pengadu" value="TIADA" class="form-radio" {{ old('rakaman_pengadu', $kertasSiasatan->rakaman_pengadu ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}>
                <span class="ml-2">TIADA Rakaman</span>
            </label>
        </div>
    </div>
    {{-- Add Saspek and Saksi radios similarly --}}
    <div>... Rakaman Saspek Radios ...</div>
    <div>... Rakaman Saksi Radios ...</div>
</div>
<hr class="my-4">


{{-- ID Siasatan Dilampirkan --}}
<h2 class="text-lg font-semibold border-b pb-1">ID Siasatan</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
     <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Adakah ID Siasatan Dilampirkan?</label>
        <div class="flex space-x-4">
             <label class="inline-flex items-center">
                <input type="radio" name="id_siasatan_dilampirkan" value="YA" class="form-radio"
                       x-model="id_siasatan_dilampirkan">
                <span class="ml-2">YA</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="id_siasatan_dilampirkan" value="TIDAK" class="form-radio"
                       x-model="id_siasatan_dilampirkan">
                <span class="ml-2">TIDAK</span>
            </label>
        </div>
    </div>
    {{-- Conditional Date Field --}}
    <div x-show="id_siasatan_dilampirkan === 'YA'">
        <label for="tarikh_id_siasatan_dilampirkan" class="block text-sm font-medium text-gray-700">Tarikh ID Dilampirkan</label>
        <input type="date" id="tarikh_id_siasatan_dilampirkan" name="tarikh_id_siasatan_dilampirkan"
               value="{{ old('tarikh_id_siasatan_dilampirkan', optional($kertasSiasatan->tarikh_id_siasatan_dilampirkan)->format('Y-m-d')) }}"
               :required="id_siasatan_dilampirkan === 'YA'"
               class="mt-1 block w-full form-input">
    </div>
</div>
<hr class="my-4">


{{-- === Barang Kes Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Barang Kes</h2>
<div class="space-y-3 mt-2">
    {{-- Add all Barang Kes fields here (radios, text inputs, selects, textarea) --}}
    {{-- Example Radio --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Adakah Barang Kes AM Didaftar Oleh IO/AIO?</label>
        <div class="flex space-x-4">
             <label class="inline-flex items-center">
                <input type="radio" name="barang_kes_am_didaftar" value="YA" class="form-radio" {{ old('barang_kes_am_didaftar', $kertasSiasatan->barang_kes_am_didaftar ?? 'TIDAK') == 'YA' ? 'checked' : '' }}>
                <span class="ml-2">YA</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="barang_kes_am_didaftar" value="TIDAK" class="form-radio" {{ old('barang_kes_am_didaftar', $kertasSiasatan->barang_kes_am_didaftar ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}>
                <span class="ml-2">TIDAK</span>
            </label>
        </div>
    </div>
    {{-- Example Text Input --}}
    <div>
        <label for="no_daftar_kes_am" class="block text-sm font-medium text-gray-700">No Daftar Barang Kes AM</label>
        <input type="text" id="no_daftar_kes_am" name="no_daftar_kes_am" value="{{ old('no_daftar_kes_am', $kertasSiasatan->no_daftar_kes_am) }}" class="mt-1 block w-full form-input">
    </div>
    {{-- Example Textarea --}}
     <div>
        <label for="ulasan_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Barang Kes</label>
        <textarea id="ulasan_barang_kes" name="ulasan_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_barang_kes', $kertasSiasatan->ulasan_barang_kes) }}</textarea>
    </div>
     {{-- ... continue for all Barang Kes fields --}}
</div>
<hr class="my-4">


{{-- === Pakar Judi / Forensik Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Pakar Judi / Forensik</h2>
<div class="space-y-3 mt-2">
    {{-- Add fields for this section (radios, selects, text input) --}}
</div>
<hr class="my-4">


{{-- === Dokumen Lain Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Dokumen Lain</h2>
<div class="space-y-3 mt-2">
    {{-- Add fields for this section (select/radio, radios) --}}
</div>
<hr class="my-4">


{{-- === RJ Forms Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Rekod Jenayah (RJ)</h2>
<div class="space-y-4 mt-2">
    {{-- RJ2 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
         <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RJ2 (Cap Jari)</label>
            <div class="flex space-x-4">
                 <label class="inline-flex items-center">
                    <input type="radio" name="rj2_status" value="Cipta" class="form-radio" x-model="rj2_status">
                    <span class="ml-2">Cipta</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="rj2_status" value="Tidak Cipta" class="form-radio" x-model="rj2_status">
                    <span class="ml-2">Tidak Cipta</span>
                </label>
            </div>
        </div>
        <div x-show="rj2_status === 'Cipta'">
            <label for="rj2_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta RJ2</label>
            <input type="date" id="rj2_tarikh" name="rj2_tarikh"
                   value="{{ old('rj2_tarikh', optional($kertasSiasatan->rj2_tarikh)->format('Y-m-d')) }}"
                   :required="rj2_status === 'Cipta'"
                   class="mt-1 block w-full form-input">
        </div>
    </div>
    {{-- RJ9, RJ10A, RJ10B, RJ99, Semboyan, Waran Tangkap... --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
         <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Permohonan Waran Tangkap Mahkamah</label>
             <div class="flex space-x-4">
                 <label class="inline-flex items-center">
                    <input type="radio" name="waran_tangkap_status" value="Mohon" class="form-radio" x-model="waran_tangkap_status">
                    <span class="ml-2">Mohon Ke Mahkamah</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="waran_tangkap_status" value="Tidak Mohon" class="form-radio" x-model="waran_tangkap_status">
                    <span class="ml-2">Tidak Mohon</span>
                </label>
            </div>
        </div>
        <div x-show="waran_tangkap_status === 'Mohon'">
            <label for="waran_tangkap_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Mohon</label>
            <input type="date" id="waran_tangkap_tarikh" name="waran_tangkap_tarikh"
                   value="{{ old('waran_tangkap_tarikh', optional($kertasSiasatan->waran_tangkap_tarikh)->format('Y-m-d')) }}"
                   :required="waran_tangkap_status === 'Mohon'"
                   class="mt-1 block w-full form-input">
        </div>
    </div>

    {{-- Ulasan RJ --}}
    <div class="mt-3">
        <label for="ulasan_isu_rj" class="block text-sm font-medium text-gray-700">Ulasan Isu RJ Dikesan</label>
        <textarea id="ulasan_isu_rj" name="ulasan_isu_rj" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_isu_rj', $kertasSiasatan->ulasan_isu_rj) }}</textarea>
    </div>
</div>
<hr class="my-4">


{{-- === Surat Pemberitahuan Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Surat Pemberitahuan (Pem)</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
    {{-- Pem 1 --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pem 1</label>
        <div class="flex space-x-4">
             <label class="inline-flex items-center">
                <input type="radio" name="pem1_status" value="Cipta" class="form-radio" {{ old('pem1_status', $kertasSiasatan->pem1_status ?? 'Tidak Cipta') == 'Cipta' ? 'checked' : '' }}>
                <span class="ml-2">Cipta</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="pem1_status" value="Tidak Cipta" class="form-radio" {{ old('pem1_status', $kertasSiasatan->pem1_status ?? 'Tidak Cipta') == 'Tidak Cipta' ? 'checked' : '' }}>
                <span class="ml-2">Tidak Cipta</span>
            </label>
        </div>
    </div>
    {{-- Pem 2, Pem 3, Pem 4... Add similarly --}}
     <div>... Pem 2 Radios ...</div>
     <div>... Pem 3 Radios ...</div>
     <div>... Pem 4 Radios ...</div>
</div>
<hr class="my-4">


{{-- === Isu-Isu Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Isu-Isu Pemeriksaan</h2>
<div class="space-y-3 mt-2">
    {{-- Example Isu --}}
    <div class="flex justify-between items-center p-3 border rounded bg-gray-50">
        <span class="text-sm text-gray-800">Isu: Arahan TPR untuk tuduh tidak dilaksanakan tindakan tuduh</span>
        <div class="flex space-x-4 flex-shrink-0 ml-4">
             <label class="inline-flex items-center">
                <input type="radio" name="isu_tpr_tuduh" value="YA" class="form-radio" {{ old('isu_tpr_tuduh', $kertasSiasatan->isu_tpr_tuduh ?? 'TIADA ISU') == 'YA' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">YA</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="isu_tpr_tuduh" value="TIADA ISU" class="form-radio" {{ old('isu_tpr_tuduh', $kertasSiasatan->isu_tpr_tuduh ?? 'TIADA ISU') == 'TIADA ISU' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">TIADA ISU</span>
            </label>
        </div>
    </div>
    {{-- Add all other Isu radios similarly... --}}
</div>
<hr class="my-4">


{{-- === KS Telah Dihantar Ke Section === --}}
<h2 class="text-lg font-semibold border-b pb-1">Status Penghantaran KS</h2>
<div class="space-y-4 mt-2">
    {{-- Ke TPR --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
         <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hantar Ke TPR Untuk Arahan Lanjut?</label>
             <div class="flex space-x-4">
                 <label class="inline-flex items-center">
                    <input type="radio" name="ks_hantar_tpr_status" value="YA" class="form-radio" x-model="ks_hantar_tpr_status">
                    <span class="ml-2">YA</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="ks_hantar_tpr_status" value="TIADA ISU" class="form-radio" x-model="ks_hantar_tpr_status">
                    <span class="ml-2">TIADA ISU</span>
                </label>
            </div>
        </div>
        <div x-show="ks_hantar_tpr_status === 'YA'">
            <label for="ks_hantar_tpr_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Hantar Ke TPR</label>
            <input type="date" id="ks_hantar_tpr_tarikh" name="ks_hantar_tpr_tarikh"
                   value="{{ old('ks_hantar_tpr_tarikh', optional($kertasSiasatan->ks_hantar_tpr_tarikh)->format('Y-m-d')) }}"
                   :required="ks_hantar_tpr_status === 'YA'"
                   class="mt-1 block w-full form-input">
        </div>
    </div>
    {{-- Ke KJSJ, D5, KBSJD... Add similarly --}}
</div>
<hr class="my-4">


{{-- === Ulasan Pemeriksa === --}}
 <h2 class="text-lg font-semibold border-b pb-1">Ulasan Pemeriksa</h2>
 <div class="space-y-3 mt-2">
     <div>
        <label for="ulasan_isu_menarik" class="block text-sm font-medium text-gray-700">Ulasan Isu Menarik</label>
        <textarea id="ulasan_isu_menarik" name="ulasan_isu_menarik" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_isu_menarik', $kertasSiasatan->ulasan_isu_menarik) }}</textarea>
    </div>
     <div>
        <label for="ulasan_keseluruhan" class="block text-sm font-medium text-gray-700">Ulasan Keseluruhan</label>
        <textarea id="ulasan_keseluruhan" name="ulasan_keseluruhan" rows="4" class="mt-1 block w-full form-textarea">{{ old('ulasan_keseluruhan', $kertasSiasatan->ulasan_keseluruhan) }}</textarea>
    </div>
 </div>

 {{-- Add standard form input classes for Tailwind Forms plugin if you use it --}}
 <style>
    .form-input, .form-select, .form-textarea, .form-radio {
        @apply rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
    }
    .form-textarea { @apply resize-vertical; }
 </style>