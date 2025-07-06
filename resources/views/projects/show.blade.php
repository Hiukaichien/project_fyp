@php
    // --- DYNAMIC CONFIGURATION SETUP ---
    use App\Models\Jenayah;
    use App\Models\Narkotik;
    use App\Models\Komersil;
    use App\Models\Trafik;
    use App\Models\OrangHilang;
    use App\Models\LaporanMatiMengejut;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    // A single source of truth for all table configurations, now with 6 types
    $paperTypes = [
        'jenayah' => ['model' => new Jenayah(), 'route' => 'projects.jenayah_data', 'title' => 'Jenayah'],
        'narkotik' => ['model' => new Narkotik(), 'route' => 'projects.narkotik_data', 'title' => 'Narkotik'],
        'komersil' => ['model' => new Komersil(), 'route' => 'projects.komersil_data', 'title' => 'Komersil'],
        'trafik' => ['model' => new Trafik(), 'route' => 'projects.trafik_data', 'title' => 'Trafik'],
        'orangHilang' => ['model' => new OrangHilang(), 'route' => 'projects.orang_hilang_data', 'title' => 'Orang Hilang'],
        'lmm' => ['model' => new LaporanMatiMengejut(), 'route' => 'projects.laporan_mati_mengejut_data', 'title' => 'LMM'],
    ];

    // Columns to ignore when dynamically generating the table from the schema
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


        .datatable-container-loading {
            min-height: 400px; /* Adjust this value as needed */
        }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Project Dashboard: ') }} {{ $project->name }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">← {{ __('Back to Projects List') }}</a>
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
                        <h3 class="text-2xl font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}</p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-papers-modal')"><i class="fas fa-file-upload mr-2"></i> {{ __('Import') }}</x-primary-button>
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'export-papers-modal')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-file-download mr-2"></i> {{ __('Export') }}
                        </button>
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}"><i class="fas fa-edit fa-lg"></i></a>
                    </div>
                </div>
                @if($project->description)<p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t pt-4">{{ $project->description }}</p>@endif
            </div>

            {{-- Collapsible Summary Tables --}}
            <x-collapsible-table 
                title="KS Lewat Edar (> 48 Jam)" 
                :collection="$ksLewat24Jam" 
                bgColor="bg-red-50 dark:bg-red-900/20" 
                pageName="lewat_page" 
            />
            <x-collapsible-table 
                title="KS Terbengkalai (> 3 Bulan)" 
                :collection="$ksTerbengkalai" 
                bgColor="bg-yellow-50 dark:bg-yellow-900/20" 
                pageName="terbengkalai_page" 
            />
            <x-collapsible-table 
                title="KS Baru Dikemaskini" 
                :collection="$ksBaruKemaskini" 
                bgColor="bg-green-50 dark:bg-green-900/20" 
                pageName="kemaskini_page" 
            />
            
            <hr class="my-6 border-gray-300 dark:border-gray-700">

            <!-- Main container for the tabbed interface -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                        @php
                            $activeClasses = 'border-indigo-500 text-indigo-600';
                            $inactiveClasses = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
                        @endphp
                        @foreach($paperTypes as $key => $config)
                            <a href="#" data-tab="{{ $key }}" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                {{ $config['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Tab Content Panels -->
                <div class="mt-6">
                    @foreach($paperTypes as $key => $config)
                        <div id="panel-{{ $key }}" class="tab-panel overflow-auto" style="display: none;">
                            <table id="{{ $key }}-datatable" class="w-full text-sm text-left" style="width:100%">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-20">
                                    <tr>
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <x-modal name="import-papers-modal" :show="$errors->has('excel_file') || $errors->has('excel_errors')" focusable>
        <form action="{{ route('projects.import', $project) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Papers to: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sila pilih kategori kertas dan muat naik fail Excel yang sepadan.</p>

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
                <label for="paper_type_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Kertas</label>
                <select name="paper_type" id="paper_type_modal" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                    <option value="Jenayah" @if(old('paper_type') == 'Jenayah') selected @endif>Jenayah</option>
                    <option value="Narkotik" @if(old('paper_type') == 'Narkotik') selected @endif>Narkotik</option>
                    <option value="Komersil" @if(old('paper_type') == 'Komersil') selected @endif>Komersil</option>
                    <option value="Trafik" @if(old('paper_type') == 'Trafik') selected @endif>Trafik</option>
                    <option value="OrangHilang" @if(old('paper_type') == 'OrangHilang') selected @endif>Orang Hilang</option>
                    <option value="LaporanMatiMengejut" @if(old('paper_type') == 'LaporanMatiMengejut') selected @endif>Laporan Mati Mengejut</option>
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

    <!-- Export Modal -->
    <x-modal name="export-papers-modal" focusable>
        <form action="{{ route('projects.export_papers', $project) }}" method="GET" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Export Papers from: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sila pilih kategori kertas yang ingin dieksport ke fail CSV.</p>
            <div class="mt-6">
                <label for="paper_type_export" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Kertas</label>
                <select name="paper_type" id="paper_type_export" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                    <option value="Jenayah">Jenayah</option>
                    <option value="Narkotik">Narkotik</option>
                    <option value="Komersil">Komersil</option>
                    <option value="Trafik">Trafik</option>
                    <option value="OrangHilang">Orang Hilang</option>
                    <option value="LaporanMatiMengejut">Laporan Mati Mengejut</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-3">
                    Export to CSV
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>
    <script>
    $(document).ready(function() {
        const initializedTables = {};
        const activeClasses = 'border-indigo-500 text-indigo-600';
        const inactiveClasses = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';

        function activateTab(tabName) {
            $('.tab-link').removeClass(activeClasses).addClass(inactiveClasses);
            $('.tab-panel').hide();
            $(`.tab-link[data-tab="${tabName}"]`).removeClass(inactiveClasses).addClass(activeClasses);
            $(`#panel-${tabName}`).show();
            initDataTable(tabName);
            sessionStorage.setItem('activeProjectTab', tabName);
        }

        function initDataTable(tabName) {
            if (initializedTables[tabName]) {
                $('#' + tabName + '-datatable').DataTable().columns.adjust().draw();
                return;
            }

            const panel = $('#panel-' + tabName);
            panel.addClass('datatable-container-loading');

            @foreach($paperTypes as $key => $config)
                if (tabName === '{{ $key }}') {
                    @php
                        $columnsForJs = array_diff(Schema::getColumnListing($config['model']->getTable()), $ignoreColumns);
                        $dtColumns = [['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false], ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false]];
                        foreach($columnsForJs as $col) {
                            $dtColumns[] = ['data' => $col, 'name' => $col, 'defaultContent' => '-'];
                        }
                    @endphp

                    $('#{{ $key }}-datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route($config['route'], $project->id) }}",
                            type: "POST",
                            data: { _token: '{{ csrf_token() }}' }
                        },
                        columns: @json($dtColumns),
                        order: [[2, 'desc']],
                        columnDefs: [
                            {
                                targets: 0,
                                className: "sticky left-0 bg-gray-50 dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                            }
                        ],
                        fixedColumns: {
                            left: 1
                        },
                        "drawCallback": function( settings ) {
                            panel.removeClass('datatable-container-loading');
                        }
                    });
                    initializedTables[tabName] = true;
                }
            @endforeach
        }

        $('.tab-link').on('click', function(e) {
            e.preventDefault();
            const tabName = $(this).data('tab');
            activateTab(tabName);
        });

        const savedTab = sessionStorage.getItem('activeProjectTab');
        if (savedTab && $(`.tab-link[data-tab="${savedTab}"]`).length) {
            activateTab(savedTab);
        } else {
            activateTab('jenayah');
        }
    });

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