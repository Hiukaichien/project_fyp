{{-- resources/views/kertas_siasatan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Senarai Kertas Siasatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alpine.js component scope for real-time search --}}
            <div x-data="realtimeSearch('{{ route('kertas_siasatan.index') }}')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">

                {{-- Upload Button --}}
                <div class="flex justify-end items-center">
                    <a href="{{ route('kertas_siasatan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Muat Naik Excel
                    </a>
                </div>

                {{-- Search Input Area --}}
                <div class="bg-gray-50 p-4 rounded shadow-sm">
                    <div class="flex items-center space-x-3">
                        <label for="search_no_ks" class="text-sm font-medium text-gray-700">Cari No. KS:</label>
                        <input type="text" name="search_no_ks" id="search_no_ks"
                               x-model="searchTerm"
                               @input.debounce.500ms="search" {{-- Trigger search 500ms after user stops typing --}}
                               placeholder="Taip No. KS untuk mencari..."
                               class="form-input rounded-md shadow-sm text-sm flex-grow">
                        <button @click="searchTerm = ''; search()" type="button" class="text-sm text-blue-600 hover:underline ml-2">Set Semula</button>
                    </div>
                </div>

                {{-- Main Table --}}
                <h3 class="text-lg font-semibold text-gray-700">Semua Kertas Siasatan</h3>
                <div class="overflow-x-auto bg-white rounded shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bil</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. KS</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh KS</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Repot</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai Penyiasat</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status KS</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kes</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        {{-- Table body updated dynamically by Alpine.js --}}
                        <tbody id="kertas-siasatan-tbody" x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                            {{-- Initial content rendered on page load --}}
                            @include('kertas_siasatan._table_rows', ['kertasSiasatans' => $kertasSiasatans])
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links (Not updated dynamically by this simple search) --}}
                <div id="pagination-links" class="mt-4">
                    {{ $kertasSiasatans->appends(request()->query())->links() }}
                </div>

                {{-- Collapsible Tables for Specific Statuses --}}
                {{-- Ensure resources/views/components/collapsible-table.blade.php exists --}}
                <x-collapsible-table title="KS Lewat Edar (> 24 Jam)" :collection="$ksLewat24Jam" bgColor="bg-red-50" />
                <x-collapsible-table title="KS Terbengkalai (> 3 Bulan)" :collection="$ksTerbengkalai" bgColor="bg-yellow-50" />
                <x-collapsible-table title="KS Baru Dikemaskini" :collection="$ksBaruKemaskini" bgColor="bg-green-50" />

            </div> {{-- End Alpine component scope --}}
        </div> {{-- End container --}}
    </div> {{-- End padding --}}

    {{-- Alpine.js function for real-time search --}}
    @push('scripts')
    <script>
        function realtimeSearch(searchUrl) {
            return {
                searchTerm: '{{ request('search_no_ks', '') }}', // Initialize with current search term from request
                // Initialize tableHtml with the server-rendered content via Blade include, properly escaped
                tableHtml: `{!! addslashes(view('kertas_siasatan._table_rows', ['kertasSiasatans' => $kertasSiasatans])->render()) !!}`,
                loading: false, // Flag for loading state (optional)

                search() {
                    this.loading = true;
                    const url = new URL(searchUrl);
                    url.searchParams.set('search_no_ks', this.searchTerm); // Add search term to URL
                    url.searchParams.set('page', '1'); // Reset to page 1 when searching

                    // Display loading state in the table body
                    this.tableHtml = '<tr><td colspan="8" class="text-center py-4 text-gray-500">Mencari...</td></tr>';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Identify as AJAX request
                            'Accept': 'text/html', // We expect HTML partial back
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            // If response is not OK, try to get error text
                            return response.text().then(text => {
                                throw new Error(`Network response was not ok (${response.status}): ${text}`);
                            });
                        }
                        return response.text(); // Get response as HTML string
                    })
                    .then(html => {
                        this.tableHtml = html; // Update table body content
                        // Note: Pagination links are not updated by this fetch.
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                        // Display error message in the table body
                        this.tableHtml = '<tr><td colspan="8" class="text-center text-red-500 py-4">Ralat memuatkan hasil carian. Semak konsol untuk butiran.</td></tr>';
                    })
                    .finally(() => {
                        this.loading = false; // Reset loading state
                    });
                }
            }
        }
    </script>
    @endpush

</x-app-layout>