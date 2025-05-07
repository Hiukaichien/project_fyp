@forelse ($kertasSiasatans as $index => $ks)
    <tr>
      
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $kertasSiasatans->firstItem() + $index }}</td>
 
        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 0;" title="{{ $ks->no_ks }}">
            {{ $ks->no_ks }}
        </td>
      
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ optional($ks->tarikh_ks)->format('d/m/Y') ?? '-' }}</td>
    
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 0;" title="{{ $ks->no_report ?? '-' }}">
            {{ $ks->no_report ?? '-' }}
        </td>
       
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 0;" title="{{ $ks->pegawai_penyiasat ?? '-' }}">
            {{ $ks->pegawai_penyiasat ?? '-' }}
        </td>
    
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ks->status_ks == 'Fail' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                {{ $ks->status_ks ?? '-' }}
            </span>
        </td>
   
        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ks->status_kes == 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $ks->status_kes ?? '-' }}
            </span>
        </td>
  
        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium space-x-2">
             <a href="{{ route('kertas_siasatan.show', $ks->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Lihat">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </a>
            <a href="{{ route('kertas_siasatan.edit', $ks->id) }}" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </a>
            <form action="{{ route('kertas_siasatan.destroy', $ks->id) }}" method="POST" class="inline" onsubmit="return confirm('Anda pasti ingin memadam Kertas Siasatan {{ $ks->no_ks }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900" title="Padam">
                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tiada rekod kertas siasatan ditemui.</td>
    </tr>
@endforelse