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
use App\Models\TrafikSeksyen;
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
        
        $validatedData['user_id'] = Auth::id();

        Project::create($validatedData);
        return Redirect::route('projects.index')->with('success', 'Projek berjaya dicipta.');
    }

    public function show(Project $project, Request $request)
    {
        Gate::authorize('access-project', $project);

        $paperTypes = [
            'Jenayah' => $project->jenayah(),
            'Narkotik' => $project->narkotik(),
            'Komersil' => $project->komersil(),
            'TrafikSeksyen' => $project->trafikSeksyen(),
            'OrangHilang' => $project->orangHilang(),
            'LaporanMatiMengejut' => $project->laporanMatiMengejut(),
        ];

        $dashboardData = [];
        $perPage = 10;

        foreach ($paperTypes as $type => $relationship) {
            $allPapers = $relationship->get();

            // Calculate Audit Statistics for this department
            $jumlahKeseluruhan = $allPapers->count();
            $jumlahDiperiksa = $allPapers->filter(fn ($paper) => !empty($paper->pegawai_pemeriksa))->count();
            $jumlahBelumDiperiksa = $jumlahKeseluruhan - $jumlahDiperiksa;

            // Filter for issue lists for this department
            $lewatItems = $allPapers->filter(fn ($paper) => $paper->lewat_edaran_48_jam_status === 'YA, LEWAT')->values();
            $terbengkalaiItems = $allPapers->filter(fn ($paper) => Str::contains($paper->terbengkalai_status, 'TERBENGKALAI'))->values();
            $kemaskiniItems = $allPapers->filter(fn ($paper) => $paper->baru_dikemaskini_status === 'TERBENGKALAI / KS BARU DIKEMASKINI')->values();

            // Create Paginators for this department
            $lewatPage = $request->get($type . '_lewat_page', 1);
            $ksLewat = new LengthAwarePaginator($lewatItems->forPage($lewatPage, $perPage), $lewatItems->count(), $perPage, $lewatPage, ['path' => $request->url(), 'pageName' => $type . '_lewat_page']);
            
            $terbengkalaiPage = $request->get($type . '_terbengkalai_page', 1);
            $ksTerbengkalai = new LengthAwarePaginator($terbengkalaiItems->forPage($terbengkalaiPage, $perPage), $terbengkalaiItems->count(), $perPage, $terbengkalaiPage, ['path' => $request->url(), 'pageName' => $type . '_terbengkalai_page']);

            $kemaskiniPage = $request->get($type . '_kemaskini_page', 1);
            $ksBaruKemaskini = new LengthAwarePaginator($kemaskiniItems->forPage($kemaskiniPage, $perPage), $kemaskiniItems->count(), $perPage, $kemaskiniPage, ['path' => $request->url(), 'pageName' => $type . '_kemaskini_page']);

            // Store all data for this department in the main array
            $dashboardData[$type] = [
                'ksLewat' => $ksLewat,
                'ksTerbengkalai' => $ksTerbengkalai,
                'ksBaruKemaskini' => $ksBaruKemaskini,
                'lewatCount' => $ksLewat->total(),
                'terbengkalaiCount' => $ksTerbengkalai->total(),
                'kemaskiniCount' => $ksBaruKemaskini->total(),
                'jumlahKeseluruhan' => $jumlahKeseluruhan,
                'jumlahDiperiksa' => $jumlahDiperiksa,
                'jumlahBelumDiperiksa' => $jumlahBelumDiperiksa,
            ];
        }

        return view('projects.show', compact('project', 'dashboardData'));
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
        Gate::authorize('access-project', $project);
        $project->delete();
        return Redirect::route('projects.index')->with('success', 'Projek dan semua kertas yang berkaitan telah berjaya dipadam.');
    }

    public function importPapers(Request $request, Project $project)
    {
        Gate::authorize('access-project', $project);
        
        $validated = $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:20480',
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'TrafikSeksyen', 'OrangHilang', 'LaporanMatiMengejut'])],
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
        Gate::authorize('access-project', $project);
        
        $validPaperTypes = ['Jenayah', 'Narkotik', 'TrafikSeksyen', 'Komersil', 'LaporanMatiMengejut', 'OrangHilang'];
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
        Gate::authorize('access-project', $project);

        $validated = $request->validate([
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'TrafikSeksyen', 'OrangHilang', 'LaporanMatiMengejut'])],
        ]);

        $paperType = $validated['paper_type'];
        $modelClass = 'App\\Models\\' . $paperType;
        
        $papers = $modelClass::where('project_id', $project->id)->get();

        if ($papers->isEmpty()) {
            return back()->with('info', 'Tiada data ditemui untuk jenis kertas "' . Str::headline($paperType) . '" dalam projek ini.');
        }

        $fileName = Str::slug($project->name) . '-' . Str::slug($paperType) . '-' . now()->format('Y-m-d') . '.csv';
        
        // Get all database columns and appended accessors
        $modelInstance = new $modelClass;
        $dbColumns = Schema::getColumnListing($modelInstance->getTable());
        $appendedColumns = $modelInstance->getAppends();
        $columns = array_merge($dbColumns, $appendedColumns);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($papers, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel compatibility with UTF-8
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            // Write the headers
            fputcsv($file, $columns);

            // Write the data rows
            foreach ($papers as $paper) {
                $row = [];
                foreach ($columns as $column) {
                    $value = $paper->{$column};

                    // --- THIS IS THE FIX ---
                    // Check if the value is an array (from a JSON column)
                    if (is_array($value)) {
                        // Convert array to a comma-separated string
                        $row[] = implode(', ', $value);
                    } else {
                        // Otherwise, add the value as is
                        $row[] = $value;
                    }
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

    // --- DATATABLES SERVER-SIDE METHODS ---
    public function getJenayahData(Project $project) { /* ... unchanged ... */ }
    public function getNarkotikData(Project $project) { /* ... unchanged ... */ }
    public function getKomersilData(Project $project) { /* ... unchanged ... */ }

    public function getTrafikSeksyenData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = TrafikSeksyen::where('project_id', $project->id);
        return DataTables::of($query)->addIndexColumn()->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikSeksyen'))->rawColumns(['action'])->make(true);
    }

    public function getOrangHilangData(Project $project) { /* ... unchanged ... */ }
    public function getLaporanMatiMengejutData(Project $project) { /* ... unchanged ... */ }
}