<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; // Import Redirect facade
use App\Models\KertasSiasatan; // Assuming this is one of your paper models
use App\Models\JenayahPaper;
use App\Models\NarkotikPaper;
use App\Models\TrafikSeksyenPaper;
use App\Models\TrafikRulePaper; 
use App\Models\KomersilPaper;  
use App\Models\LaporanMatiMengejutPaper; 
use App\Models\OrangHilangPaper; 
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse; // Add this import
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::sortable()->orderBy('project_date', 'desc')->paginate(10);
        return view('projects.project', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'project_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        Project::create($validatedData);
        return Redirect::route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $ksLewat24Jam = $project->kertasSiasatan()->where('edar_lebih_24_jam_status', 'YA, EDARAN LEWAT 24 JAM')->get();
        $ksTerbengkalai = $project->kertasSiasatan()->where('terbengkalai_3_bulan_status', 'YA, TERBENGKALAI LEBIH 3 BULAN')->get();
        $ksBaruKemaskini = $project->kertasSiasatan()->where('baru_kemaskini_status', 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI')->get();

        return view('projects.show', compact(
            'project',
            'ksLewat24Jam',
            'ksTerbengkalai',
            'ksBaruKemaskini'
        ));
    }

    public function getKertasSiasatanData(Project $project)
    {
        $query = KertasSiasatan::where('project_id', $project->id)->select('kertas_siasatans.*');

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $viewUrl = route('kertas_siasatan.show', $row->id);
                $editUrl = route('kertas_siasatan.edit', $row->id);
                $disassociateUrl = route('projects.disassociate_paper', ['project' => $row->project_id, 'paperType' => 'KertasSiasatan', 'paperId' => $row->id]);

                $actionBtn = '<div class="space-x-2 flex items-center">';
                $actionBtn .= '<a href="'.$viewUrl.'" class="text-indigo-600 hover:text-indigo-900" title="Lihat"><i class="fas fa-eye"></i></a>';
                $actionBtn .= '<a href="'.$editUrl.'" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini"><i class="fas fa-edit"></i></a>';
                $actionBtn .= '<form action="'.$disassociateUrl.'" method="POST" class="inline" onsubmit="return confirm(\'Anda pasti ingin mengeluarkan Kertas Siasatan ini?\')">'.csrf_field().'<button type="submit" class="text-orange-600 hover:text-orange-900" title="Keluarkan dari Projek"><i class="fas fa-unlink"></i></button></form>';
                $actionBtn .= '</div>';
                return $actionBtn;
            })
            ->editColumn('tarikh_ks', fn($row) => $row->tarikh_ks ? $row->tarikh_ks->format('d/m/Y') : '-')
            ->editColumn('tarikh_minit_a', fn($row) => $row->tarikh_minit_a ? $row->tarikh_minit_a->format('d/m/Y') : '-')
            ->editColumn('tarikh_minit_b', fn($row) => $row->tarikh_minit_b ? $row->tarikh_minit_b->format('d/m/Y') : '-')
            ->editColumn('tarikh_minit_c', fn($row) => $row->tarikh_minit_c ? $row->tarikh_minit_c->format('d/m/Y') : '-')
            ->editColumn('tarikh_minit_d', fn($row) => $row->tarikh_minit_d ? $row->tarikh_minit_d->format('d/m/Y') : '-')
            ->editColumn('tarikh_status_ks_semasa_diperiksa', fn($row) => $row->tarikh_status_ks_semasa_diperiksa ? $row->tarikh_status_ks_semasa_diperiksa->format('d/m/Y') : '-')
            ->editColumn('tarikh_id_siasatan_dilampirkan', fn($row) => $row->tarikh_id_siasatan_dilampirkan ? $row->tarikh_id_siasatan_dilampirkan->format('d/m/Y') : '-')
            ->editColumn('rj2_tarikh', fn($row) => $row->rj2_tarikh ? $row->rj2_tarikh->format('d/m/Y') : '-')
            ->editColumn('rj9_tarikh', fn($row) => $row->rj9_tarikh ? $row->rj9_tarikh->format('d/m/Y') : '-')
            ->editColumn('rj10a_tarikh', fn($row) => $row->rj10a_tarikh ? $row->rj10a_tarikh->format('d/m/Y') : '-')
            ->editColumn('rj10b_tarikh', fn($row) => $row->rj10b_tarikh ? $row->rj10b_tarikh->format('d/m/Y') : '-')
            ->editColumn('rj99_tarikh', fn($row) => $row->rj99_tarikh ? $row->rj99_tarikh->format('d/m/Y') : '-')
            ->editColumn('semboyan_kesan_tangkap_tarikh', fn($row) => $row->semboyan_kesan_tangkap_tarikh ? $row->semboyan_kesan_tangkap_tarikh->format('d/m/Y') : '-')
            ->editColumn('waran_tangkap_tarikh', fn($row) => $row->waran_tangkap_tarikh ? $row->waran_tangkap_tarikh->format('d/m/Y') : '-')
            ->editColumn('ks_hantar_tpr_tarikh', fn($row) => $row->ks_hantar_tpr_tarikh ? $row->ks_hantar_tpr_tarikh->format('d/m/Y') : '-')
            ->editColumn('ks_hantar_kjsj_tarikh', fn($row) => $row->ks_hantar_kjsj_tarikh ? $row->ks_hantar_kjsj_tarikh->format('d/m/Y') : '-')
            ->editColumn('ks_hantar_d5_tarikh', fn($row) => $row->ks_hantar_d5_tarikh ? $row->ks_hantar_d5_tarikh->format('d/m/Y') : '-')
            ->editColumn('ks_hantar_kbsjd_tarikh', fn($row) => $row->ks_hantar_kbsjd_tarikh ? $row->ks_hantar_kbsjd_tarikh->format('d/m/Y') : '-')
            ->rawColumns(['action'])
            ->make(true);
    }
    
    // 


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'project_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        $project->update($validatedData);
        return Redirect::route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * (Stub - implement if needed)
     */
    public function destroy(Project $project)
    {
        // $project->delete();
        // return Redirect::route('projects.index')->with('success', 'Project deleted successfully.');
        return Redirect::route('projects.index')->with('info', 'Delete functionality not yet implemented.');
    }

    /**
     * Associate an existing paper with the project.
     */
    public function associatePaper(Request $request, Project $project)
    {
        $validated = $request->validate([
            'paper_id' => 'required', 
            'paper_type' => 'required|string|in:KertasSiasatan,JenayahPaper,NarkotikPaper,TrafikSeksyenPaper,TrafikRulePaper,KomersilPaper,LaporanMatiMengejutPaper,OrangHilangPaper', 
        ]);

        $paperModelClass = 'App\\Models\\' . $validated['paper_type'];

        if (!class_exists($paperModelClass)) {
            return redirect()->route('projects.show', $project)->with('error', 'Invalid paper type specified.');
        }

        $paper = $paperModelClass::find($validated['paper_id']);

        if (!$paper) {
            return redirect()->route('projects.show', $project)->with('error', 'Paper not found.');
        }

        // Check if paper is already associated
        if ($paper->project_id) {
             if ($paper->project_id == $project->id) {
                 return redirect()->route('projects.show', $project)->with('info', ucfirst($validated['paper_type']) . ' is already associated with this project.');
             } else {
                 // Find the other project it's associated with for a more informative message
                 $otherProject = Project::find($paper->project_id);
                 $otherProjectName = $otherProject ? $otherProject->name : 'another project';
                 return redirect()->route('projects.show', $project)->with('error', ucfirst($validated['paper_type']) . ' is already associated with ' . $otherProjectName . '.');
             }
        }

        $paper->project_id = $project->id;
        $paper->save();

        return redirect()->route('projects.show', $project)->with('success', ucfirst($validated['paper_type']) . ' successfully associated with the project.');
    }

    /**
     * Disassociate a paper from the project.
     * This function is called via a route when a form is submitted from a Blade view.
     */
    public function disassociatePaper(Request $request, Project $project, $paperType, $paperId)
    {
        // Validate paperType to ensure it's one of the expected models
        $validPaperTypes = ['KertasSiasatan', 'JenayahPaper', 'NarkotikPaper', 'TrafikSeksyenPaper', 'TrafikRulePaper', 'KomersilPaper', 'LaporanMatiMengejutPaper', 'OrangHilangPaper'];
        if (!in_array($paperType, $validPaperTypes)) {
            return redirect()->route('projects.show', $project)->with('error', 'Invalid paper type specified for disassociation.');
        }

        $paperModelClass = 'App\\Models\\' . $paperType;

        if (!class_exists($paperModelClass)) {
            return redirect()->route('projects.show', $project)->with('error', 'Invalid paper model specified.');
        }

        $paper = $paperModelClass::where('id', $paperId)->where('project_id', $project->id)->first();

        if (!$paper) {
            return redirect()->route('projects.show', $project)->with('error', 'Paper not found or not associated with this project.');
        }

        $paper->project_id = null;
        $paper->save();

        return redirect()->route('projects.show', $project)->with('success', ucfirst($paperType) . ' successfully removed from the project.');
    }

    /**
     * Download associated papers as a CSV file.
     */
    public function downloadAssociatedPapersCsv(Project $project)
    {
        $allPapersData = $project->allAssociatedPapers(); // Assuming this method returns all types of papers

        $fileName = Str::slug($project->name) . '-associated-papers.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Paper Type', 'Identifier', 'Details', 'Status KS', 'Status Kes', 'Pegawai Penyiasat', 'Tarikh KS']; // Adjust columns as needed

        $callback = function() use ($allPapersData, $columns, $project) { // Added $project here
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($allPapersData as $type => $papersCollection) {
                $paperTypeDisplay = Str::title(str_replace('_', ' ', Str::before($type, 'Papers')));
                if ($type === 'kertas_siasatan') { // KertasSiasatan might be paginated, handle it
                    $papersCollection = $project->kertasSiasatan()->get(); // Get all KS for this project
                }

                foreach ($papersCollection as $paper) {
                    $identifier = $paper->no_ks ?? $paper->no_kst ?? $paper->no_lmm ?? $paper->no_ks_oh ?? $paper->name ?? "ID: {$paper->id}";
                    $details = ''; // Add any other specific details you want to extract
                                    // For example, $paper->description or other fields
                    
                    $statusKs = $paper->status_ks ?? '-';
                    $statusKes = $paper->status_kes ?? '-';
                    $pegawaiPenyiasat = $paper->pegawai_penyiasat ?? '-';
                    $tarikhKs = isset($paper->tarikh_ks) ? (is_string($paper->tarikh_ks) ? \Carbon\Carbon::parse($paper->tarikh_ks)->format('d/m/Y') : optional($paper->tarikh_ks)->format('d/m/Y')) : '-';


                    $row = [
                        $paperTypeDisplay,
                        $identifier,
                        $details, // You might want to populate this with more specific info
                        $statusKs,
                        $statusKes,
                        $pegawaiPenyiasat,
                        $tarikhKs,
                    ];
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}