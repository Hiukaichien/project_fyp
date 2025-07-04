<x-app-layout>
    {{-- Add DataTables CSS and custom CSS for sort icons --}}
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <style>
        /* Add sorting icons to the table headers */
        table.dataTable th.dt-ordering-asc::after,
        table.dataTable th.dt-ordering-desc::after {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-left: 0.5em;
        }
        table.dataTable th.dt-ordering-asc::after {
            content: "\f0de"; /* fa-sort-up */
        }
        table.dataTable th.dt-ordering-desc::after {
            content: "\f0dd"; /* fa-sort-down */
        }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Project Dashboard: ') }} {{ $project->name }}
            </h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                ← {{ __('Back to Projects List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Session Messages etc. --}}
            @if (session('success')) <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">{{ session('success') }}</div> @endif
            @if (session('error') || $errors->has('excel_errors') || $errors->has('excel_file'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat Import!</strong>
                    @if (session('error'))<span class="block sm:inline">{{ session('error') }}</span>@endif
                    @error('excel_file')<span class="block sm:inline">{{ $message }}</span>@enderror
                    @if ($errors->has('excel_errors'))
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->get('excel_errors') as $failure)<li>Baris {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }} (Nilai: {{ implode(', ', $failure->values()) }})</li>@endforeach
                        </ul>
                    @endif
                </div>
            @endif
            @if (session('info')) <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">{{ session('info') }}</div> @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-1">
                    <div>
                        <h3 class="text-2xl font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}</p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-ks-modal')"><i class="fas fa-file-upload mr-2"></i> {{ __('Import') }}</x-primary-button>
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}"><i class="fas fa-edit fa-lg"></i></a>
                        <a href="{{ route('projects.download_csv', $project) }}" class="text-green-600 dark:text-green-400 hover:text-green-500" title="{{ __('Download Associated Papers CSV') }}"><i class="fas fa-file-csv fa-lg"></i></a>
                    </div>
                </div>
                @if($project->description)<p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t pt-4">{{ $project->description }}</p>@endif
            </div>

            <x-collapsible-table title="KS Lewat Edar (> 24 Jam)" :collection="$ksLewat24Jam" bgColor="bg-red-50 dark:bg-red-900/20" />
            <x-collapsible-table title="KS Terbengkalai (> 3 Bulan)" :collection="$ksTerbengkalai" bgColor="bg-yellow-50 dark:bg-yellow-900/20" />
            <x-collapsible-table title="KS Baru Dikemaskini" :collection="$ksBaruKemaskini" bgColor="bg-green-50 dark:bg-green-900/20" />
            
            <hr class="my-6 border-gray-300 dark:border-gray-700">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">          
                <h4 class="font-semibold text-lg mb-4">{{ __('Associated Kertas Siasatan') }}</h4>
                <div class="overflow-x-auto">
                    <table id="kertas-siasatan-datatable" class="w-full text-sm text-left" style="width:100%">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                @php $fillableColumns = (new \App\Models\KertasSiasatan())->getFillable(); @endphp
                                <th class="px-4 py-3 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">Tindakan</th>
                                <th class="px-4 py-3">No.</th>
                                @foreach($fillableColumns as $column)
                                    @if($column !== 'project_id')<th scope="col" class="px-4 py-3">{{ Str::of($column)->replace('_', ' ')->title() }}</th>@endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <x-modal name="import-ks-modal" :show="$errors->has('excel_file') || $errors->has('excel_errors')" focusable>
        <form action="{{ route('kertas_siasatan.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Import Kertas Siasatan to Project') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Muat naik fail Excel untuk menambah atau mengemaskini rekod Kertas Siasatan untuk projek <strong>{{ $project->name }}</strong>.
            </p>
                                <div class="mb-4 p-4 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-blue-600 rounded text-blue-800 dark:text-blue-200 text-sm">
                        <p>Sila pastikan fail Excel anda mengandungi sekurang-kurangnya lajur berikut dengan tajuk yang betul:</p>
                        <ul class="list-disc list-inside ml-4 mt-2">
                            <li><code>no_kertas_siasatan</code> (Wajib & Unik)</li>
                            <li><code>tarikh_ks</code></li>
                            <li><code>no_repot</code></li>
                            <li><code>pegawai_penyiasat</code>, etc.</li>
                        </ul>
                        <p class="mt-2">Semua rekod dalam fail ini akan dikaitkan secara automatik dengan projek <span class="font-bold">{{ $project->name }}</span>.</p>
                    </div>
            <div class="mt-6">
                <label for="excel_file_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Fail Excel (.xlsx,.xls,.csv)</label>
                <input type="file" name="excel_file" id="excel_file_modal" required accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/40 dark:file:text-blue-200 dark:hover:file:bg-blue-900/60 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button class="ms-3">{{ __('Import File') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script>
    $(document).ready(function() {
        const fillableColumns = @json((new \App\Models\KertasSiasatan())->getFillable());
        let dtColumns = [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }
        ];
        fillableColumns.forEach(function(column) {
            if (column !== 'project_id') {
                dtColumns.push({ data: column, name: column, defaultContent: '-' });
            }
        });

        $('#kertas-siasatan-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('projects.kertas_siasatan_data', $project->id) }}",
                type: "POST",
                data: { _token: '{{ csrf_token() }}' }
            },
            columns: dtColumns,
            order: [[2, 'desc']], // Order by 'no_ks' column
            columnDefs: [
                { targets: 0, width: "100px", className: "sticky left-0 bg-white dark:bg-gray-800" }
            ]
        });
    });
    </script>
    @endpush
</x-app-layout>