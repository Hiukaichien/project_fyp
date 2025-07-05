<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaperImport;

// Import all 8 paper models
use App\Models\KertasSiasatan;
use App\Models\JenayahPaper;
use App\Models\NarkotikPaper;
use App\Models\TrafikSeksyenPaper;
use App\Models\TrafikRulePaper;
use App\Models\KomersilPaper;
use App\Models\LaporanMatiMengejutPaper;
use App\Models\OrangHilangPaper;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('project_date', 'desc')->paginate(10);
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
        // Get a single collection of all papers associated with this project
        $allPapers = $project->allPapersMerged();

        // Filter the merged collection to get the data for the summary cards
        $ksLewat24Jam = $allPapers->filter(function ($paper) {
            return isset($paper->edar_lebih_24_jam_status) && Str::contains($paper->edar_lebih_24_jam_status, 'LEWAT');
        });

        $ksTerbengkalai = $allPapers->filter(function ($paper) {
            return isset($paper->terbengkalai_3_bulan_status) && Str::contains($paper->terbengkalai_3_bulan_status, 'TERBENGKALAI');
        });

        $ksBaruKemaskini = $allPapers->filter(function ($paper) {
            return isset($paper->baru_kemaskini_status) && Str::contains($paper->baru_kemaskini_status, 'BARU DIKEMASKINI');
        });
        
        // Pass all three variables to the view
        return view('projects.show', compact(
            'project',
            'ksLewat24Jam',
            'ksTerbengkalai',
            'ksBaruKemaskini' 
        ));
    }

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
        // Safely disassociate all papers before deleting the project
        foreach ($project->allAssociatedPapers() as $papersCollection) {
            foreach ($papersCollection as $paper) {
                $paper->project_id = null;
                $paper->save();
            }
        }
        $project->delete();
        return Redirect::route('projects.index')->with('success', 'Project and all paper associations have been deleted.');
    }

    public function importPapers(Request $request, Project $project)
    {
        $validated = $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:20480',
            'paper_type' => ['required', 'string', Rule::in(['KertasSiasatan', 'JenayahPaper', 'NarkotikPaper', 'KomersilPaper', 'TrafikSeksyenPaper', 'TrafikRulePaper', 'OrangHilangPaper', 'LaporanMatiMengejutPaper'])],
        ]);

        try {
            Excel::import(new PaperImport($project->id, $validated['paper_type']), $request->file('excel_file'));
            $friendlyName = Str::of($validated['paper_type'])->replace('Paper', ' Paper')->headline();
            return back()->with('success', $friendlyName . ' berjaya diimport ke projek ini.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ralat semasa memproses fail: ' . $e->getMessage())->withInput();
        }
    }
    
    public function disassociatePaper(Request $request, Project $project, $paperType, $paperId)
    {
        $validPaperTypes = ['KertasSiasatan', 'JenayahPaper', 'NarkotikPaper', 'TrafikSeksyenPaper', 'TrafikRulePaper', 'KomersilPaper', 'LaporanMatiMengejutPaper', 'OrangHilangPaper'];
        if (!in_array($paperType, $validPaperTypes)) {
            return redirect()->route('projects.show', $project)->with('error', 'Invalid paper type specified.');
        }
        $paperModelClass = 'App\\Models\\' . $paperType;
        $paper = $paperModelClass::where('id', $paperId)->where('project_id', $project->id)->firstOrFail();
        $paper->project_id = null;
        $paper->save();
        $friendlyName = Str::of($paperType)->replace('Paper', ' Paper')->headline();
        return redirect()->route('projects.show', $project)->with('success', $friendlyName . ' successfully removed from the project.');
    }

    public function downloadAssociatedPapersCsv(Project $project)
    {
        $fileName = Str::slug($project->name) . '-all-papers.csv';
        $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$fileName", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0"];
        
        $columns = ['Jenis Kertas', 'No. Rujukan Unik', 'IO/AIO', 'Seksyen', 'Status', 'Tarikh'];

        $callback = function() use ($project, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($project->allAssociatedPapers() as $type => $papers) {
                foreach ($papers as $paper) {
                    $paperData = $this->mapPaperDataForCsv($paper);
                    fputcsv($file, $paperData);
                }
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
    
    private function mapPaperDataForCsv($paper): array
    {
        $modelName = class_basename($paper);
        $paperType = Str::of($modelName)->replace('Paper', ' Paper')->headline();
        
        $identifier = $paper->no_ks ?? $paper->no_kst ?? $paper->no_lmm ?? $paper->no_ks_oh ?? 'N/A';
        $io = $paper->io_aio ?? $paper->pegawai_penyiasat ?? 'N/A';
        $seksyen = $paper->seksyen ?? $paper->seksyen_dibuka ?? 'N/A';
        $status = $paper->status_kes ?? $paper->status_ks ?? $paper->status_oh ?? $paper->status_lmm ?? 'N/A';
        $date = $paper->tarikh_ks ?? $paper->tarikh_ks_dibuka ?? $paper->tarikh_laporan_polis ?? $paper->tarikh_daftar ?? 'N/A';

        return [
            $paperType,
            $identifier,
            $io,
            $seksyen,
            $status,
            $date ? Carbon::parse($date)->format('d/m/Y') : 'N/A',
        ];
    }

        public function exportPapers(Request $request, Project $project)
    {
        $validated = $request->validate([
            'paper_type' => ['required', 'string', Rule::in(['KertasSiasatan', 'JenayahPaper', 'NarkotikPaper', 'KomersilPaper', 'TrafikSeksyenPaper', 'TrafikRulePaper', 'OrangHilangPaper', 'LaporanMatiMengejutPaper'])],
        ]);

        $paperType = $validated['paper_type'];
        $modelClass = 'App\\Models\\' . $paperType;
        
        $papers = $modelClass::where('project_id', $project->id)->get();

        if ($papers->isEmpty()) {
            return back()->with('info', 'Tiada data ditemui untuk jenis kertas "' . Str::headline($paperType) . '" dalam projek ini.');
        }

        $fileName = Str::slug($project->name) . '-' . Str::slug($paperType) . '.csv';
        $columns = Schema::getColumnListing((new $modelClass)->getTable());

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($papers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($papers as $paper) {
                $row = [];
                foreach ($columns as $column) {
                    $row[] = $paper->{$column};
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function buildActionButtons($row, $paperType)
    {
        $disassociateUrl = route('projects.disassociate_paper', ['project' => $row->project_id, 'paperType' => $paperType, 'paperId' => $row->id]);
        
        // --- This logic is correct and does not need to change ---
        $showUrl = route('kertas_siasatan.show', ['paperType' => $paperType, 'id' => $row->id]);
        $editUrl = route('kertas_siasatan.edit', ['paperType' => $paperType, 'id' => $row->id]);

        $actions = '<a href="'.$showUrl.'" class="text-indigo-600 hover:text-indigo-900" title="Lihat"><i class="fas fa-eye"></i></a>';
        $actions .= '<a href="'.$editUrl.'" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini"><i class="fas fa-edit"></i></a>';

        $actions .= '<form action="'.$disassociateUrl.'" method="POST" class="inline" onsubmit="return confirm(\'Anda pasti ingin mengeluarkan kertas ini?\')">' .
               csrf_field() .
               '<button type="submit" class="text-orange-600" title="Keluarkan"><i class="fas fa-unlink"></i></button></form>';
        
        return '<div class="flex items-center space-x-2">' . $actions . '</div>';
    }


    // --- DATATABLES SERVER-SIDE METHODS ---
    // --- FIX: Added ->addIndexColumn() to every method ---
    public function getKertasSiasatanData(Project $project) {
        $query = KertasSiasatan::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'KertasSiasatan'))->rawColumns(['action'])->make(true);
    }
    public function getJenayahPapersData(Project $project) {
        $query = JenayahPaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'JenayahPaper'))->rawColumns(['action'])->make(true);
    }
    public function getNarkotikPapersData(Project $project) {
        $query = NarkotikPaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'NarkotikPaper'))->rawColumns(['action'])->make(true);
    }
    public function getKomersilPapersData(Project $project) {
        $query = KomersilPaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'KomersilPaper'))->rawColumns(['action'])->make(true);
    }
    public function getTrafikSeksyenPapersData(Project $project) {
        $query = TrafikSeksyenPaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikSeksyenPaper'))->rawColumns(['action'])->make(true);
    }
    public function getTrafikRulePapersData(Project $project) {
        $query = TrafikRulePaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikRulePaper'))->rawColumns(['action'])->make(true);
    }
    public function getOrangHilangPapersData(Project $project) {
        $query = OrangHilangPaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'OrangHilangPaper'))->rawColumns(['action'])->make(true);
    }
    public function getLaporanMatiMengejutPapersData(Project $project) {
        $query = LaporanMatiMengejutPaper::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'LaporanMatiMengejutPaper'))->rawColumns(['action'])->make(true);
    }
}