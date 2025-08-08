<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Cipta Pengguna Baharu</h2>
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali
                        </a>
                    </div>

                    <div class="max-w-2xl">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf
                            
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Penuh')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Username -->
                            <div class="mt-4">
                                <x-input-label for="username" :value="__('ID Pengguna')" />
                                <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <!-- Email Address -->
                            <div class="mt-4">
                                <x-input-label for="email" :value="__('Emel')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Superadmin Status - Commented out for now --}}
                            {{-- <div class="mt-4">
                                <x-input-label for="superadmin" :value="__('Status Pengguna')" />
                                <select id="superadmin" 
                                        name="superadmin" 
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                        required>
                                    <option value="no" {{ old('superadmin') === 'no' ? 'selected' : '' }}>
                                        Pengguna Biasa
                                    </option>
                                    <option value="yes" {{ old('superadmin') === 'yes' ? 'selected' : '' }}>
                                        Admin
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('superadmin')" class="mt-2" />
                            </div> --}}

                            <!-- Password -->
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Kata Laluan')" />
                                <x-text-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Sahkan Kata Laluan')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                type="password"
                                                name="password_confirmation" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

            <!-- Project Visibility -->
            <div class="mt-4">
                <x-input-label for="visible_projects" :value="__('Projek Yang Boleh Dilihat')" />
                <div class="mt-2 space-y-2">
                    <label class="flex items-center">
                        <input type="radio" 
                               name="project_visibility" 
                               value="all" 
                               class="mr-2" 
                               {{ old('project_visibility') === 'all' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Semua projek (tidak terhad)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" 
                               name="project_visibility" 
                               value="selected" 
                               class="mr-2" 
                               {{ old('project_visibility', 'selected') === 'selected' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Projek terpilih sahaja</span>
                    </label>
                </div>
                
                <div id="project-selection" class="mt-4 space-y-2 {{ old('project_visibility', 'selected') === 'all' ? 'hidden' : '' }}">
                    <label class="text-sm font-medium text-gray-700">Pilih Projek:</label>
                    
                    <!-- Search input -->
                    <div class="mb-3">
                        <input type="text" 
                               id="project-search" 
                               placeholder="Cari projek..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div id="project-list" class="max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3 bg-gray-50">
                        @forelse($projects as $project)
                            <label class="flex items-center py-1">
                                <input type="checkbox" 
                                       name="visible_projects[]" 
                                       value="{{ $project->id }}" 
                                       class="mr-2"
                                       {{ is_array(old('visible_projects')) && in_array($project->id, old('visible_projects')) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $project->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $project->project_date->format('d/m/Y') }}) - Pemilik: {{ $project->user->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 italic">Tiada projek tersedia</p>
                        @endforelse
                    </div>
                    
                    <!-- No results message -->
                    <div id="no-results" class="hidden text-sm text-gray-500 italic p-3 text-center">
                        Tiada projek dijumpai
                    </div>
                </div>
                <x-input-error :messages="$errors->get('visible_projects')" class="mt-2" />
            </div>                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    {{ __('Cipta Pengguna') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle project visibility and search functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="project_visibility"]');
            const projectSelection = document.getElementById('project-selection');
            const searchInput = document.getElementById('project-search');
            const projectList = document.getElementById('project-list');
            const noResults = document.getElementById('no-results');
            
            // Handle radio button change
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'all') {
                        if (projectSelection) {
                            projectSelection.classList.add('hidden');
                        }
                        // Uncheck all project checkboxes when switching to "all"
                        const checkboxes = document.querySelectorAll('input[name="visible_projects[]"]');
                        checkboxes.forEach(cb => cb.checked = false);
                    } else {
                        if (projectSelection) {
                            projectSelection.classList.remove('hidden');
                        }
                    }
                });
            });

            // Handle search functionality
            if (searchInput && projectList) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const labels = projectList.querySelectorAll('label');
                    let hasVisibleItems = false;
                    
                    labels.forEach(label => {
                        const text = label.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            label.style.display = 'flex';
                            hasVisibleItems = true;
                        } else {
                            label.style.display = 'none';
                        }
                    });
                    
                    // Show/hide no results message
                    if (hasVisibleItems || searchTerm === '') {
                        if (projectList) projectList.style.display = 'block';
                        if (noResults) noResults.classList.add('hidden');
                    } else {
                        if (projectList) projectList.style.display = 'none';
                        if (noResults) noResults.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
