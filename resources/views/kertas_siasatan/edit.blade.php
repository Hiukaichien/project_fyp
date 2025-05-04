{{-- resources/views/kertas_siasatan/edit.blade.php --}}

<x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kemaskini Kertas Siasatan: {{ $kertasSiasatan->no_ks }}</h2>
</x-slot>

    {{-- Display Validation Errors --}}
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

    {{-- The Form with Alpine.js Initialization --}}
    <form method="POST" action="{{ route('kertas_siasatan.update', $kertasSiasatan->id) }}"
          x-data="{
              // Initialize Alpine state with data from the model (use old() first for validation errors)
              status_ks_semasa_diperiksa: @js(old('status_ks_semasa_diperiksa', $kertasSiasatan->status_ks_semasa_diperiksa ?? '')),
              id_siasatan_dilampirkan: @js(old('id_siasatan_dilampirkan', $kertasSiasatan->id_siasatan_dilampirkan ?? 'TIDAK')),

              rj2_status: @js(old('rj2_status', $kertasSiasatan->rj2_status ?? 'Tidak Cipta')),
              rj9_status: @js(old('rj9_status', $kertasSiasatan->rj9_status ?? 'Tidak Cipta')),
              rj10a_status: @js(old('rj10a_status', $kertasSiasatan->rj10a_status ?? 'Tidak Cipta')),
              rj10b_status: @js(old('rj10b_status', $kertasSiasatan->rj10b_status ?? 'Tidak Cipta')),
              rj99_status: @js(old('rj99_status', $kertasSiasatan->rj99_status ?? 'Tidak Cipta')),
              semboyan_kesan_tangkap_status: @js(old('semboyan_kesan_tangkap_status', $kertasSiasatan->semboyan_kesan_tangkap_status ?? 'Tidak Cipta')),
              waran_tangkap_status: @js(old('waran_tangkap_status', $kertasSiasatan->waran_tangkap_status ?? 'Tidak Mohon')),

              ks_hantar_tpr_status: @js(old('ks_hantar_tpr_status', $kertasSiasatan->ks_hantar_tpr_status ?? 'TIADA ISU')),
              ks_hantar_kjsj_status: @js(old('ks_hantar_kjsj_status', $kertasSiasatan->ks_hantar_kjsj_status ?? 'TIADA ISU')),
              ks_hantar_d5_status: @js(old('ks_hantar_d5_status', $kertasSiasatan->ks_hantar_d5_status ?? 'TIADA ISU')),
              ks_hantar_kbsjd_status: @js(old('ks_hantar_kbsjd_status', $kertasSiasatan->ks_hantar_kbsjd_status ?? 'TIADA ISU')),
          }"
          class="space-y-6 bg-white p-6 shadow rounded"
          novalidate
    >
        @csrf
        @method('PUT')

        {{-- === Display Non-Editable Fields === --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded border">
            <div><span class="font-semibold">No. Kertas Siasatan:</span> {{ $kertasSiasatan->no_ks }}</div>
            <div><span class="font-semibold">Tarikh KS:</span> {{ optional($kertasSiasatan->tarikh_ks)->format('d/m/Y') ?? '-' }}</div>
             <div><span class="font-semibold">No. Repot:</span> {{ $kertasSiasatan->no_report ?? '-' }}</div>
            <div><span class="font-semibold">Jenis Jabatan:</span> {{ $kertasSiasatan->jenis_jabatan_ks ?? '-' }}</div>
            <div><span class="font-semibold">Pegawai Penyiasat:</span> {{ $kertasSiasatan->pegawai_penyiasat ?? '-' }}</div>
            <div><span class="font-semibold">Seksyen:</span> {{ $kertasSiasatan->seksyen ?? '-' }}</div>
             <div><span class="font-semibold">Status KS (IPRS):</span> {{ $kertasSiasatan->status_ks ?? '-' }}</div>
             <div><span class="font-semibold">Status Kes (IPRS):</span> {{ $kertasSiasatan->status_kes ?? '-' }}</div>
        </div>
        <hr>

        {{-- === Tarikh Edaran Minit === --}}
        <h2 class="text-lg font-semibold border-b pb-1">Tarikh Edaran Minit</h2>
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

        {{-- Calculated Statuses (Read Only) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 p-2 bg-gray-100 rounded">
            <div><span class="font-semibold">Edaran > 24 Jam:</span> {{ $kertasSiasatan->edar_lebih_24_jam_status ?? 'N/A' }}</div>
            <div><span class="font-semibold">Terbengkalai > 3 Bulan:</span> {{ $kertasSiasatan->terbengkalai_3_bulan_status ?? 'N/A' }}</div>
            <div><span class="font-semibold">Baru Kemaskini Lepas Semboyan JIPS:</span> {{ $kertasSiasatan->baru_kemaskini_status ?? 'N/A' }}</div>
        </div>
        <hr>

        {{-- === Status KS Semasa Diperiksa === --}}
        <h2 class="text-lg font-semibold border-b pb-1">Status KS Semasa Diperiksa</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <label for="status_ks_semasa_diperiksa" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status_ks_semasa_diperiksa" name="status_ks_semasa_diperiksa"
                        x-model="status_ks_semasa_diperiksa"
                        class="mt-1 block w-full form-select">
                    <option value="">-- Sila Pilih --</option>
                    @foreach(['Siasatan Aktif', 'Rujuk TPR', 'Rujuk PPN', 'Rujuk KJSJ', 'Rujuk KBSJD', 'KUS/Sementara', 'Jatuh Hukum', 'KUS/Fail'] as $status)
                        <option value="{{ $status }}" {{-- Alpine x-model handles selection --}}> {{ $status }} </option>
                    @endforeach
                </select>
            </div>
            {{-- Conditional Date Field --}}
            <div x-show="status_ks_semasa_diperiksa" x-transition>
                <label for="tarikh_status_ks_semasa_diperiksa" class="block text-sm font-medium text-gray-700">Tarikh Status</label>
                <input type="date" id="tarikh_status_ks_semasa_diperiksa" name="tarikh_status_ks_semasa_diperiksa"
                       value="{{ old('tarikh_status_ks_semasa_diperiksa', optional($kertasSiasatan->tarikh_status_ks_semasa_diperiksa)->format('Y-m-d')) }}"
                       :required="status_ks_semasa_diperiksa"
                       class="mt-1 block w-full form-input">
            </div>
        </div>
        <hr>

        {{-- === Rakaman Percakapan === --}}
        <h2 class="text-lg font-semibold border-b pb-1">Rakaman Percakapan (112 KPJ)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
             {{-- Pengadu --}}
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Pengadu</legend>
                <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rakaman_pengadu" value="YA" class="form-radio" {{ old('rakaman_pengadu', $kertasSiasatan->rakaman_pengadu ?? 'TIADA') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA, Dirakam</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rakaman_pengadu" value="TIADA" class="form-radio" {{ old('rakaman_pengadu', $kertasSiasatan->rakaman_pengadu ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA Rakaman</span> </label> </div> </fieldset>
             {{-- Saspek --}}
            <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Saspek</legend>
                 <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rakaman_saspek" value="YA" class="form-radio" {{ old('rakaman_saspek', $kertasSiasatan->rakaman_saspek ?? 'TIADA') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA, Dirakam</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rakaman_saspek" value="TIADA" class="form-radio" {{ old('rakaman_saspek', $kertasSiasatan->rakaman_saspek ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA Rakaman</span> </label> </div> </fieldset>
             {{-- Saksi --}}
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Saksi</legend>
                 <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rakaman_saksi" value="YA" class="form-radio" {{ old('rakaman_saksi', $kertasSiasatan->rakaman_saksi ?? 'TIADA') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA, Dirakam</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rakaman_saksi" value="TIADA" class="form-radio" {{ old('rakaman_saksi', $kertasSiasatan->rakaman_saksi ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA Rakaman</span> </label> </div> </fieldset>
        </div>
        <hr>

        {{-- === ID Siasatan === --}}
        <h2 class="text-lg font-semibold border-b pb-1">ID Siasatan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Adakah ID Siasatan Dilampirkan?</legend>
                <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="id_siasatan_dilampirkan" value="YA" class="form-radio" x-model="id_siasatan_dilampirkan"> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="id_siasatan_dilampirkan" value="TIDAK" class="form-radio" x-model="id_siasatan_dilampirkan"> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
            {{-- Conditional Date Field --}}
            <div x-show="id_siasatan_dilampirkan === 'YA'" x-transition>
                <label for="tarikh_id_siasatan_dilampirkan" class="block text-sm font-medium text-gray-700">Tarikh ID Dilampirkan</label>
                <input type="date" id="tarikh_id_siasatan_dilampirkan" name="tarikh_id_siasatan_dilampirkan"
                       value="{{ old('tarikh_id_siasatan_dilampirkan', optional($kertasSiasatan->tarikh_id_siasatan_dilampirkan)->format('Y-m-d')) }}"
                       :required="id_siasatan_dilampirkan === 'YA'"
                       class="mt-1 block w-full form-input">
            </div>
        </div>
        <hr>

        {{-- === Barang Kes Section === --}}
        <h2 class="text-lg font-semibold border-b pb-1">Barang Kes</h2>
        <div class="space-y-3 mt-2">
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Adakah Barang Kes AM Didaftar Oleh IO/AIO?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="barang_kes_am_didaftar" value="YA" class="form-radio" {{ old('barang_kes_am_didaftar', $kertasSiasatan->barang_kes_am_didaftar ?? 'TIDAK') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="barang_kes_am_didaftar" value="TIDAK" class="form-radio" {{ old('barang_kes_am_didaftar', $kertasSiasatan->barang_kes_am_didaftar ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <div> <label for="no_daftar_kes_am" class="block text-sm font-medium text-gray-700">No Daftar Barang Kes AM</label> <input type="text" id="no_daftar_kes_am" name="no_daftar_kes_am" value="{{ old('no_daftar_kes_am', $kertasSiasatan->no_daftar_kes_am) }}" class="mt-1 block w-full form-input"> </div>
                 <div> <label for="no_daftar_kes_senjata_api" class="block text-sm font-medium text-gray-700">No Daftar Senjata Api</label> <input type="text" id="no_daftar_kes_senjata_api" name="no_daftar_kes_senjata_api" value="{{ old('no_daftar_kes_senjata_api', $kertasSiasatan->no_daftar_kes_senjata_api) }}" class="mt-1 block w-full form-input"> </div>
                 <div> <label for="no_daftar_kes_berharga" class="block text-sm font-medium text-gray-700">No Daftar Berharga</label> <input type="text" id="no_daftar_kes_berharga" name="no_daftar_kes_berharga" value="{{ old('no_daftar_kes_berharga', $kertasSiasatan->no_daftar_kes_berharga) }}" class="mt-1 block w-full form-input"> </div>
             </div>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Adakah Gambar Rampasan Dilampirkan?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="gambar_rampasan_dilampirkan" value="YA" class="form-radio" {{ old('gambar_rampasan_dilampirkan', $kertasSiasatan->gambar_rampasan_dilampirkan ?? 'TIDAK') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="gambar_rampasan_dilampirkan" value="TIDAK" class="form-radio" {{ old('gambar_rampasan_dilampirkan', $kertasSiasatan->gambar_rampasan_dilampirkan ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
             <div> <label for="kedudukan_barang_kes" class="block text-sm font-medium text-gray-700">Kedudukan Semasa Barang Kes</label> <select id="kedudukan_barang_kes" name="kedudukan_barang_kes" class="mt-1 block w-full form-select"> <option value="">-- Sila Pilih --</option> @foreach(['Dalam Peti Besi KPD', 'Simpanan Stor Barang Kes', 'Dalam Simpanan IO/AIO', 'Dalam Simpanan Mahkamah', 'Barang Kes Tidak Dapat Di Kesan Dan Tidak Di Ketahui Status.'] as $status) <option value="{{ $status }}" {{ old('kedudukan_barang_kes', $kertasSiasatan->kedudukan_barang_kes) == $status ? 'selected' : '' }}>{{ $status }}</option> @endforeach </select> </div>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Surat Serah/Terima (IO/AIO & Stor)?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="surat_serah_terima_stor" value="ADA" class="form-radio" {{ old('surat_serah_terima_stor', $kertasSiasatan->surat_serah_terima_stor ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="surat_serah_terima_stor" value="TIADA" class="form-radio" {{ old('surat_serah_terima_stor', $kertasSiasatan->surat_serah_terima_stor ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Arahan Pelupusan (TPR/Mahkamah)?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="arahan_pelupusan" value="YA" class="form-radio" {{ old('arahan_pelupusan', $kertasSiasatan->arahan_pelupusan ?? 'TIDAK') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="arahan_pelupusan" value="TIDAK" class="form-radio" {{ old('arahan_pelupusan', $kertasSiasatan->arahan_pelupusan ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
             <div> <label for="tatacara_pelupusan" class="block text-sm font-medium text-gray-700">Tatacara Pelupusan</label> <select id="tatacara_pelupusan" name="tatacara_pelupusan" class="mt-1 block w-full form-select"> <option value="">-- Sila Pilih --</option> @foreach(['Serah Semula Pemilik atau Pihak Menuntut', 'Lupus Dengan Cara Bakar', 'Lupus Dengan Cara Tanam', 'Serah Ke Perbendaharaan @ Wang Hasil Kerajaan', 'Pelupusan Belum Dilaksanakan Kerana Masih Menunggu Arahan Lanjut KJSJ/KBSJD Setelah Menerima Arahan Minit TPR', 'Pelupusan Belum Dilaksanakan Namun Telah Ada Arahan Lupus Oleh TPR Dan KJSJ/KBSJD', 'Langsung Tiada Usaha Untuk Membuat Pelupusan Barang Kes Setelah Ada Arahan TPR Dan KJSJ/KBSJD'] as $cara) <option value="{{ $cara }}" {{ old('tatacara_pelupusan', $kertasSiasatan->tatacara_pelupusan) == $cara ? 'selected' : '' }}>{{ $cara }}</option> @endforeach </select> </div>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Resit Kew.38E Dilampirkan?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="resit_kew38e_dilampirkan" value="YA" class="form-radio" {{ old('resit_kew38e_dilampirkan', $kertasSiasatan->resit_kew38e_dilampirkan ?? 'TIDAK') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="resit_kew38e_dilampirkan" value="TIDAK" class="form-radio" {{ old('resit_kew38e_dilampirkan', $kertasSiasatan->resit_kew38e_dilampirkan ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Sijil Pelupusan Dilampirkan?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="sijil_pelupusan_dilampirkan" value="YA" class="form-radio" {{ old('sijil_pelupusan_dilampirkan', $kertasSiasatan->sijil_pelupusan_dilampirkan ?? 'TIDAK') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="sijil_pelupusan_dilampirkan" value="TIDAK" class="form-radio" {{ old('sijil_pelupusan_dilampirkan', $kertasSiasatan->sijil_pelupusan_dilampirkan ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Gambar Pelupusan Dilampirkan?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="gambar_pelupusan_dilampirkan" value="ADA" class="form-radio" {{ old('gambar_pelupusan_dilampirkan', $kertasSiasatan->gambar_pelupusan_dilampirkan ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="gambar_pelupusan_dilampirkan" value="TIADA" class="form-radio" {{ old('gambar_pelupusan_dilampirkan', $kertasSiasatan->gambar_pelupusan_dilampirkan ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
            </div>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Surat Serah/Terima (IO/AIO & Penuntut)?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="surat_serah_terima_penuntut" value="YA" class="form-radio" {{ old('surat_serah_terima_penuntut', $kertasSiasatan->surat_serah_terima_penuntut ?? 'TIDAK') == 'YA' ? 'checked' : '' }}> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="surat_serah_terima_penuntut" value="TIDAK" class="form-radio" {{ old('surat_serah_terima_penuntut', $kertasSiasatan->surat_serah_terima_penuntut ?? 'TIDAK') == 'TIDAK' ? 'checked' : '' }}> <span class="ml-2">TIDAK</span> </label> </div> </fieldset>
             <div> <label for="ulasan_barang_kes" class="block text-sm font-medium text-gray-700">Ulasan Barang Kes</label> <textarea id="ulasan_barang_kes" name="ulasan_barang_kes" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_barang_kes', $kertasSiasatan->ulasan_barang_kes) }}</textarea> </div>
        </div>
        <hr>

         {{-- === Pakar Judi / Forensik Section === --}}
         <h2 class="text-lg font-semibold border-b pb-1">Pakar Judi / Forensik</h2>
         <div class="space-y-3 mt-2">
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Surat Mohon Pakar Judi?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="surat_mohon_pakar_judi" value="ADA" class="form-radio" {{ old('surat_mohon_pakar_judi', $kertasSiasatan->surat_mohon_pakar_judi ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="surat_mohon_pakar_judi" value="TIADA" class="form-radio" {{ old('surat_mohon_pakar_judi', $kertasSiasatan->surat_mohon_pakar_judi ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Laporan Pakar Judi?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="laporan_pakar_judi" value="ADA" class="form-radio" {{ old('laporan_pakar_judi', $kertasSiasatan->laporan_pakar_judi ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="laporan_pakar_judi" value="TIADA" class="form-radio" {{ old('laporan_pakar_judi', $kertasSiasatan->laporan_pakar_judi ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Keputusan Pakar Judi</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="keputusan_pakar_judi" value="Mengesahkan Sebagai Judi" class="form-radio" {{ old('keputusan_pakar_judi', $kertasSiasatan->keputusan_pakar_judi ?? '') == 'Mengesahkan Sebagai Judi' ? 'checked' : '' }}> <span class="ml-2">Positif (+)</span> </label> <label class="inline-flex items-center"> <input type="radio" name="keputusan_pakar_judi" value="Tiada Mengesahkan Sebagai Judi" class="form-radio" {{ old('keputusan_pakar_judi', $kertasSiasatan->keputusan_pakar_judi ?? '') == 'Tiada Mengesahkan Sebagai Judi' ? 'checked' : '' }}> <span class="ml-2">Negatif (-)</span> </label> </div> </fieldset>
             </div>
             <div> <label for="kategori_perjudian" class="block text-sm font-medium text-gray-700">Kategori Perjudian Dimainkan</label> <input type="text" id="kategori_perjudian" name="kategori_perjudian" value="{{ old('kategori_perjudian', $kertasSiasatan->kategori_perjudian) }}" class="mt-1 block w-full form-input"> </div>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Surat Mohon Forensik?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="surat_mohon_forensik" value="ADA" class="form-radio" {{ old('surat_mohon_forensik', $kertasSiasatan->surat_mohon_forensik ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="surat_mohon_forensik" value="TIADA" class="form-radio" {{ old('surat_mohon_forensik', $kertasSiasatan->surat_mohon_forensik ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Laporan Forensik?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="laporan_forensik" value="ADA" class="form-radio" {{ old('laporan_forensik', $kertasSiasatan->laporan_forensik ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="laporan_forensik" value="TIADA" class="form-radio" {{ old('laporan_forensik', $kertasSiasatan->laporan_forensik ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Keputusan Forensik</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="keputusan_forensik" value="Mengesahkan Aplikasi Sebagai Judi" class="form-radio" {{ old('keputusan_forensik', $kertasSiasatan->keputusan_forensik ?? '') == 'Mengesahkan Aplikasi Sebagai Judi' ? 'checked' : '' }}> <span class="ml-2">Positif (+)</span> </label> <label class="inline-flex items-center"> <input type="radio" name="keputusan_forensik" value="Tiada Mengesahkan Aplikasi Sebagai Judi" class="form-radio" {{ old('keputusan_forensik', $kertasSiasatan->keputusan_forensik ?? '') == 'Tiada Mengesahkan Aplikasi Sebagai Judi' ? 'checked' : '' }}> <span class="ml-2">Negatif (-)</span> </label> </div> </fieldset>
             </div>
         </div>
         <hr>

         {{-- === Dokumen Lain Section === --}}
         <h2 class="text-lg font-semibold border-b pb-1">Dokumen Lain</h2>
          <div class="space-y-3 mt-2">
             <div> <label for="surat_jamin_polis" class="block text-sm font-medium text-gray-700">Surat Jamin Polis [PDRM (A)43]</label> <select id="surat_jamin_polis" name="surat_jamin_polis" class="mt-1 block w-full form-select"> <option value="">-- Sila Pilih --</option> @foreach(['ADA', 'TIADA', 'Masih Guna Buku', 'Cetak iPRS'] as $status) <option value="{{ $status }}" {{ old('surat_jamin_polis', $kertasSiasatan->surat_jamin_polis) == $status ? 'selected' : '' }}>{{ $status }}</option> @endforeach </select> </div>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Lakaran Rajah Kasar Lokasi?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="lakaran_lokasi" value="ADA" class="form-radio" {{ old('lakaran_lokasi', $kertasSiasatan->lakaran_lokasi ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="lakaran_lokasi" value="TIADA" class="form-radio" {{ old('lakaran_lokasi', $kertasSiasatan->lakaran_lokasi ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Gambar Sebenar Lokasi?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="gambar_lokasi" value="ADA" class="form-radio" {{ old('gambar_lokasi', $kertasSiasatan->gambar_lokasi ?? 'TIADA') == 'ADA' ? 'checked' : '' }}> <span class="ml-2">ADA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="gambar_lokasi" value="TIADA" class="form-radio" {{ old('gambar_lokasi', $kertasSiasatan->gambar_lokasi ?? 'TIADA') == 'TIADA' ? 'checked' : '' }}> <span class="ml-2">TIADA</span> </label> </div> </fieldset>
             </div>
         </div>
         <hr>


        {{-- === RJ Forms Section === --}}
        <h2 class="text-lg font-semibold border-b pb-1">Rekod Jenayah (RJ)</h2>
        <div class="space-y-4 mt-2">
            {{-- RJ2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">RJ2 (Cap Jari)</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rj2_status" value="Cipta" class="form-radio" x-model="rj2_status"> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rj2_status" value="Tidak Cipta" class="form-radio" x-model="rj2_status"> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
                <div x-show="rj2_status === 'Cipta'" x-transition>
                    <label for="rj2_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta RJ2</label>
                    <input type="date" id="rj2_tarikh" name="rj2_tarikh"
                           value="{{ old('rj2_tarikh', optional($kertasSiasatan->rj2_tarikh)->format('Y-m-d')) }}"
                           :required="rj2_status === 'Cipta'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- RJ9 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">RJ9 (Rekod Tangkapan)</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rj9_status" value="Cipta" class="form-radio" x-model="rj9_status"> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rj9_status" value="Tidak Cipta" class="form-radio" x-model="rj9_status"> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
                <div x-show="rj9_status === 'Cipta'" x-transition>
                    <label for="rj9_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta RJ9</label>
                    <input type="date" id="rj9_tarikh" name="rj9_tarikh"
                           value="{{ old('rj9_tarikh', optional($kertasSiasatan->rj9_tarikh)->format('Y-m-d')) }}"
                           :required="rj9_status === 'Cipta'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- RJ10A --}}
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">RJ10A (Wanted Person)</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rj10a_status" value="Cipta" class="form-radio" x-model="rj10a_status"> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rj10a_status" value="Tidak Cipta" class="form-radio" x-model="rj10a_status"> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
                <div x-show="rj10a_status === 'Cipta'" x-transition>
                    <label for="rj10a_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta RJ10A</label>
                    <input type="date" id="rj10a_tarikh" name="rj10a_tarikh"
                           value="{{ old('rj10a_tarikh', optional($kertasSiasatan->rj10a_tarikh)->format('Y-m-d')) }}"
                           :required="rj10a_status === 'Cipta'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- RJ10B --}}
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">RJ10B (Wanted Kenderaan)</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rj10b_status" value="Cipta" class="form-radio" x-model="rj10b_status"> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rj10b_status" value="Tidak Cipta" class="form-radio" x-model="rj10b_status"> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
                <div x-show="rj10b_status === 'Cipta'" x-transition>
                    <label for="rj10b_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta RJ10B</label>
                    <input type="date" id="rj10b_tarikh" name="rj10b_tarikh"
                           value="{{ old('rj10b_tarikh', optional($kertasSiasatan->rj10b_tarikh)->format('Y-m-d')) }}"
                           :required="rj10b_status === 'Cipta'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- RJ99 --}}
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">RJ99 (Keputusan Kes)</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="rj99_status" value="Cipta" class="form-radio" x-model="rj99_status"> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="rj99_status" value="Tidak Cipta" class="form-radio" x-model="rj99_status"> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
                <div x-show="rj99_status === 'Cipta'" x-transition>
                    <label for="rj99_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta RJ99</label>
                    <input type="date" id="rj99_tarikh" name="rj99_tarikh"
                           value="{{ old('rj99_tarikh', optional($kertasSiasatan->rj99_tarikh)->format('Y-m-d')) }}"
                           :required="rj99_status === 'Cipta'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- Semboyan Kesan / Tangkap --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Semboyan Kesan/Tangkap Daerah Lain</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="semboyan_kesan_tangkap_status" value="Cipta" class="form-radio" x-model="semboyan_kesan_tangkap_status"> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="semboyan_kesan_tangkap_status" value="Tidak Cipta" class="form-radio" x-model="semboyan_kesan_tangkap_status"> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
                <div x-show="semboyan_kesan_tangkap_status === 'Cipta'" x-transition>
                    <label for="semboyan_kesan_tangkap_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Cipta Semboyan</label>
                    <input type="date" id="semboyan_kesan_tangkap_tarikh" name="semboyan_kesan_tangkap_tarikh"
                           value="{{ old('semboyan_kesan_tangkap_tarikh', optional($kertasSiasatan->semboyan_kesan_tangkap_tarikh)->format('Y-m-d')) }}"
                           :required="semboyan_kesan_tangkap_status === 'Cipta'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- Waran Tangkap --}}
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Mohon Waran Tangkap Mahkamah?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="waran_tangkap_status" value="Mohon" class="form-radio" x-model="waran_tangkap_status"> <span class="ml-2">Mohon</span> </label> <label class="inline-flex items-center"> <input type="radio" name="waran_tangkap_status" value="Tidak Mohon" class="form-radio" x-model="waran_tangkap_status"> <span class="ml-2">Tidak Mohon</span> </label> </div> </fieldset>
                <div x-show="waran_tangkap_status === 'Mohon'" x-transition>
                    <label for="waran_tangkap_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Mohon</label>
                    <input type="date" id="waran_tangkap_tarikh" name="waran_tangkap_tarikh"
                           value="{{ old('waran_tangkap_tarikh', optional($kertasSiasatan->waran_tangkap_tarikh)->format('Y-m-d')) }}"
                           :required="waran_tangkap_status === 'Mohon'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- Ulasan RJ --}}
            <div class="mt-3"> <label for="ulasan_isu_rj" class="block text-sm font-medium text-gray-700">Ulasan Isu RJ Dikesan</label> <textarea id="ulasan_isu_rj" name="ulasan_isu_rj" rows="3" class="mt-1 block w-full form-textarea">{{ old('ulasan_isu_rj', $kertasSiasatan->ulasan_isu_rj) }}</textarea> </div>
        </div>
        <hr>

         {{-- === Surat Pemberitahuan Section === --}}
         <h2 class="text-lg font-semibold border-b pb-1">Surat Pemberitahuan (Pem)</h2>
         <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Pem 1</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="pem1_status" value="Cipta" class="form-radio" {{ old('pem1_status', $kertasSiasatan->pem1_status ?? 'Tidak Cipta') == 'Cipta' ? 'checked' : '' }}> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="pem1_status" value="Tidak Cipta" class="form-radio" {{ old('pem1_status', $kertasSiasatan->pem1_status ?? 'Tidak Cipta') == 'Tidak Cipta' ? 'checked' : '' }}> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Pem 2</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="pem2_status" value="Cipta" class="form-radio" {{ old('pem2_status', $kertasSiasatan->pem2_status ?? 'Tidak Cipta') == 'Cipta' ? 'checked' : '' }}> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="pem2_status" value="Tidak Cipta" class="form-radio" {{ old('pem2_status', $kertasSiasatan->pem2_status ?? 'Tidak Cipta') == 'Tidak Cipta' ? 'checked' : '' }}> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Pem 3</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="pem3_status" value="Cipta" class="form-radio" {{ old('pem3_status', $kertasSiasatan->pem3_status ?? 'Tidak Cipta') == 'Cipta' ? 'checked' : '' }}> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="pem3_status" value="Tidak Cipta" class="form-radio" {{ old('pem3_status', $kertasSiasatan->pem3_status ?? 'Tidak Cipta') == 'Tidak Cipta' ? 'checked' : '' }}> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
             <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Pem 4</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="pem4_status" value="Cipta" class="form-radio" {{ old('pem4_status', $kertasSiasatan->pem4_status ?? 'Tidak Cipta') == 'Cipta' ? 'checked' : '' }}> <span class="ml-2">Cipta</span> </label> <label class="inline-flex items-center"> <input type="radio" name="pem4_status" value="Tidak Cipta" class="form-radio" {{ old('pem4_status', $kertasSiasatan->pem4_status ?? 'Tidak Cipta') == 'Tidak Cipta' ? 'checked' : '' }}> <span class="ml-2">Tidak Cipta</span> </label> </div> </fieldset>
         </div>
         <hr>

         {{-- === Isu-Isu Section === --}}
         <h2 class="text-lg font-semibold border-b pb-1">Isu-Isu Pemeriksaan</h2>
         <div class="space-y-3 mt-2">
            {{-- Helper function for cleaner Isu display --}}
            @php
                function render_isu_radio($fieldName, $label, $model) {
                    $currentValue = old($fieldName, $model->$fieldName ?? 'TIADA ISU');
                    echo '<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 border rounded bg-gray-50">';
                    echo '<span class="text-sm text-gray-800 mb-2 sm:mb-0 sm:mr-4">' . e($label) . '</span>';
                    echo '<fieldset class="flex-shrink-0"><div class="flex space-x-4">';
                    echo '<label class="inline-flex items-center"><input type="radio" name="' . e($fieldName) . '" value="YA" class="form-radio" ' . ($currentValue == 'YA' ? 'checked' : '') . '><span class="ml-2 text-sm">YA</span></label>';
                    echo '<label class="inline-flex items-center"><input type="radio" name="' . e($fieldName) . '" value="TIADA ISU" class="form-radio" ' . ($currentValue == 'TIADA ISU' ? 'checked' : '') . '><span class="ml-2 text-sm">TIADA ISU</span></label>';
                    echo '</div></fieldset></div>';
                }
            @endphp

            {!! render_isu_radio('isu_tpr_tuduh', 'Isu: Arahan TPR untuk tuduh tidak dilaksanakan tindakan tuduh', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_ks_lengkap_tiada_rujuk_tpr', 'Isu: KS Telah Lengkap Namun Tidak Rujuk Ke TPR Untuk Mendapatkan Arahan Tuduh', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_tpr_arah_lupus_belum_laksana', 'Isu: TPR Telah Mengarahkan Barang Kes Dilupuskan Namun Tindakan Pelupusan Masih Belum Dilaksanakan', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_tpr_arah_pulang_belum_laksana', 'Isu: TPR Telah Mengarahkan Barang Kes Dipulangkan Semula Kepada Pemilik Namun Tindakan Belum Dilaksanakan', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_tpr_arah_kesan_tangkap_tiada_tindakan', 'Isu:Arahan TPR Supaya Jalankan Usaha Kesan / Tangkap Suspek Namun Tiada Sebarang Tindakan Dilaksanakan', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus', 'Isu: Kes Berkeputusan Jatuh Hukum Barang Kes Tidak Dirujuk Semula Kepada TPR Untuk Arahan Pelupusan', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_nfa_oleh_kbsjd_sahaja', 'Isu: KS Tidak Dirujuk Kepada TPR Untuk NFA Sebaliknya Di NFA Oleh KBSJD Sahaja', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_selesai_jatuh_hukum_belum_kus_fail', 'Isu: Siasatan Yang Telah Selesai Dan Berkeputusan Jatuh Hukum Masih Belum Dilaksanakan Tindakan KUS/Fail', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_ks_warisan_terbengkalai', 'Isu: KS Berstatus Warisan Tiada Sebarang Tindakan Oleh IO/AIO Warisan Untuk Melengkapkan / Menyelesaikan Siasatan', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_kbsjd_simpan_ks', 'Isu: KBSJD Dikesan Menyimpan KS Dan Tidak Mengedarkan KS Itu Kepada IO/AIO Menyebabkan Terbengkalai', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_sio_simpan_ks', 'Isu: SIO Dikesan Menyimpan KS Dan Tidak Mengedarkan KS Itu Kepada IO/AIO Menyebabkan Terbengkalai', $kertasSiasatan) !!}
            {!! render_isu_radio('isu_ks_pada_tpr', 'Isu: KS Masih Berada Pada TPR Dan Belum Dikembalikan Sehingga Waktu Pemeriksaan', $kertasSiasatan) !!}

         </div>
         <hr>

        {{-- === KS Telah Dihantar Ke Section === --}}
        <h2 class="text-lg font-semibold border-b pb-1">Status Penghantaran KS</h2>
        <div class="space-y-4 mt-2">
            {{-- Ke TPR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Hantar Ke TPR Untuk Arahan Lanjut?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_tpr_status" value="YA" class="form-radio" x-model="ks_hantar_tpr_status"> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_tpr_status" value="TIADA ISU" class="form-radio" x-model="ks_hantar_tpr_status"> <span class="ml-2">TIADA ISU</span> </label> </div> </fieldset>
                <div x-show="ks_hantar_tpr_status === 'YA'" x-transition>
                    <label for="ks_hantar_tpr_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Hantar Ke TPR</label>
                    <input type="date" id="ks_hantar_tpr_tarikh" name="ks_hantar_tpr_tarikh"
                           value="{{ old('ks_hantar_tpr_tarikh', optional($kertasSiasatan->ks_hantar_tpr_tarikh)->format('Y-m-d')) }}"
                           :required="ks_hantar_tpr_status === 'YA'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- Ke KJSJ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Hantar Ke KJSJ Untuk Arahan Lanjut?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_kjsj_status" value="YA" class="form-radio" x-model="ks_hantar_kjsj_status"> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_kjsj_status" value="TIADA ISU" class="form-radio" x-model="ks_hantar_kjsj_status"> <span class="ml-2">TIADA ISU</span> </label> </div> </fieldset>
                <div x-show="ks_hantar_kjsj_status === 'YA'" x-transition>
                    <label for="ks_hantar_kjsj_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Hantar Ke KJSJ</label>
                    <input type="date" id="ks_hantar_kjsj_tarikh" name="ks_hantar_kjsj_tarikh"
                           value="{{ old('ks_hantar_kjsj_tarikh', optional($kertasSiasatan->ks_hantar_kjsj_tarikh)->format('Y-m-d')) }}"
                           :required="ks_hantar_kjsj_status === 'YA'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- Ke D5 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Hantar Ke D5 B.Aman Untuk Arahan Lanjut?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_d5_status" value="YA" class="form-radio" x-model="ks_hantar_d5_status"> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_d5_status" value="TIADA ISU" class="form-radio" x-model="ks_hantar_d5_status"> <span class="ml-2">TIADA ISU</span> </label> </div> </fieldset>
                <div x-show="ks_hantar_d5_status === 'YA'" x-transition>
                    <label for="ks_hantar_d5_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Hantar Ke D5</label>
                    <input type="date" id="ks_hantar_d5_tarikh" name="ks_hantar_d5_tarikh"
                           value="{{ old('ks_hantar_d5_tarikh', optional($kertasSiasatan->ks_hantar_d5_tarikh)->format('Y-m-d')) }}"
                           :required="ks_hantar_d5_status === 'YA'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
            {{-- Ke KBSJD --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded bg-gray-50">
                 <fieldset> <legend class="block text-sm font-medium text-gray-700 mb-1">Hantar Ke KBSJD Untuk Arahan Lanjut?</legend> <div class="flex space-x-4"> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_kbsjd_status" value="YA" class="form-radio" x-model="ks_hantar_kbsjd_status"> <span class="ml-2">YA</span> </label> <label class="inline-flex items-center"> <input type="radio" name="ks_hantar_kbsjd_status" value="TIADA ISU" class="form-radio" x-model="ks_hantar_kbsjd_status"> <span class="ml-2">TIADA ISU</span> </label> </div> </fieldset>
                <div x-show="ks_hantar_kbsjd_status === 'YA'" x-transition>
                    <label for="ks_hantar_kbsjd_tarikh" class="block text-sm font-medium text-gray-700">Tarikh Hantar Ke KBSJD</label>
                    <input type="date" id="ks_hantar_kbsjd_tarikh" name="ks_hantar_kbsjd_tarikh"
                           value="{{ old('ks_hantar_kbsjd_tarikh', optional($kertasSiasatan->ks_hantar_kbsjd_tarikh)->format('Y-m-d')) }}"
                           :required="ks_hantar_kbsjd_status === 'YA'"
                           class="mt-1 block w-full form-input">
                </div>
            </div>
        </div>
        <hr>


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

        {{-- Submit Button --}}
        <div class="flex justify-end pt-4 mt-6 border-t">
            <a href="{{ route('kertas_siasatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3 shadow-sm">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                Kemaskini Kertas Siasatan
            </button>
        </div>

        {{-- Add standard form input classes for Tailwind Forms plugin or manual styling --}}
         <style>
            .form-input, .form-select, .form-textarea {
                @apply rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm;
            }
             .form-radio {
                 @apply rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50;
             }
            .form-textarea { @apply resize-vertical; }
            [x-cloak] { display: none !important; } /* Hide Alpine elements until initialized */
         </style>

    </form>
</x-app-layout>