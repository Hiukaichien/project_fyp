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
     * The index method has been removed. All browsing of Kertas Siasatan records
     * is now handled through the ProjectController@show method, which provides
     * a project-scoped dashboard view.
     */

    /**
     * The create method has been removed. The upload form is now integrated
     * directly into the projects.show view.
     */

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            // Pass the project_id to the import class constructor
            Excel::import(new KertasSiasatanImport($request->project_id), $request->file('excel_file'));

            return redirect()->route('projects.show', $request->project_id)
                             ->with('success', 'Fail Excel berjaya dimuatnaik dan Kertas Siasatan telah dikaitkan dengan projek ini.');

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

        // Redirect back to the project dashboard if it exists, otherwise to the KS show page
        if ($kertasSiasatan->project) {
            return redirect()->route('projects.show', $kertasSiasatan->project_id)
                             ->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dikemaskini.');
        }

        return redirect()->route('kertas_siasatan.show', $kertasSiasatan)
                         ->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dikemaskini.');
    }

    public function destroy(KertasSiasatan $kertasSiasatan)
    {
        try {
            $projectId = $kertasSiasatan->project_id;
            $kertasSiasatan->delete();
            
            $redirectRoute = $projectId ? route('projects.show', $projectId) : route('projects.index');

            return redirect($redirectRoute)
                             ->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dipadam.');
        } catch (\Exception $e) {
            return redirect()->back()
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
            return Redirect::back()->with('info', 'Tiada Kertas Siasatan yang dikaitkan dengan projek "' . $project->name . '" untuk dieksport.');
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