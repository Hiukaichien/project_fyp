<x-app-layout>
    {{-- Add DataTables CSS to the head --}}
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
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
            {{-- Session Messages and Project Info --}}
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {{ session('success') }}
                </div>
            @endif
             @if (session('error') || $errors->has('excel_errors') || $errors->has('excel_file'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat Import!</strong>
                    @if (session('error'))<span class="block sm:inline">{{ session('error') }}</span>@endif
                    @error('excel_file')<span class="block sm:inline">{{ $message }}</span>@enderror
                    @if ($errors->has('excel_errors'))
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->get('excel_errors') as $failure)
                                <li>Baris {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }} (Nilai: {{ implode(', ', $failure->values()) }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
            @if (session('info'))
                <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-1">
                    <div>
                        <h3 class="text-2xl font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-ks-modal')">
                            <i class="fas fa-file-upload mr-2"></i> {{ __('Import') }}
                        </x-primary-button>
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}"><i class="fas fa-edit fa-lg"></i></a>
                        <a href="{{ route('projects.download_csv', $project) }}" class="text-green-600 dark:text-green-400 hover:text-green-500" title="{{ __('Download Associated Papers CSV') }}"><i class="fas fa-file-csv fa-lg"></i></a>
                    </div>
                </div>
                @if($project->description)
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t pt-4">{{ $project->description }}</p>
                @endif
            </div>

            {{-- Collapsible Stat Tables --}}
            <x-collapsible-table title="KS Lewat Edar (> 24 Jam)" :collection="$ksLewat24Jam" bgColor="bg-red-50 dark:bg-red-900/20" />
            <x-collapsible-table title="KS Terbengkalai (> 3 Bulan)" :collection="$ksTerbengkalai" bgColor="bg-yellow-50 dark:bg-yellow-900/20" />
            <x-collapsible-table title="KS Baru Dikemaskini" :collection="$ksBaruKemaskini" bgColor="bg-green-50 dark:bg-green-900/20" />
            
            <hr class="my-6 border-gray-300 dark:border-gray-700">

            {{-- Other Paper Association Forms --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="font-semibold text-lg mb-3">{{ __('Add Other Existing Papers to Project') }}</h4>
                @php
                    $otherPaperTypesForAdding = [
                        'JenayahPaper' => $unassignedJenayahPapers ?? collect(),
                        'NarkotikPaper' => $unassignedNarkotikPapers ?? collect(),
                        'TrafikSeksyenPaper' => $unassignedTrafikSeksyenPapers ?? collect(),
                        'TrafikRulePaper' => $unassignedTrafikRulePapers ?? collect(),
                        'KomersilPaper' => $unassignedKomersilPapers ?? collect(),
                        'LaporanMatiMengejutPaper' => $unassignedLaporanMatiMengejutPapers ?? collect(),
                        'OrangHilangPaper' => $unassignedOrangHilangPapers ?? collect(),
                    ];
                @endphp

                @foreach($otherPaperTypesForAdding as $modelName => $unassignedPapers)
                    @if($unassignedPapers && $unassignedPapers->isNotEmpty())
                        <div x-data="{
                            searchTerm: '',
                            selectedPaperId: null,
                            selectedPaperText: '',
                            isOpen: false,
                            originalOptions: {{ $unassignedPapers->map(function($paper) {
                                $displayIdentifier = $paper->no_ks ?? $paper->no_kst ?? $paper->no_lmm ?? $paper->no_ks_oh ?? $paper->name ?? "ID: {$paper->id}";
                                return ['id' => $paper->id, 'text' => $displayIdentifier];
                            })->values()->toJson() }},
                            get filteredOptions() {
                                const term = this.searchTerm ? this.searchTerm.trim().toLowerCase() : '';
                                if (term === '') { return this.originalOptions; }
                                return this.originalOptions.filter(option => option.text.toLowerCase().includes(term));
                            },
                            selectOption(option) {
                                this.selectedPaperId = option.id;
                                this.selectedPaperText = option.text;
                                this.searchTerm = option.text;
                                this.isOpen = false;
                            },
                            resetSearch() {
                                if (this.selectedPaperId) { this.selectedPaperId = null; }
                                this.isOpen = true;
                            }
                        }" @click.away="isOpen = false" class="mb-6 relative">
                            <form action="{{ route('projects.associate_paper', $project->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="paper_type" value="{{ $modelName }}">
                                <input type="hidden" name="paper_id" x-model="selectedPaperId">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div class="md:col-span-2">
                                        <label for="{{ Str::snake($modelName) }}_searchable_associate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Search & Select ') }}{{ Str::title(Str::snake($modelName, ' ')) }}</label>
                                        <input type="text"
                                               id="{{ Str::snake($modelName) }}_searchable_associate"
                                               x-model="searchTerm"
                                               @input="resetSearch()"
                                               @focus="isOpen = true"
                                               @click="isOpen = true"
                                               @keydown.escape.prevent="isOpen = false"
                                               placeholder="Type to search..."
                                               autocomplete="off"
                                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <div x-show="isOpen" class="absolute z-10 mt-1 w-full md:w-auto bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto" style="min-width: calc( (100% / 3 * 2) - 1rem );">
                                            <ul class="py-1">
                                                <template x-if="filteredOptions.length === 0 && searchTerm.trim() !== ''"><li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No matching ') }}{{ Str::title(Str::snake($modelName, ' ')) }}{{ __(' found') }}</li></template>
                                                <template x-if="originalOptions.length === 0 && searchTerm.trim() === ''"><li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No unassigned ') }}{{ Str::title(Str::snake($modelName, ' ')) }}{{ __(' found') }}</li></template>
                                                <template x-for="option in filteredOptions" :key="option.id"><li @click="selectOption(option)" class="px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer" x-text="option.text"></li></template>
                                            </ul>
                                        </div>
                                        @if ($errors->has('paper_id') && old('paper_type') == $modelName)
                                            <p class="text-red-500 text-xs mt-1">{{ $errors->first('paper_id') }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <button type="submit" :disabled="!selectedPaperId" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-50 transition ease-in-out duration-150">
                                            {{ __('Add ') }}{{ Str::title(Str::snake($modelName, ' ')) }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-700">

            {{-- KERTAS SIASATAN TABLE SECTION --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">          
                <h4 class="font-semibold text-lg mb-4">{{ __('Associated Kertas Siasatan') }}</h4>
                <div class="overflow-x-auto">
                    <table id="kertas-siasatan-datatable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400" style="width:100%">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Tindakan</th>
                                <th scope="col" class="px-4 py-3">No.</th>
                                <th scope="col" class="px-4 py-3">No. KS</th>
                                <th scope="col" class="px-4 py-3">Tarikh KS</th>
                                <th scope="col" class="px-4 py-3">No. Repot</th>
                                <th scope="col" class="px-4 py-3">Pegawai Penyiasat</th>
                                <th scope="col" class="px-4 py-3">Status KS</th>
                                <th scope="col" class="px-4 py-3">Status Kes</th>
                                <th scope="col" class="px-4 py-3">Seksyen</th>
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
                Muat naik fail Excel untuk menambah atau mengemaskini rekod Kertas Siasatan untuk projek **{{ $project->name }}**.
            </p>
            <div class="mt-6">
                <label for="excel_file_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Fail Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="excel_file" id="excel_file_modal" required accept=".xlsx,.xls,.csv"
                       class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/40 dark:file:text-blue-200 dark:hover:file:bg-blue-900/60 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
        $('#kertas-siasatan-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('projects.kertas_siasatan_data', $project->id) }}',
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'no_ks', name: 'no_ks' },
                { data: 'tarikh_ks', name: 'tarikh_ks' },
                { data: 'no_report', name: 'no_report' },
                { data: 'pegawai_penyiasat', name: 'pegawai_penyiasat' },
                { data: 'status_ks', name: 'status_ks' },
                { data: 'status_kes', name: 'status_kes' },
                { data: 'seksyen', name: 'seksyen' }
            ],
            order: [[1, 'desc']], // Default order by ID
            columnDefs: [
                { "width": "120px", "targets": 0 }
            ]
        });
    });
    </script>
    @endpush
</x-app-layout>