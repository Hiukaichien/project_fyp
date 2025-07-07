<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Terima kasih kerana mendaftar! Sebelum bermula, sila sahkan alamat emel anda dengan mengklik pautan yang kami hantar. Jika anda tidak menerima emel tersebut, kami dengan senang hati akan menghantar yang baru.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Pautan pengesahan baru telah dihantar ke alamat emel yang anda berikan semasa pendaftaran.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Hantar Semula Emel Pengesahan') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>