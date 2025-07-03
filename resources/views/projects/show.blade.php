<x-app-layout>
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
            {{-- Display specific Excel import errors --}}
            @if (session('error') || $errors->has('excel_errors') || $errors->has('excel_file'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat Import!</strong>
                    @if (session('error'))
                        <span class="block sm:inline">{{ session('error') }}</span>
                    @endif
                    @error('excel_file')
                        <span class="block sm:inline">{{ $message }}</span>
                    @enderror
                    @if ($errors->has('excel_errors'))
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
                        {{-- Import Button --}}
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-ks-modal')">
                            <i class="fas fa-file-upload mr-2"></i>
                            {{ __('Import') }}
                        </x-primary-button>
                        
                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}">
                            <i class="fas fa-edit fa-lg"></i>
                        </a>
                        <a href="{{ route('projects.download_csv', $project) }}" class="text-green-600 dark:text-green-400 hover:text-green-500" title="{{ __('Download Associated Papers CSV') }}">
                            <i class="fas fa-file-csv fa-lg"></i>
                        </a>
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

            {{-- KERTAS SIASATAN TABLE SECTION --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">          
                <div x-data="realtimeSearchForProjectKS('{{ route('projects.show', $project) }}', '{{ $project->id }}')" class="space-y-6">
                    <h4 class="font-semibold text-lg">{{ __('Associated Kertas Siasatan') }}</h4>
                    {{-- Search Input Area --}}
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow-sm space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="search_no_ks_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cari No. KS:</label>
                                <input type="text" id="search_no_ks_project_show" x-model="searchTerm" @input.debounce.500ms="performFilterSearch()" placeholder="Taip No. KS..." class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                            </div>
                            <div>
                                <label for="search_tarikh_ks_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh KS:</label>
                                <input type="text" id="search_tarikh_ks_project_show" x-model="searchTarikhKs" @input.debounce.500ms="performFilterSearch()" placeholder="YYYY, M/YY, D/M/YY" class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                            </div>
                            <div>
                                <label for="search_pegawai_penyiasat_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai Penyiasat:</label>
                                <input type="text" id="search_pegawai_penyiasat_project_show" x-model="searchPegawaiPenyiasat" @input.debounce.500ms="performFilterSearch()" placeholder="Nama Pegawai..." class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                            </div>
                            <div>
                                <label for="search_status_ks_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status KS:</label>
                                <div class="flex items-center space-x-2 mt-1">
                                    <select id="search_status_ks_project_show" x-model="searchStatusKs" @change="performFilterSearch()" class="form-select rounded-md shadow-sm text-sm w-full dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                        <option value="">Semua Status</option>
                                        <option value="Siasatan Aktif">Siasatan Aktif</option>
                                        <option value="KUS/Fail">KUS/Fail</option>
                                        <option value="Rujuk TPR">Rujuk TPR</option>
                                        {{-- Add other statuses as needed --}}
                                    </select>
                                    <button @click="resetFiltersAndSearch()" type="button" title="Set Semula Carian" class="p-2 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 rounded-md">
                                        <i class="fas fa-undo-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
                        <div style="min-height: 100px;">
                            <table class="divide-y divide-gray-200 dark:divide-gray-700" style="min-width: 6000px;">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                     <tr>
                                        <th class="sticky left-0 bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tindakan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                                        
                                        {{-- Maklumat Asas --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. KS</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tarikh KS</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. Repot</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jenis Jabatan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pegawai Penyiasat</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status KS</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Kes</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Seksyen</th>
                                        
                                        {{-- Minit & Status --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Minit A</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Minit B</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Minit C</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Minit D</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Edaran</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Terbengkalai</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Kemaskini</th>

                                        {{-- Status Semasa Diperiksa --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status KS Diperiksa</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tarikh Status Diperiksa</th>

                                        {{-- Rakaman Percakapan --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rakam Pengadu</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rakam Saspek</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rakam Saksi</th>

                                        {{-- Barang Kes --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">BK Didaftar</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. Daftar AM</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. Daftar Senjata</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. Daftar Berharga</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gambar Rampasan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kedudukan BK</th>

                                        {{-- Isu Isu --}}
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Isu TPR Tuduh</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Isu KS Lengkap</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Isu TPR Lupus</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Isu TPR Pulang</th>
                                    </tr>
                                </thead>
                                <tbody id="kertas-siasatan-tbody" x-html="tableHtml" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    {{-- Initial content is rendered from the controller --}}
                                    @include('projects._associated_kertas_siasatan_table_rows', [
                                        'kertasSiasatans' => $associatedKertasSiasatanPaginated,
                                        'project' => $project
                                    ])
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pagination-links-container mt-4" x-html="paginationHtml" @click.prevent="handlePaginationClick($event)">
                        @if($associatedKertasSiasatanPaginated)
                            {{ $associatedKertasSiasatanPaginated->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <x-modal name="import-ks-modal" :show="$errors->has('excel_file') || $errors->has('excel_errors')" focusable>
        <form action="{{ route('kertas_siasatan.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Import Kertas Siasatan to Project') }}
            </h2>

            <p class="mt-1 mb-3 text-sm text-gray-600 dark:text-gray-400">
                Muat naik fail Excel untuk menambah atau mengemaskini rekod Kertas Siasatan untuk projek <span class="font-bold">{{ $project->name }}</span>.
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
                <label for="excel_file_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Fail Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="excel_file" id="excel_file_modal" required accept=".xlsx,.xls,.csv"
                       class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100
                              dark:file:bg-blue-900/40 dark:file:text-blue-200 dark:hover:file:bg-blue-900/60
                              border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Import File') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
    function realtimeSearchForProjectKS(baseUrl, projectId) {
        return {
            searchTerm: '', 
            searchTarikhKs: '',
            searchPegawaiPenyiasat: '',
            searchStatusKs: '',
            currentSort: '',
            currentDirection: '',
            tableHtml: '',
            paginationHtml: '',
            loading: false,
            loadingTimeout: null,
            baseUrl: baseUrl,
            projectId: projectId,

            init() {
                const urlParams = new URLSearchParams(window.location.search);
                this.searchTerm = urlParams.get('search_no_ks') || '';
                this.searchTarikhKs = urlParams.get('search_tarikh_ks') || '';
                this.searchPegawaiPenyiasat = urlParams.get('search_pegawai_penyiasat') || '';
                this.searchStatusKs = urlParams.get('search_status_ks') || '';
                this.currentSort = urlParams.get('sort') || '';
                this.currentDirection = urlParams.get('direction') || '';
                
                const tableBody = this.$el.querySelector('#kertas-siasatan-tbody');
                if(tableBody) this.tableHtml = tableBody.innerHTML;
                
                const paginationContainer = this.$el.querySelector('.pagination-links-container');
                if(paginationContainer) this.paginationHtml = paginationContainer.innerHTML;
            },
            
            resetFiltersAndSearch() {
                this.searchTerm = '';
                this.searchTarikhKs = '';
                this.searchPegawaiPenyiasat = '';
                this.searchStatusKs = '';
                this.performFilterSearch();
            },

            performFilterSearch() {
                const url = new URL(this.baseUrl);
                this.applyParamsToUrl(url, true);
                this.fetchResults(url);
            },
            
            applyParamsToUrl(urlInstance, resetPageToOne = false, pageNumber = null) {
                if (this.searchTerm) urlInstance.searchParams.set('search_no_ks', this.searchTerm); else urlInstance.searchParams.delete('search_no_ks');
                if (this.searchTarikhKs) urlInstance.searchParams.set('search_tarikh_ks', this.searchTarikhKs); else urlInstance.searchParams.delete('search_tarikh_ks');
                if (this.searchPegawaiPenyiasat) urlInstance.searchParams.set('search_pegawai_penyiasat', this.searchPegawaiPenyiasat); else urlInstance.searchParams.delete('search_pegawai_penyiasat');
                if (this.searchStatusKs) urlInstance.searchParams.set('search_status_ks', this.searchStatusKs); else urlInstance.searchParams.delete('search_status_ks');
                if (this.currentSort) urlInstance.searchParams.set('sort', this.currentSort); else urlInstance.searchParams.delete('sort');
                if (this.currentDirection) urlInstance.searchParams.set('direction', this.currentDirection); else urlInstance.searchParams.delete('direction');

                urlInstance.searchParams.set('project_id', this.projectId);
                
                if (resetPageToOne) urlInstance.searchParams.set('page', '1');
                else if (pageNumber) urlInstance.searchParams.set('page', pageNumber);
            },

            fetchResults(url, maintainScroll = false) {
                clearTimeout(this.loadingTimeout);
                this.loading = true;
                const scrollY = maintainScroll ? window.scrollY : undefined;

                this.loadingTimeout = setTimeout(() => {
                    if (this.loading) {
                        this.tableHtml = `<tr><td colspan="35" class="text-center py-10 text-gray-500"><svg class="animate-spin h-5 w-5 text-gray-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuatkan...</td></tr>`;
                        this.paginationHtml = '';
                    }
                }, 300);

                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(response => {
                    clearTimeout(this.loadingTimeout);
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    this.tableHtml = data.table_html;
                    this.paginationHtml = data.pagination_html;
                    
                    const newUrlState = new URL(window.location.href);
                    newUrlState.search = url.search;
                    if (newUrlState.toString() !== window.location.href) {
                        window.history.pushState({path: newUrlState.toString()}, '', newUrlState.toString());
                    }

                    if (maintainScroll && typeof scrollY !== 'undefined') {
                        this.$nextTick(() => { window.scrollTo(0, scrollY); });
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    this.tableHtml = `<tr><td colspan="35" class="text-center text-red-500 py-4">Ralat memuatkan hasil carian.</td></tr>`;
                    this.paginationHtml = '<p class="text-red-500 text-center">Ralat memuatkan paginasi.</p>';
                })
                .finally(() => { this.loading = false; });
            },

            handlePaginationClick(event) {
                const link = event.target.closest('a');
                if (link && link.href) {
                    const clickedUrl = new URL(link.href);
                    const pageNumber = clickedUrl.searchParams.get('page') || '1';
                    
                    const fetchUrl = new URL(this.baseUrl);
                    this.applyParamsToUrl(fetchUrl, false, pageNumber);
                    this.fetchResults(fetchUrl, true);
                }
            },
            
            handleSortClick(column) {
                if (this.currentSort === column) {
                    if (this.currentDirection === 'asc') {
                        this.currentDirection = 'desc';
                    } else {
                        this.currentSort = '';
                        this.currentDirection = '';
                    }
                } else {
                    this.currentSort = column;
                    this.currentDirection = 'asc';
                }
                this.performFilterSearch();
            }
        }
    }
    </script>
    @endpush
</x-app-layout>