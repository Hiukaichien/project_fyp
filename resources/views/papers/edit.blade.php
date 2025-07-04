<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Audit / Kemaskini: {{ $paperType }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('kertas_siasatan.update', ['paperType' => class_basename($paper), 'id' => $paper->id]) }}" class="space-y-6 bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($paper->getAttributes() as $key => $value)
                        @if(!in_array($key, ['id', 'project_id', 'created_at', 'updated_at']))
                            <div>
                                <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">{{ Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</label>
                                <input type="text" name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $value) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="flex justify-end pt-4 mt-6 border-t">
                    <a href="{{ route('projects.show', $paper->project_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3 shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                        Kemaskini
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>