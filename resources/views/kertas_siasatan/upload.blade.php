{{-- resources/views/kertas_siasatan/upload.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Muat Naik Fail Excel Kertas Siasatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Display general session success message --}}
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Berjaya!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Display general session error message --}}
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Ralat!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Display general form validation errors (not specific to Excel rows or file itself) --}}
                @if ($errors->any() && !$errors->has('excel_errors') && !$errors->has('excel_file'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Ralat Borang!</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                @if ($error !== $errors->first('excel_file')) {{-- Avoid duplicating excel_file error --}}
                                    <li>{{ $error }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Display specific file upload validation errors --}}
                @error('excel_file')
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Ralat Fail!</strong>
                        <span class="block sm:inline">{{ $message }}</span>
                    </div>
                @enderror

                {{-- Display Excel row validation errors (from Maatwebsite\Excel\Validators\ValidationException) --}}
                @if ($errors->has('excel_errors'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Terdapat ralat dalam fail Excel pada baris berikut:</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->get('excel_errors') as $failure)
                                <li>Baris {{ $failure->row() }}:
                                    @foreach ($failure->errors() as $error)
                                        {{ $error }}
                                    @endforeach
                                    (Nilai: {{ implode(', ', $failure->values()) }})
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded text-blue-800 text-sm">
                    <p>Sila pastikan fail Excel anda mengandungi sekurang-kurangnya lajur berikut dengan tajuk yang betul:</p>
                    <ul class="list-disc list-inside ml-4 mt-2">
                        <li><code>no_kertas_siasatan</code> (Wajib & Unik)</li>
                        <li><code>tarikh_ks</code> (Format: YYYY-MM-DD atau DD/MM/YYYY - sistem akan cuba memproses kedua-duanya)</li>
                        <li><code>no_repot</code></li>
                        <li><code>jenis_jabatan_ks</code></li>
                        <li><code>pegawai_penyiasat</code></li>
                        <li><code>status_ks</code></li>
                        <li><code>status_kes</code></li>
                        <li><code>seksyen</code></li>
                    </ul>
                    <p class="mt-2">Sistem akan cuba mengemaskini rekod sedia ada berdasarkan <code>no_kertas_siasatan</code> atau mencipta rekod baru.</p>
                </div>


                <form action="{{ route('kertas_siasatan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf {{-- CRITICAL: CSRF Protection --}}

                    <div class="mb-4">
                        <label for="excel_file" class="block text-sm font-medium text-gray-700">Pilih Fail Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" name="excel_file" id="excel_file" required
                               accept=".xlsx,.xls,.csv"
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                      border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('kertas_siasatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Muat Naik & Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>