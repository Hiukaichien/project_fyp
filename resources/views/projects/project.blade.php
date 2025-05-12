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
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow mr-4">
                                            <div class="flex justify-between items-center mb-1">
                                                <a href="{{ route('projects.show', $project) }}" class="text-xl font-semibold text-blue-600 dark:text-blue-400 hover:underline">{{ $project->name }}</a>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 ml-2 whitespace-nowrap">
                                                    {{ __('') }} {{ \Carbon\Carbon::parse($project->project_date)->format('d M Y') }}
                                                </p>
                                            </div>
                                            @if($project->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ Str::limit($project->description, 150) }}</p>
                                            @endif
                                        </div>
                                        {{-- Optional: Add links for edit/delete here or on the show page --}}
                                        {{-- <div class="flex space-x-2 flex-shrink-0">
                                            <a href="{{ route('projects.edit', $project) }}" class="text-sm text-yellow-600 hover:underline">Edit</a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </div> --}}
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