<?php

namespace App\Http\Controllers;

// Laravel Core and Helpers
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

// App-specific Models
use App\Models\Project;
use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\Trafik_Seksyen;
use App\Models\Komersil;
use App\Models\LaporanMatiMengejut;
use App\Models\OrangHilang;

// App-specific Imports
use App\Imports\PaperImport;

// Third-party Packages
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ProjectController extends Controller
{
    public function index()
    {
        // Show only projects belonging to the authenticated user.
        $projects = Project::where('user_id', Auth::id())
                           ->orderBy('project_date', 'desc')
                           ->paginate(10);
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
        
        // **FIX APPLIED**: Using Auth::id() for clarity and to satisfy linters.
        $validatedData['user_id'] = Auth::id();

        Project::create($validatedData);
        return Redirect::route('projects.index')->with('success', 'Projek berjaya dicipta.');
    }

    public function show(Project $project, Request $request)
    {
        // **IDOR FIX**: Authorize that the user can access this project.
        Gate::authorize('access-project', $project);

        $allPapers = $project->allPapersMerged();

        $perPage = 10;
        $lewatItems = $allPapers->filter(fn ($paper) => !empty($paper->tarikh_minit_pertama) && isset($paper->edar_lebih_24_jam_status) && Str::contains($paper->edar_lebih_24_jam_status, 'LEWAT'))->values();
        $terbengkalaiItems = $allPapers->filter(fn ($paper) => !empty($paper->tarikh_minit_pertama) && isset($paper->terbengkalai_3_bulan_status) && Str::contains($paper->terbengkalai_3_bulan_status, 'TERBENGKALAI'))->values();
        $kemaskiniItems = $allPapers->filter(fn ($paper) => !empty($paper->tarikh_minit_pertama) && isset($paper->baru_kemaskini_status) && Str::contains($paper->baru_kemaskini_status, 'BARU DIKEMASKINI'))->values();

        $lewatPage = $request->get('lewat_page', 1);
        $ksLewat24Jam = new LengthAwarePaginator(
            $lewatItems->forPage($lewatPage, $perPage), $lewatItems->count(), $perPage, $lewatPage,
            ['path' => $request->url(), 'pageName' => 'lewat_page']
        );

        $terbengkalaiPage = $request->get('terbengkalai_page', 1);
        $ksTerbengkalai = new LengthAwarePaginator(
            $terbengkalaiItems->forPage($terbengkalaiPage, $perPage), $terbengkalaiItems->count(), $perPage, $terbengkalaiPage,
            ['path' => $request->url(), 'pageName' => 'terbengkalai_page']
        );

        $kemaskiniPage = $request->get('kemaskini_page', 1);
        $ksBaruKemaskini = new LengthAwarePaginator(
            $kemaskiniItems->forPage($kemaskiniPage, $perPage), $kemaskiniItems->count(), $perPage, $kemaskiniPage,
            ['path' => $request->url(), 'pageName' => 'kemaskini_page']
        );
        
          $lewatCount = $ksLewat24Jam->total();
        $terbengkalaiCount = $ksTerbengkalai->total();
        $kemaskiniCount = $ksBaruKemaskini->total();

        return view('projects.show', compact('project', 'ksLewat24Jam', 'ksTerbengkalai',  'ksBaruKemaskini',
    'lewatCount',
    'terbengkalaiCount',
    'kemaskiniCount'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('access-project', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('access-project', $project);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'project_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        $project->update($validatedData);
        return Redirect::route('projects.show', $project)->with('success', 'Projek berjaya dikemaskini.');
    }

    public function destroy(Project $project)
    {
        // 1. Authorize that the current user can delete this project.
        Gate::authorize('access-project', $project);

        // 2. Delete the project. 
        // The database will automatically handle deleting all associated papers
        // because of the onDelete('cascade') constraint on your paper migrations.
        $project->delete();
        
        // 3. Redirect with a success message.
        return Redirect::route('projects.index')->with('success', 'Projek dan semua kertas yang berkaitan telah berjaya dipadam.');
    }

public function importPapers(Request $request, Project $project)
{
    Gate::authorize('access-project', $project);
    
    $validated = $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv|max:20480',
        'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'Trafik_Seksyen', 'OrangHilang', 'LaporanMatiMengejut'])],
    ]);

    $import = new PaperImport($project->id, Auth::id(), $validated['paper_type']);

    try {
        Excel::import($import, $request->file('excel_file'));

        $successCount = $import->getSuccessCount();
        $skippedRows = $import->getSkippedRows();
        $friendlyName = Str::headline($validated['paper_type']);
        
        $feedback = "Import Selesai. {$successCount} rekod {$friendlyName} baharu berjaya diimport.";
        
        if (!empty($skippedRows)) {
            return back()
                ->with('success', $feedback)
                ->withErrors([
                    'excel_file' => 'Beberapa rekod telah dilangkau:',
                    'excel_errors' => $skippedRows
                ]);
        }

        return back()->with('success', $feedback);

    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return back()->with('error', 'Ralat tidak dijangka semasa memproses fail: ' . $e->getMessage())->withInput();
    }
}
    
    public function destroyPaper(Request $request, Project $project, $paperType, $paperId)
    {
        // **IDOR FIX**: Authorize that the user can access the parent project.
        Gate::authorize('access-project', $project);
        
        $validPaperTypes = ['Jenayah', 'Narkotik', 'Trafik_Seksyen', 'Komersil', 'LaporanMatiMengejut', 'OrangHilang'];
        if (!in_array($paperType, $validPaperTypes)) {
            return redirect()->route('projects.show', $project)->with('error', 'Jenis kertas yang dinyatakan tidak sah.');
        }

        $paperModelClass = 'App\\Models\\' . $paperType;
        $paper = $paperModelClass::where('id', $paperId)->where('project_id', $project->id)->firstOrFail();

        $paper->delete();

        $friendlyName = Str::headline($paperType);
        return redirect()->route('projects.show', $project)->with('success', $friendlyName . ' telah berjaya dipadam secara kekal.');
    }

    public function exportPapers(Request $request, Project $project)
    {
        // **IDOR FIX**: Authorize that the user can access this project.
        Gate::authorize('access-project', $project);

        $validated = $request->validate([
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'Trafik_Seksyen', 'OrangHilang', 'LaporanMatiMengejut'])],
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

    // --- DATATABLES SERVER-SIDE METHODS - AUTHORIZATION ADDED ---
    public function getJenayahData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = Jenayah::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Jenayah'))->rawColumns(['action'])->make(true);
    }
    public function getNarkotikData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = Narkotik::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Narkotik'))->rawColumns(['action'])->make(true);
    }
    public function getKomersilData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = Komersil::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Komersil'))->rawColumns(['action'])->make(true);
    }
    public function getTrafikSeksyenData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = Trafik_Seksyen::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Trafik_Seksyen'))->rawColumns(['action'])->make(true);
    }
    public function getOrangHilangData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = OrangHilang::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'OrangHilang'))->rawColumns(['action'])->make(true);
    }
    public function getLaporanMatiMengejutData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = LaporanMatiMengejut::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'LaporanMatiMengejut'))->rawColumns(['action'])->make(true);
    }
}