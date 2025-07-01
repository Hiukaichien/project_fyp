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

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Use sortable scope and paginate the results
        // Default sort can be set in Project model using $sortableAs array or apply a default here
        $projects = Project::sortable()
                            ->orderBy('project_date', 'desc') // Retain default sort if no sort params from request
                            ->paginate(10); // Paginate results
        return view('projects.project', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'project_date' => 'required|date',
            'description' => 'nullable|string', // Description is in your model's fillable
        ]);

        Project::create($validatedData);

        return Redirect::route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Request $request)
    {
        // This method now also handles searching and filtering for the project's Kertas Siasatan
        $query = $project->kertasSiasatan();

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

        // Handle AJAX requests for the Kertas Siasatan table
        if ($request->ajax()) {
            $pageName = $request->input('page_name_param', 'page');
            $kertasSiasatans = $query->sortable()->paginate(10, ['*'], $pageName)->appends($request->except('page', 'page_name_param'));

            $tableHtml = view('projects._associated_kertas_siasatan_table_rows', [
                'kertasSiasatans' => $kertasSiasatans,
                'project' => $project
            ])->render();
            $paginationHtml = $kertasSiasatans->links()->toHtml();

            return response()->json([
                'table_html' => $tableHtml,
                'pagination_html' => $paginationHtml,
            ]);
        }

        // --- For initial page load ---
        $unassignedKertasSiasatan = KertasSiasatan::whereNull('project_id')->sortable()->orderBy('no_ks')->get();
        $unassignedJenayahPapers = JenayahPaper::whereNull('project_id')->sortable()->get(); 
        $unassignedNarkotikPapers = NarkotikPaper::whereNull('project_id')->sortable()->get();
        $unassignedTrafikSeksyenPapers = TrafikSeksyenPaper::whereNull('project_id')->sortable()->get();
        $unassignedTrafikRulePapers = TrafikRulePaper::whereNull('project_id')->sortable()->orderBy('no_kst')->get();
        $unassignedKomersilPapers = KomersilPaper::whereNull('project_id')->sortable()->get();
        $unassignedLaporanMatiMengejutPapers = LaporanMatiMengejutPaper::whereNull('project_id')->sortable()->get();
        $unassignedOrangHilangPapers = OrangHilangPaper::whereNull('project_id')->sortable()->get();
        
        $pageNameForKSTable = 'ks_project_page';
        $associatedKertasSiasatanPaginated = $query->sortable()->paginate(10, ['*'], $pageNameForKSTable);
        
        $allPapers = $project->allAssociatedPapers();

        // Calculate stats scoped to the current project
        $projectKSQuery = $project->kertasSiasatan();
        $ksLewat24Jam = $projectKSQuery->clone()->where('edar_lebih_24_jam_status', 'YA, EDARAN LEWAT 24 JAM')->orderBy('tarikh_ks', 'desc')->get();
        $ksTerbengkalai = $projectKSQuery->clone()->where('terbengkalai_3_bulan_status', 'YA, TERBENGKALAI LEBIH 3 BULAN')->orderBy('tarikh_ks', 'desc')->get();
        $ksBaruKemaskini = $projectKSQuery->clone()->where('baru_kemaskini_status', 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI')->orderBy('updated_at', 'desc')->get();

        return view('projects.show', compact(
            'project',
            'unassignedKertasSiasatan',
            'unassignedJenayahPapers',
            'unassignedNarkotikPapers',
            'unassignedTrafikSeksyenPapers',
            'unassignedTrafikRulePapers',
            'unassignedKomersilPapers',
            'unassignedLaporanMatiMengejutPapers',
            'unassignedOrangHilangPapers',
            'associatedKertasSiasatanPaginated',
            'allPapers',
            'ksLewat24Jam',
            'ksTerbengkalai',
            'ksBaruKemaskini'
        ));
    }


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