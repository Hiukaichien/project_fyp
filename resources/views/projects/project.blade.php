@php use Illuminate\Support\Str; @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Projects') }}
            </h2>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Create New Project') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                            {{ session('info') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold mb-4">{{ __('Existing Projects') }}</h3>
                    @if($projects->isEmpty())
                        <p>{{ __('No projects found.') }} <a href="{{ route('projects.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Create one now!') }}</a></p>
                    @else
                        <div class="space-y-4">
                            @foreach ($projects as $project)
                                {{-- Main container is now a non-clickable DIV --}}
                                <div class="flex flex-col border border-gray-200 dark:border-gray-700 rounded-md shadow-sm">
                                    
                                    {{-- The top segment is now a clickable link to the project dashboard --}}
                                    <a href="{{ route('projects.show', $project) }}" class="flex-grow p-4 group rounded-t-md transition-colors duration-300 ease-in-out hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <div class="flex justify-between items-start">
                                            {{-- Project Name --}}
                                            <span class="text-xl font-semibold text-blue-600 dark:text-blue-400 group-hover:underline">
                                                {{ $project->name }}
                                            </span>
                                            {{-- Project Dates --}}
                                            <div class="text-right flex-shrink-0 ml-4">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($project->project_date)->format('d M Y') }}
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap" title="Last Updated">
                                                    {{ __('Updated:') }} {{ $project->updated_at->format('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($project->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-300 mt-2">
                                                {{ Str::limit($project->description, 150) }}
                                            </p>
                                        @endif
                                    </a>

                                    {{-- The icon bar is now a separate DIV outside of the <a> tag --}}
                                    <div class="p-4 pt-3 border-t border-gray-200 dark:border-gray-600 flex justify-end items-center space-x-4 bg-gray-50 dark:bg-gray-800 rounded-b-md">
                                        
                                        {{-- Edit Project Icon (independent link) --}}
                                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Project') }}">
                                            <i class="fas fa-edit fa-lg"></i>
                                        </a>

                                        {{-- Delete Project Icon (independent form) --}}
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this project? This action cannot be undone.') }}');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500" title="{{ __('Delete Project') }}">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>