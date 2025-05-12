<?php

namespace App\Http\Controllers;

use App\Models\KertasSiasatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KertasSiasatanImport;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Log;

class KertasSiasatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KertasSiasatan::query();
        $query->select('kertas_siasatans.*');

        if ($request->filled('project_id')) {
            $query->where('kertas_siasatans.project_id', $request->input('project_id'));
        }

        if ($request->filled('search_no_ks')) {
            $query->where('no_ks', 'like', '%' . $request->search_no_ks . '%');
        }
        
        if ($request->filled('search_tarikh_ks')) {
            $dateInput = trim($request->search_tarikh_ks);
            $year = null; $month = null; $day = null;
            $dateInput = str_replace(['.', '-'], '/', $dateInput);
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2}|\d{4})$/', $dateInput, $matches)) {
                $d_match = (int)$matches[1]; $m_match = (int)$matches[2]; $y_match = (int)$matches[3];
                if (strlen($matches[3]) == 2) { $y_match += ($y_match < 70 ? 2000 : 1900); }
                if (checkdate($m_match, $d_match, $y_match)) { $day = $d_match; $month = $m_match; $year = $y_match; }
            } elseif (preg_match('/^(\d{1,2})\/(\d{2}|\d{4})$/', $dateInput, $matches)) {
                $m_match = (int)$matches[1]; $y_match = (int)$matches[2];
                if (strlen($matches[2]) == 2) { $y_match += ($y_match < 70 ? 2000 : 1900); }
                if ($m_match >= 1 && $m_match <= 12) { $month = $m_match; $year = $y_match; }
            } elseif (preg_match('/^(\d{4})$/', $dateInput, $matches)) {
                $year = (int)$matches[1];
            } elseif (preg_match('/^(\d{2})$/', $dateInput, $matches)) {
                $y_match = (int)$matches[1]; $year = $y_match + ($y_match < 70 ? 2000 : 1900);
            }
            if ($year && $month && $day) { $query->whereDate('tarikh_ks', Carbon::create($year, $month, $day)->toDateString()); }
            elseif ($year && $month) { $query->whereYear('tarikh_ks', $year)->whereMonth('tarikh_ks', $month); }
            elseif ($year) { $query->whereYear('tarikh_ks', $year); }
            else { Log::info("Unrecognized date format for search_tarikh_ks: " . $request->search_tarikh_ks); }
        }
        if ($request->filled('search_pegawai_penyiasat')) {
            $query->where('pegawai_penyiasat', 'like', '%' . $request->search_pegawai_penyiasat . '%');
        }
        if ($request->filled('search_status_ks')) {
            $query->where('status_ks', $request->search_status_ks);
        }

        $sortParamName = config('columnsortable.sort_parameter_name', 'sort');
        if (empty($sortParamName)) $sortParamName = 'sort';
        $directionParamName = config('columnsortable.direction_parameter_name', 'direction');
        if (empty($directionParamName)) $directionParamName = 'direction';

        if ($request->query->has($sortParamName) && $request->query->get($sortParamName) === '') {
            $currentQueryAsArray = $request->query->all();
            unset($currentQueryAsArray[$sortParamName]);
            if (isset($currentQueryAsArray[$directionParamName])) {
                unset($currentQueryAsArray[$directionParamName]);
            }
            $request->query->replace($currentQueryAsArray);
        }
        
        $pageName = $request->input('page_name_param', 'page'); // Default to 'page' if not provided

        $kertasSiasatans = $query->sortable()
                         ->paginate(10, ['*'], $pageName) 
                         ->appends($request->except('page_name_param')); 

        if ($request->ajax()) {
            $tableHtml = view('kertas_siasatan._table_rows', compact('kertasSiasatans'))->render();
            $paginationHtml = $kertasSiasatans->links()->toHtml();

            return response()->json([
                'table_html' => $tableHtml,
                'pagination_html' => $paginationHtml,
            ]);
        }

        // For full page load (main KS index)
        $ksLewat24Jam = KertasSiasatan::where('edar_lebih_24_jam_status', 'YA, EDARAN LEWAT 24 JAM')->orderBy('tarikh_ks', 'desc')->get();
        $ksTerbengkalai = KertasSiasatan::where('terbengkalai_3_bulan_status', 'YA, TERBENGKALAI LEBIH 3 BULAN')->orderBy('tarikh_ks', 'desc')->get();
        $ksBaruKemaskini = KertasSiasatan::where('baru_kemaskini_status', 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI')->orderBy('updated_at', 'desc')->get();

        return view('kertas_siasatan.index', compact(
            'kertasSiasatans',
            'ksLewat24Jam',
            'ksTerbengkalai',
            'ksBaruKemaskini'
        ));
    }

    public function create()
    {
        return view('kertas_siasatan.upload'); // View for upload form
    }

        //handle excel upload
    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240', // Validate file
        ]);

        try {
            // Use Laravel Excel to import data
            Excel::import(new KertasSiasatanImport, $request->file('excel_file'));

            return redirect()->route('kertas_siasatan.index')
                             ->with('success', 'Fail Excel berjaya dimuatnaik dan diproses.');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             return redirect()->back()->withErrors(['excel_errors' => $failures])->withInput();
        } catch (\Exception $e) {
             return redirect()->back()->with('error', 'Ralat semasa memproses fail: ' . $e->getMessage())->withInput();
        }
    }

    public function show(KertasSiasatan $kertasSiasatan) // Route Model Binding
    {
         return view('kertas_siasatan.show', compact('kertasSiasatan'));
    }

    /**
     * Show the form for editing the specified resource.
     * This is the main Audit/Update form.
     */
    public function edit(KertasSiasatan $kertasSiasatan) // Route Model Binding
    {
        // Define options for dropdowns/radios here or pass them from a service/config
        $statusKsOptions = ['Siasatan Aktif', 'Rujuk TPR', 'Rujuk PPN', 'Rujuk KJSJ', 'Rujuk KBSJD', 'KUS/Sementara', 'Jatuh Hukum', 'KUS/Fail'];
        $kedudukanBrgKesOptions = ['Dalam Peti Besi KPD', 'Simpanan Stor Barang Kes', /* ... other options ... */];
        // ... add other option arrays as needed

        return view('kertas_siasatan.edit', compact('kertasSiasatan', 'statusKsOptions', 'kedudukanBrgKesOptions' /*, ... other options */));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KertasSiasatan $kertasSiasatan) // Route Model Binding
    {
        // --- Validation ---
        $validatedData = $request->validate([
            // Basic Info (Usually not editable via form, but validate if they are)
            'no_ks' => 'sometimes|required|string|max:255|unique:kertas_siasatans,no_ks,'.$kertasSiasatan->getKey(),
            'tarikh_ks' => 'nullable|date',
            'no_report' => 'nullable|string|max:255',
            'jenis_jabatan_ks' => 'nullable|string|max:255',
            'pegawai_penyiasat' => 'nullable|string|max:255',
            'status_ks' => 'nullable|string|max:255',
            'status_kes' => 'nullable|string|max:255',
            'seksyen' => 'nullable|string|max:255',

            // Minit Edaran
            'tarikh_minit_a' => 'nullable|date',
            'tarikh_minit_b' => 'nullable|date|after_or_equal:tarikh_minit_a',
            'tarikh_minit_c' => 'nullable|date|after_or_equal:tarikh_minit_b',
            'tarikh_minit_d' => 'nullable|date|after_or_equal:tarikh_minit_c',

            // Status Semasa Diperiksa
            'status_ks_semasa_diperiksa' => 'nullable|string|max:255',
            'tarikh_status_ks_semasa_diperiksa' => 'nullable|required_with:status_ks_semasa_diperiksa|date',

            // Rakaman Percakapan
            'rakaman_pengadu' => 'nullable|in:YA,TIADA',
            'rakaman_saspek' => 'nullable|in:YA,TIADA',
            'rakaman_saksi' => 'nullable|in:YA,TIADA',

            // ID Siasatan Lampiran
            'id_siasatan_dilampirkan' => 'nullable|in:YA,TIDAK',
            'tarikh_id_siasatan_dilampirkan' => 'nullable|required_if:id_siasatan_dilampirkan,YA|date',

            // Barang Kes
            'barang_kes_am_didaftar' => 'nullable|in:YA,TIDAK',
            'no_daftar_kes_am' => 'nullable|string|max:255',
            'no_daftar_kes_senjata_api' => 'nullable|string|max:255',
            'no_daftar_kes_berharga' => 'nullable|string|max:255',
            'gambar_rampasan_dilampirkan' => 'nullable|in:YA,TIDAK',
            'kedudukan_barang_kes' => 'nullable|string|max:255', 
            'surat_serah_terima_stor' => 'nullable|in:ADA,TIADA',
            'arahan_pelupusan' => 'nullable|in:YA,TIDAK',
            'tatacara_pelupusan' => 'nullable|string|max:255', 
            'resit_kew38e_dilampirkan' => 'nullable|in:YA,TIDAK',
            'sijil_pelupusan_dilampirkan' => 'nullable|in:YA,TIDAK',
            'gambar_pelupusan_dilampirkan' => 'nullable|in:ADA,TIADA',
            'surat_serah_terima_penuntut' => 'nullable|in:YA,TIDAK',
            'ulasan_barang_kes' => 'nullable|string',

            // Pakar Judi / Forensik
            'surat_mohon_pakar_judi' => 'nullable|in:ADA,TIADA',
            'laporan_pakar_judi' => 'nullable|in:ADA,TIADA',
            'keputusan_pakar_judi' => 'nullable|string|max:255', 
            'kategori_perjudian' => 'nullable|string|max:255',
            'surat_mohon_forensik' => 'nullable|in:ADA,TIADA',
            'laporan_forensik' => 'nullable|in:ADA,TIADA',
            'keputusan_forensik' => 'nullable|string|max:255', 

            // Dokumen Lain
            'surat_jamin_polis' => 'nullable|string|max:255', 
            'lakaran_lokasi' => 'nullable|in:ADA,TIADA',
            'gambar_lokasi' => 'nullable|in:ADA,TIADA',

            // RJ Forms
            'rj2_status' => 'nullable|in:Cipta,Tidak Cipta',
            'rj2_tarikh' => 'nullable|required_if:rj2_status,Cipta|date',
            'rj9_status' => 'nullable|in:Cipta,Tidak Cipta',
            'rj9_tarikh' => 'nullable|required_if:rj9_status,Cipta|date',
            'rj10a_status' => 'nullable|in:Cipta,Tidak Cipta',
            'rj10a_tarikh' => 'nullable|required_if:rj10a_status,Cipta|date',
            'rj10b_status' => 'nullable|in:Cipta,Tidak Cipta',
            'rj10b_tarikh' => 'nullable|required_if:rj10b_status,Cipta|date',
            'rj99_status' => 'nullable|in:Cipta,Tidak Cipta',
            'rj99_tarikh' => 'nullable|required_if:rj99_status,Cipta|date',
            'semboyan_kesan_tangkap_status' => 'nullable|in:Cipta,Tidak Cipta',
            'semboyan_kesan_tangkap_tarikh' => 'nullable|required_if:semboyan_kesan_tangkap_status,Cipta|date',
            'waran_tangkap_status' => 'nullable|in:Mohon,Tidak Mohon',
            'waran_tangkap_tarikh' => 'nullable|required_if:waran_tangkap_status,Mohon|date',
            'ulasan_isu_rj' => 'nullable|string',

            // Surat Pemberitahuan
            'pem1_status' => 'nullable|in:Cipta,Tidak Cipta',
            'pem2_status' => 'nullable|in:Cipta,Tidak Cipta',
            'pem3_status' => 'nullable|in:Cipta,Tidak Cipta',
            'pem4_status' => 'nullable|in:Cipta,Tidak Cipta',

            // Isu-Isu
            'isu_tpr_tuduh' => 'nullable|in:YA,TIADA ISU',
            'isu_ks_lengkap_tiada_rujuk_tpr' => 'nullable|in:YA,TIADA ISU',
            'isu_tpr_arah_lupus_belum_laksana' => 'nullable|in:YA,TIADA ISU',
            'isu_tpr_arah_pulang_belum_laksana' => 'nullable|in:YA,TIADA ISU',
            'isu_tpr_arah_kesan_tangkap_tiada_tindakan' => 'nullable|in:YA,TIADA ISU',
            'isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus' => 'nullable|in:YA,TIADA ISU',
            'isu_nfa_oleh_kbsjd_sahaja' => 'nullable|in:YA,TIADA ISU',
            'isu_selesai_jatuh_hukum_belum_kus_fail' => 'nullable|in:YA,TIADA ISU',
            'isu_ks_warisan_terbengkalai' => 'nullable|in:YA,TIADA ISU',
            'isu_kbsjd_simpan_ks' => 'nullable|in:YA,TIADA ISU',
            'isu_sio_simpan_ks' => 'nullable|in:YA,TIADA ISU',
            'isu_ks_pada_tpr' => 'nullable|in:YA,TIADA ISU',

            // KS Hantar Status
            'ks_hantar_tpr_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_tpr_tarikh' => 'nullable|required_if:ks_hantar_tpr_status,YA|date',
            'ks_hantar_kjsj_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_kjsj_tarikh' => 'nullable|required_if:ks_hantar_kjsj_status,YA|date',
            'ks_hantar_d5_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_d5_tarikh' => 'nullable|required_if:ks_hantar_d5_status,YA|date',
            'ks_hantar_kbsjd_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_kbsjd_tarikh' => 'nullable|required_if:ks_hantar_kbsjd_status,YA|date',

            // Ulasan Pemeriksa
            'ulasan_isu_menarik' => 'nullable|string',
            'ulasan_keseluruhan' => 'nullable|string',
        ]);

        // --- Update Logic ---
        $kertasSiasatan->fill($validatedData); 
        $kertasSiasatan->save(); 

        return redirect()->route('kertas_siasatan.index')
                         ->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dikemaskini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KertasSiasatan $kertasSiasatan) // Route Model Binding
    {
        try {
            $kertasSiasatan->delete();
            return redirect()->route('kertas_siasatan.index')
                             ->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dipadam.');
        } catch (\Exception $e) {
            return redirect()->route('kertas_siasatan.index')
                             ->with('error', 'Gagal memadam Kertas Siasatan: ' . $e->getMessage());
        }
    }
}