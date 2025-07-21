{{-- FILE: resources/views/projects/show.blade.php (Part 1 of 5) --}}
@php
    // --- DYNAMIC CONFIGURATION SETUP ---
    use App\Models\Jenayah;
    use App\Models\Narkotik;
    use App\Models\Komersil;
    use App\Models\TrafikSeksyen;
    use App\Models\TrafikRule;
    use App\Models\OrangHilang;
    use App\Models\LaporanMatiMengejut;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    // A single source of truth for all table configurations.
    // Keys are PascalCase to match the $dashboardData array from the controller.
    $paperTypes = [
        'Jenayah' => ['model' => new Jenayah(), 'route' => 'projects.jenayah_data', 'title' => 'Jenayah'],
        'Narkotik' => ['model' => new Narkotik(), 'route' => 'projects.narkotik_data', 'title' => 'Narkotik'],
        'Komersil' => ['model' => new Komersil(), 'route' => 'projects.komersil_data', 'title' => 'Komersil'],
        'TrafikSeksyen' => ['model' => new TrafikSeksyen(), 'route' => 'projects.trafik_seksyen_data', 'title' => 'Trafik Seksyen'],
        'TrafikRule' => ['model' => new TrafikRule(), 'route' => 'projects.trafik_rule_data', 'title' => 'Trafik Rule'],
        'OrangHilang' => ['model' => new OrangHilang(), 'route' => 'projects.orang_hilang_data', 'title' => 'Orang Hilang'],
        'LaporanMatiMengejut' => ['model' => new LaporanMatiMengejut(), 'route' => 'projects.laporan_mati_mengejut_data', 'title' => 'LMM'],
    ];

    $ignoreColumns = ['id', 'user_id', 'project_id', 'created_at', 'updated_at'];
@endphp

<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.1/css/fixedColumns.dataTables.css">
    <style>
        table.dataTable th.dt-ordering-asc::after, table.dataTable th.dt-ordering-desc::after { font-family: "Font Awesome 5 Free"; font-weight: 900; margin-left: 0.5em; }
        table.dataTable th.dt-ordering-asc::after { content: "\f0de"; }
        table.dataTable th.dt-ordering-desc::after { content: "\f0dd"; }
        .is-restoring-scroll { visibility: hidden; }
        .datatable-container-loading { min-height: 400px; }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-black-200 leading-tight">{{ __('Dashboard Projek: ') }} {{ $project->name }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-blue-100 underline">← {{ __('Kembali ke Senarai Projek') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success')) 
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {{ session('success') }}
                </div> 
            @endif

            @if (session('error'))
                 <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Project Details Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-1">
                    <div>
                        <h3 class="text-2xl dark:text-white font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            @php
                                $date = \Carbon\Carbon::parse($project->project_date);
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
                                    5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
                                ];
                                $malayMonth = $months[$date->month];
                            @endphp
                            {{ $date->day }} {{ $malayMonth }} {{ $date->year }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-papers-modal')"><i class="fas fa-file-upload mr-2"></i> {{ __('muat naik') }}</x-primary-button>
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'export-papers-modal')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-file-download mr-2"></i> {{ __('Eksport') }}
                        </button>
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Projek') }}"><i class="fas fa-edit fa-lg"></i></a>
                    </div>
                </div>
                @if($project->description)<p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t pt-4">{{ $project->description }}</p>@endif
            </div>

{{-- FILE: resources/views/projects/show.blade.php (Part 3 of 5) --}}
            {{-- Unified Tabbed Interface --}}
<div x-data="{ activeTab: sessionStorage.getItem('activeProjectTab') || 'Jenayah' }" 
     x-init="
        // Initialize the DataTable for the starting tab (either from memory or the default).
        initDataTable(activeTab); 
        
        // Watch for when the activeTab changes.
        $watch('activeTab', value => {
            // 1. **Save the new tab's name to session storage.** This is the crucial fix.
            sessionStorage.setItem('activeProjectTab', value);

            // 2. Initialize the DataTable for the newly selected tab.
            initDataTable(value);
        });
     "
     class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- SINGLE Tab Header Navigation --}}
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                        @foreach($paperTypes as $key => $config)
                            <a href="#" @click.prevent="activeTab = '{{ $key }}'"
                               :class="{ 'border-indigo-500 text-indigo-600': activeTab === '{{ $key }}', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== '{{ $key }}' }"
                               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                {{ $config['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                {{-- Tab Content Panels --}}
                <div class="mt-6">
                    @foreach($paperTypes as $key => $config)
                        @php 
                            // Get the dashboard data for the current tab. This now works because keys match.
                            $data = $dashboardData[$key] ?? null;
                        @endphp
                        <div x-show="activeTab === '{{ $key }}'" x-cloak>
                            @if($data)
                                {{-- Dashboard Section (Charts & Summaries) is now INSIDE each tab --}}
                                <div class="mb-12">
                                    <x-dashboard-section :key="$key" :data="$data" />
                                </div>
                                
                                <div class="my-8 border-t border-gray-200 dark:border-gray-700"></div>
                                <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Butiran Terperinci: {{ $config['title'] }}</h4>
                            @endif

                            {{-- DataTable Section --}}
                            <div class="overflow-auto">
                                <table id="{{ $key }}-datatable" class="w-full text-sm text-left" style="width:100%">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-20">
                                        <tr>
                                            {{-- Use the table's actual columns for DataTables --}}
                                            @php $columns = array_diff(Schema::getColumnListing($config['model']->getTable()), $ignoreColumns); @endphp
                                            <th class="px-4 py-3 sticky left-0 bg-gray-50 dark:bg-gray-700 z-30 border-r border-gray-200 dark:border-gray-600">Tindakan</th>
                                            <th class="px-4 py-3">No.</th>
                                            @foreach($columns as $column)
                                                <th scope="col" class="px-4 py-3">{{ Str::of($column)->replace('_', ' ')->title() }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


{{-- FILE: resources/views/projects/show.blade.php (Part 4 of 5) --}}
    {{-- Import Modal --}}
    <x-modal name="import-papers-modal" :show="$errors->has('excel_file') || $errors->has('excel_errors')" focusable>
        <form action="{{ route('projects.import', $project) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-black-100">Muat Naik Kertas Siasatan ke: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-grey-600">Sila pilih kategori kertas dan muat naik fail Excel yang sepadan.</p>

            @if ($errors->has('excel_file') || $errors->has('excel_errors'))
                <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">{{ $errors->first('excel_file') }}</p>
                    @if ($errors->has('excel_errors'))
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->get('excel_errors') as $errorMessage)
                                <li>{{ $errorMessage }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div class="mt-6">
                <label for="paper_type_modal" class="block text-sm font-medium text-gray-700 dark:text-black-200">Kategori Kertas</label>
                <select name="paper_type" id="paper_type_modal" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                    <option value="Jenayah" @if(old('paper_type') == 'Jenayah') selected @endif>Jenayah</option>
                    <option value="Narkotik" @if(old('paper_type') == 'Narkotik') selected @endif>Narkotik</option>
                    <option value="Komersil" @if(old('paper_type') == 'Komersil') selected @endif>Komersil</option>
                    <option value="TrafikSeksyen" @if(old('paper_type') == 'TrafikSeksyen') selected @endif>Trafik Seksyen</option>
                    <option value="TrafikRule" @if(old('paper_type') == 'TrafikRule') selected @endif>Trafik Rule</option>
                    <option value="OrangHilang" @if(old('paper_type') == 'OrangHilang') selected @endif>Orang Hilang</option>
                    <option value="LaporanMatiMengejut" @if(old('paper_type') == 'LaporanMatiMengejut') selected @endif>Laporan Mati Mengejut</option>
                </select>
            </div>
            <div class="mt-6">
                <label for="excel_file_modal" class="block text-sm font-medium text-gray-700 dark:text-black-300">Pilih Fail Excel</label>
                <div class="mt-1 flex items-center">
                    <input type="file" name="excel_file" id="excel_file_modal" required accept=".xlsx,.xls,.csv" class="hidden">
                    <button type="button" onclick="document.getElementById('excel_file_modal').click()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-file-upload mr-2"></i>Pilih Fail
                    </button>
                    <span id="file-name" class="ml-3 text-sm text-gray-500">Tiada fail dipilih</span>
                </div>
                <script>
                    document.getElementById('excel_file_modal').addEventListener('change', function(e) {
                        const fileName = e.target.files[0] ? e.target.files[0].name : 'Tiada fail dipilih';
                        document.getElementById('file-name').textContent = fileName;
                    });
                </script>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="ms-3">{{ __('Muat Naik Fail') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Export Modal -->
    <x-modal name="export-papers-modal" focusable>
        <form action="{{ route('projects.export_papers', $project) }}" method="GET" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">Eksport Kertas Siasatan dari: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sila pilih kategori kertas yang ingin dieksport ke fail CSV.</p>
            <div class="mt-6">
                <label for="paper_type_export" class="block text-sm font-medium text-gray-700 dark:text-gray-700">Kategori Kertas</label>
                <select name="paper_type" id="paper_type_export" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                    <option value="Jenayah">Jenayah</option>
                    <option value="Narkotik">Narkotik</option>
                    <option value="Komersil">Komersil</option>
                    <option value="TrafikSeksyen">Trafik Seksyen</option>
                    <option value="TrafikRule">Trafik Rule</option>
                    <option value="OrangHilang">Orang Hilang</option>
                    <option value="LaporanMatiMengejut">Laporan Mati Mengejut</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-3">
                    Eksport ke CSV
                </button>
            </div>
        </form>
    </x-modal>
{{-- FILE: resources/views/projects/show.blade.php (Part 5 of 5) --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Global variable to keep track of initialized DataTables
    const initializedTables = {};

    // Function to initialize a DataTable for a given tabName (e.g., 'Jenayah')
    function initDataTable(tabName) {
        // If the DataTable for this tab is already initialized, simply return.
        if (initializedTables[tabName]) {
            return;
        }

        const tableId = `#${tabName}-datatable`;

        if (!$(tableId).length) {
            console.warn(`DataTable element not found for tab: ${tabName} with ID: ${tableId}`);
            return;
        }

        const panel = $(tableId).closest('.overflow-auto');
        if (panel.length) {
            panel.addClass('datatable-container-loading dark:text-white');
        }

        @foreach($paperTypes as $key => $config)
            if (tabName === '{{ $key }}') {
                @php
                    // Get the model instance, raw DB columns, and appended accessors for the current model.
                    $modelInstance = new $config['model'];
                    $rawDbColumns = Schema::getColumnListing($modelInstance->getTable());
                    $appendedAccessors = $modelInstance->getAppends();

                    $booleanDbColumnsWithTextAccessors = [
                        'arahan_minit_oleh_sio_status', 'arahan_minit_ketua_bahagian_status', 'arahan_minit_ketua_jabatan_status',
                        'arahan_minit_oleh_ya_tpr_status', 'adakah_barang_kes_didaftarkan', 'adakah_sijil_surat_kebenaran_ipo',
                        'status_id_siasatan_dikemaskini', 'status_rajah_kasar_tempat_kejadian', 'status_gambar_tempat_kejadian',
                        'status_gambar_post_mortem_mayat_di_hospital', 'status_gambar_barang_kes_am', 'status_gambar_barang_kes_berharga', 'status_gambar_barang_kes_kenderaan',
                        'status_gambar_barang_kes_darah', 'status_gambar_barang_kes_kontraban', 'status_rj2', 'status_rj2b',
                        'status_rj9', 'status_rj99', 'status_rj10a', 'status_rj10b', 'status_saman_pdrm_s_257', 'status_saman_pdrm_s_167',
                        'status_semboyan_pertama_wanted_person', 'status_semboyan_kedua_wanted_person', 'status_semboyan_ketiga_wanted_person',
                        'status_penandaan_kelas_warna', 'status_permohonan_laporan_post_mortem_mayat', 'status_laporan_penuh_bedah_siasat',
                        'status_permohonan_laporan_jabatan_kimia', 'status_laporan_penuh_jabatan_kimia', 'status_permohonan_laporan_jabatan_patalogi',
                        'status_laporan_penuh_jabatan_patalogi', 'status_permohonan_laporan_puspakom', 'status_laporan_penuh_puspakom',
                        'status_permohonan_laporan_jkr', 'status_laporan_penuh_jkr', 'status_permohonan_laporan_jpj', 'status_laporan_penuh_jpj',
                        'status_permohonan_laporan_imigresen', 'status_laporan_penuh_imigresen', 'muka_surat_4_barang_kes_ditulis',
                        'muka_surat_4_dengan_arahan_tpr', 'muka_surat_4_keputusan_kes_dicatat', 'fail_lmm_ada_keputusan_koroner'
                    ];

                    $jsonArrayColumns = [
                        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', 'status_pem', 'status_pergerakan_barang_kes',
                        'status_barang_kes_selesai_siasatan', 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', 'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                        'adakah_borang_serah_terima_pegawai_tangkapan', 'adakah_borang_serah_terima_pemilik_saksi', 'keputusan_akhir_mahkamah'
                    ];

                    $dtColumns = [
                        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                    ];

                    foreach($rawDbColumns as $column) {
                        if (in_array($column, ['id', 'project_id', 'created_at', 'updated_at'])) continue;

                        $columnConfig = ['name' => $column, 'defaultContent' => '-', 'orderable' => true, 'searchable' => true];

                        if (in_array($column, $booleanDbColumnsWithTextAccessors)) {
                            $accessorName = $column . '_text';
                            if (in_array($accessorName, $appendedAccessors)) {
                                $columnConfig['data'] = $accessorName;
                                $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title() . ' (Status)';
                            } else {
                                $columnConfig['data'] = $column;
                                $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title();
                            }
                        } else if (in_array($column, $jsonArrayColumns)) {
                            $columnConfig['data'] = $column;
                            $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title();
                            $columnConfig['orderable'] = false;
                            $columnConfig['searchable'] = false;
                            // Use a placeholder string for the render function
                            $columnConfig['render'] = '%%JSON_RENDER%%';
                        } else {
                            $columnConfig['data'] = $column;
                            $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title();
                        }
                        $dtColumns[] = $columnConfig;
                    }
                @endphp

                // Step 1: Get the column configuration from PHP
                let dtColumnsConfig = @json($dtColumns);

                // Step 2: Define the actual render function in JavaScript
                const jsonRenderFunction = function(data, type, row) {
                    if (data === null || data === undefined) return "-";
                    let parsedData = data;
                    // Check if data is a string that looks like a JSON array
                    if (typeof data === "string" && data.startsWith('[') && data.endsWith(']')) {
                        try {
                            parsedData = JSON.parse(data);
                        } catch (e) {
                            return data; // Return original string if it's not valid JSON
                        }
                    }
                    // Check if we now have a valid array
                    if (Array.isArray(parsedData)) {
                        return parsedData.length > 0 ? parsedData.join(", ") : "-";
                    }
                    // If not an array or parsable string, return it as is
                    return parsedData;
                };

                // Step 3: Loop through the config and replace the placeholder
                dtColumnsConfig.forEach(function(column) {
                    if (column.render === '%%JSON_RENDER%%') {
                        column.render = jsonRenderFunction;
                    }
                });

                // Step 4: Initialize the DataTable with the corrected configuration
                $(tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route($config['route'], $project->id) }}",
                        type: "POST",
                        data: { _token: '{{ csrf_token() }}' }
                    },
                    columns: dtColumnsConfig, // Use the processed JavaScript variable
                    order: [[2, 'desc']],
                    columnDefs: [{
                        targets: 0,
                        className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                    }],
                    fixedColumns: { left: 1 },
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tunjukkan _MENU_ entri",
                        info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                        infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                        emptyTable: "Tiada data tersedia dalam jadual"
                    },
                    "drawCallback": function( settings ) {
                        if (panel.length) {
                            panel.removeClass('datatable-container-loading');
                        }
                    }
                });
                initializedTables[tabName] = true;
            }
        @endforeach
    }

    // Initialize all Chart.js charts on initial page load.
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($dashboardData as $key => $data)
            @php
                $hasIssueData = ($data['lewatCount'] ?? 0) > 0 || ($data['terbengkalaiCount'] ?? 0) > 0 || ($data['kemaskiniCount'] ?? 0) > 0;
                $hasAuditData = ($data['jumlahKeseluruhan'] ?? 0) > 0;
                $jumlahDiperiksa = $data['jumlahDiperiksa'] ?? 0;
                $jumlahBelumDiperiksa = $data['jumlahBelumDiperiksa'] ?? 0;
                $jumlahKeseluruhan = $data['jumlahKeseluruhan'] ?? 0;
            @endphp

            @if($hasAuditData)
                const auditCtx_{{ $key }} = document.getElementById('auditPieChart-{{ $key }}')?.getContext('2d');
                if (auditCtx_{{ $key }}) {
                    new Chart(auditCtx_{{ $key }}, {
                        type: 'pie',
                        data: {
                            labels: ['Jumlah Diperiksa (KS)', 'Jumlah Belum Diperiksa (KS)'],
                            datasets: [{
                                data: [{{ $jumlahDiperiksa }}, {{ $jumlahBelumDiperiksa }}],
                                backgroundColor: ['#0ea5e9', '#cbd5e1'],
                                borderWidth: 0,
                                borderColor: '#FFFFFF'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                                                return data.labels.map(function(label, i) {
                                                    const value = data.datasets[0].data[i];
                                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                    return {
                                                        text: `${label} (${percentage}%) (${value})`,
                                                        fillStyle: data.datasets[0].backgroundColor[i],
                                                        strokeStyle: data.datasets[0].borderColor[i],
                                                        lineWidth: data.datasets[0].borderWidth,
                                                        hidden: chart.getDatasetMeta(0).data[i].hidden,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Peratusan Status Pemeriksaan (Keseluruhan)'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw;
                                            const total = context.chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                            return `${value} (${percentage})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

            @if($hasIssueData)
                const statusCtx_{{ $key }} = document.getElementById('statusPieChart-{{ $key }}')?.getContext('2d');
                if (statusCtx_{{ $key }}) {
                    new Chart(statusCtx_{{ $key }}, {
                        type: 'pie',
                        data: {
                            labels: ['KS Lewat Edar (> 48 Jam)', 'KS Terbengkalai (> 3 Bulan)', 'KS Baru Dikemaskini'],
                            datasets: [{
                                data: [{{ $data['lewatCount'] }}, {{ $data['terbengkalaiCount'] }}, {{ $data['kemaskiniCount'] }}],
                                backgroundColor: ['#F87171', '#FBBF24', '#34D399'],
                                borderWidth: 0,
                                borderColor: '#FFFFFF'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                                                return data.labels.map(function(label, i) {
                                                    const value = data.datasets[0].data[i];
                                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                    return {
                                                        text: `${label} (${percentage}%) (${value})`,
                                                        fillStyle: data.datasets[0].backgroundColor[i],
                                                        strokeStyle: data.datasets[0].borderColor[i],
                                                        lineWidth: data.datasets[0].borderWidth,
                                                        hidden: chart.getDatasetMeta(0).data[i].hidden,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Ringkasan Status Isu Siasatan'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw;
                                            const total = context.chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                            return `${value} (${percentage})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif
        @endforeach
    });

    // Handle scroll position restoration
    (function() {
        if (sessionStorage.getItem('scrollPosition')) {
            document.body.classList.add('is-restoring-scroll');
        }
        document.addEventListener('DOMContentLoaded', function () {
            const paginationContainers = document.querySelectorAll('.pagination-links');
            function handlePaginationClick(event) {
                const link = event.target.closest('a');
                if (link && link.href) {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                }
            }
            paginationContainers.forEach(container => {
                container.addEventListener('click', handlePaginationClick);
            });
            const scrollPosition = sessionStorage.getItem('scrollPosition');
            if (scrollPosition) {
                document.body.classList.remove('is-restoring-scroll');
                window.scrollTo(0, parseInt(scrollPosition, 10));
                sessionStorage.removeItem('scrollPosition');
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>