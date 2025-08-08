<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">
                            @if($user->id === Auth::id())
                                Edit Profil : {{ $user->name }}
                            @else
                                Edit Pengguna: {{ $user->name }}
                            @endif
                        </h2>
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali
                        </a>
                    </div>

                    <div class="max-w-2xl">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Penuh')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Username -->
                            <div class="mt-4">
                                <x-input-label for="username" :value="__('ID Pengguna')" />
                                <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username)" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <!-- Email Address -->
                            <div class="mt-4">
                                <x-input-label for="email" :value="__('Emel')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="email" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Superadmin Status - Commented out temporarily --}}
                            {{-- <div class="mt-4">
                                <x-input-label for="superadmin" :value="__('Status Pengguna')" />
                                
                                @if($user->id === Auth::id())
                                    <!-- Current user editing their own account - disable role change -->
                                    <select id="superadmin" 
                                            name="superadmin" 
                                            class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed rounded-md shadow-sm" 
                                            disabled>
                                        <option value="yes" {{ $user->superadmin === 'yes' ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                        <option value="no" {{ $user->superadmin === 'no' ? 'selected' : '' }}>
                                            Pengguna Biasa
                                        </option>
                                    </select>
                                    <!-- Hidden field to maintain the current value -->
                                    <input type="hidden" name="superadmin" value="{{ $user->superadmin }}">
                                    <p class="mt-2 text-sm text-amber-600">
                                        <strong>Nota:</strong> Anda tidak boleh mengubah status peranan akaun sendiri untuk keselamatan.
                                    </p>
                                @else
                                    <!-- Editing other users - allow role change -->
                                    <select id="superadmin" 
                                            name="superadmin" 
                                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                            required>
                                        <option value="no" {{ old('superadmin', $user->superadmin) === 'no' ? 'selected' : '' }}>
                                            Pengguna Biasa
                                        </option>
                                        <option value="yes" {{ old('superadmin', $user->superadmin) === 'yes' ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                    </select>
                                @endif
                                
                                <x-input-error :messages="$errors->get('superadmin')" class="mt-2" />
                            </div> --}}

                            <!-- Password (Optional for update) -->
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Kata Laluan Baharu (Kosongkan jika tidak mahu tukar)')" />
                                <x-text-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Sahkan Kata Laluan Baharu')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                type="password"
                                                name="password_confirmation" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <!-- Project Visibility -->
                            @if($user->id !== Auth::id())
                                @php
                                    // Determine the original state from database
                                    $originalUser = \App\Models\User::find($user->id);
                                    $isAllProjects = is_null($originalUser->visible_projects);
                                    
                                    // Get owned project IDs
                                    $ownedProjectIds = $user->projects->pluck('id')->toArray();
                                    
                                    // If user can see all projects (visible_projects is null)
                                    if ($isAllProjects) {
                                        // In "all projects" mode, still show owned projects as checked for display
                                        $currentVisibleProjects = $ownedProjectIds;
                                        $showAsSelected = false; // Show "all projects" radio as selected
                                    } else {
                                        // User has specific visible projects
                                        $currentVisibleProjects = $originalUser->visible_projects ?? [];
                                        $showAsSelected = true; // Show "selected projects" radio as selected
                                        
                                        // Always include owned projects if in selected mode
                                        $currentVisibleProjects = array_unique(array_merge($currentVisibleProjects, $ownedProjectIds));
                                    }
                                @endphp
                                <div class="mt-4">
                                    <x-input-label for="visible_projects" :value="__('Projek Yang Boleh Dilihat')" />
                                    <div class="mt-2 space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   name="project_visibility" 
                                                   value="all" 
                                                   class="mr-2" 
                                                   {{ !$showAsSelected ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Semua projek (tidak terhad)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   name="project_visibility" 
                                                   value="selected" 
                                                   class="mr-2" 
                                                   {{ $showAsSelected ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Projek terpilih sahaja</span>
                                        </label>
                                    </div>
                                    
                                    <div id="project-selection" class="mt-4 space-y-2 {{ !$showAsSelected ? 'hidden' : '' }}">
                                        <label class="text-sm font-medium text-gray-700">Pilih Projek:</label>
                                        
                                        <!-- Search input -->
                                        <div class="mb-3">
                                            <input type="text" 
                                                   id="project-search" 
                                                   placeholder="Cari projek..." 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        
                                        <div id="project-list" class="max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3 bg-gray-50">
                                            @foreach($projects as $project)
                                                @php
                                                    $isOwned = $project->user_id == $user->id;
                                                @endphp
                                                <label class="flex items-center py-1 {{ $isOwned ? 'bg-gray-100 rounded px-2' : '' }}">
                                                    <input type="checkbox" 
                                                           name="visible_projects[]" 
                                                           value="{{ $project->id }}" 
                                                           class="mr-2 {{ $isOwned ? 'text-gray-400' : '' }}"
                                                           {{ in_array($project->id, $currentVisibleProjects) ? 'checked' : '' }}
                                                           {{ $isOwned ? 'disabled' : '' }}>
                                                    
                                                    @if($isOwned)
                                                        {{-- Hidden input to ensure owned projects are always included --}}
                                                        <input type="hidden" name="visible_projects[]" value="{{ $project->id }}">
                                                    @endif
                                                    
                                                    <span class="text-sm {{ $isOwned ? 'text-gray-500' : 'text-gray-700' }}">{{ $project->name }}</span>
                                                    <span class="text-xs text-gray-500 ml-2">({{ $project->project_date->format('d/m/Y') }})
                                                        @if($isOwned)
                                                            <span class="text-gray-500">- Pemilik (Tidak Boleh Diubah)</span>
                                                        @else
                                                            - Pemilik: {{ $project->user->name }}
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        
                                        <!-- No results message -->
                                        <div id="no-results" class="hidden text-sm text-gray-500 italic p-3 text-center">
                                            Tiada projek dijumpai
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('visible_projects')" class="mt-2" />
                                </div>
                            @endif

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    @if($user->id === Auth::id())
                                        {{ __('Kemaskini Profil Sendiri') }}
                                    @else
                                        {{ __('Kemaskini Pengguna') }}
                                    @endif
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle project visibility radio buttons and search -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="project_visibility"]');
            const projectSelection = document.getElementById('project-selection');
            const checkboxes = document.querySelectorAll('input[name="visible_projects[]"]');
            const searchInput = document.getElementById('project-search');
            const projectList = document.getElementById('project-list');
            const noResults = document.getElementById('no-results');

            // Handle radio button changes
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'all') {
                        projectSelection.classList.add('hidden');
                        // Uncheck all project checkboxes when "all" is selected
                        checkboxes.forEach(checkbox => checkbox.checked = false);
                    } else if (this.value === 'selected') {
                        projectSelection.classList.remove('hidden');
                    }
                });
            });

            // Handle project search
            if (searchInput && projectList) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    const projectLabels = projectList.querySelectorAll('label');
                    let visibleCount = 0;

                    projectLabels.forEach(label => {
                        const projectText = label.textContent.toLowerCase();
                        const shouldShow = searchTerm === '' || projectText.includes(searchTerm);
                        
                        if (shouldShow) {
                            label.style.display = 'flex';
                            visibleCount++;
                        } else {
                            label.style.display = 'none';
                        }
                    });

                    // Show/hide "no results" message
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResults.classList.remove('hidden');
                        projectList.style.display = 'none';
                    } else {
                        noResults.classList.add('hidden');
                        projectList.style.display = 'block';
                    }
                });
            }
        });
    </script>
</x-app-layout>
