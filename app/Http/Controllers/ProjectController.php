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

    /**
     * Provides the data for the Yajra Datatable.
     * This version is more robust and includes all columns.
     */
    public function getKertasSiasatanData(Project $project, Request $request)
    {
        // FIX: Select only the columns needed for the DataTable for better performance.
        $query = KertasSiasatan::where('project_id', $project->id)
            ->select([
                'id', 'no_ks', 'tarikh_ks', 'no_report', 'pegawai_penyiasat',
                'status_ks', 'status_kes', 'seksyen', 'project_id'
            ]);

        return DataTables::of($query)
            ->addIndexColumn() // Adds the DT_RowIndex column for numbering
            ->addColumn('action', function ($row) {
                // Check if project_id exists before creating the route
                $disassociateUrl = $row->project_id 
                    ? route('projects.disassociate_paper', ['project' => $row->project_id, 'paperType' => 'KertasSiasatan', 'paperId' => $row->id]) 
                    : '#';

                $actionBtn = '<div class="flex items-center space-x-2">';
                $actionBtn .= '<a href="'.route('kertas_siasatan.show', $row->id).'" class="text-indigo-600 hover:text-indigo-900" title="Lihat"><i class="fas fa-eye"></i></a>';
                $actionBtn .= '<a href="'.route('kertas_siasatan.edit', $row->id).'" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini"><i class="fas fa-edit"></i></a>';
                
                // Only show the disassociate button if the URL is valid
                if ($disassociateUrl !== '#') {
                    $actionBtn .= '<form action="'.$disassociateUrl.'" method="POST" class="inline" onsubmit="return confirm(\'Anda pasti ingin mengeluarkan Kertas Siasatan ini?\')">'.csrf_field().'<button type="submit" class="text-orange-600 hover:text-orange-900" title="Keluarkan dari Projek"><i class="fas fa-unlink"></i></button></form>';
                }

                $actionBtn .= '</div>';
                return $actionBtn;
            })
            // FIX: Only format the 'tarikh_ks' column as it's the only date displayed.
            ->editColumn('tarikh_ks', fn($row) => optional($row->tarikh_ks)->format('d/m/Y'))
            ->rawColumns(['action'])
            ->make(true);
    }
    // ... all other methods remain the same ...
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

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

    public function destroy(Project $project)
    {
        return Redirect::route('projects.index')->with('info', 'Delete functionality not yet implemented.');
    }

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
        if ($paper->project_id) {
             if ($paper->project_id == $project->id) {
                 return redirect()->route('projects.show', $project)->with('info', ucfirst($validated['paper_type']) . ' is already associated with this project.');
             } else {
                 $otherProject = Project::find($paper->project_id);
                 $otherProjectName = $otherProject ? $otherProject->name : 'another project';
                 return redirect()->route('projects.show', $project)->with('error', ucfirst($validated['paper_type']) . ' is already associated with ' . $otherProjectName . '.');
             }
        }
        $paper->project_id = $project->id;
        $paper->save();
        return redirect()->route('projects.show', $project)->with('success', ucfirst($validated['paper_type']) . ' successfully associated with the project.');
    }

    public function disassociatePaper(Request $request, Project $project, $paperType, $paperId)
    {
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

    public function downloadAssociatedPapersCsv(Project $project)
    {
        $allPapersData = $project->allAssociatedPapers();
        $fileName = Str::slug($project->name) . '-associated-papers.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $columns = ['Paper Type', 'Identifier', 'Details', 'Status KS', 'Status Kes', 'Pegawai Penyiasat', 'Tarikh KS'];
        $callback = function() use ($allPapersData, $columns, $project) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($allPapersData as $type => $papersCollection) {
                $paperTypeDisplay = Str::title(str_replace('_', ' ', Str::before($type, 'Papers')));
                if ($type === 'kertas_siasatan') {
                    $papersCollection = $project->kertasSiasatan()->get();
                }
                foreach ($papersCollection as $paper) {
                    $identifier = $paper->no_ks ?? $paper->no_kst ?? $paper->no_lmm ?? $paper->no_ks_oh ?? $paper->name ?? "ID: {$paper->id}";
                    $details = '';
                    $statusKs = $paper->status_ks ?? '-';
                    $statusKes = $paper->status_kes ?? '-';
                    $pegawaiPenyiasat = $paper->pegawai_penyiasat ?? '-';
                    $tarikhKs = isset($paper->tarikh_ks) ? (is_string($paper->tarikh_ks) ? \Carbon\Carbon::parse($paper->tarikh_ks)->format('d/m/Y') : optional($paper->tarikh_ks)->format('d/m/Y')) : '-';
                    $row = [$paperTypeDisplay, $identifier, $details, $statusKs, $statusKes, $pegawaiPenyiasat, $tarikhKs,];
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        };
        return new StreamedResponse($callback, 200, $headers);
    }
}