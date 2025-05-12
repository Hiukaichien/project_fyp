<?php

namespace App\Http\Controllers;

use App\Models\KertasSiasatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KertasSiasatanImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $pageName = $request->input('page_name_param', 'page');

        $kertasSiasatans = $query->sortable()
                         ->paginate(10, ['*'], $pageName)
                         ->appends($request->except(['page', 'page_name_param']));

        if ($request->ajax()) {
            $viewName = 'kertas_siasatan._table_rows';
            $viewData = ['kertasSiasatans' => $kertasSiasatans];

            if ($request->filled('project_id')) {
                $project = Project::find($request->input('project_id'));
                if ($project) {
                    $viewName = 'projects._associated_kertas_siasatan_table_rows';
                    $viewData['project'] = $project;
                }
            }

            $tableHtml = view($viewName, $viewData)->render();
            $paginationHtml = $kertasSiasatans->links()->toHtml();

            return response()->json([
                'table_html' => $tableHtml,
                'pagination_html' => $paginationHtml,
            ]);
        }

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
        $projects_for_export = Project::orderBy('name')->get();
        return view('kertas_siasatan.upload', compact('projects_for_export'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
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

    public function show(KertasSiasatan $kertasSiasatan)
    {
         return view('kertas_siasatan.show', compact('kertasSiasatan'));
    }

    public function edit(KertasSiasatan $kertasSiasatan)
    {
        $statusKsOptions = ['Siasatan Aktif', 'Rujuk TPR', 'Rujuk PPN', 'Rujuk KJSJ', 'Rujuk KBSJD', 'KUS/Sementara', 'Jatuh Hukum', 'KUS/Fail'];
        $kedudukanBrgKesOptions = ['Dalam Peti Besi KPD', 'Simpanan Stor Barang Kes'];

        return view('kertas_siasatan.edit', compact('kertasSiasatan', 'statusKsOptions', 'kedudukanBrgKesOptions'));
    }

    public function update(Request $request, KertasSiasatan $kertasSiasatan)
    {
        $validatedData = $request->validate([
            'no_ks' => 'sometimes|required|string|max:255|unique:kertas_siasatans,no_ks,'.$kertasSiasatan->getKey(),
            'tarikh_ks' => 'nullable|date',
            'no_report' => 'nullable|string|max:255',
            'jenis_jabatan_ks' => 'nullable|string|max:255',
            'pegawai_penyiasat' => 'nullable|string|max:255',
            'status_ks' => 'nullable|string|max:255',
            'status_kes' => 'nullable|string|max:255',
            'seksyen' => 'nullable|string|max:255',
            'tarikh_minit_a' => 'nullable|date',
            'tarikh_minit_b' => 'nullable|date|after_or_equal:tarikh_minit_a',
            'tarikh_minit_c' => 'nullable|date|after_or_equal:tarikh_minit_b',
            'tarikh_minit_d' => 'nullable|date|after_or_equal:tarikh_minit_c',
            'status_ks_semasa_diperiksa' => 'nullable|string|max:255',
            'tarikh_status_ks_semasa_diperiksa' => 'nullable|required_with:status_ks_semasa_diperiksa|date',
            'rakaman_pengadu' => 'nullable|in:YA,TIADA',
            'rakaman_saspek' => 'nullable|in:YA,TIADA',
            'rakaman_saksi' => 'nullable|in:YA,TIADA',
            'id_siasatan_dilampirkan' => 'nullable|in:YA,TIDAK',
            'tarikh_id_siasatan_dilampirkan' => 'nullable|required_if:id_siasatan_dilampirkan,YA|date',
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
            'surat_mohon_pakar_judi' => 'nullable|in:ADA,TIADA',
            'laporan_pakar_judi' => 'nullable|in:ADA,TIADA',
            'keputusan_pakar_judi' => 'nullable|string|max:255',
            'kategori_perjudian' => 'nullable|string|max:255',
            'surat_mohon_forensik' => 'nullable|in:ADA,TIADA',
            'laporan_forensik' => 'nullable|in:ADA,TIADA',
            'keputusan_forensik' => 'nullable|string|max:255',
            'surat_jamin_polis' => 'nullable|string|max:255',
            'lakaran_lokasi' => 'nullable|in:ADA,TIADA',
            'gambar_lokasi' => 'nullable|in:ADA,TIADA',
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
            'pem1_status' => 'nullable|in:Cipta,Tidak Cipta',
            'pem2_status' => 'nullable|in:Cipta,Tidak Cipta',
            'pem3_status' => 'nullable|in:Cipta,Tidak Cipta',
            'pem4_status' => 'nullable|in:Cipta,Tidak Cipta',
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
            'ks_hantar_tpr_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_tpr_tarikh' => 'nullable|required_if:ks_hantar_tpr_status,YA|date',
            'ks_hantar_kjsj_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_kjsj_tarikh' => 'nullable|required_if:ks_hantar_kjsj_status,YA|date',
            'ks_hantar_d5_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_d5_tarikh' => 'nullable|required_if:ks_hantar_d5_status,YA|date',
            'ks_hantar_kbsjd_status' => 'nullable|in:YA,TIADA ISU',
            'ks_hantar_kbsjd_tarikh' => 'nullable|required_if:ks_hantar_kbsjd_status,YA|date',
            'ulasan_isu_menarik' => 'nullable|string',
            'ulasan_keseluruhan' => 'nullable|string',
        ]);

        $kertasSiasatan->fill($validatedData);
        $kertasSiasatan->save();

        return redirect()->route('kertas_siasatan.index')
                         ->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dikemaskini.');
    }

    public function destroy(KertasSiasatan $kertasSiasatan)
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

    public function exportByProject(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($request->project_id);
        $kertasSiasatans = $project->kertasSiasatan()->get();

        if ($kertasSiasatans->isEmpty()) {
            return Redirect::route('kertas_siasatan.create')->with('info', 'Tiada Kertas Siasatan yang dikaitkan dengan projek "' . $project->name . '" untuk dieksport.');
        }

        $fileName = 'kertas-siasatan-' . Str::slug($project->name) . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'No. Kertas Siasatan',
            'Tarikh KS',
            'No. Repot',
            'Jenis Jabatan KS',
            'Pegawai Penyiasat',
            'Status KS',
            'Status Kes',
            'Seksyen',
        ];

        $callback = function() use ($kertasSiasatans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($kertasSiasatans as $ks) {
                $row = [
                    $ks->no_ks ?? '-',
                    optional($ks->tarikh_ks)->format('d/m/Y') ?? '-',
                    $ks->no_report ?? '-',
                    $ks->jenis_jabatan_ks ?? '-',
                    $ks->pegawai_penyiasat ?? '-',
                    $ks->status_ks ?? '-',
                    $ks->status_kes ?? '-',
                    $ks->seksyen ?? '-',
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}