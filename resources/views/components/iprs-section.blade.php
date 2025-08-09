@props(['paper', 'mode' => 'view'])

<div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
    <div class="flex items-center mb-4">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-lg font-medium text-blue-800">
                Maklumat Standard IPRS
                @if($mode === 'view')
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Rujukan Sahaja
                    </span>
                @endif
            </h3>
            <p class="mt-1 text-sm text-blue-700">
                8 elemen standard untuk semua jenis kertas siasatan mengikut format IPRS
                @if($mode === 'view')
                    <span class="text-blue-600 font-medium">(Data rujukan - tidak boleh diubah dalam audit)</span>
                @endif
            </p>
        </div>
    </div>

    @if($mode === 'edit')
        {{-- Edit Mode Form --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="iprs_no_kertas_siasatan" class="block text-sm font-medium text-gray-700">
                    1. No. Kertas Siasatan <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="iprs_no_kertas_siasatan" 
                       id="iprs_no_kertas_siasatan"
                       value="{{ old('iprs_no_kertas_siasatan', $paper->iprs_no_kertas_siasatan ?? '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: KS001/2024">
                @error('iprs_no_kertas_siasatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_tarikh_ks" class="block text-sm font-medium text-gray-700">
                    2. Tarikh KS <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="iprs_tarikh_ks" 
                       id="iprs_tarikh_ks"
                       value="{{ old('iprs_tarikh_ks', optional($paper->iprs_tarikh_ks)->format('Y-m-d')) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('iprs_tarikh_ks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_no_repot" class="block text-sm font-medium text-gray-700">
                    3. No. Repot <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="iprs_no_repot" 
                       id="iprs_no_repot"
                       value="{{ old('iprs_no_repot', $paper->iprs_no_repot ?? '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: RPT001/2024">
                @error('iprs_no_repot')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_jenis_jabatan_ks" class="block text-sm font-medium text-gray-700">
                    4. Jenis Jabatan / KS <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="iprs_jenis_jabatan_ks" 
                       id="iprs_jenis_jabatan_ks"
                       value="{{ old('iprs_jenis_jabatan_ks', $paper->iprs_jenis_jabatan_ks ?? class_basename($paper)) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: Trafik Seksyen">
                @error('iprs_jenis_jabatan_ks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_pegawai_penyiasat" class="block text-sm font-medium text-gray-700">
                    5. Pegawai Penyiasat <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="iprs_pegawai_penyiasat" 
                       id="iprs_pegawai_penyiasat"
                       value="{{ old('iprs_pegawai_penyiasat', $paper->iprs_pegawai_penyiasat ?? '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nama Pegawai Penyiasat">
                @error('iprs_pegawai_penyiasat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_status_ks" class="block text-sm font-medium text-gray-700">
                    6. Status KS <span class="text-red-500">*</span>
                </label>
                <select name="iprs_status_ks" 
                        id="iprs_status_ks"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Status KS</option>
                    <option value="Dalam Siasatan" {{ old('iprs_status_ks', $paper->iprs_status_ks) === 'Dalam Siasatan' ? 'selected' : '' }}>Dalam Siasatan</option>
                    <option value="Selesai Siasatan" {{ old('iprs_status_ks', $paper->iprs_status_ks) === 'Selesai Siasatan' ? 'selected' : '' }}>Selesai Siasatan</option>
                    <option value="Tertangguh" {{ old('iprs_status_ks', $paper->iprs_status_ks) === 'Tertangguh' ? 'selected' : '' }}>Tertangguh</option>
                </select>
                @error('iprs_status_ks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_status_kes" class="block text-sm font-medium text-gray-700">
                    7. Status Kes <span class="text-red-500">*</span>
                </label>
                <select name="iprs_status_kes" 
                        id="iprs_status_kes"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Status Kes</option>
                    <option value="Dalam Proses" {{ old('iprs_status_kes', $paper->iprs_status_kes) === 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="Selesai" {{ old('iprs_status_kes', $paper->iprs_status_kes) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Terbengkalai" {{ old('iprs_status_kes', $paper->iprs_status_kes) === 'Terbengkalai' ? 'selected' : '' }}>Terbengkalai</option>
                </select>
                @error('iprs_status_kes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="iprs_seksyen" class="block text-sm font-medium text-gray-700">
                    8. Seksyen <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="iprs_seksyen" 
                       id="iprs_seksyen"
                       value="{{ old('iprs_seksyen', $paper->iprs_seksyen ?? '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: Seksyen 302 KKHP">
                @error('iprs_seksyen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

    @else
        {{-- View Mode Display --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">1. No. Kertas Siasatan</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    {{ $paper->iprs_no_kertas_siasatan ?: '-' }}
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">2. Tarikh KS</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    {{ $paper->iprs_tarikh_ks ? $paper->iprs_tarikh_ks->format('d/m/Y') : '-' }}
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">3. No. Repot</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    {{ $paper->iprs_no_repot ?: '-' }}
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">4. Jenis Jabatan / KS</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    {{ $paper->iprs_jenis_jabatan_ks ?: class_basename($paper) }}
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">5. Pegawai Penyiasat</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    {{ $paper->iprs_pegawai_penyiasat ?: '-' }}
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">6. Status KS</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $paper->iprs_status_ks === 'Selesai Siasatan' ? 'bg-green-100 text-green-800' : 
                           ($paper->iprs_status_ks === 'Dalam Siasatan' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $paper->iprs_status_ks ?: 'Tidak Ditetapkan' }}
                    </span>
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">7. Status Kes</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $paper->iprs_status_kes === 'Selesai' ? 'bg-green-100 text-green-800' : 
                           ($paper->iprs_status_kes === 'Dalam Proses' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $paper->iprs_status_kes ?: 'Tidak Ditetapkan' }}
                    </span>
                </dd>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <dt class="text-sm font-medium text-gray-500 mb-1">8. Seksyen</dt>
                <dd class="text-lg font-semibold text-gray-900">
                    {{ $paper->iprs_seksyen ?: '-' }}
                </dd>
            </div>
        </div>
    @endif
</div>
