{{-- resources/views/kertas_siasatan/upload.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Muat Naik Fail Excel Kertas Siasatan') }}
        </h2>
    </x-slot>

    <div class="py-12"> {{-- Add padding --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> {{-- Add container --}}
            {{-- Remove the old h1 title as it's now in the header slot --}}
            {{-- <h1 class="text-2xl font-semibold text-gray-700 mb-6">Muat Naik Fail Excel Kertas Siasatan</h1> --}}

            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded text-blue-800 text-sm">
                <p>Sila pastikan fail Excel anda mengandungi sekurang-kurangnya lajur berikut dengan tajuk yang betul:</p>
                <ul class="list-disc list-inside ml-4 mt-2">
                    <li><code>no_kertas_siasatan</code> (Wajib & Unik)</li>
                    <li><code>tarikh_ks</code> (Format: YYYY-MM-DD atau DD/MM/YYYY)</li>
                    <li><code>no_repot</code></li>
                    <li><code>jenis_jabatan_ks</code></li>
                    <li><code>pegawai_penyiasat</code></li>
                    <li><code>status_ks</code></li>
                    <li><code>status_kes</code></li>
                    <li><code>seksyen</code></li>
                </ul>
                <p class="mt-2">Sistem akan cuba mengemaskini rekod sedia ada berdasarkan <code>no_kertas_siasatan</code> atau mencipta rekod baru.</p>
            </div>

            {{-- Display general form errors --}}
            @if ($errors->any() && !$errors->has('excel_errors'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Ralat!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
             {{-- Note: Excel validation errors are handled in the main layout (app.blade.php) --}}


            <form action="{{ route('kertas_siasatan.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
                @csrf
                <div>
                    <label for="excel_file" class="block text-sm font-medium text-gray-700">Pilih Fail Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="excel_file" id="excel_file" required accept=".xlsx, .xls, .csv"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                  border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                     @error('excel_file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-6">
                     <a href="{{ route('kertas_siasatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Muat Naik & Proses
                    </button>
                </div>
            </form>
        </div> {{-- Close container div --}}
    </div> {{-- Close padding div --}}
</x-app-layout>