<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Project Details: ') }} {{ $project->name }}
            </h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                ← {{ __('Back to Projects') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('info'))
                        <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                            {{ session('info') }}
                        </div>
                    @endif
                    <h3 class="text-2xl font-semibold mb-2">{{ $project->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <strong>{{ __('Date:') }}</strong> {{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}
                    </p>

                    @if($project->description)
                        <div class="mb-4">
                            <h4 class="font-semibold text-lg">{{ __('Description:') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->description }}</p>
                        </div>
                    @endif

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    {{-- Section to display associated papers --}}
                    <h4 class="font-semibold text-lg mb-3">{{ __('Associated Papers') }}</h4>
                    
                    @php
                        // Ensure $allPapers is an array, defaulting to an empty array if null.
                        $allPapers = $project->allAssociatedPapers() ?? []; 
                    @endphp

                    {{-- KERTAS SIASATAN - Displayed like kertas_siasatan.index.blade.php --}}
                    {{-- Check against the specific relationship or the potentially existing key in $allPapers --}}
                    @if (isset($allPapers['kertas_siasatan']) && $allPapers['kertas_siasatan']->isNotEmpty())
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h5 class="font-medium mt-2 capitalize text-md mb-2">Kertas Siasatan Papers</h5>
                            <div x-data="realtimeSearch('{{ route('kertas_siasatan.index') }}', '{{ $project->id }}', 'ks_project_page')" class="space-y-6">
                                {{-- Search Input Area --}}
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow-sm space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                        <div>
                                            <label for="search_no_ks_project" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cari No. KS:</label>
                                            <input type="text" id="search_no_ks_project"
                                                   x-model="searchTerm"
                                                   @input.debounce.500ms="performFilterSearch()"
                                                   placeholder="Taip No. KS..."
                                                   class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                        </div>
                                        <div>
                                            <label for="search_tarikh_ks_project" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh KS:</label>
                                            <input type="text" id="search_tarikh_ks_project"
                                                   x-model="searchTarikhKs"
                                                   @input.debounce.500ms="performFilterSearch()"
                                                   placeholder="YYYY, M/YY, D/M/YY"
                                                   class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                        </div>
                                        <div>
                                            <label for="search_pegawai_penyiasat_project" class="text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai Penyiasat:</label>
                                            <input type="text" id="search_pegawai_penyiasat_project"
                                                   x-model="searchPegawaiPenyiasat"
                                                   @input.debounce.500ms="performFilterSearch()"
                                                   placeholder="Nama Pegawai..."
                                                   class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                        </div>
                                        <div>
                                            <label for="search_status_ks_project" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status KS:</label>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <select id="search_status_ks_project"
                                                        x-model="searchStatusKs"
                                                        @change="performFilterSearch()"
                                                        class="form-select rounded-md shadow-sm text-sm w-full dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                                    <option value="">Semua Status</option>
                                                    <option value="Siasatan Aktif">Siasatan Aktif</option>
                                                    <option value="KUS/Fail">KUS/Fail</option>
                                                </select>
                                                <button @click="searchTerm = ''; searchTarikhKs = ''; searchPegawaiPenyiasat = ''; searchStatusKs = ''; performFilterSearch()"
                                                        type="button" title="Set Semula Carian"
                                                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 rounded-md">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Main Table --}}
                                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
                                    <div style="min-height: 100px;">
                                        <table class="divide-y divide-gray-200 dark:divide-gray-700" style="table-layout: fixed; min-width: 960px;">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                 <tr>
                                                     <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 5%;">No.</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 20%;">No. KS</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">@sortablelink('tarikh_ks', 'Tarikh KS', [], ['page_name' => 'ks_project_page', 'project_id' => $project->id])</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">No. Repot</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">Pegawai Penyiasat</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">Status KS</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">Status Kes</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody x-html="tableHtml" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @include('kertas_siasatan._table_rows', ['kertasSiasatans' => $associatedKertasSiasatanPaginated])
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Pagination Links --}}
                                <div x-html="paginationHtml" @click.prevent="handlePaginationClick($event)">
                                    {{ $associatedKertasSiasatanPaginated->appends(request()->except(['page', 'ks_project_page']))->links() }}
                                </div>
                            </div>
                        </div>
                    @elseif ($project->kertasSiasatan->isEmpty() && !isset($allPapers['kertas_siasatan']))
                        {{-- This case handles if Kertas Siasatan relationship is empty and it wasn't in $allPapers either --}}
                        {{-- Potentially show a specific "No Kertas Siasatan" message here if desired, or let the final empty check handle it --}}
                    @endif
                    {{-- End Kertas Siasatan Section --}}

                    {{-- Placeholder for other associated paper types (can be simple lists or similar complex tables if needed) --}}
                    @php
                        // $allPapers is now guaranteed to be an array.
                        // Unset will not cause an error if the key doesn't exist.
                        unset($allPapers['kertas_siasatan']);
                    @endphp
                    @forelse ($allPapers as $type => $papers)
                        @if ($papers->isNotEmpty())
                            <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                                 <div class="flex justify-between items-center mb-2">
                                    <h5 class="font-medium capitalize text-md">
                                        {{ str_replace('_', ' ', $type) }} 
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $papers->count() }} {{ __('item(s)') }})</span>
                                    </h5>
                                    @php
                                        $indexRouteName = Str::snake(Str::plural(Str::studly($type))) . '.index';
                                        if ($type === 'laporan_mati_mengejut_papers') $indexRouteName = 'laporan_mati_mengejut_papers.index';
                                    @endphp
                                    @if(Route::has($indexRouteName))
                                        <a href="{{ route($indexRouteName, ['project_id_filter' => $project->id]) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ __('Manage All') }} &rarr;
                                        </a>
                                    @endif
                                </div>
                                <ul class="space-y-2">
                                    @foreach ($papers->take(5) as $paper)
                                        <li class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-md">
                                            <span class="text-sm">
                                                @php 
                                                    $displayText = "Paper ID: {$paper->id}"; 
                                                    $viewRoute = null; $editRoute = null; $deleteRoute = null; $paperModelName = '';
                                                    $paperRoutePrefix = Str::snake(Str::plural(Str::studly(Str::singular($type))));
                                                     if ($type === 'laporan_mati_mengejut_papers') $paperRoutePrefix = 'laporan_mati_mengejut_papers';

                                                    if (isset($paper->no_ks)) $displayText = $paper->no_ks;
                                                    elseif (isset($paper->case_number)) $displayText = $paper->case_number;
                                                    elseif (isset($paper->report_no)) $displayText = $paper->report_no;
                                                    elseif (isset($paper->no_kst)) $displayText = $paper->no_kst;
                                                    elseif (isset($paper->reference_no)) $displayText = $paper->reference_no;
                                                    elseif (isset($paper->report_number)) $displayText = $paper->report_number;
                                                    elseif (isset($paper->missing_person_name)) $displayText = $paper->missing_person_name;
                                                    else $displayText = "ID: {$paper->id}";

                                                    if (Route::has($paperRoutePrefix . '.show')) $viewRoute = route($paperRoutePrefix . '.show', $paper->id);
                                                    if (Route::has($paperRoutePrefix . '.edit')) $editRoute = route($paperRoutePrefix . '.edit', $paper->id);
                                                    if (Route::has($paperRoutePrefix . '.destroy')) $deleteRoute = route($paperRoutePrefix . '.destroy', $paper->id);
                                                    
                                                    $paperModelName = Str::studly(Str::singular($type));
                                                    if ($type === 'laporan_mati_mengejut_papers') $paperModelName = 'LaporanMatiMengejutPaper';

                                                @endphp
                                                {{ $displayText }}
                                            </span>
                                            <div class="flex space-x-2">
                                                @if($viewRoute)
                                                    <a href="{{ $viewRoute }}" class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">{{ __('View') }}</a>
                                                @endif
                                                @if($editRoute)
                                                    <a href="{{ $editRoute }}" class="text-xs px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">{{ __('Edit') }}</a>
                                                @endif
                                                @if($paperModelName)
                                                <form action="{{ route('projects.disassociate_paper', ['project' => $project->id, 'paperType' => $paperModelName, 'paperId' => $paper->id]) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to remove this paper from the project?') }}');">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="text-xs px-2 py-1 bg-orange-500 text-white rounded hover:bg-orange-600">{{ __('Remove') }}</button>
                                                </form>
                                                @endif
                                                @if($deleteRoute)
                                                    <form action="{{ $deleteRoute }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to PERMANENTLY DELETE this paper? This action cannot be undone.') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-xs px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">{{ __('Delete') }}</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                     @if($papers->count() > 5)
                                        <li class="text-center pt-2">
                                            <a href="{{ Route::has($indexRouteName) ? route($indexRouteName, ['project_id_filter' => $project->id]) : '#' }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                                {{ __('View all') }} {{ $papers->count() }} {{ str_replace('_', ' ', $type) }}...
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    @empty
                         {{-- This @empty is for $allPapers loop (which now excludes kertas_siasatan). 
                              Check if Kertas Siasatan (from the project model directly) was also empty. --}}
                         @if ($project->kertasSiasatan->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No associated papers found for this project yet.') }}</p>
                         @endif
                    @endforelse
                    {{-- End Associated Papers Section --}}

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    {{-- Section to Add Existing Papers to Project --}}
                    <h4 class="font-semibold text-lg mb-3">{{ __('Add Existing Paper to Project') }}</h4>

                    {{-- Form to add Kertas Siasatan --}}
                    <form action="{{ route('projects.associate_paper', $project->id) }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="paper_type" value="KertasSiasatan">
                        
                        <div class="mb-4">
                            <label for="kertas_siasatan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Select Kertas Siasatan to Add') }}</label>
                            <select name="paper_id" id="kertas_siasatan_id" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">-- {{ __('Select Paper') }} --</option>
                                @forelse ($unassignedKertasSiasatan as $ks)
                                    <option value="{{ $ks->id }}">{{ $ks->no_ks }} - {{ $ks->pegawai_penyiasat ?? 'N/A' }}</option>
                                @empty
                                    <option value="" disabled>{{ __('No unassigned Kertas Siasatan found') }}</option>
                                @endforelse
                            </select>
                            @error('paper_id_KertasSiasatan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Add Kertas Siasatan to Project') }}
                        </button>
                    </form>

                    {{-- Other forms for adding papers remain unchanged --}}
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function realtimeSearch(baseUrl, projectId = null, pageName = 'page') {
            return {
                searchTerm: '',
                searchTarikhKs: '',
                searchPegawaiPenyiasat: '',
                searchStatusKs: '',
                tableHtml: document.querySelector(`[x-data^="realtimeSearch('${baseUrl}'"].replace(/\s/g,'') === this.\$el.getAttribute('x-data').replace(/\s/g,''))`).querySelector('tbody').innerHTML,
                paginationHtml: document.querySelector(`[x-data^="realtimeSearch('${baseUrl}'"].replace(/\s/g,'') === this.\$el.getAttribute('x-data').replace(/\s/g,''))`).querySelector('[x-html="paginationHtml"]').innerHTML,
                loading: false,
                loadingTimeout: null,
                projectId: projectId,
                pageName: pageName,
                baseUrl: baseUrl,

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.searchTerm = urlParams.get('search_no_ks_project') || '';
                    this.searchTarikhKs = urlParams.get('search_tarikh_ks_project') || '';

                    const currentTableContainer = this.$el;
                    this.tableHtml = currentTableContainer.querySelector('tbody').innerHTML;
                    this.paginationHtml = currentTableContainer.querySelector('[x-html="paginationHtml"]').innerHTML;

                    this.$el.querySelectorAll('thead th a').forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            const url = new URL(e.currentTarget.href);
                            this.applyFiltersAndSortToUrl(url);
                            this.fetchResults(url, true);
                        });
                    });
                },
                
                performFilterSearch() {
                    const url = new URL(this.baseUrl);
                    this.applyFiltersAndSortToUrl(url);
                    url.searchParams.set(this.pageName, '1');
                    this.fetchResults(url, false);
                },
                
                applyFiltersAndSortToUrl(urlInstance) {
                    const updateSearchParam = (key, value) => {
                        if (value) urlInstance.searchParams.set(key, value);
                        else urlInstance.searchParams.delete(key);
                    };
                    updateSearchParam('search_no_ks', this.searchTerm); 
                    updateSearchParam('search_tarikh_ks', this.searchTarikhKs);
                    updateSearchParam('search_pegawai_penyiasat', this.searchPegawaiPenyiasat);
                    updateSearchParam('search_status_ks', this.searchStatusKs);

                    if (this.projectId) {
                        urlInstance.searchParams.set('project_id', this.projectId);
                    }
                    urlInstance.searchParams.set('page_name', this.pageName);

                    const currentWindowUrlParams = new URLSearchParams(window.location.search);
                    if (currentWindowUrlParams.has('sort') && !urlInstance.searchParams.has('sort')) {
                        urlInstance.searchParams.set('sort', currentWindowUrlParams.get('sort'));
                    }
                    if (currentWindowUrlParams.has('direction') && !urlInstance.searchParams.has('direction')) {
                        urlInstance.searchParams.set('direction', currentWindowUrlParams.get('direction'));
                    }
                },

                fetchResults(url, maintainScroll = false) {
                    clearTimeout(this.loadingTimeout);
                    this.loading = true;
                    const scrollY = maintainScroll ? window.scrollY : undefined;
                    const tableBody = this.$el.querySelector('tbody');
                    const paginationDiv = this.$el.querySelector('[x-html="paginationHtml"]');

                    this.loadingTimeout = setTimeout(() => {
                        if (this.loading) {
                            tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-gray-500 dark:text-gray-300"><svg class="animate-spin h-5 w-5 text-gray-500 dark:text-gray-300 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuatkan...</td></tr>';
                            paginationDiv.innerHTML = '';
                        }
                    }, 300);

                    fetch(url.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', }
                    })
                    .then(response => {
                        clearTimeout(this.loadingTimeout);
                        if (!response.ok) { return response.text().then(text => { throw new Error(`Network response was not ok (${response.status}): ${text}`); }); }
                        return response.json();
                    })
                    .then(data => {
                        tableBody.innerHTML = data.table_html;
                        paginationDiv.innerHTML = data.pagination_html;
                        if (window.history.pushState) {
                            const newUrl = new URL(window.location.href);
                            url.searchParams.forEach((value, key) => newUrl.searchParams.set(key, value));
                            if (this.projectId && newUrl.toString() !== window.location.href) { 
                            }
                        }
                        if (maintainScroll && typeof scrollY !== 'undefined') {
                            this.$nextTick(() => { window.scrollTo(0, scrollY); });
                        }
                    })
                    .catch(error => {
                        clearTimeout(this.loadingTimeout);
                        console.error('Error fetching search results:', error);
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-red-500 py-4">Ralat memuatkan hasil carian.</td></tr>';
                        paginationDiv.innerHTML = '<p class="text-red-500 text-center">Ralat memuatkan paginasi.</p>';
                    })
                    .finally(() => { this.loading = false; });
                },

                handlePaginationClick(event) {
                    const link = event.target.closest('a');
                    if (link && link.href) {
                        event.preventDefault();
                        const fetchUrl = new URL(link.href);
                        this.applyFiltersAndSortToUrl(fetchUrl); 
                        this.fetchResults(fetchUrl, true);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>