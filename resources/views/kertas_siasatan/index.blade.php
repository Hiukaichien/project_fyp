<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="realtimeSearch('{{ route('kertas_siasatan.index') }}')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">

                <x-collapsible-table title="KS Lewat Edar (> 24 Jam)" :collection="$ksLewat24Jam" bgColor="bg-red-50" />
                <x-collapsible-table title="KS Terbengkalai (> 3 Bulan)" :collection="$ksTerbengkalai" bgColor="bg-yellow-50" />
                <x-collapsible-table title="KS Baru Dikemaskini" :collection="$ksBaruKemaskini" bgColor="bg-green-50" />
                <hr class="my-6 border-gray-300 dark:border-gray-700">
                
                {{-- Search Input Area --}}
                <div class="bg-gray-50 p-4 rounded shadow-sm space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                        <div class="lg:col-span-1">
                            <label for="search_no_ks" class="text-sm font-medium text-gray-700">Cari No. KS:</label>
                            <input type="text" name="search_no_ks" id="search_no_ks"
                                   x-model="searchTerm"
                                   @input.debounce.500ms="performFilterSearch()"
                                   placeholder="Taip No. KS..."
                                   class="form-input rounded-md shadow-sm text-sm w-full mt-1">
                        </div>

                        <div class="lg:col-span-1">
                            <label for="search_tarikh_ks" class="text-sm font-medium text-gray-700">Tarikh KS:</label>
                            <input type="text" name="search_tarikh_ks" id="search_tarikh_ks"
                                   x-model="searchTarikhKs"
                                   @input.debounce.500ms="performFilterSearch()"
                                   placeholder="YYYY, M/YY, D/M/YY"
                                   class="form-input rounded-md shadow-sm text-sm w-full mt-1">
                        </div>

                        <div class="lg:col-span-1">
                            <label for="search_pegawai_penyiasat" class="text-sm font-medium text-gray-700">Pegawai Penyiasat:</label>
                            <input type="text" name="search_pegawai_penyiasat" id="search_pegawai_penyiasat"
                                   x-model="searchPegawaiPenyiasat"
                                   @input.debounce.500ms="performFilterSearch()"
                                   placeholder="Nama Pegawai..."
                                   class="form-input rounded-md shadow-sm text-sm w-full mt-1">
                        </div>

                        {{-- Status KS and Reset Icon Button --}}
                        <div class="lg:col-span-1">
                            <label for="search_status_ks" class="text-sm font-medium text-gray-700">Status KS:</label>
                            <div class="flex items-center space-x-2 mt-1">
                                <select name="search_status_ks" id="search_status_ks"
                                        x-model="searchStatusKs"
                                        @change="performFilterSearch()"
                                        class="form-select rounded-md shadow-sm text-sm w-full"> {{-- Removed mt-1 as parent has it --}}
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
                                <button @click="searchTerm = ''; searchTarikhKs = ''; searchPegawaiPenyiasat = ''; searchStatusKs = ''; performFilterSearch()"
                                        type="button"
                                        title="Set Semula Carian"
                                        class="p-2 text-gray-500 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 rounded-md">
                                    <i class="fas fa-undo-alt"></i> {{-- Or fa-times for a cross --}}
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- Removed the separate div for reset button --}}
                    {{-- <div class="flex justify-end pt-2">
                        <button @click="searchTerm = ''; searchTarikhKs = ''; searchPegawaiPenyiasat = ''; searchStatusKs = ''; performFilterSearch()" type="button" class="text-sm text-blue-600 hover:underline px-4 py-2">Set Semula</button>
                    </div> --}}
                </div>

                <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Semua Kertas Siasatan</h3>
                {{-- Upload Button --}}
                    <a href="{{ route('kertas_siasatan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Import/Export
                    </a>
                </div>

                 {{-- Main Table --}}
                <div class="overflow-x-auto bg-white rounded shadow">
                    <div style="min-height: 200px;">
                        <table class="divide-y divide-gray-200" style="table-layout: fixed; min-width: 960px;">
                            <thead class="bg-gray-50">
                                 <tr>
                                     <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">No.</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 20%;">No. KS</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">@sortablelink('tarikh_ks', 'Tarikh KS')</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">No. Repot</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Pegawai Penyiasat</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Status KS</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Status Kes</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody id="kertas-siasatan-tbody" x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Links --}}
                <div id="pagination-links" class="mt-4" x-html="paginationHtml" @click.prevent="handlePaginationClick($event)">
                    @if(isset($kertasSiasatans))
                        {{ $kertasSiasatans->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

 @push('scripts')
    <script>
        function realtimeSearch(baseUrl) {
            return {
                searchTerm: '{{ request('search_no_ks', '') }}',
                searchTarikhKs: '{{ request('search_tarikh_ks', '') }}',
                searchPegawaiPenyiasat: '{{ request('search_pegawai_penyiasat', '') }}',
                searchStatusKs: '{{ request('search_status_ks', '') }}',
                tableHtml: `{!! addslashes(view('kertas_siasatan._table_rows', ['kertasSiasatans' => $kertasSiasatans])->render()) !!}`,
                paginationHtml: `{!! addslashes($kertasSiasatans->appends(request()->query())->links()->toHtml()) !!}`,
                loading: false,
                loadingTimeout: null,

                performFilterSearch() { /* ... (same as before) ... */
                    const url = new URL(baseUrl);
                    this.applyFiltersAndSortToUrl(url);
                    url.searchParams.set('page', '1');
                    this.fetchResults(url, false);
                },
                
                applyFiltersAndSortToUrl(urlInstance) { /* ... (same as before) ... */
                    const updateSearchParam = (key, value) => {
                        if (value) urlInstance.searchParams.set(key, value);
                        else urlInstance.searchParams.delete(key);
                    };
                    updateSearchParam('search_no_ks', this.searchTerm);
                    updateSearchParam('search_tarikh_ks', this.searchTarikhKs);
                    updateSearchParam('search_pegawai_penyiasat', this.searchPegawaiPenyiasat);
                    updateSearchParam('search_status_ks', this.searchStatusKs);

                    const currentWindowUrlParams = new URLSearchParams(window.location.search);
                    if (currentWindowUrlParams.has('sort') && !urlInstance.searchParams.has('sort')) {
                        urlInstance.searchParams.set('sort', currentWindowUrlParams.get('sort'));
                    }
                    if (currentWindowUrlParams.has('direction') && !urlInstance.searchParams.has('direction')) {
                        urlInstance.searchParams.set('direction', currentWindowUrlParams.get('direction'));
                    }
                },

                fetchResults(url, maintainScroll = false) { /* ... (same as before, ensure tbody is updated directly) ... */
                    clearTimeout(this.loadingTimeout);
                    this.loading = true;
                    const scrollY = maintainScroll ? window.scrollY : undefined;
                    this.loadingTimeout = setTimeout(() => {
                        if (this.loading) {
                            document.getElementById('kertas-siasatan-tbody').innerHTML = '<tr><td colspan="8" class="text-center py-10 text-gray-500"><svg class="animate-spin h-5 w-5 text-gray-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuatkan...</td></tr>';
                            this.paginationHtml = '';
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
                        document.getElementById('kertas-siasatan-tbody').innerHTML = data.table_html;
                        this.paginationHtml = data.pagination_html;
                        if (window.history.pushState) {
                            const newUrl = url.toString();
                            if (newUrl !== window.location.href) { window.history.pushState({path: newUrl}, '', newUrl); }
                        }
                        if (maintainScroll && typeof scrollY !== 'undefined') {
                            this.$nextTick(() => { window.scrollTo(0, scrollY); });
                        }
                    })
                    .catch(error => {
                        clearTimeout(this.loadingTimeout);
                        console.error('Error fetching search results:', error);
                        document.getElementById('kertas-siasatan-tbody').innerHTML = '<tr><td colspan="8" class="text-center text-red-500 py-4">Ralat memuatkan hasil carian.</td></tr>';
                        this.paginationHtml = '<p class="text-red-500 text-center">Ralat memuatkan paginasi.</p>';
                    })
                    .finally(() => { this.loading = false; });
                },

                handlePaginationClick(event) { /* ... (same as before) ... */
                    const link = event.target.closest('a');
                    if (link && link.href) {
                        const fetchUrl = new URL(link.href);
                        this.applyFiltersAndSortToUrl(fetchUrl);
                        this.fetchResults(fetchUrl, true);
                    }
                }
                // No handleTableClick for sortable links
            }
        }
    </script>
    @endpush
</x-app-layout>