<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaperImport;

use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\Trafik;
use App\Models\Komersil;
use App\Models\LaporanMatiMengejut;
use App\Models\OrangHilang;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;


class ProjectController extends Controller
{
    // --- (index, create, store methods are fine, no changes needed) ---
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

    public function show(Project $project, Request $request) // <-- ADD Request $request HERE
    {
        // Get a single collection of all papers associated with this project
        $allPapers = $project->allPapersMerged();

        // --- PAGINATION LOGIC ---
        $perPage = 10; // Set how many items you want per page for the summary tables

        // Filter ALL items first for each category
        $lewatItems = $allPapers->filter(fn ($paper) => isset($paper->edar_lebih_24_jam_status) && Str::contains($paper->edar_lebih_24_jam_status, 'LEWAT'));
        $terbengkalaiItems = $allPapers->filter(fn ($paper) => isset($paper->terbengkalai_3_bulan_status) && Str::contains($paper->terbengkalai_3_bulan_status, 'TERBENGKALAI'));
        $kemaskiniItems = $allPapers->filter(fn ($paper) => isset($paper->baru_kemaskini_status) && Str::contains($paper->baru_kemaskini_status, 'BARU DIKEMASKINI'));

        // Manually create a paginator for "Lewat Edar"
        $lewatPage = $request->get('lewat_page', 1); // <-- Use $request->get()
        $ksLewat24Jam = new LengthAwarePaginator(
            $lewatItems->forPage($lewatPage, $perPage),
            $lewatItems->count(),
            $perPage,
            $lewatPage,
            ['path' => $request->url(), 'pageName' => 'lewat_page'] // <-- Use $request->url()
        );

        // Manually create a paginator for "Terbengkalai"
        $terbengkalaiPage = $request->get('terbengkalai_page', 1); // <-- Use $request->get()
        $ksTerbengkalai = new LengthAwarePaginator(
            $terbengkalaiItems->forPage($terbengkalaiPage, $perPage),
            $terbengkalaiItems->count(),
            $perPage,
            $terbengkalaiPage,
            ['path' => $request->url(), 'pageName' => 'terbengkalai_page'] // <-- Use $request->url()
        );

        // Manually create a paginator for "Baru Kemaskini"
        $kemaskiniPage = $request->get('kemaskini_page', 1); // <-- Use $request->get()
        $ksBaruKemaskini = new LengthAwarePaginator(
            $kemaskiniItems->forPage($kemaskiniPage, $perPage),
            $kemaskiniItems->count(),
            $perPage,
            $kemaskiniPage,
            ['path' => $request->url(), 'pageName' => 'kemaskini_page'] // <-- Use $request->url()
        );
        
        // Pass the new paginator objects to the view
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
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'Trafik', 'OrangHilang', 'LaporanMatiMengejut'])],
        ]);

        try {
            Excel::import(new PaperImport($project->id, $validated['paper_type']), $request->file('excel_file'));
            $friendlyName = Str::headline($validated['paper_type']);
            return back()->with('success', $friendlyName . ' berjaya diimport ke projek ini.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ralat semasa memproses fail: ' . $e->getMessage())->withInput();
        }
    }
    
    public function destroyPaper(Request $request, Project $project, $paperType, $paperId)
    {
        // Use the same validation logic to find the correct model
        $validPaperTypes = ['Jenayah', 'Narkotik', 'Trafik', 'Komersil', 'LaporanMatiMengejut', 'OrangHilang'];
        if (!in_array($paperType, $validPaperTypes)) {
            return redirect()->route('projects.show', $project)->with('error', 'Invalid paper type specified.');
        }

        $paperModelClass = 'App\\Models\\' . $paperType;
        $paper = $paperModelClass::where('id', $paperId)->where('project_id', $project->id)->firstOrFail();

        $paper->delete();

        $friendlyName = Str::headline($paperType);
        return redirect()->route('projects.show', $project)->with('success', $friendlyName . ' paper has been permanently deleted.');
    }

    public function exportPapers(Request $request, Project $project)
    {
        $validated = $request->validate([
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'Trafik', 'OrangHilang', 'LaporanMatiMengejut'])],
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
        // The new route for deleting the paper
        $destroyUrl = route('projects.destroy_paper', ['project' => $row->project_id, 'paperType' => $paperType, 'paperId' => $row->id]);
        
        $showUrl = route('kertas_siasatan.show', ['paperType' => $paperType, 'id' => $row->id]);
        $editUrl = route('kertas_siasatan.edit', ['paperType' => $paperType, 'id' => $row->id]);

        $actions = '<a href="'.$showUrl.'" class="text-indigo-600 hover:text-indigo-900" title="Lihat"><i class="fas fa-eye"></i></a>';
        $actions .= '<a href="'.$editUrl.'" class="text-green-600 hover:text-green-900" title="Audit/Kemaskini"><i class="fas fa-edit"></i></a>';

        $actions .= '<form action="'.$destroyUrl.'" method="POST" class="inline" onsubmit="return confirm(\'Anda pasti ingin memadam kertas ini secara kekal?\')">' .
                    csrf_field() .
                    method_field('DELETE') . 
                    '<button type="submit" class="text-red-600 hover:text-red-900" title="Padam Kekal"><i class="fas fa-trash-alt"></i></button></form>';
        
        return '<div class="flex items-center space-x-2">' . $actions . '</div>';
    }


    // --- DATATABLES SERVER-SIDE METHODS ---
    public function getJenayahData(Project $project) {
        $query = Jenayah::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Jenayah'))->rawColumns(['action'])->make(true);
    }
    public function getNarkotikData(Project $project) {
        $query = Narkotik::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Narkotik'))->rawColumns(['action'])->make(true);
    }
    public function getKomersilData(Project $project) {
        $query = Komersil::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Komersil'))->rawColumns(['action'])->make(true);
    }
    public function getTrafikData(Project $project) { // Consolidated method
        $query = Trafik::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Trafik'))->rawColumns(['action'])->make(true);
    }
    public function getOrangHilangData(Project $project) {
        $query = OrangHilang::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'OrangHilang'))->rawColumns(['action'])->make(true);
    }
    public function getLaporanMatiMengejutData(Project $project) {
        $query = LaporanMatiMengejut::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'LaporanMatiMengejut'))->rawColumns(['action'])->make(true);
    }
}