@props(['paper', 'mode' => 'view'])

@if($mode === 'edit')
    {{-- Edit Mode Form (Original blue container for clear distinction) --}}
    <div class="bg-blue-50 border-blue-500 p-6 mb-6 rounded-lg">
        <h3 class="text-lg font-medium text-blue-800 mb-4">
            Kemaskini Maklumat IPRS
        </h3>
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
    </div>

@else

    {{-- View Mode Display (Bahagian Style with plain text for statuses) --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-blue-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Maklumat IPRS
            </h3>
        </div>
        <div class="border-t border-gray-200">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">No. Kertas Siasatan</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->iprs_no_kertas_siasatan ?: '-' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Tarikh KS</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($paper->iprs_tarikh_ks)
                            @php
                                // Accepts string or Carbon
                                $tarikh = $paper->iprs_tarikh_ks instanceof \Carbon\Carbon ? $paper->iprs_tarikh_ks : \Carbon\Carbon::parse($paper->iprs_tarikh_ks);
                            @endphp
                            {{ $tarikh->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">No. Repot</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->iprs_no_repot ?: '-' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Jenis Jabatan / KS</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->iprs_jenis_jabatan_ks ?: '-' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Pegawai Penyiasat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->iprs_pegawai_penyiasat ?: '-' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status KS</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $paper->iprs_status_ks ?: '-' }}
                    </dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status Kes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $paper->iprs_status_kes ?: '-' }}
                    </dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Seksyen</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $paper->iprs_seksyen ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endif