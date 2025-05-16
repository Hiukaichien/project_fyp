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
                    {{-- Session Messages and Project Info --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>{{ __('') }}</strong> {{ \Carbon\Carbon::parse($project->project_date)->format('F d, Y') }}
                        </p>
                    </div>

                    @if($project->description)
                        <div class="mb-4">
                            <h4 class="font-semibold text-lg">{{ __('') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->description }}</p>
                        </div>
                    @endif

                    

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    <h4 class="font-semibold text-lg mb-3">{{ __('Add Existing Paper to Project') }}</h4>

                    {{-- Kertas Siasatan Association Form with Searchable Dropdown --}}
                    <div x-data="{
                        searchTerm: '',
                        selectedPaperId: null,
                        selectedPaperText: '',
                        isOpen: false,
                        originalOptions: {{ $unassignedKertasSiasatan->map(fn($ks) => ['id' => $ks->id, 'text' => $ks->no_ks . ' - ' . ($ks->pegawai_penyiasat ?? 'N/A')])->values()->toJson() }},
                        get filteredOptions() {
                            const term = this.searchTerm ? this.searchTerm.trim().toLowerCase() : '';
                            if (term === '') {
                                return this.originalOptions;
                            }
                            return this.originalOptions.filter(option => 
                                option.text.toLowerCase().includes(term)
                            );
                        },
                        selectOption(option) {
                            this.selectedPaperId = option.id;
                            this.selectedPaperText = option.text;
                            this.searchTerm = option.text;
                            this.isOpen = false;
                        },
                        resetSearch() {
                            if (this.selectedPaperId) {
                                this.selectedPaperId = null;
                            }
                            this.isOpen = true;
                        }
                    }" @click.away="isOpen = false" class="mb-6 relative">
                        <form action="{{ route('projects.associate_paper', $project->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="paper_type" value="KertasSiasatan">
                            <input type="hidden" name="paper_id" x-model="selectedPaperId">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <div class="md:col-span-2">
                                    <label for="kertas_siasatan_searchable_associate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('') }}</label>
                                    <input type="text" 
                                           id="kertas_siasatan_searchable_associate"
                                           x-model="searchTerm"
                                           @input="resetSearch()"
                                           @focus="isOpen = true"
                                           @click="isOpen = true"
                                           @keydown.escape.prevent="isOpen = false"
                                           @keydown.down.prevent="isOpen = true"
                                           placeholder="Type to search No. KS or Pegawai..."
                                           autocomplete="off"
                                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    
                                    <div x-show="isOpen" 
                                         class="absolute z-10 mt-1 w-full md:w-auto bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"
                                         style="min-width: calc( (100% / 3 * 2) - 1rem );">
                                        <ul class="py-1">
                                            <template x-if="filteredOptions.length === 0 && searchTerm.trim() !== ''">
                                                <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No matching Kertas Siasatan found') }}</li>
                                            </template>
                                            <template x-if="originalOptions.length === 0 && searchTerm.trim() === ''">
                                                 <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No unassigned Kertas Siasatan found') }}</li>
                                            </template>
                                            <template x-for="option in filteredOptions" :key="option.id">
                                                <li @click="selectOption(option)"
                                                    class="px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                                                    x-text="option.text">
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    @if ($errors->has('paper_id') && old('paper_type') == 'KertasSiasatan')
                                        <p class="text-red-500 text-xs mt-1">{{ $errors->first('paper_id') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <button type="submit" :disabled="!selectedPaperId"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-50 transition ease-in-out duration-150">
                                        {{ __('Add Kertas Siasatan') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    <div class="flex items-baseline mb-3">
                        <h4 class="font-semibold text-lg">{{ __('Associated Papers') }}</h4>
                        @if($associatedKertasSiasatanPaginated && $associatedKertasSiasatanPaginated->total() > 0)
                            <span class="ml-2 text-sm">({{ $associatedKertasSiasatanPaginated->total() }}{{ __('') }})</span>
                        @else
                            <span class="ml-2 text-sm">(0 {{ __('') }})</span>
                        @endif
                    </div>
                    
                    @php
                        $allPapers = $project->allAssociatedPapers() ?? []; 
                        $pageNameForKSTable = 'ks_project_page'; 
                    @endphp

                    {{-- KERTAS SIASATAN TABLE SECTION --}}
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">          
                        <div x-data="realtimeSearchForProjectKS('{{ route('kertas_siasatan.index') }}', '{{ $project->id }}', '{{ $pageNameForKSTable }}')" class="space-y-6">
                            {{-- Search Input Area --}}
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow-sm space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                    <div>
                                        <label for="search_no_ks_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cari No. KS:</label>
                                        <input type="text" id="search_no_ks_project_show"
                                               x-model="searchTerm"
                                               @input.debounce.500ms="performFilterSearch()"
                                               placeholder="Taip No. KS..."
                                               class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label for="search_tarikh_ks_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh KS:</label>
                                        <input type="text" id="search_tarikh_ks_project_show"
                                               x-model="searchTarikhKs"
                                               @input.debounce.500ms="performFilterSearch()"
                                               placeholder="YYYY, M/YY, D/M/YY"
                                               class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label for="search_pegawai_penyiasat_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai Penyiasat:</label>
                                        <input type="text" id="search_pegawai_penyiasat_project_show"
                                               x-model="searchPegawaiPenyiasat"
                                               @input.debounce.500ms="performFilterSearch()"
                                               placeholder="Nama Pegawai..."
                                               class="form-input rounded-md shadow-sm text-sm w-full mt-1 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label for="search_status_ks_project_show" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status KS:</label>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <select id="search_status_ks_project_show"
                                                    x-model="searchStatusKs"
                                                    @change="performFilterSearch()"
                                                    class="form-select rounded-md shadow-sm text-sm w-full dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200">
                                                <option value="">Semua Status</option>
                                                <option value="Siasatan Aktif">Siasatan Aktif</option>
                                                <option value="KUS/Fail">KUS/Fail</option>
                                                <option value="Rujuk TPR">Rujuk TPR</option>
                                                <option value="Rujuk PPN">Rujuk PPN</option>
                                                <option value="Rujuk KJSJ">Rujuk KJSJ</option>
                                                <option value="Rujuk KBSJD">Rujuk KBSJD</option>
                                                <option value="KUS/Sementara">KUS/Sementara</option>
                                                <option value="Jatuh Hukum">Jatuh Hukum</option>
                                            </select>
                                            <button @click="resetFiltersAndSearch()"
                                                    type="button" title="Set Semula Carian"
                                                    class="p-2 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 rounded-md">
                                                <i class="fas fa-undo-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
                                <div style="min-height: 100px;">
                                    <table class="divide-y divide-gray-200 dark:divide-gray-700" style="table-layout: fixed; min-width: 960px;">
                                        <thead @click.prevent="handleSortClick($event)" class="bg-gray-50 dark:bg-gray-700">
                                             <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 5%;">No.</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 20%;">
                                                    No. KS
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">
                                                    @sortablelink('tarikh_ks', 'Tarikh KS', ['project_id' => $project->id, 'page_name' => $pageNameForKSTable], ['class' => 'inline-flex items-center space-x-1'])
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">No. Repot</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">
                                                    Pegawai Penyiasat
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">
                                                    Status KS
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 10%;">
                                                    Status Kes
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 15%;">Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody x-html="tableHtml" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @include('projects._associated_kertas_siasatan_table_rows', [
                                                'kertasSiasatans' => $associatedKertasSiasatanPaginated,
                                                'project' => $project //project object to seperate from normal kertas siasatan table.
                                            ])
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="pagination-links-container mt-4" x-html="paginationHtml" @click.prevent="handlePaginationClick($event)">
                                @if($associatedKertasSiasatanPaginated)
                                    @php
                                        // Restore 'page_name' in the appends array for Laravel's pagination links.
                                        // This helps Kyslik generate correct sort links if it needs to.
                                        $appendsArray = collect(request()->query()) // This uses the request() helper function
                                            ->except(['page', $pageNameForKSTable]) 
                                            ->put('project_id', $project->id)      
                                            ->put('page_name', $pageNameForKSTable) 
                                            ->all();
                                    @endphp
                                    {{-- Pass the pageName to links() method for Laravel Paginator --}}
                                    {{ $associatedKertasSiasatanPaginated->setPageName($pageNameForKSTable)->appends($appendsArray)->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- End Kertas Siasatan Section --}}

                    @php
                        unset($allPapers['kertas_siasatan']);
                    @endphp
                    
                    @forelse ($allPapers as $type => $papers)
                        @if ($papers->isNotEmpty())
                            <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                                 <div class="flex justify-between items-center mb-2">
                                    <h5 class="font-medium capitalize text-md">
                                        {{ str_replace('_', ' ', Illuminate\Support\Str::title(Str::before($type, 'Papers'))) }} Papers
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $papers->count() }} {{ __('item(s)') }})</span>
                                    </h5>
                                </div>
                                <ul class="space-y-2">
                                    @foreach ($papers->take(5) as $paper)
                                        <li class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-md">
                                            <span class="text-sm">
                                                @php 
                                                    $paperModelName = Illuminate\Support\Str::studly(Illuminate\Support\Str::singular(Str::before($type, 'Papers')));
                                                    $displayText = $paperModelName . " ID: {$paper->id}"; 
                                                    $paperRoutePrefix = Illuminate\Support\Str::kebab($paperModelName);

                                                    if (isset($paper->no_ks)) $displayText = $paper->no_ks;
                                                    elseif (isset($paper->no_kst)) $displayText = $paper->no_kst;
                                                    elseif (isset($paper->no_lmm)) $displayText = $paper->no_lmm;
                                                    elseif (isset($paper->no_ks_oh)) $displayText = $paper->no_ks_oh;
                                                    elseif (isset($paper->name)) $displayText = $paper->name;

                                                    $viewRoute = Route::has($paperRoutePrefix . '.show') ? route($paperRoutePrefix . '.show', $paper->id) : null;
                                                    $editRoute = Route::has($paperRoutePrefix . '.edit') ? route($paperRoutePrefix . '.edit', $paper->id) : null;
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
                                                    <button type="submit" class="text-xs px-2 py-1 bg-orange-500 text-white rounded hover:bg-orange-600">{{ __('Remove') }}</button>
                                                </form>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                     @if($papers->count() > 5 && Route::has($indexRouteName))
                                        <li class="text-center pt-2">
                                            <a href="{{ route($indexRouteName, ['project_id_filter' => $project->id]) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                                {{ __('View all') }} {{ $papers->count() }} {{ str_replace('_', ' ', Illuminate\Support\Str::title(Str::before($type, 'Papers'))) }} Papers...
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    @empty
                         @if ($project->kertasSiasatan->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No associated papers found for this project yet.') }}</p>
                         @endif
                    @endforelse

                    @php
                        $otherPaperTypesForAdding = [
                            'JenayahPaper' => $unassignedJenayahPapers,
                            'NarkotikPaper' => $unassignedNarkotikPapers,
                            'TrafikSeksyenPaper' => $unassignedTrafikSeksyenPapers,
                            'TrafikRulePaper' => $unassignedTrafikRulePapers,
                            'KomersilPaper' => $unassignedKomersilPapers,
                            'LaporanMatiMengejutPaper' => $unassignedLaporanMatiMengejutPapers,
                            'OrangHilangPaper' => $unassignedOrangHilangPapers,
                        ];
                    @endphp

                    @foreach($otherPaperTypesForAdding as $modelName => $unassignedPapers)
                        @if($unassignedPapers && $unassignedPapers->isNotEmpty())
                            <div x-data="{
                                searchTerm: '',
                                selectedPaperId: null,
                                selectedPaperText: '',
                                isOpen: false,
                                originalOptions: {{ $unassignedPapers->map(function($paper) use ($modelName) {
                                    $displayIdentifier = $paper->no_ks ?? $paper->no_kst ?? $paper->no_lmm ?? $paper->no_ks_oh ?? $paper->name ?? "ID: {$paper->id}";
                                    return ['id' => $paper->id, 'text' => $displayIdentifier];
                                })->values()->toJson() }},
                                get filteredOptions() {
                                    const term = this.searchTerm ? this.searchTerm.trim().toLowerCase() : '';
                                    if (term === '') {
                                        return this.originalOptions;
                                    }
                                    return this.originalOptions.filter(option => 
                                        option.text.toLowerCase().includes(term)
                                    );
                                },
                                selectOption(option) {
                                    this.selectedPaperId = option.id;
                                    this.selectedPaperText = option.text;
                                    this.searchTerm = option.text;
                                    this.isOpen = false;
                                },
                                resetSearch() {
                                    if (this.selectedPaperId) {
                                        this.selectedPaperId = null;
                                    }
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

                                            <div x-show="isOpen"
                                                 class="absolute z-10 mt-1 w-full md:w-auto bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"
                                                 style="min-width: calc( (100% / 3 * 2) - 1rem );">
                                                <ul class="py-1">
                                                    <template x-if="filteredOptions.length === 0 && searchTerm.trim() !== ''">
                                                        <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No matching ') }}{{ Str::title(Str::snake($modelName, ' ')) }}{{ __(' found') }}</li>
                                                    </template>
                                                     <template x-if="originalOptions.length === 0 && searchTerm.trim() === ''">
                                                        <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No unassigned ') }}{{ Str::title(Str::snake($modelName, ' ')) }}{{ __(' found') }}</li>
                                                    </template>
                                                    <template x-for="option in filteredOptions" :key="option.id">
                                                        <li @click="selectOption(option)"
                                                            class="px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                                                            x-text="option.text">
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                            @if ($errors->has('paper_id') && old('paper_type') == $modelName)
                                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('paper_id') }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            <button type="submit" :disabled="!selectedPaperId"
                                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-50 transition ease-in-out duration-150">
                                                {{ __('Add ') }}{{ Str::title(Str::snake($modelName, ' ')) }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
function realtimeSearchForProjectKS(baseUrl, projectId, pageNameForTable) {
    return {
        searchTerm: '', 
        searchTarikhKs: '',
        searchPegawaiPenyiasat: '',
        searchStatusKs: '',
        currentSort: '',      // To store current sort column
        currentDirection: '', // To store current sort direction

        tableHtml: '', 
        paginationHtml: '',
        loading: false,
        loadingTimeout: null,
        
        projectId: projectId,
        pageNameQueryKey: pageNameForTable, // This is 'ks_project_page'
        baseUrl: baseUrl, // This is '{{ route('kertas_siasatan.index') }}'

        init() {
            const urlParams = new URLSearchParams(window.location.search);
            this.searchTerm = urlParams.get('search_no_ks') || '';
            this.searchTarikhKs = urlParams.get('search_tarikh_ks') || '';
            this.searchPegawaiPenyiasat = urlParams.get('search_pegawai_penyiasat') || '';
            this.searchStatusKs = urlParams.get('search_status_ks') || '';
            
            const pageParamExistsForThisTable = urlParams.has(this.pageNameQueryKey);
            const otherPageParamsExist = Array.from(urlParams.keys()).some(key => key.endsWith('_page') && key !== this.pageNameQueryKey);

            if (pageParamExistsForThisTable || !otherPageParamsExist) {
                this.currentSort = urlParams.get('sort') || ''; 
                this.currentDirection = urlParams.get('direction') || '';
            } else {
                this.currentSort = '';
                this.currentDirection = '';
            }

            const currentTableContainer = this.$el;
            const tbodyElement = currentTableContainer.querySelector('tbody');
            const paginationElement = currentTableContainer.querySelector('.pagination-links-container');

            if (tbodyElement) this.tableHtml = tbodyElement.innerHTML;
            if (paginationElement) this.paginationHtml = paginationElement.innerHTML;
            
            this.updateSortIcons(); // Call to set initial icons
        },
        
        updateSortIcons() {
            // Target the specific sortable link for 'tarikh_ks'
            const sortableLink = this.$el.querySelector('thead th a[href*="sort=tarikh_ks"]');
            if (!sortableLink) return;

            const icon = sortableLink.querySelector('i.fa'); // Assuming Font Awesome <i> tag
            if (!icon) return;

            // Reset attributes and icon class first
            sortableLink.removeAttribute('aria-sort');
            icon.className = 'fa fa-fw fa-sort'; // Default icon (e.g., a generic sort icon)

            // Apply active sort state
            if (this.currentSort === 'tarikh_ks') { // Check if 'tarikh_ks' is the active sort column
                if (this.currentDirection === 'asc') {
                    sortableLink.setAttribute('aria-sort', 'ascending');
                    icon.className = 'fa fa-fw fa-sort-up'; // Ascending icon
                } else if (this.currentDirection === 'desc') {
                    sortableLink.setAttribute('aria-sort', 'descending');
                    icon.className = 'fa fa-fw fa-sort-down'; // Descending icon
                }
                // If direction is empty (unsorted), it remains 'fa-sort' due to the reset above.
            }
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
            this.applyParamsToUrl(url, true, null, { sort: this.currentSort, direction: this.currentDirection });
            this.fetchResults(url, false);
        },
        
        applyParamsToUrl(urlInstance, resetPageToOne = false, pageNumber = null, sortInfo = null) {
            ['search_no_ks', 'search_tarikh_ks', 'search_pegawai_penyiasat', 'search_status_ks', 
             'sort', 'direction', this.pageNameQueryKey, 'project_id', 'page_name', 'page_name_param']
            .forEach(p => urlInstance.searchParams.delete(p));

            if (this.searchTerm) urlInstance.searchParams.set('search_no_ks', this.searchTerm);
            if (this.searchTarikhKs) urlInstance.searchParams.set('search_tarikh_ks', this.searchTarikhKs);
            if (this.searchPegawaiPenyiasat) urlInstance.searchParams.set('search_pegawai_penyiasat', this.searchPegawaiPenyiasat);
            if (this.searchStatusKs) urlInstance.searchParams.set('search_status_ks', this.searchStatusKs);
            
            urlInstance.searchParams.set('project_id', this.projectId);
            urlInstance.searchParams.set('page_name_param', this.pageNameQueryKey); 

            // Table Sorting
            if (sortInfo && sortInfo.sort !== undefined) {
                if (sortInfo.sort) {
                    urlInstance.searchParams.set('sort', sortInfo.sort);
                    if (sortInfo.direction) {
                        urlInstance.searchParams.set('direction', sortInfo.direction);
                    } else {
                        urlInstance.searchParams.delete('direction'); // Kyslik expects direction to be absent for unsorted
                    }
                } else { // sortInfo.sort is empty, meaning reset sort for this column
                    urlInstance.searchParams.delete('sort');
                    urlInstance.searchParams.delete('direction');
                }
                // Update Alpine's current sort state to reflect what's being requested
                this.currentSort = sortInfo.sort || '';
                this.currentDirection = sortInfo.direction || '';
            }
            // End of restored sortInfo handling

            let targetPage = '1';
            if (resetPageToOne) {
                targetPage = '1';
            } else if (pageNumber) {
                targetPage = pageNumber;
            } else {
                const currentBrowserUrlParams = new URLSearchParams(window.location.search);
                if(currentBrowserUrlParams.has(this.pageNameQueryKey) && !resetPageToOne){
                    targetPage = currentBrowserUrlParams.get(this.pageNameQueryKey);
                }
            }
            urlInstance.searchParams.set(this.pageNameQueryKey, targetPage);
        },

        fetchResults(url, maintainScroll = false) {
            clearTimeout(this.loadingTimeout);
            this.loading = true;
            const scrollY = maintainScroll ? window.scrollY : undefined;
            
            const tableBody = this.$el.querySelector('tbody'); 
            const paginationDiv = this.$el.querySelector('.pagination-links-container');

            this.loadingTimeout = setTimeout(() => {
                if (this.loading) {
                    if (tableBody) tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-gray-500 dark:text-gray-300"><svg class="animate-spin h-5 w-5 text-gray-500 dark:text-gray-300 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuatkan...</td></tr>`;
                    if (paginationDiv) paginationDiv.innerHTML = '';
                }
            }, 300);

            console.log("Fetching KS Table URL:", url.toString());

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', }
            })
            .then(response => {
                clearTimeout(this.loadingTimeout);
                if (!response.ok) { return response.text().then(text => { throw new Error(`Network error (${response.status}): ${text.substring(0,500)}`); }); }
                return response.json();
            })
            .then(data => {
                this.tableHtml = data.table_html; 
                this.paginationHtml = data.pagination_html; 
                
                this.updateSortIcons(); // Update icons after table content is updated

                if (window.history.pushState) {
                    const newUrlState = new URL(window.location.href);
                    const paramsFromFetch = new URLSearchParams(url.search);
                    
                    ['search_no_ks', 'search_tarikh_ks', 'search_pegawai_penyiasat', 'search_status_ks', 
                     'sort', 'direction', this.pageNameQueryKey, 'project_id', 'page_name_param'] // include page_name_param
                    .forEach(p => newUrlState.searchParams.delete(p));

                    paramsFromFetch.forEach((value, key) => {
                        if (key !== 'page_name_param' && key !== 'page_name') { 
                            newUrlState.searchParams.set(key, value);
                        } else if (key === this.pageNameQueryKey) { 
                             newUrlState.searchParams.set(key, value);
                        }
                    });

                    if (paramsFromFetch.has('project_id')) newUrlState.searchParams.set('project_id', paramsFromFetch.get('project_id'));
                    if (paramsFromFetch.has(this.pageNameQueryKey)) newUrlState.searchParams.set(this.pageNameQueryKey, paramsFromFetch.get(this.pageNameQueryKey));

                    if (newUrlState.toString() !== window.location.href) {
                        window.history.pushState({path: newUrlState.toString()}, '', newUrlState.toString());
                    }
                }
                if (maintainScroll && typeof scrollY !== 'undefined') {
                    this.$nextTick(() => { window.scrollTo(0, scrollY); });
                }
            })
            .catch(error => {
                clearTimeout(this.loadingTimeout);
                console.error('Error fetching search results for project KS table:', error);
                const tableBody = this.$el.querySelector('tbody'); 
                const paginationDiv = this.$el.querySelector('.pagination-links-container');
                if (tableBody) tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-red-500 py-4">Ralat memuatkan hasil carian.</td></tr>';
                if (paginationDiv) paginationDiv.innerHTML = '<p class="text-red-500 text-center">Ralat memuatkan paginasi.</p>';
            })
            .finally(() => { this.loading = false; });
        },

        handlePaginationClick(event) {
            const link = event.target.closest('a');
            if (link && link.href) {
                const clickedUrlParams = new URL(link.href).searchParams;
                const pageNumber = clickedUrlParams.get(this.pageNameQueryKey) || '1';

                const fetchUrl = new URL(this.baseUrl);
                this.applyParamsToUrl(fetchUrl, false, pageNumber, { sort: this.currentSort, direction: this.currentDirection }); 
                this.fetchResults(fetchUrl, true);
            }
        },

        handleSortClick(event) {
            const link = event.target.closest('th a'); 
            if (link && link.href) {
                const clickedUrlParams = new URL(link.href).searchParams;
                const sortColumnFromLink = clickedUrlParams.get('sort'); 
                
                let newSortColumn = sortColumnFromLink;
                let newDirection;

                if (this.currentSort === sortColumnFromLink) {
                    if (this.currentDirection === 'asc') {
                        newDirection = 'desc';
                    } else if (this.currentDirection === 'desc') {
                        newDirection = ''; 
                        newSortColumn = ''; 
                    } else {
                        newDirection = 'asc';
                    }
                } else {
                    newDirection = 'asc';
                }
                
                const fetchUrl = new URL(this.baseUrl);
                this.applyParamsToUrl(fetchUrl, true, null, { sort: newSortColumn, direction: newDirection }); 
                this.fetchResults(fetchUrl, true);
            }
        }
    }
}
    </script>
    @endpush
</x-app-layout>