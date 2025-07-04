<x-app-layout>
    {{-- Add DataTables CSS and custom CSS for sort icons --}}
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <style>
        table.dataTable th.dt-ordering-asc::after,
        table.dataTable th.dt-ordering-desc::after {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-left: 0.5em;
        }
        table.dataTable th.dt-ordering-asc::after { content: "\f0de"; } /* fa-sort-up */
        table.dataTable th.dt-ordering-desc::after { content: "\f0dd"; } /* fa-sort-down */
        [x-cloak] { display: none !important; }
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
            {{-- Session Messages for feedback --}}
            @if (session('success')) <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div> @endif
            @if (session('error')) <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">{{ session('error') }}</div> @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat Import!</strong>
                    @error('excel_file')<span class="block sm:inline">{{ $message }}</span>@enderror
                    @if ($errors->has('excel_errors'))
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->get('excel_errors') as $failure)<li>Baris {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }}</li>@endforeach
                        </ul>
                    @endif
                </div>
            @endif

            {{-- Project Details Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-1">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}</p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-papers-modal')"><i class="fas fa-file-upload mr-2"></i> {{ __('Import') }}</x-primary-button>
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}"><i class="fas fa-edit fa-lg"></i></a>
                    </div>
                </div>
                @if($project->description)<p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">{{ $project->description }}</p>@endif
            </div>

                        {{-- Collapsible Summary Tables --}}
            <x-collapsible-table title="KS Lewat Edar (> 24 Jam)" :collection="$ksLewat24Jam" bgColor="bg-red-50 dark:bg-red-900/20" />
            <x-collapsible-table title="KS Terbengkalai (> 3 Bulan)" :collection="$ksTerbengkalai" bgColor="bg-yellow-50 dark:bg-yellow-900/20" />
            <x-collapsible-table title="KS Baru Dikemaskini" :collection="$ksBaruKemaskini" bgColor="bg-green-50 dark:bg-green-900/20" />

            {{-- Tabbed Interface for Datatables --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ activeTab: 'kertasSiasatan' }">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                        <a href="#" @click.prevent="activeTab = 'kertasSiasatan'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'kertasSiasatan' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Kertas Siasatan</a>
                        <a href="#" @click.prevent="activeTab = 'jenayah'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'jenayah' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Jenayah</a>
                        <a href="#" @click.prevent="activeTab = 'narkotik'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'narkotik' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Narkotik</a>
                        <a href="#" @click.prevent="activeTab = 'komersil'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'komersil' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Komersil</a>
                        <a href="#" @click.prevent="activeTab = 'trafikSeksyen'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'trafikSeksyen' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Trafik (Seksyen)</a>
                        <a href="#" @click.prevent="activeTab = 'trafikRule'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'trafikRule' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Trafik (Rule)</a>
                        <a href="#" @click.prevent="activeTab = 'orangHilang'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'orangHilang' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Orang Hilang</a>
                        <a href="#" @click.prevent="activeTab = 'lmm'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'lmm' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">LMM</a>
                    </nav>
                </div>

                <!-- Tab Content Panels -->
                <div class="mt-6">
                    <div x-show="activeTab === 'kertasSiasatan'" x-cloak><div class="overflow-x-auto"><table id="kertas-siasatan-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KS</th><th>Tarikh KS</th><th>Pegawai Penyiasat</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'jenayah'" x-cloak><div class="overflow-x-auto"><table id="jenayah-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KS</th><th>IO/AIO</th><th>Seksyen</th><th>Tarikh Laporan</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'narkotik'" x-cloak><div class="overflow-x-auto"><table id="narkotik-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KS</th><th>IO/AIO</th><th>Seksyen</th><th>Tarikh Laporan</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'komersil'" x-cloak><div class="overflow-x-auto"><table id="komersil-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KS</th><th>IO/AIO</th><th>Seksyen</th><th>Tarikh KS Dibuka</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'trafikSeksyen'" x-cloak><div class="overflow-x-auto"><table id="trafik-seksyen-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KST</th><th>IO/AIO</th><th>Seksyen</th><th>Tarikh Daftar</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'trafikRule'" x-cloak><div class="overflow-x-auto"><table id="trafik-rule-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KST</th><th>IO/AIO</th><th>No Saman</th><th>Tarikh Daftar</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'orangHilang'" x-cloak><div class="overflow-x-auto"><table id="orang-hilang-datatable" class="w-full"><thead><tr><th>Action</th><th>No. KS(OH)</th><th>IO/AIO</th><th>Tarikh Laporan</th><th>Status</th></tr></thead></table></div></div>
                    <div x-show="activeTab === 'lmm'" x-cloak><div class="overflow-x-auto"><table id="lmm-datatable" class="w-full"><thead><tr><th>Action</th><th>No. LMM</th><th>IO/AIO</th><th>Tarikh Laporan</th><th>Status SDR</th></tr></thead></table></div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <x-modal name="import-papers-modal" :show="$errors->any()" focusable>
        <form action="{{ route('projects.import', $project) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Papers to: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sila pilih kategori kertas dan muat naik fail Excel yang sepadan.</p>
            
            <div class="mt-6">
                <label for="paper_type_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Kertas</label>
                <select name="paper_type" id="paper_type_modal" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                    <option value="KertasSiasatan">Kertas Siasatan (Am)</option>
                    <option value="JenayahPaper">Jenayah</option>
                    <option value="NarkotikPaper">Narkotik</option>
                    <option value="KomersilPaper">Komersil</option>
                    <option value="TrafikSeksyenPaper">Trafik (Seksyen)</option>
                    <option value="TrafikRulePaper">Trafik (Rule)</option>
                    <option value="OrangHilangPaper">Orang Hilang</option>
                    <option value="LaporanMatiMengejutPaper">Laporan Mati Mengejut</option>
                </select>
            </div>

            <div class="mt-6">
                <label for="excel_file_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Fail Excel</label>
                <input type="file" name="excel_file" id="excel_file_modal" required accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
        const initializedTables = {};

        // Function to initialize a DataTable if it hasn't been already
        const initDataTable = (tableId, ajaxUrl, columns) => {
            if (!initializedTables[tableId]) {
                initializedTables[tableId] = $('#' + tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: ajaxUrl,
                        type: "POST",
                        data: { _token: '{{ csrf_token() }}' }
                    },
                    columns: columns,
                    scrollX: true, // Enable horizontal scrolling
                    // Add any other default options here
                });
            }
        };

        // Define column configurations for each table
        const tableConfigs = {
            kertasSiasatan: {
                id: 'kertas-siasatan-datatable',
                url: "{{ route('projects.kertas_siasatan_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_ks', name: 'no_ks', title: 'No. KS' },
                    { data: 'tarikh_ks', name: 'tarikh_ks', title: 'Tarikh KS' },
                    { data: 'pegawai_penyiasat', name: 'pegawai_penyiasat', title: 'Pegawai Penyiasat' },
                    { data: 'status_ks', name: 'status_ks', title: 'Status KS' },
                ]
            },
            jenayah: {
                id: 'jenayah-datatable',
                url: "{{ route('projects.jenayah_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_ks', name: 'no_ks', title: 'No. KS' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'seksyen', name: 'seksyen', title: 'Seksyen' },
                    { data: 'tarikh_laporan_polis', name: 'tarikh_laporan_polis', title: 'Tarikh Laporan' }
                ]
            },
            narkotik: {
                id: 'narkotik-datatable',
                url: "{{ route('projects.narkotik_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_ks', name: 'no_ks', title: 'No. KS' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'seksyen', name: 'seksyen', title: 'Seksyen' },
                    { data: 'tarikh_laporan_polis', name: 'tarikh_laporan_polis', title: 'Tarikh Laporan' }
                ]
            },
            komersil: {
                id: 'komersil-datatable',
                url: "{{ route('projects.komersil_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_ks', name: 'no_ks', title: 'No. KS' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'seksyen', name: 'seksyen', title: 'Seksyen' },
                    { data: 'tarikh_ks_dibuka', name: 'tarikh_ks_dibuka', title: 'Tarikh KS Dibuka' }
                ]
            },
            trafikSeksyen: {
                id: 'trafik-seksyen-datatable',
                url: "{{ route('projects.trafik_seksyen_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_kst', name: 'no_kst', title: 'No. KST' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'seksyen', name: 'seksyen', title: 'Seksyen' },
                    { data: 'tarikh_daftar', name: 'tarikh_daftar', title: 'Tarikh Daftar' }
                ]
            },
            trafikRule: {
                id: 'trafik-rule-datatable',
                url: "{{ route('projects.trafik_rule_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_kst', name: 'no_kst', title: 'No. KST' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'no_saman', name: 'no_saman', title: 'No Saman' },
                    { data: 'tarikh_daftar', name: 'tarikh_daftar', title: 'Tarikh Daftar' }
                ]
            },
            orangHilang: {
                id: 'orang-hilang-datatable',
                url: "{{ route('projects.orang_hilang_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_ks_oh', name: 'no_ks_oh', title: 'No. KS(OH)' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'tarikh_laporan_polis', name: 'tarikh_laporan_polis', title: 'Tarikh Laporan' },
                    { data: 'status_oh', name: 'status_oh', title: 'Status OH' }
                ]
            },
            lmm: {
                id: 'lmm-datatable',
                url: "{{ route('projects.laporan_mati_mengejut_papers_data', $project->id) }}",
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, title: 'Action' },
                    { data: 'no_lmm', name: 'no_lmm', title: 'No. LMM' },
                    { data: 'io_aio', name: 'io_aio', title: 'IO/AIO' },
                    { data: 'tarikh_laporan_polis', name: 'tarikh_laporan_polis', title: 'Tarikh Laporan' },
                    { data: 'status_sdr', name: 'status_sdr', title: 'Status SDR' }
                ]
            }
        };

        // Initialize the first table immediately
        initDataTable(
            tableConfigs.kertasSiasatan.id,
            tableConfigs.kertasSiasatan.url,
            tableConfigs.kertasSiasatan.columns
        );
        
        // Listen for tab clicks to initialize other tables on demand
        $('a[data-tab]').on('click', function(e) {
            const tabName = $(this).data('tab');
            if (tableConfigs[tabName]) {
                initDataTable(
                    tableConfigs[tabName].id,
                    tableConfigs[tabName].url,
                    tableConfigs[tabName].columns
                );
            }
        });

        // Use Alpine's watcher to trigger initialization when a tab becomes active
        // This is a more robust way to handle dynamic content
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                const activeTab = Alpine.store('app').activeTab; // Assumes you set activeTab in a global store or component
                
                if (tableConfigs[activeTab]) {
                    initDataTable(
                        tableConfigs[activeTab].id,
                        tableConfigs[activeTab].url,
                        tableConfigs[activeTab].columns
                    );
                }
            });
        });

    });
    </script>
    @endpush
</x-app-layout>