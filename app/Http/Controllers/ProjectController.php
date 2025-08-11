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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

// App-specific Models
use App\Models\Project;
use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\TrafikSeksyen;
use App\Models\TrafikRule;
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
        if (Auth::user()->superadmin == 'yes') {
            // Superadmins can see all projects
            $projects = Project::orderBy('updated_at', 'desc')->paginate(10);
        } else {
            // Regular users: show projects based on visible_projects setting
            $user = Auth::user();
            
            if (is_null($user->visible_projects)) {
                // User can see all projects (legacy behavior)
                $projects = Project::orderBy('updated_at', 'desc')->paginate(10);
            } else {
                // User can only see specific projects + their own projects
                $visibleProjectIds = array_unique(array_merge(
                    $user->visible_projects, // Projects explicitly made visible to them
                    $user->projects()->pluck('id')->toArray() // Projects they own
                ));
                
                $projects = Project::whereIn('id', $visibleProjectIds)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(10);
            }
        }

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
            'TrafikRule' => $project->trafikRule(),
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
            // Access the properties directly. Laravel's accessors will handle them.
            $lewatItems = $allPapers->filter(fn ($paper) => $paper->lewat_edaran_status === 'LEWAT')->values();
            
            // Handle terbengkalai status differently for TrafikRule
            $terbengkalaiItems = $allPapers->filter(function ($paper) {
                return $paper->terbengkalai_status_dc === 'TERBENGKALAI MELEBIHI 3 BULAN' || 
                    $paper->terbengkalai_status_da === 'TERBENGKALAI MELEBIHI 3 BULAN';
            })->values();
            
            $kemaskiniItems = $allPapers->filter(
                fn ($paper) => $paper->baru_dikemaskini_status === 'TERBENGKALAI / KS BARU DIKEMASKINI'
            )->values();

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
            'excel_file' => 'required_without:temp_file_path|file|mimes:xlsx,xls,csv|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/csv,application/octet-stream,text/x-csv,application/x-csv,text/comma-separated-values|max:512000',
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'TrafikSeksyen', 'TrafikRule', 'OrangHilang', 'LaporanMatiMengejut'])],
            'confirm_overwrite' => 'sometimes|boolean',
            'temp_file_path' => 'sometimes|string',
        ], [
            'excel_file.required_without' => 'Fail Excel adalah wajib.',
            'excel_file.file' => 'Medan fail Excel mestilah fail yang sah.',
            'excel_file.mimes' => 'Fail Excel mestilah jenis fail: xlsx, xls, csv.',
            'excel_file.mimetypes' => 'Fail Excel mestilah jenis fail: xlsx, xls, csv.',
            'excel_file.max' => 'Fail Excel tidak boleh melebihi 500MB.',
            'paper_type.required' => 'Kategori kertas adalah wajib.',
            'paper_type.in' => 'Kategori kertas yang dipilih tidak sah.',
        ]);

        // Check if user confirmed overwrite
        $confirmOverwrite = $request->boolean('confirm_overwrite', false);
        
        // First pass: detect duplicates if not confirming overwrite
        if (!$confirmOverwrite && !$request->has('temp_file_path')) {
            $detectImport = new PaperImport($project->id, Auth::id(), $validated['paper_type'], 'detect');
            
            try {
                Excel::import($detectImport, $request->file('excel_file'));
                $duplicateRecords = $detectImport->getDuplicateRecords();
                $newRecordsCount = $detectImport->getNewRecordsCount();
                $totalRecords = $newRecordsCount + count($duplicateRecords);
                
                // If duplicates found, return them for confirmation modal
                if (!empty($duplicateRecords)) {
                    // Store the file temporarily for the second pass
                    $file = $request->file('excel_file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    
                    // Use a more direct approach
                    $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
                    if (!is_dir($tempDir)) {
                        mkdir($tempDir, 0755, true);
                    }
                    
                    $fullPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
                    $file->move($tempDir, $fileName);
                    
                    // Verify file was stored successfully
                    if (!file_exists($fullPath)) {
                        return back()->with('error', 'Gagal menyimpan fail sementara. Sila cuba lagi.');
                    }
                    
                    Log::info('Temporary file stored at: ' . $fullPath);
                    
                    return back()->with([
                        'duplicates_found' => true,
                        'duplicate_records' => $duplicateRecords,
                        'new_records_count' => $newRecordsCount,
                        'total_records_count' => $totalRecords,
                        'temp_file_path' => 'temp' . DIRECTORY_SEPARATOR . $fileName,
                        'paper_type' => $validated['paper_type'],
                        'project_id' => $project->id,
                    ]);
                }
                
                // No duplicates, proceed with normal import
                $import = new PaperImport($project->id, Auth::id(), $validated['paper_type'], 'update');
                Excel::import($import, $request->file('excel_file'));
                
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                return back()->with('error', 'Ralat tidak dijangka semasa memproses fail: ' . $e->getMessage())->withInput();
            }
        } else {
            // Handle both overwrite confirmation and cancel scenarios
            $tempFilePath = $request->input('temp_file_path');
            
            Log::info('Looking for temp file path: ' . $tempFilePath);
            
            if (!$tempFilePath) {
                return back()->with('error', 'Fail sementara tidak dijumpai. Sila cuba lagi.')->withInput();
            }
            
            $tempFile = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $tempFilePath));
            Log::info('Looking for temp file at: ' . $tempFile);
            Log::info('File exists: ' . (file_exists($tempFile) ? 'YES' : 'NO'));
            
            if (!file_exists($tempFile)) {
                return back()->with('error', 'Fail sementara tidak dijumpai. Sila cuba lagi.')->withInput();
            }
            
            // If confirm_overwrite is false (cancel button), just clean up and return
            if (!$confirmOverwrite) {
                unlink($tempFile);
                return back()->with('info', 'Import dibatalkan.');
            }
            
            // User confirmed overwrite, proceed with update
            $import = new PaperImport($project->id, Auth::id(), $validated['paper_type'], 'update');
            
            try {
                Excel::import($import, $tempFile);
                // Clean up temp file
                unlink($tempFile);
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                return back()->with('error', 'Ralat tidak dijangka semasa memproses fail: ' . $e->getMessage())->withInput();
            }
        }

        // Process results and show feedback
        $createdCount = $import->getCreatedCount();
        $updatedCount = $import->getUpdatedCount();
        $skippedCount = $import->getSkippedCount();
        $updatedRecords = $import->getUpdatedRecords();
        $skippedRows = $import->getSkippedRows();
        $friendlyName = Str::headline($validated['paper_type']);
        
        // Build detailed feedback message
        $feedback = "Import Selesai. ";
        
        if ($createdCount > 0) {
            $feedback .= "{$createdCount} rekod {$friendlyName} baharu berjaya dicipta. ";
        }
        
        if ($updatedCount > 0) {
            $feedback .= "{$updatedCount} rekod {$friendlyName} berjaya dikemaskini. ";
        }
        
        if ($confirmOverwrite && $updatedCount > 0) {
            $feedback .= "Data sedia ada telah ditimpa. ";
        }

        return back()->with('success', $feedback);
    }
        
        public function getPapersForDestroy(Project $project)
    {
        // First, ensure the authenticated user is authorized to access this project
        Gate::authorize('access-project', $project);

        $paperTypes = [
            'Jenayah' => $project->jenayah()->count(),
            'Narkotik' => $project->narkotik()->count(),
            'Komersil' => $project->komersil()->count(),
            'TrafikSeksyen' => $project->trafikSeksyen()->count(),
            'TrafikRule' => $project->trafikRule()->count(),
            'OrangHilang' => $project->orangHilang()->count(),
            'LaporanMatiMengejut' => $project->laporanMatiMengejut()->count(),
        ];

        // Filter out paper types with zero count
        $paperTypes = array_filter($paperTypes, function($count) {
            return $count > 0;
        });

        return response()->json($paperTypes);
    }
        
        public function destroyAllPapers(Project $project)
    {
        // First, ensure the authenticated user is authorized to access this project
        Gate::authorize('access-project', $project);

        // Efficiently delete all related papers for each type
        // This runs a DELETE query for each relationship without loading models into memory
        $project->jenayah()->delete();
        $project->narkotik()->delete();
        $project->komersil()->delete();
        $project->trafikSeksyen()->delete();
        $project->trafikRule()->delete();
        $project->orangHilang()->delete();
        $project->laporanMatiMengejut()->delete();

        // Redirect back to the project page with a success message
        return Redirect::route('projects.show', $project)
            ->with('success', 'Semua kertas siasatan dalam projek ini telah berjaya dipadam.');
    }

    public function destroySelectedPapers(Request $request, Project $project)
    {
        // First, ensure the authenticated user is authorized to access this project
        Gate::authorize('access-project', $project);

        $selectedTypes = $request->input('selected_types', []);
        $deletedCount = 0;

        foreach ($selectedTypes as $type) {
            switch ($type) {
                case 'Jenayah':
                    $count = $project->jenayah()->count();
                    $project->jenayah()->delete();
                    $deletedCount += $count;
                    break;
                case 'Narkotik':
                    $count = $project->narkotik()->count();
                    $project->narkotik()->delete();
                    $deletedCount += $count;
                    break;
                case 'Komersil':
                    $count = $project->komersil()->count();
                    $project->komersil()->delete();
                    $deletedCount += $count;
                    break;
                case 'TrafikSeksyen':
                    $count = $project->trafikSeksyen()->count();
                    $project->trafikSeksyen()->delete();
                    $deletedCount += $count;
                    break;
                case 'TrafikRule':
                    $count = $project->trafikRule()->count();
                    $project->trafikRule()->delete();
                    $deletedCount += $count;
                    break;
                case 'OrangHilang':
                    $count = $project->orangHilang()->count();
                    $project->orangHilang()->delete();
                    $deletedCount += $count;
                    break;
                case 'LaporanMatiMengejut':
                    $count = $project->laporanMatiMengejut()->count();
                    $project->laporanMatiMengejut()->delete();
                    $deletedCount += $count;
                    break;
            }
        }

        // Redirect back to the project page with a success message
        return Redirect::route('projects.show', $project)
            ->with('success', "{$deletedCount} kertas siasatan telah berjaya dipadam.");
    }
    public function destroyPaper(Request $request, Project $project, $paperType, $paperId)
    {
        Gate::authorize('access-project', $project);
        
        $validPaperTypes = ['Jenayah', 'Narkotik', 'TrafikSeksyen','TrafikRule', 'Komersil', 'LaporanMatiMengejut', 'OrangHilang'];
        if (!in_array($paperType, $validPaperTypes)) {
            return redirect()->route('projects.show', $project)->with('error', 'Jenis kertas yang dinyatakan tidak sah.');
        }

        $paperModelClass = 'App\\Models\\' . $paperType;
        $paper = $paperModelClass::where('id', $paperId)->where('project_id', $project->id)->firstOrFail();

        $paper->delete();

        $friendlyName = Str::headline($paperType);
        return redirect()->route('projects.show', $project)->with('success', $friendlyName . ' telah berjaya dipadam secara kekal.');
    }

// FILE: app/Http/Controllers/ProjectController.php

public function exportPapers(Request $request, Project $project)
{
    Gate::authorize('access-project', $project);

    $validated = $request->validate([
        'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'TrafikSeksyen', 'TrafikRule', 'OrangHilang', 'LaporanMatiMengejut'])],
    ]);

    $paperType = $validated['paper_type'];
    $modelClass = 'App\\Models\\' . $paperType;
    
    $papers = $modelClass::where('project_id', $project->id)->get();

    if ($papers->isEmpty()) {
        return back()->with('info', 'Tiada data ditemui untuk jenis kertas "' . Str::headline($paperType) . '" dalam projek ini.');
    }

    $fileName = Str::slug($project->name) . '-' . Str::slug($paperType) . '-' . now()->format('Y-m-d') . '.csv';

    // --- FINAL UNIFIED LOGIC WITH MANUAL ORDERING ---

    // 1. Get all available attributes from the model's database table.
    $modelInstance = new $modelClass;
    $dbColumns = Schema::getColumnListing($modelInstance->getTable());

    // 2. Define the Bahagian 1 columns that need their headers changed to `_b1`.
    $bahagian1DbColumns = [ 'no_kertas_siasatan', 'no_repot_polis', 'pegawai_penyiasat', 'tarikh_laporan_polis_dibuka', 'seksyen', 'no_lmm_t', 'no_fail_lmm_t', 'no_fail_lmm_sdr' ];

    // 3. Define the timestamp columns that must go at the end.
    $timestampColumns = ['created_at', 'updated_at'];
    
    // 4. Create the final, ordered list of database columns for the export.
    // Start with all columns, then remove the timestamps to re-add them at the end.
    $orderedColumns = array_diff($dbColumns, $timestampColumns);

    // Re-add the timestamps to the end of the list to enforce order.
    $orderedColumns = array_merge($orderedColumns, $timestampColumns);
    
    // Also add any "appended" attributes from the model to the very end.
    $orderedColumns = array_merge($orderedColumns, $modelInstance->getAppends());

    // 5. Build the final mapping of CSV_Header => db_column based on our controlled order.
    $finalColumnMap = [];
    $finalColumnMap['no'] = 'no'; // Add sequence number first.

    foreach ($orderedColumns as $dbColumn) {
        if (in_array($dbColumn, $bahagian1DbColumns)) {
            // For Bahagian 1, create the '_b1' header.
            $csvHeader = $dbColumn . '_b1';
            $finalColumnMap[$csvHeader] = $dbColumn;
        } else {
            // For all others, the header and DB column name are the same.
            $finalColumnMap[$dbColumn] = $dbColumn;
        }
    }

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];
    
    // The callback function remains the same and will use the correctly ordered map.
    $callback = function() use ($papers, $finalColumnMap) {
        $file = fopen('php://output', 'w');
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($file, array_keys($finalColumnMap));

        $rowNumber = 1;
        foreach ($papers as $paper) {
            $row = [];
            foreach ($finalColumnMap as $header => $dbColumn) {
                if ($dbColumn === 'no') {
                    $row[] = $rowNumber;
                    continue;
                }
                $value = $paper->{$dbColumn};
                if (is_array($value)) {
                    $row[] = implode(', ', $value);
                } elseif (is_bool($value)) {
                    $row[] = $value ? 'Ya' : 'Tidak';
                } elseif (in_array($dbColumn, ['status_rj2', 'status_rj2b', 'status_rj9', 'status_rj99', 'status_rj10a', 'status_rj10b'])) {
                    $textAttribute = $dbColumn . '_text';
                    $row[] = $paper->{$textAttribute} ?? $value;
                } elseif (is_null($value)) {
                    $row[] = '';
                } else {
                    $row[] = $value;
                }
            }
            fputcsv($file, $row);
            $rowNumber++;
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

// FILE: app/Http/Controllers/ProjectController.php

public function getJenayahData(Project $project)
{
    Gate::authorize('access-project', $project);
    $query = Jenayah::where('project_id', $project->id);

        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Jenayah'))

        // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
        ->addColumn('lewat_edaran_status', function($row) {
            return $row->lewat_edaran_status;
        })
        ->addColumn('terbengkalai_status_dc', function($row) {
            return $row->terbengkalai_status_dc;
        })
        ->addColumn('terbengkalai_status_da', function($row) {
            return $row->terbengkalai_status_da;
        })
        ->addColumn('baru_dikemaskini_status', function($row) {
            return $row->baru_dikemaskini_status;
        })

        ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')

        ->editColumn('updated_at', function ($row) {
            return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
        })
        ->editColumn('created_at', function ($row) {
            return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
        })

        // --- BAHAGIAN 1: Maklumat Asas ---
        ->editColumn('tarikh_laporan_polis_dibuka', fn($r) => optional($r->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-')

        // --- BAHAGIAN 2: Pemeriksaan & Status ---
        ->editColumn('tarikh_edaran_minit_ks_pertama', fn($r) => optional($r->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_kedua', fn($r) => optional($r->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_sebelum_akhir', fn($r) => optional($r->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_akhir', fn($r) => optional($r->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_semboyan_pemeriksaan_jips_ke_daerah', fn($r) => optional($r->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-')

        // --- BAHAGIAN 3: Arahan & Keputusan ---
        ->editColumn('arahan_minit_oleh_sio_status', fn($row) => $this->formatBoolean($row->arahan_minit_oleh_sio_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_oleh_sio_tarikh', fn($r) => optional($r->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_ketua_bahagian_status', fn($row) => $this->formatBoolean($row->arahan_minit_ketua_bahagian_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_ketua_bahagian_tarikh', fn($r) => optional($r->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_ketua_jabatan_status', fn($row) => $this->formatBoolean($row->arahan_minit_ketua_jabatan_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_ketua_jabatan_tarikh', fn($r) => optional($r->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_oleh_ya_tpr_status', fn($row) => $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', fn($r) => optional($r->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') ?? '-')

        // --- BAHAGIAN 4: Barang Kes ---
        ->editColumn('adakah_barang_kes_didaftarkan', fn($row) => $this->formatBoolean($row->adakah_barang_kes_didaftarkan))
        ->editColumn('status_pergerakan_barang_kes', function ($row) {
            if ($row->status_pergerakan_barang_kes === 'Lain-Lain' && !empty($row->status_pergerakan_barang_kes_lain)) {
                return 'Lain-lain: ' . htmlspecialchars($row->status_pergerakan_barang_kes_lain);
            }
            return htmlspecialchars($row->status_pergerakan_barang_kes ?? '-');
        })
        ->editColumn('status_barang_kes_selesai_siasatan', function ($row) {
            if ($row->status_barang_kes_selesai_siasatan === 'Lain-Lain' && !empty($row->status_barang_kes_selesai_siasatan_lain)) {
                return 'Lain-lain: ' . htmlspecialchars($row->status_barang_kes_selesai_siasatan_lain);
            }
            return htmlspecialchars($row->status_barang_kes_selesai_siasatan ?? '-');
        })
        ->editColumn('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', function ($row) {
            if ($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan === 'Lain-Lain' && !empty($row->kaedah_pelupusan_barang_kes_lain)) {
                return 'Lain-lain: ' . htmlspecialchars($row->kaedah_pelupusan_barang_kes_lain);
            }
            return htmlspecialchars($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan ?? '-');
        })
        ->editColumn('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', function ($row) {
            return htmlspecialchars($row->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan ?? '-');
        })
        ->editColumn('resit_kew38e_pelupusan_wang_tunai', function ($row) {
            return htmlspecialchars($row->resit_kew38e_pelupusan_wang_tunai ?? '-');
        })
        ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan', function ($row) {
            return htmlspecialchars($row->adakah_borang_serah_terima_pegawai_tangkapan ?? '-');
        })
        ->editColumn('adakah_borang_serah_terima_pemilik_saksi', fn($row) => $this->formatThreeStateString($row->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan'))
        ->editColumn('adakah_sijil_surat_kebenaran_ipo', fn($row) => $this->formatThreeStateString($row->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan'))
        ->editColumn('adakah_gambar_pelupusan', fn($row) => $this->formatThreeStateString($row->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan'))

        // --- BAHAGIAN 5: Dokumen Siasatan ---
        ->editColumn('status_id_siasatan_dikemaskini', fn($row) => $this->formatBoolean($row->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak'))
        ->editColumn('status_rajah_kasar_tempat_kejadian', fn($row) => $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_tempat_kejadian', fn($row) => $this->formatBoolean($row->status_gambar_tempat_kejadian, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_post_mortem_mayat_di_hospital', fn($row) => $this->formatBoolean($row->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_am', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_am, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_berharga', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_berharga, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_kenderaan', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_darah', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_darah, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_kontraban', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada'))

        // --- BAHAGIAN 6: Borang & Semakan ---
        ->editColumn('status_pem', fn($row) => $this->formatArrayField($row->status_pem))
        ->editColumn('status_rj2', fn($row) => $this->formatThreeState($row->status_rj2, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_rj2', fn($r) => optional($r->tarikh_rj2)->format('d/m/Y') ?? '-')
        ->editColumn('status_rj2b', fn($row) => $this->formatThreeState($row->status_rj2b, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_rj2b', fn($r) => optional($r->tarikh_rj2b)->format('d/m/Y') ?? '-')
        ->editColumn('status_rj9', fn($row) => $this->formatThreeState($row->status_rj9, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_rj9', fn($r) => optional($r->tarikh_rj9)->format('d/m/Y') ?? '-')
        ->editColumn('status_rj99', fn($row) => $this->formatThreeState($row->status_rj99, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_rj99', fn($r) => optional($r->tarikh_rj99)->format('d/m/Y') ?? '-')
        ->editColumn('status_rj10a', fn($row) => $this->formatThreeState($row->status_rj10a, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_rj10a', fn($r) => optional($r->tarikh_rj10a)->format('d/m/Y') ?? '-')
        ->editColumn('status_rj10b', fn($row) => $this->formatThreeState($row->status_rj10b, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_rj10b', fn($r) => optional($r->tarikh_rj10b)->format('d/m/Y') ?? '-')
        ->editColumn('status_semboyan_pertama_wanted_person', fn($row) => $this->formatThreeStateString($row->status_semboyan_pertama_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_semboyan_pertama_wanted_person', fn($r) => optional($r->tarikh_semboyan_pertama_wanted_person)->format('d/m/Y') ?? '-')
        ->editColumn('status_semboyan_kedua_wanted_person', fn($row) => $this->formatThreeStateString($row->status_semboyan_kedua_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_semboyan_kedua_wanted_person', fn($r) => optional($r->tarikh_semboyan_kedua_wanted_person)->format('d/m/Y') ?? '-')
        ->editColumn('status_semboyan_ketiga_wanted_person', fn($row) => $this->formatThreeStateString($row->status_semboyan_ketiga_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('tarikh_semboyan_ketiga_wanted_person', fn($r) => optional($r->tarikh_semboyan_ketiga_wanted_person)->format('d/m/Y') ?? '-')
        ->editColumn('status_penandaan_kelas_warna', fn($row) => $this->formatBoolean($row->status_penandaan_kelas_warna))

        // --- BAHAGIAN 7: Agensi Luar ---
        ->editColumn('status_permohonan_laporan_pakar_judi', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_pakar_judi, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_pakar_judi', fn($r) => optional($r->tarikh_permohonan_laporan_pakar_judi)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_pakar_judi', fn($row) => $this->formatPakarJudiLaporan($row->status_laporan_penuh_pakar_judi))
        ->editColumn('tarikh_laporan_penuh_pakar_judi', fn($r) => optional($r->tarikh_laporan_penuh_pakar_judi)->format('d/m/Y') ?? '-')
        ->editColumn('keputusan_laporan_pakar_judi', fn($row) => $row->keputusan_laporan_pakar_judi ?? '-')
        ->editColumn('status_permohonan_laporan_post_mortem_mayat', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_post_mortem_mayat', fn($r) => optional($r->tarikh_permohonan_laporan_post_mortem_mayat)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_bedah_siasat', fn($row) => $this->formatBoolean($row->status_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_bedah_siasat', fn($r) => optional($r->tarikh_laporan_penuh_bedah_siasat)->format('d/m/Y') ?? '-')
        ->editColumn('status_permohonan_laporan_jabatan_kimia', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_jabatan_kimia', fn($r) => optional($r->tarikh_permohonan_laporan_jabatan_kimia)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_jabatan_kimia', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_jabatan_kimia', fn($r) => optional($r->tarikh_laporan_penuh_jabatan_kimia)->format('d/m/Y') ?? '-')
        ->editColumn('status_permohonan_laporan_jabatan_patalogi', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_jabatan_patalogi', fn($r) => optional($r->tarikh_permohonan_laporan_jabatan_patalogi)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_jabatan_patalogi', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_jabatan_patalogi', fn($r) => optional($r->tarikh_laporan_penuh_jabatan_patalogi)->format('d/m/Y') ?? '-')
        ->editColumn('status_permohonan_laporan_puspakom', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_puspakom, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_puspakom', fn($r) => optional($r->tarikh_permohonan_laporan_puspakom)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_puspakom', fn($row) => $this->formatBoolean($row->status_laporan_penuh_puspakom, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_puspakom', fn($r) => optional($r->tarikh_laporan_penuh_puspakom)->format('d/m/Y') ?? '-')
        ->editColumn('status_permohonan_laporan_jpj', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jpj, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_jpj', fn($r) => optional($r->tarikh_permohonan_laporan_jpj)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_jpj', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jpj, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_jpj', fn($r) => optional($r->tarikh_laporan_penuh_jpj)->format('d/m/Y') ?? '-')
        ->editColumn('permohonan_laporan_pengesahan_masuk_keluar_malaysia', fn($row) => $this->formatBoolean($row->permohonan_laporan_pengesahan_masuk_keluar_malaysia, 'Ada', 'Tiada'))
        ->editColumn('tarikh_permohonan_laporan_imigresen', fn($r) => optional($r->tarikh_permohonan_laporan_imigresen)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_imigresen', fn($row) => $this->formatBoolean($row->status_laporan_penuh_imigresen, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_imigresen', fn($r) => optional($r->tarikh_laporan_penuh_imigresen)->format('d/m/Y') ?? '-')
        ->editColumn('status_permohonan_laporan_kastam', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_kastam, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_kastam', fn($r) => optional($r->tarikh_permohonan_laporan_kastam)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_kastam', fn($row) => $this->formatBoolean($row->status_laporan_penuh_kastam, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_kastam', fn($r) => optional($r->tarikh_laporan_penuh_kastam)->format('d/m/Y') ?? '-')
        ->editColumn('status_permohonan_laporan_forensik_pdrm', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_forensik_pdrm, 'Dibuat', 'Tidak'))
        ->editColumn('tarikh_permohonan_laporan_forensik_pdrm', fn($r) => optional($r->tarikh_permohonan_laporan_forensik_pdrm)->format('d/m/Y') ?? '-')
        ->editColumn('status_laporan_penuh_forensik_pdrm', fn($row) => $this->formatBoolean($row->status_laporan_penuh_forensik_pdrm, 'Diterima', 'Tidak'))
        ->editColumn('tarikh_laporan_penuh_forensik_pdrm', fn($r) => optional($r->tarikh_laporan_penuh_forensik_pdrm)->format('d/m/Y') ?? '-')
        // keputusan_laporan_forensik_pdrm and jenis_ujian_analisis_forensik will be handled by frontend JSON processing

        // --- BAHAGIAN 8: Status Fail ---
        ->editColumn('muka_surat_4_barang_kes_ditulis', fn($row) => $this->formatBoolean($row->muka_surat_4_barang_kes_ditulis))
        ->editColumn('muka_surat_4_dengan_arahan_tpr', fn($row) => $this->formatBoolean($row->muka_surat_4_dengan_arahan_tpr))
        ->editColumn('muka_surat_4_keputusan_kes_dicatat', fn($row) => $this->formatBoolean($row->muka_surat_4_keputusan_kes_dicatat))
        ->editColumn('fail_lmm_ada_keputusan_koroner', fn($row) => $this->formatBoolean($row->fail_lmm_ada_keputusan_koroner))
        ->editColumn('status_kus_fail', fn($row) => $this->formatBoolean($row->status_kus_fail))

        ->rawColumns([
            'action', 'status_pem',
            'arahan_minit_oleh_sio_status', 'arahan_minit_ketua_bahagian_status', 'arahan_minit_ketua_jabatan_status',
            'arahan_minit_oleh_ya_tpr_status', 'adakah_barang_kes_didaftarkan', 'adakah_borang_serah_terima_pemilik_saksi',
            'adakah_sijil_surat_kebenaran_ipo', 'adakah_gambar_pelupusan', 'status_id_siasatan_dikemaskini',
            'status_rajah_kasar_tempat_kejadian', 'status_gambar_tempat_kejadian', 'status_gambar_post_mortem_mayat_di_hospital',
            'status_gambar_barang_kes_am', 'status_gambar_barang_kes_berharga', 'status_gambar_barang_kes_kenderaan',
            'status_gambar_barang_kes_darah', 'status_gambar_barang_kes_kontraban', 'status_rj2', 'status_rj2b', 'status_rj9',
            'status_rj99', 'status_rj10a', 'status_rj10b', 'status_semboyan_pertama_wanted_person', 'status_semboyan_kedua_wanted_person',
            'status_semboyan_ketiga_wanted_person', 'status_penandaan_kelas_warna', 'status_permohonan_laporan_pakar_judi',
            'status_laporan_penuh_pakar_judi', 'keputusan_laporan_pakar_judi', 'status_permohonan_laporan_post_mortem_mayat', 'status_laporan_penuh_bedah_siasat',
            'status_permohonan_laporan_jabatan_kimia', 'status_laporan_penuh_jabatan_kimia', 'status_permohonan_laporan_jabatan_patalogi',
            'status_laporan_penuh_jabatan_patalogi', 'status_permohonan_laporan_puspakom', 'status_laporan_penuh_puspakom',
            'status_permohonan_laporan_jpj', 'status_laporan_penuh_jpj', 'permohonan_laporan_pengesahan_masuk_keluar_malaysia',
            'status_laporan_penuh_imigresen', 'status_permohonan_laporan_kastam', 'status_laporan_penuh_kastam',
            'status_permohonan_laporan_forensik_pdrm', 'status_laporan_penuh_forensik_pdrm', 'muka_surat_4_barang_kes_ditulis',
            'muka_surat_4_dengan_arahan_tpr', 'muka_surat_4_keputusan_kes_dicatat', 'fail_lmm_ada_keputusan_koroner', 'status_kus_fail'
        ])
        ->make(true);
}

// FILE: app/Http/Controllers/ProjectController.php

public function getKomersilData(Project $project)
{
    Gate::authorize('access-project', $project);
    $query = Komersil::where('project_id', $project->id);

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Komersil'))

        // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
        ->addColumn('lewat_edaran_status', function($row) {
            return $row->lewat_edaran_status;
        })
        ->addColumn('terbengkalai_status_dc', function($row) {
            return $row->terbengkalai_status_dc;
        })
        ->addColumn('terbengkalai_status_da', function($row) {
            return $row->terbengkalai_status_da;
        })
        ->addColumn('baru_dikemaskini_status', function($row) {
            return $row->baru_dikemaskini_status;
        })

        ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')

        ->editColumn('updated_at', function ($row) {
            return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
        })

        ->editColumn('created_at', function ($row) {
            return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
        })

        // --- EXPLICIT DATE FORMATTING for all Komersil date columns ---
        ->editColumn('tarikh_laporan_polis_dibuka', fn($r) => optional($r->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_pertama', fn($r) => optional($r->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_kedua', fn($r) => optional($r->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_sebelum_akhir', fn($r) => optional($r->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_akhir', fn($r) => optional($r->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_semboyan_pemeriksaan_jips_ke_daerah', fn($r) => optional($r->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_oleh_sio_tarikh', fn($r) => optional($r->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_ketua_bahagian_tarikh', fn($r) => optional($r->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_ketua_jabatan_tarikh', fn($r) => optional($r->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', fn($r) => optional($r->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj2', fn($r) => optional($r->tarikh_rj2)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj2b', fn($r) => optional($r->tarikh_rj2b)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj9', fn($r) => optional($r->tarikh_rj9)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj99', fn($r) => optional($r->tarikh_rj99)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj10a', fn($r) => optional($r->tarikh_rj10a)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj10b', fn($r) => optional($r->tarikh_rj10b)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_semboyan_pertama_wanted_person', fn($r) => optional($r->tarikh_semboyan_pertama_wanted_person)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_semboyan_kedua_wanted_person', fn($r) => optional($r->tarikh_semboyan_kedua_wanted_person)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_semboyan_ketiga_wanted_person', fn($r) => optional($r->tarikh_semboyan_ketiga_wanted_person)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_post_mortem_mayat', fn($r) => optional($r->tarikh_permohonan_laporan_post_mortem_mayat)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO', fn($r) => optional($r->tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_puspakom', fn($r) => optional($r->tarikh_permohonan_laporan_puspakom)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_puspakom', fn($r) => optional($r->tarikh_laporan_penuh_puspakom)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_jkr', fn($r) => optional($r->tarikh_permohonan_laporan_jkr)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_jkr', fn($r) => optional($r->tarikh_laporan_penuh_jkr)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_jpj', fn($r) => optional($r->tarikh_permohonan_laporan_jpj)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_jpj', fn($r) => optional($r->tarikh_laporan_penuh_jpj)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_imigresen', fn($r) => optional($r->tarikh_permohonan_laporan_imigresen)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_imigresen', fn($r) => optional($r->tarikh_laporan_penuh_imigresen)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_kastam', fn($r) => optional($r->tarikh_permohonan_laporan_kastam)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_kastam', fn($r) => optional($r->tarikh_laporan_penuh_kastam)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_forensik_pdrm', fn($r) => optional($r->tarikh_permohonan_laporan_forensik_pdrm)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_forensik_pdrm', fn($r) => optional($r->tarikh_laporan_penuh_forensik_pdrm)->format('d/m/Y') ?? '-')

        // --- BOOLEAN FORMATTING ---
        ->editColumn('arahan_minit_oleh_sio_status', fn($row) => $this->formatBoolean($row->arahan_minit_oleh_sio_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_ketua_bahagian_status', fn($row) => $this->formatBoolean($row->arahan_minit_ketua_bahagian_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_ketua_jabatan_status', fn($row) => $this->formatBoolean($row->arahan_minit_ketua_jabatan_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_oleh_ya_tpr_status', fn($row) => $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status, 'Ada', 'Tiada'))
        ->editColumn('adakah_barang_kes_didaftarkan', fn($row) => $this->formatBoolean($row->adakah_barang_kes_didaftarkan))
        ->editColumn('adakah_sijil_surat_kebenaran_ipo', fn($row) => $this->formatBoolean($row->adakah_sijil_surat_kebenaran_ipo, 'Ada', 'Tiada'))
        ->editColumn('status_id_siasatan_dikemaskini', fn($row) => $this->formatBoolean($row->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak'))
        ->editColumn('status_rajah_kasar_tempat_kejadian', fn($row) => $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_tempat_kejadian', fn($row) => $this->formatBoolean($row->status_gambar_tempat_kejadian, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_am', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_am, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_berharga', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_berharga, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_kenderaan', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_darah', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_darah, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_kontraban', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada'))
        ->editColumn('status_rj2', fn($row) => $this->formatThreeState($row->status_rj2, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_rj2b', fn($row) => $this->formatThreeState($row->status_rj2b, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_rj9', fn($row) => $this->formatThreeState($row->status_rj9, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_rj99', fn($row) => $this->formatThreeState($row->status_rj99, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_rj10a', fn($row) => $this->formatThreeState($row->status_rj10a, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_rj10b', fn($row) => $this->formatThreeState($row->status_rj10b, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_semboyan_pertama_wanted_person', fn($row) => $this->formatThreeStateString($row->status_semboyan_pertama_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_semboyan_kedua_wanted_person', fn($row) => $this->formatThreeStateString($row->status_semboyan_kedua_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_semboyan_ketiga_wanted_person', fn($row) => $this->formatThreeStateString($row->status_semboyan_ketiga_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan'))
        ->editColumn('status_penandaan_kelas_warna', fn($row) => $this->formatBoolean($row->status_penandaan_kelas_warna))
        ->editColumn('status_saman_pdrm_s_257', fn($row) => $this->formatBoolean($row->status_saman_pdrm_s_257, 'Dicipta', 'Tidak'))
        ->editColumn('status_saman_pdrm_s_167', fn($row) => $this->formatBoolean($row->status_saman_pdrm_s_167, 'Dicipta', 'Tidak'))
        ->editColumn('status_permohonan_laporan_post_mortem_mayat', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_1_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_1_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_1_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_1_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_2_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_2_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_2_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_2_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_3_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_3_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_3_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_3_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_4_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_4_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_4_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_4_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_5_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_5_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_5_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_5_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_1_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_1_telco_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_2_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_2_telco_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_3_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_3_telco_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_4_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_4_telco_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_E_FSA_5_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_permohonan_E_FSA_5_telco_oleh_IO_AIO, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO', fn($row) => $this->formatString($row->status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_puspakom', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_puspakom, 'Ada', 'Tiada'))
        ->editColumn('status_laporan_penuh_puspakom', fn($row) => $this->formatBoolean($row->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tiada'))
        ->editColumn('status_permohonan_laporan_jkr', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jkr, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_jkr', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jkr, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_jpj', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jpj, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_jpj', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jpj, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_imigresen', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_imigresen, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_imigresen', fn($row) => $this->formatBoolean($row->status_laporan_penuh_imigresen, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_kastam', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_kastam, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_kastam', fn($row) => $this->formatBoolean($row->status_laporan_penuh_kastam, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_forensik_pdrm', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_forensik_pdrm, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_forensik_pdrm', fn($row) => $this->formatBoolean($row->status_laporan_penuh_forensik_pdrm, 'Diterima', 'Tidak'))
        ->editColumn('muka_surat_4_barang_kes_ditulis', fn($row) => $this->formatBoolean($row->muka_surat_4_barang_kes_ditulis))
        ->editColumn('muka_surat_4_dengan_arahan_tpr', fn($row) => $this->formatBoolean($row->muka_surat_4_dengan_arahan_tpr))
        ->editColumn('muka_surat_4_keputusan_kes_dicatat', fn($row) => $this->formatBoolean($row->muka_surat_4_keputusan_kes_dicatat))
        ->editColumn('fail_lmm_ada_keputusan_koroner', fn($row) => $this->formatBoolean($row->fail_lmm_ada_keputusan_koroner))
        ->editColumn('status_kus_fail', fn($row) => $this->formatBoolean($row->status_kus_fail))

        // --- JSON/Array Field Formatting ---
        ->editColumn('status_pem', fn($row) => $this->formatArrayField($row->status_pem))
        ->editColumn('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', fn($row) => $this->formatArrayField($row->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan))
        
        // --- Combined "Lain-lain: text" formatting like TrafikSeksyen, Jenayah, Narkotik ---
        ->editColumn('status_pergerakan_barang_kes', function ($row) {
            if ($row->status_pergerakan_barang_kes === 'Lain-Lain' && !empty($row->status_pergerakan_barang_kes_lain)) {
                return 'Lain-lain: ' . htmlspecialchars($row->status_pergerakan_barang_kes_lain);
            }
            return htmlspecialchars($row->status_pergerakan_barang_kes ?? '-');
        })
        ->editColumn('status_barang_kes_selesai_siasatan', function ($row) {
            if ($row->status_barang_kes_selesai_siasatan === 'Lain-Lain' && !empty($row->status_barang_kes_selesai_siasatan_lain)) {
                return 'Lain-lain: ' . htmlspecialchars($row->status_barang_kes_selesai_siasatan_lain);
            }
            return htmlspecialchars($row->status_barang_kes_selesai_siasatan ?? '-');
        })
        ->editColumn('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', function ($row) {
            if ($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan === 'Lain-Lain' && !empty($row->kaedah_pelupusan_barang_kes_lain)) {
                return 'Lain-lain: ' . htmlspecialchars($row->kaedah_pelupusan_barang_kes_lain);
            }
            return htmlspecialchars($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan ?? '-');
        })
        ->editColumn('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', fn($row) => $this->formatArrayField($row->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan))
        ->editColumn('resit_kew_38e_bagi_pelupusan', fn($row) => $this->formatArrayField($row->resit_kew_38e_bagi_pelupusan))
        ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan', fn($row) => $this->formatThreeStateString($row->adakah_borang_serah_terima_pegawai_tangkapan, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan'))
        ->editColumn('adakah_borang_serah_terima_pemilik_saksi', fn($row) => $this->formatThreeStateString($row->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan'))
        ->editColumn('adakah_sijil_surat_kebenaran_ipd', fn($row) => $this->formatBoolean($row->adakah_sijil_surat_kebenaran_ipd, 'Ada Dilampirkan', 'Tidak'))
        ->editColumn('adakah_gambar_pelupusan', fn($row) => $this->formatBoolean($row->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak'))
        // keputusan_akhir_mahkamah will be handled by frontend JSON processing

        // --- RawColumns must include all columns with HTML ---
        ->rawColumns(array_merge(
            [
                'action', 'status_pem', 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', 'status_pergerakan_barang_kes', 
                'status_barang_kes_selesai_siasatan', 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', 
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', 'resit_kew_38e_bagi_pelupusan', 
                'adakah_borang_serah_terima_pegawai_tangkapan', 'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipd', 'adakah_gambar_pelupusan',
                // RJ status fields that use formatThreeState (need to be in rawColumns for HTML badges)
                'status_rj2', 'status_rj2b', 'status_rj9', 'status_rj99', 'status_rj10a', 'status_rj10b',
                // E-FSA string fields that use formatString method
                'status_permohonan_E_FSA_1_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_1_oleh_IO_AIO',
                'status_permohonan_E_FSA_2_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_2_oleh_IO_AIO',
                'status_permohonan_E_FSA_3_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_3_oleh_IO_AIO',
                'status_permohonan_E_FSA_4_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_4_oleh_IO_AIO',
                'status_permohonan_E_FSA_5_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_5_oleh_IO_AIO',
                'status_permohonan_E_FSA_1_telco_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO',
                'status_permohonan_E_FSA_2_telco_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO',
                'status_permohonan_E_FSA_3_telco_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO',
                'status_permohonan_E_FSA_4_telco_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO',
                'status_permohonan_E_FSA_5_telco_oleh_IO_AIO', 'status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO',
                // Boolean status fields that use formatBoolean (need to be in rawColumns for HTML badges)
                'status_permohonan_laporan_puspakom', 'status_laporan_penuh_puspakom',
                'status_permohonan_laporan_jkr', 'status_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj', 'status_laporan_penuh_jpj',
                'status_permohonan_laporan_imigresen', 'status_laporan_penuh_imigresen',
                'status_permohonan_laporan_kastam', 'status_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm', 'status_laporan_penuh_forensik_pdrm',
                'status_permohonan_laporan_post_mortem_mayat'
            ],
            collect((new Komersil)->getCasts())->filter(fn($type) => $type === 'boolean')->keys()->all()
        ))
        ->make(true);
}

 public function getNarkotikData(Project $project)
    {
        Gate::authorize('access-project', $project);
        $query = Narkotik::where('project_id', $project->id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'Narkotik'))
            
            // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
            ->addColumn('lewat_edaran_status', function($row) {
                return $row->lewat_edaran_status;
            })
            ->addColumn('terbengkalai_status_dc', function($row) {
                return $row->terbengkalai_status_dc;
            })
            ->addColumn('terbengkalai_status_da', function($row) {
                return $row->terbengkalai_status_da;
            })
            ->addColumn('baru_dikemaskini_status', function($row) {
                return $row->baru_dikemaskini_status;
            })

            ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')

            // date formatting
            ->editColumn('tarikh_laporan_polis_dibuka', function ($row) {
                return optional($row->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_pertama', function ($row) {
                return optional($row->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_kedua', function ($row) {
                return optional($row->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_sebelum_akhir', function ($row) {
                return optional($row->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_akhir', function ($row) {
                return optional($row->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_pemeriksaan_jips_ke_daerah', function ($row) {
                return optional($row->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_oleh_sio_tarikh', function ($row) {
                return optional($row->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_ketua_bahagian_tarikh', function ($row) {
                return optional($row->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_ketua_jabatan_tarikh', function ($row) {
                return optional($row->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', function ($row) {
                return optional($row->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj2', function ($row) {
                return optional($row->tarikh_rj2)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj2b', function ($row) {
                return optional($row->tarikh_rj2b)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj9', function ($row) {
                return optional($row->tarikh_rj9)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj99', function ($row) {
                return optional($row->tarikh_rj99)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj10a', function ($row) {
                return optional($row->tarikh_rj10a)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj10b', function ($row) {
                return optional($row->tarikh_rj10b)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_pertama_wanted_person', function ($row) {
                return optional($row->tarikh_semboyan_pertama_wanted_person)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_kedua_wanted_person', function ($row) {
                return optional($row->tarikh_semboyan_kedua_wanted_person)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_ketiga_wanted_person', function ($row) {
                return optional($row->tarikh_semboyan_ketiga_wanted_person)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jabatan_kimia', function ($row) {
                return optional($row->tarikh_permohonan_laporan_jabatan_kimia)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jabatan_kimia', function ($row) {
                return optional($row->tarikh_laporan_penuh_jabatan_kimia)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jabatan_patalogi', function ($row) {
                return optional($row->tarikh_permohonan_laporan_jabatan_patalogi)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jabatan_patalogi', function ($row) {
                return optional($row->tarikh_laporan_penuh_jabatan_patalogi)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_puspakom', function ($row) {
                return optional($row->tarikh_permohonan_laporan_puspakom)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_puspakom', function ($row) {
                return optional($row->tarikh_laporan_penuh_puspakom)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jpj', function ($row) {
                return optional($row->tarikh_permohonan_laporan_jpj)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jpj', function ($row) {
                return optional($row->tarikh_laporan_penuh_jpj)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_imigresen', function ($row) {
                return optional($row->tarikh_permohonan_laporan_imigresen)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_imigresen', function ($row) {
                return optional($row->tarikh_laporan_penuh_imigresen)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_kastam', function ($row) {
                return optional($row->tarikh_permohonan_laporan_kastam)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_kastam', function ($row) {
                return optional($row->tarikh_laporan_penuh_kastam)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_forensik_pdrm', function ($row) {
                return optional($row->tarikh_permohonan_laporan_forensik_pdrm)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_forensik_pdrm', function ($row) {
                return optional($row->tarikh_laporan_penuh_forensik_pdrm)->format('d/m/Y') ?? '-';
            })
            ->editColumn('created_at', function ($row) {
                return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->editColumn('updated_at', function ($row) {
                return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
            })

            // boolean formatting
            ->editColumn('arahan_minit_oleh_sio_status', function ($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_sio_status, 'Ada', 'Tiada');
            })
            ->editColumn('arahan_minit_ketua_bahagian_status', function ($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_bahagian_status, 'Ada', 'Tiada');
            })
            ->editColumn('arahan_minit_ketua_jabatan_status', function ($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_jabatan_status, 'Ada', 'Tiada');
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_status', function ($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status, 'Ada', 'Tiada');
            })
            ->editColumn('adakah_barang_kes_didaftarkan', function ($row) {
                return $this->formatBoolean($row->adakah_barang_kes_didaftarkan);
            })
            ->editColumn('resit_kew38e_pelupusan_wang_tunai', function ($row) {
                return $this->formatBoolean($row->resit_kew38e_pelupusan_wang_tunai, 'Ada Dilampirkan', 'Tidak Dilampirkan');
            })
            ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan', function ($row) {
                return $this->formatThreeStateString($row->adakah_borang_serah_terima_pegawai_tangkapan, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan');
            })
            ->editColumn('adakah_borang_serah_terima_pemilik_saksi', function ($row) {
                return $this->formatThreeStateString($row->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan');
            })
            ->editColumn('adakah_sijil_surat_kebenaran_ipo', function ($row) {
                return $this->formatBoolean($row->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan');
            })
            ->editColumn('adakah_gambar_pelupusan', function ($row) {
                return $this->formatBoolean($row->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan');
            })
            ->editColumn('status_id_siasatan_dikemaskini', function ($row) {
                return $this->formatBoolean($row->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini');
            })
            ->editColumn('status_rajah_kasar_tempat_kejadian', function ($row) {
                return $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_tempat_kejadian', function ($row) {
                return $this->formatBoolean($row->status_gambar_tempat_kejadian, 'Ada', 'Tiada');
            })
            ->editColumn('gambar_botol_urin_3d_berseal', function ($row) {
                return $this->formatBoolean($row->gambar_botol_urin_3d_berseal, 'Ada', 'Tiada');
            })
            ->editColumn('gambar_pembalut_urin_dan_test_strip', function ($row) {
                return $this->formatBoolean($row->gambar_pembalut_urin_dan_test_strip, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_am', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_am, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_berharga', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_berharga, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_kenderaan', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_dadah', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_dadah, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_ketum', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_ketum, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_darah', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_darah, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_barang_kes_kontraban', function ($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada');
            })
            ->editColumn('status_rj2', function ($row) {
                return $this->formatThreeState($row->status_rj2, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_rj2b', function ($row) {
                return $this->formatThreeState($row->status_rj2b, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_rj9', function ($row) {
                return $this->formatThreeState($row->status_rj9, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_rj99', function ($row) {
                return $this->formatThreeState($row->status_rj99, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_rj10a', function ($row) {
                return $this->formatThreeState($row->status_rj10a, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_rj10b', function ($row) {
                return $this->formatThreeState($row->status_rj10b, 'Ada/Cipta', 'Tiada/Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_semboyan_pertama_wanted_person', function ($row) {
                return $this->formatThreeStateString($row->status_semboyan_pertama_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_semboyan_kedua_wanted_person', function ($row) {
                return $this->formatThreeStateString($row->status_semboyan_kedua_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_semboyan_ketiga_wanted_person', function ($row) {
                return $this->formatThreeStateString($row->status_semboyan_ketiga_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_penandaan_kelas_warna', function ($row) {
                return $this->formatBoolean($row->status_penandaan_kelas_warna);
            })
            ->editColumn('status_permohonan_laporan_jabatan_kimia', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jabatan_kimia, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_jabatan_kimia', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jabatan_kimia, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('status_permohonan_laporan_jabatan_patalogi', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jabatan_patalogi, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_jabatan_patalogi', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jabatan_patalogi, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('status_permohonan_laporan_puspakom', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_puspakom, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_puspakom', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('status_permohonan_laporan_jpj', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jpj, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_jpj', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jpj, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('status_permohonan_laporan_imigresen', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_imigresen, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_imigresen', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_imigresen, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('status_permohonan_laporan_kastam', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_kastam, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_kastam', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_kastam, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('status_permohonan_laporan_forensik_pdrm', function ($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_forensik_pdrm, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_forensik_pdrm', function ($row) {
                return $this->formatBoolean($row->status_laporan_penuh_forensik_pdrm, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('muka_surat_4_barang_kes_ditulis', function ($row) {
                return $this->formatBoolean($row->muka_surat_4_barang_kes_ditulis);
            })
            ->editColumn('muka_surat_4_dengan_arahan_tpr', function ($row) {
                return $this->formatBoolean($row->muka_surat_4_dengan_arahan_tpr);
            })
            ->editColumn('muka_surat_4_keputusan_kes_dicatat', function ($row) {
                return $this->formatBoolean($row->muka_surat_4_keputusan_kes_dicatat);
            })
            ->editColumn('fail_lmm_ada_keputusan_koroner', function ($row) {
                return $this->formatBoolean($row->fail_lmm_ada_keputusan_koroner);
            })
            ->editColumn('status_kus_fail', function ($row) {
                return $this->formatBoolean($row->status_kus_fail);
            })

            //JSON
            ->editColumn('status_pem', function ($row) {
                return $this->formatArrayField($row->status_pem);
            })

            ->editColumn('keputusan_akhir_mahkamah', function ($row) {
                return $this->formatArrayField($row->keputusan_akhir_mahkamah);
            })


            // string/HTML
            ->editColumn('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', function ($row) {
                return htmlspecialchars($row->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan ?? '-');
            })
            ->editColumn('status_pergerakan_barang_kes', function ($row) {
                if ($row->status_pergerakan_barang_kes === 'Ujian Makmal' && !empty($row->status_pergerakan_barang_kes_makmal)) {
                    return 'Ujian Makmal: ' . htmlspecialchars($row->status_pergerakan_barang_kes_makmal);
                }
                if ($row->status_pergerakan_barang_kes === 'Lain-lain' && !empty($row->status_pergerakan_barang_kes_lain)) {
                    return 'Lain-lain: ' . htmlspecialchars($row->status_pergerakan_barang_kes_lain);
                }
                return htmlspecialchars($row->status_pergerakan_barang_kes ?? '-');
            })
            ->editColumn('status_barang_kes_selesai_siasatan', function ($row) {
                if ($row->status_barang_kes_selesai_siasatan === 'Dilupuskan ke Perbendaharaan' && !empty($row->status_barang_kes_selesai_siasatan_RM)) {
                    return 'Dilupuskan ke Perbendaharaan: ' . htmlspecialchars($row->status_barang_kes_selesai_siasatan_RM);
                }
                if ($row->status_barang_kes_selesai_siasatan === 'Lain-lain' && !empty($row->status_barang_kes_selesai_siasatan_lain)) {
                    return 'Lain-lain: ' . htmlspecialchars($row->status_barang_kes_selesai_siasatan_lain);
                }
                return htmlspecialchars($row->status_barang_kes_selesai_siasatan ?? '-');
            })
            ->editColumn('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', function ($row) {
                if ($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan === 'Lain-lain' && !empty($row->kaedah_pelupusan_barang_kes_lain)) {
                    return 'Lain-lain: ' . htmlspecialchars($row->kaedah_pelupusan_barang_kes_lain);
                }
                return htmlspecialchars($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan ?? '-');
            })
            ->editColumn('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', function ($row) {
                return htmlspecialchars($row->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan ?? '-');
            })
            ->editColumn('keputusan_akhir_mahkamah', function ($row) {
                return $this->formatArrayField($row->keputusan_akhir_mahkamah);
            })

            ->rawColumns([
                'action',
                'arahan_minit_oleh_sio_status',
                'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_jabatan_status',
                'arahan_minit_oleh_ya_tpr_status',
                'adakah_barang_kes_didaftarkan',
                'resit_kew38e_pelupusan_wang_tunai',
                'adakah_borang_serah_terima_pegawai_tangkapan',
                'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipo',
                'adakah_gambar_pelupusan',
                'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian',
                'gambar_botol_urin_3d_berseal',
                'gambar_pembalut_urin_dan_test_strip',
                'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_kenderaan',
                'status_gambar_barang_kes_dadah',
                'status_gambar_barang_kes_ketum',
                'status_gambar_barang_kes_darah',
                'status_gambar_barang_kes_kontraban',
                'status_rj2',
                'status_rj2b',
                'status_rj9',
                'status_rj99',
                'status_rj10a',
                'status_rj10b',
                'status_semboyan_pertama_wanted_person',
                'status_semboyan_kedua_wanted_person',
                'status_semboyan_ketiga_wanted_person',
                'status_penandaan_kelas_warna',
                'status_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi',
                'status_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom',
                'status_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj',
                'status_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen',
                'status_permohonan_laporan_kastam',
                'status_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm',
                'status_laporan_penuh_forensik_pdrm',
                'keputusan_laporan_forensik_pdrm',
                'jenis_ujian_analisis_forensik',
                'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail',
                'status_pem',
                'keputusan_akhir_mahkamah', // Array field for badges
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan',
                'status_pergerakan_barang_kes',
                'status_barang_kes_selesai_siasatan',
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
            ])
            ->make(true);
    }
    

    

    public function getTrafikSeksyenData(Project $project) {
        Gate::authorize('access-project', $project);
         $query = TrafikSeksyen::where('project_id', $project->id)
                ->select('trafik_seksyen.*');
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikSeksyen'))
            
            // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
            ->addColumn('lewat_edaran_status', function($row) {
                return $row->lewat_edaran_status;
            })
            ->addColumn('terbengkalai_status_dc', function($row) {
                return $row->terbengkalai_status_dc;
            })
            ->addColumn('terbengkalai_status_da', function($row) {
                return $row->terbengkalai_status_da;
            })
            ->addColumn('baru_dikemaskini_status', function($row) {
                return $row->baru_dikemaskini_status;
            })


            ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')

            // Format Date fields
            ->editColumn('tarikh_laporan_polis_dibuka', function($row) {
                return optional($row->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_pertama', function($row) {
                return optional($row->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_kedua', function($row) {
                return optional($row->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_sebelum_akhir', function($row) {
                return optional($row->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_akhir', function($row) {
                return optional($row->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_pemeriksaan_jips_ke_daerah', function($row) {
                return optional($row->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_oleh_sio_tarikh', function($row) {
                return optional($row->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_ketua_bahagian_tarikh', function($row) {
                return optional($row->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_ketua_jabatan_tarikh', function($row) {
                return optional($row->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', function($row) {
                return optional($row->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj2', function($row) {
                return optional($row->tarikh_rj2)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj2b', function($row) {
                return optional($row->tarikh_rj2b)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj9', function($row) {
                return optional($row->tarikh_rj9)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj99', function($row) {
                return optional($row->tarikh_rj99)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj10a', function($row) {
                return optional($row->tarikh_rj10a)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj10b', function($row) {
                return optional($row->tarikh_rj10b)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_pertama_wanted_person', function($row) {
                return optional($row->tarikh_semboyan_pertama_wanted_person)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_kedua_wanted_person', function($row) {
                return optional($row->tarikh_semboyan_kedua_wanted_person)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_ketiga_wanted_person', function($row) {
                return optional($row->tarikh_semboyan_ketiga_wanted_person)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_post_mortem_mayat', function($row) {
                return optional($row->tarikh_permohonan_laporan_post_mortem_mayat)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_bedah_siasat', function($row) {
                return optional($row->tarikh_laporan_penuh_bedah_siasat)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jabatan_kimia', function($row) {
                return optional($row->tarikh_permohonan_laporan_jabatan_kimia)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jabatan_kimia', function($row) {
                return optional($row->tarikh_laporan_penuh_jabatan_kimia)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jabatan_patalogi', function($row) {
                return optional($row->tarikh_permohonan_laporan_jabatan_patalogi)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jabatan_patalogi', function($row) {
                return optional($row->tarikh_laporan_penuh_jabatan_patalogi)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_puspakom', function($row) {
                return optional($row->tarikh_permohonan_laporan_puspakom)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_puspakom', function($row) {
                return optional($row->tarikh_laporan_penuh_puspakom)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jkr', function($row) {
                return optional($row->tarikh_permohonan_laporan_jkr)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jkr', function($row) {
                return optional($row->tarikh_laporan_penuh_jkr)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jpj', function($row) {
                return optional($row->tarikh_permohonan_laporan_jpj)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jpj', function($row) {
                return optional($row->tarikh_laporan_penuh_jpj)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_imigresen', function($row) {
                return optional($row->tarikh_permohonan_laporan_imigresen)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_imigresen', function($row) {
                return optional($row->tarikh_laporan_penuh_imigresen)->format('d/m/Y') ?? '-';
            })
            
            // Format boolean fields
            ->editColumn('arahan_minit_oleh_sio_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_sio_status);
            })
            ->editColumn('arahan_minit_ketua_bahagian_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_bahagian_status);
            })
            ->editColumn('arahan_minit_ketua_jabatan_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_jabatan_status);
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status);
            })
            ->editColumn('adakah_barang_kes_didaftarkan', function($row) {
                return $this->formatBoolean($row->adakah_barang_kes_didaftarkan);
            })
            ->editColumn('adakah_borang_serah_terima_pemilik_saksi', function($row) {
                return $this->formatThreeState($row->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan');
            })
            ->editColumn('adakah_sijil_surat_kebenaran_ipo', function($row) {
                return $this->formatBoolean($row->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan');
            })
            ->editColumn('adakah_gambar_pelupusan', function($row) {
                return $this->formatBoolean($row->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan');
            })
            ->editColumn('status_id_siasatan_dikemaskini', function($row) {
                return $this->formatBoolean($row->status_id_siasatan_dikemaskini);
            })
            ->editColumn('status_rajah_kasar_tempat_kejadian', function($row) {
                return $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian);
            })
            ->editColumn('status_gambar_tempat_kejadian', function($row) {
                return $this->formatBoolean($row->status_gambar_tempat_kejadian);
            })
            ->editColumn('status_gambar_post_mortem_mayat_di_hospital', function($row) {
                return $this->formatBoolean($row->status_gambar_post_mortem_mayat_di_hospital);
            })
            ->editColumn('status_gambar_barang_kes_am', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_am);
            })
            ->editColumn('status_gambar_barang_kes_berharga', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_berharga);
            })
            ->editColumn('status_gambar_barang_kes_kenderaan', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_kenderaan);
            })
            ->editColumn('status_gambar_barang_kes_darah', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_darah);
            })
            ->editColumn('status_gambar_barang_kes_kontraban', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_kontraban);
            })
            ->editColumn('status_rj2', function($row) {
                return $this->formatBoolean($row->status_rj2);
            })
            ->editColumn('status_rj2b', function($row) {
                return $this->formatBoolean($row->status_rj2b);
            })
            ->editColumn('status_rj9', function($row) {
                return $this->formatBoolean($row->status_rj9);
            })
            ->editColumn('status_rj99', function($row) {
                return $this->formatBoolean($row->status_rj99);
            })
            ->editColumn('status_rj10a', function($row) {
                return $this->formatBoolean($row->status_rj10a);
            })
            ->editColumn('status_rj10b', function($row) {
                return $this->formatBoolean($row->status_rj10b);
            })
            ->editColumn('status_saman_pdrm_s_257', function($row) {
                return $this->formatBoolean($row->status_saman_pdrm_s_257);
            })
            ->editColumn('status_saman_pdrm_s_167', function($row) {
                return $this->formatBoolean($row->status_saman_pdrm_s_167);
            })
            ->editColumn('status_semboyan_pertama_wanted_person', function($row) {
                return $this->formatThreeStateString($row->status_semboyan_pertama_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_semboyan_kedua_wanted_person', function($row) {
                return $this->formatThreeStateString($row->status_semboyan_kedua_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_semboyan_ketiga_wanted_person', function($row) {
                return $this->formatThreeStateString($row->status_semboyan_ketiga_wanted_person, 'Ada / Cipta', 'Tiada / Tidak Cipta', 'Tidak Berkaitan');
            })
            ->editColumn('status_penandaan_kelas_warna', function($row) {
                return $this->formatBoolean($row->status_penandaan_kelas_warna);
            })
            ->editColumn('status_permohonan_laporan_post_mortem_mayat', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_post_mortem_mayat);
            })
            ->editColumn('status_laporan_penuh_bedah_siasat', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_bedah_siasat);
            })
            ->editColumn('status_permohonan_laporan_jabatan_kimia', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jabatan_kimia);
            })
            ->editColumn('status_laporan_penuh_jabatan_kimia', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jabatan_kimia);
            })
            ->editColumn('status_permohonan_laporan_jabatan_patalogi', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jabatan_patalogi);
            })
            ->editColumn('status_laporan_penuh_jabatan_patalogi', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jabatan_patalogi);
            })
            ->editColumn('status_permohonan_laporan_puspakom', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_puspakom);
            })
            ->editColumn('status_laporan_penuh_puspakom', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_puspakom);
            })
            ->editColumn('status_permohonan_laporan_jkr', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jkr);
            })
            ->editColumn('status_laporan_penuh_jkr', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jkr);
            })
            ->editColumn('status_permohonan_laporan_jpj', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jpj);
            })
            ->editColumn('status_laporan_penuh_jpj', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jpj);
            })
            ->editColumn('status_permohonan_laporan_imigresen', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_imigresen);
            })
            ->editColumn('status_laporan_penuh_imigresen', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_imigresen);
            })
            ->editColumn('muka_surat_4_barang_kes_ditulis', function($row) {
                return $this->formatBoolean($row->muka_surat_4_barang_kes_ditulis);
            })
            ->editColumn('muka_surat_4_dengan_arahan_tpr', function($row) {
                return $this->formatBoolean($row->muka_surat_4_dengan_arahan_tpr);
            })
            ->editColumn('muka_surat_4_keputusan_kes_dicatat', function($row) {
                return $this->formatBoolean($row->muka_surat_4_keputusan_kes_dicatat);
            })
            ->editColumn('fail_lmm_ada_keputusan_koroner', function($row) {
                return $this->formatBoolean($row->fail_lmm_ada_keputusan_koroner);
            })
            ->editColumn('status_kus_fail', function($row) { // Now a boolean
                return $this->formatBoolean($row->status_kus_fail);
            })
            
            // Format L.M.M (T) boolean fields
            ->editColumn('adakah_ms2_lmm_t_disahkan_oleh_kpd', function($row) {
                return $this->formatBoolean($row->adakah_ms2_lmm_t_disahkan_oleh_kpd);
            })
            ->editColumn('adakah_lmm_t_dirujuk_kepada_ya_koroner', function($row) {
                return $this->formatBoolean($row->adakah_lmm_t_dirujuk_kepada_ya_koroner);
            })
            ->editColumn('keputusan_ya_koroner_lmm_t', function($row) {
                return htmlspecialchars($row->keputusan_ya_koroner_lmm_t ?? '-');
            })
            
            // Format JSON array fields (status_pem)
            ->editColumn('status_pem', function($row) {
                return $this->formatArrayField($row->status_pem);
            })

            // Custom formatting for fields with "Lain-Lain" and _lain column
            ->editColumn('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', function($row) {
                return htmlspecialchars($row->adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan ?? '-');
            })
            ->editColumn('status_pergerakan_barang_kes', function($row) {
                if ($row->status_pergerakan_barang_kes === 'Lain-Lain' && !empty($row->status_pergerakan_barang_kes_lain)) {
                    return 'Lain-lain: ' . htmlspecialchars($row->status_pergerakan_barang_kes_lain);
                }
                return htmlspecialchars($row->status_pergerakan_barang_kes ?? '-');
            })
            ->editColumn('status_barang_kes_selesai_siasatan', function($row) {
                if ($row->status_barang_kes_selesai_siasatan === 'Lain-Lain' && !empty($row->status_barang_kes_selesai_siasatan_lain)) {
                    return 'Lain-lain: ' . htmlspecialchars($row->status_barang_kes_selesai_siasatan_lain);
                }
                return htmlspecialchars($row->status_barang_kes_selesai_siasatan ?? '-');
            })
            ->editColumn('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', function($row) {
                if ($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan === 'Lain-Lain' && !empty($row->kaedah_pelupusan_barang_kes_lain)) {
                    return 'Lain-lain: ' . htmlspecialchars($row->kaedah_pelupusan_barang_kes_lain);
                }
                return htmlspecialchars($row->barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan ?? '-');
            })
            ->editColumn('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', function($row) {
                return htmlspecialchars($row->adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan ?? '-');
            })
            ->editColumn('resit_kew38e_pelupusan_wang_tunai', function($row) {
                return htmlspecialchars($row->resit_kew38e_pelupusan_wang_tunai ?? '-');
            })
            ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan', function($row) {
                return htmlspecialchars($row->adakah_borang_serah_terima_pegawai_tangkapan ?? '-');
            })
            ->editColumn('keputusan_akhir_mahkamah', function($row) { // Now an array (checkboxes)
                return $this->formatArrayField($row->keputusan_akhir_mahkamah);
            })
            ->editColumn('lain_lain_permohonan_laporan', function($row) { // Ensure this is also handled for display
                return htmlspecialchars($row->lain_lain_permohonan_laporan ?? '-');
            })

            // NEW JKJR, Kastam, and Forensik PDRM fields
            ->editColumn('tarikh_permohonan_laporan_jkjr', function($row) {
                return optional($row->tarikh_permohonan_laporan_jkjr)->format('d/m/Y') ?? '-';
            })
            ->editColumn('status_permohonan_laporan_jkjr', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jkjr, 'Ada', 'Tiada');
            })
            ->editColumn('tarikh_laporan_penuh_jkjr', function($row) {
                return optional($row->tarikh_laporan_penuh_jkjr)->format('d/m/Y') ?? '-';
            })
            ->editColumn('status_laporan_penuh_jkjr', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jkjr, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('tarikh_permohonan_laporan_kastam', function($row) {
                return optional($row->tarikh_permohonan_laporan_kastam)->format('d/m/Y') ?? '-';
            })
            ->editColumn('status_permohonan_laporan_kastam', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_kastam, 'Ada', 'Tiada');
            })
            ->editColumn('tarikh_laporan_penuh_kastam', function($row) {
                return optional($row->tarikh_laporan_penuh_kastam)->format('d/m/Y') ?? '-';
            })
            ->editColumn('status_laporan_penuh_kastam', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_kastam, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('tarikh_permohonan_laporan_forensik_pdrm', function($row) {
                return optional($row->tarikh_permohonan_laporan_forensik_pdrm)->format('d/m/Y') ?? '-';
            })
            ->editColumn('status_permohonan_laporan_forensik_pdrm', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_forensik_pdrm, 'Ada', 'Tiada');
            })
            ->editColumn('tarikh_laporan_penuh_forensik_pdrm', function($row) {
                return optional($row->tarikh_laporan_penuh_forensik_pdrm)->format('d/m/Y') ?? '-';
            })
            ->editColumn('status_laporan_penuh_forensik_pdrm', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_forensik_pdrm, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('jenis_barang_kes_forensik', function($row) {
                return htmlspecialchars($row->jenis_barang_kes_forensik ?? '-');
            })

            ->rawColumns(array_merge([
                'action',
                // Raw columns for boolean fields
                'arahan_minit_oleh_sio_status',
                'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_jabatan_status',
                'arahan_minit_oleh_ya_tpr_status',
                'adakah_barang_kes_didaftarkan',
                'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipo',
                'adakah_gambar_pelupusan',
                'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian',
                'status_gambar_post_mortem_mayat_di_hospital',
                'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_kenderaan',
                'status_gambar_barang_kes_darah',
                'status_gambar_barang_kes_kontraban',
                'status_rj2',
                'status_rj2b',
                'status_rj9',
                'status_rj99',
                'status_rj10a',
                'status_rj10b',
                'status_saman_pdrm_s_257',
                'status_saman_pdrm_s_167',
                'status_semboyan_pertama_wanted_person',
                'status_semboyan_kedua_wanted_person',
                'status_semboyan_ketiga_wanted_person',
                'status_penandaan_kelas_warna',
                'status_permohonan_laporan_post_mortem_mayat',
                'status_laporan_penuh_bedah_siasat',
                'status_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi',
                'status_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom',
                'status_permohonan_laporan_jkr',
                'status_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj',
                'status_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen',
                'status_permohonan_laporan_jkjr',
                'status_laporan_penuh_jkjr',
                'status_permohonan_laporan_kastam',
                'status_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm',
                'status_laporan_penuh_forensik_pdrm',
                'keputusan_laporan_forensik_pdrm',
                'jenis_ujian_analisis_forensik',
                'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail', // Now a boolean
                'keputusan_akhir_mahkamah',
                // Raw columns for JSON/complex string fields
                'status_pem',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', // Now a string
                'status_pergerakan_barang_kes', // Now a string
                'status_barang_kes_selesai_siasatan', // Now a string
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', // Now a string
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', // Now a string
                'resit_kew38e_pelupusan_wang_tunai', // Now a string
                'adakah_borang_serah_terima_pegawai_tangkapan', // Now a string
                'lain_lain_permohonan_laporan', // String, ensuring it's in rawColumns if special chars might exist
            ]))
            ->editColumn('created_at', function ($row) {
                return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->editColumn('updated_at', function ($row) {
                return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->make(true);
    }
    
    public function getTrafikRuleData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = TrafikRule::where('project_id', $project->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikRule'))
            
            // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
            ->addColumn('lewat_edaran_status', function($row) {
                return $row->lewat_edaran_status;
            })
            ->addColumn('terbengkalai_status_dc', function($row) {
                return $row->terbengkalai_status_dc;
            })
            ->addColumn('terbengkalai_status_da', function($row) {
                return $row->terbengkalai_status_da;
            })
            ->addColumn('baru_dikemaskini_status', function($row) {
                return $row->baru_dikemaskini_status;
            })

            ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')


            // Format Date fields
            ->editColumn('tarikh_laporan_polis_dibuka', function($row) {
                return optional($row->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_pertama', function($row) {
                return optional($row->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_kedua', function($row) {
                return optional($row->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_sebelum_akhir', function($row) {
                return optional($row->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_akhir', function($row) {
                return optional($row->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_semboyan_pemeriksaan_jips_ke_daerah', function($row) {
                return optional($row->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_fail_lmm_t_pertama', function($row) {
                return optional($row->tarikh_edaran_minit_fail_lmm_t_pertama)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_fail_lmm_t_kedua', function($row) {
                return optional($row->tarikh_edaran_minit_fail_lmm_t_kedua)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir', function($row) {
                return optional($row->tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_edaran_minit_fail_lmm_t_akhir', function($row) {
                return optional($row->tarikh_edaran_minit_fail_lmm_t_akhir)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_oleh_sio_tarikh', function($row) {
                return optional($row->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_ketua_bahagian_tarikh', function($row) {
                return optional($row->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_ketua_jabatan_tarikh', function($row) {
                return optional($row->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', function($row) {
                return optional($row->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj10b', function($row) {
                return optional($row->tarikh_rj10b)->format('d/m/Y') ?? '-';
            })
            
            // RJ Fields - Added date formatters
            ->editColumn('tarikh_rj2', function($row) {
                return optional($row->tarikh_rj2)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj2b', function($row) {
                return optional($row->tarikh_rj2b)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj9', function($row) {
                return optional($row->tarikh_rj9)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj99', function($row) {
                return optional($row->tarikh_rj99)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_rj10a', function($row) {
                return optional($row->tarikh_rj10a)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jkr', function($row) {
                return optional($row->tarikh_permohonan_laporan_jkr)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_jkr', function($row) {
                return optional($row->tarikh_laporan_penuh_jkr)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jpj', function($row) {
                return optional($row->tarikh_permohonan_laporan_jpj)->format('d/m/Y') ?? '-';
            })
            // --- Format new date columns ---
            ->editColumn('tarikh_laporan_penuh_jpj', function($row) {
                return optional($row->tarikh_laporan_penuh_jpj)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_permohonan_laporan_jkjr', function($row) {
                return optional($row->tarikh_permohonan_laporan_jkjr)->format('d/m/Y') ?? '-';
            })
            // --- END of new date columns ---
            ->editColumn('tarikh_laporan_penuh_jkjr', function($row) {
                return optional($row->tarikh_laporan_penuh_jkjr)->format('d/m/Y') ?? '-';
            })
            
            // PUSPAKOM - Added date formatters
            ->editColumn('tarikh_permohonan_laporan_puspakom', function($row) {
                return optional($row->tarikh_permohonan_laporan_puspakom)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_puspakom', function($row) {
                return optional($row->tarikh_laporan_penuh_puspakom)->format('d/m/Y') ?? '-';
            })
            
            // HOSPITAL - Added date formatters
            ->editColumn('tarikh_permohonan_laporan_hospital', function($row) {
                return optional($row->tarikh_permohonan_laporan_hospital)->format('d/m/Y') ?? '-';
            })
            ->editColumn('tarikh_laporan_penuh_hospital', function($row) {
                return optional($row->tarikh_laporan_penuh_hospital)->format('d/m/Y') ?? '-';
            })
            
            // Format boolean fields
            ->editColumn('arahan_minit_oleh_sio_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_sio_status);
            })
            ->editColumn('arahan_minit_ketua_bahagian_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_bahagian_status);
            })
            ->editColumn('arahan_minit_ketua_jabatan_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_jabatan_status);
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status);
            })
            ->editColumn('status_id_siasatan_dikemaskini', function($row) {
                return $this->formatBoolean($row->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak');
            })
            ->editColumn('status_rajah_kasar_tempat_kejadian', function($row) {
                return $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada');
            })
            ->editColumn('status_gambar_tempat_kejadian', function($row) {
                return $this->formatBoolean($row->status_gambar_tempat_kejadian, 'Ada', 'Tiada');
            })
            ->editColumn('status_rj10b', function($row) {
                return $this->formatBoolean($row->status_rj10b, 'Cipta', 'Tidak');
            })
            
            // RJ Fields - Added boolean formatters
            ->editColumn('status_rj2', function($row) {
                return $this->formatBoolean($row->status_rj2, 'Diterima', 'Tidak');
            })
            ->editColumn('status_rj2b', function($row) {
                return $this->formatBoolean($row->status_rj2b, 'Diterima', 'Tidak');
            })
            ->editColumn('status_rj9', function($row) {
                return $this->formatBoolean($row->status_rj9, 'Diterima', 'Tidak');
            })
            ->editColumn('status_rj99', function($row) {
                return $this->formatBoolean($row->status_rj99, 'Diterima', 'Tidak');
            })
            ->editColumn('status_rj10a', function($row) {
                return $this->formatBoolean($row->status_rj10a, 'Diterima', 'Tidak');
            })
            ->editColumn('status_saman_pdrm_s_257', function($row) {
                return $this->formatBoolean($row->status_saman_pdrm_s_257, 'Dicipta', 'Tidak');
            })
            ->editColumn('status_saman_pdrm_s_167', function($row) {
                return $this->formatBoolean($row->status_saman_pdrm_s_167, 'Dicipta', 'Tidak');
            })
            ->editColumn('status_permohonan_laporan_jkr', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jkr);
            })
            ->editColumn('status_laporan_penuh_jkr', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jkr, 'Dilampirkan', 'Tidak');
            })
            ->editColumn('status_permohonan_laporan_jpj', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jpj);
            })
             // --- ADDED: Format new boolean columns ---
            ->editColumn('status_laporan_penuh_jpj', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jpj, 'Dilampirkan', 'Tidak');
            })
            ->editColumn('status_permohonan_laporan_jkjr', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_jkjr, 'Ada', 'Tiada');
            })
            // --- END of new boolean columns ---
            ->editColumn('status_laporan_penuh_jkjr', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jkjr, 'Dilampirkan', 'Tiada');
            })
            
            // PUSPAKOM - Added boolean formatters
            ->editColumn('status_permohonan_laporan_puspakom', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_puspakom, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_puspakom', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tiada');
            })
            
            // HOSPITAL - Added boolean formatters
            ->editColumn('status_permohonan_laporan_hospital', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_hospital, 'Ada', 'Tiada');
            })
            ->editColumn('status_laporan_penuh_hospital', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_hospital, 'Dilampirkan', 'Tiada');
            })
            ->editColumn('adakah_muka_surat_4_keputusan_kes_dicatat', function($row) {
                return $this->formatBoolean($row->adakah_muka_surat_4_keputusan_kes_dicatat);
            })
            ->editColumn('adakah_ks_kus_fail_selesai', function($row) {
                return $this->formatBoolean($row->adakah_ks_kus_fail_selesai);
            })
            ->editColumn('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan', function($row) {
                return $this->formatBoolean($row->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan);
            })
            ->editColumn('fail_lmm_t_muka_surat_2_disahkan_kpd', function($row) {
                return $this->formatBoolean($row->fail_lmm_t_muka_surat_2_disahkan_kpd, 'Telah Disahkan', 'Belum Disahkan');
            })

            // Format JSON array fields (status_pem)
            ->editColumn('status_pem', function($row) {
                return $this->formatArrayField($row->status_pem);
            })
            
            // Format keputusan_akhir_mahkamah as checkbox array (like TrafikSeksyen)
            ->editColumn('keputusan_akhir_mahkamah', function ($row) {
                return $this->formatArrayField($row->keputusan_akhir_mahkamah);
            })

            ->rawColumns([
                'action',
                // IPRS Standard Fields
                'iprs_status_ks',
                'iprs_status_kes',
                'arahan_minit_oleh_sio_status',
                'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_jabatan_status',
                'arahan_minit_oleh_ya_tpr_status',
                'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian',
                'status_pem',
                'keputusan_akhir_mahkamah', // Added back for array formatting
                
                // RJ Status fields
                'status_rj2',
                'status_rj2b',
                'status_rj9',
                'status_rj99',
                'status_rj10a',
                'status_rj10b',
                
                // Saman fields
                'status_saman_pdrm_s_257',
                'status_saman_pdrm_s_167',
                
                // JKR/JPJ/JKJR fields  
                'status_permohonan_laporan_jkr',
                'status_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj',
                'status_permohonan_laporan_jkjr',
                'status_laporan_penuh_jkjr',
                
                // PUSPAKOM fields
                'status_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom',
                
                // Hospital fields
                'status_permohonan_laporan_hospital',
                'status_laporan_penuh_hospital',
                
                // Other boolean fields
                'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_ks_kus_fail_selesai',
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan',
                'fail_lmm_t_muka_surat_2_disahkan_kpd',
            ])
            ->editColumn('created_at', function ($row) {
                return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->editColumn('updated_at', function ($row) {
                return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->make(true);
    }
    
public function getOrangHilangData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = OrangHilang::where('project_id', $project->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'OrangHilang'))

            // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
            ->addColumn('lewat_edaran_status', function($row) {
                return $row->lewat_edaran_status;
            })
            ->addColumn('terbengkalai_status_dc', function($row) {
                return $row->terbengkalai_status_dc;
            })
            ->addColumn('terbengkalai_status_da', function($row) {
                return $row->terbengkalai_status_da;
            })
            ->addColumn('baru_dikemaskini_status', function($row) {
                return $row->baru_dikemaskini_status;
            })

            ->editColumn('updated_at', function ($row) {
                return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->editColumn('created_at', function ($row) {
                return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
            })

            ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')

            // Format date fields
            ->editColumn('tarikh_laporan_polis_dibuka', function($row) {
                return $row->tarikh_laporan_polis_dibuka ? $row->tarikh_laporan_polis_dibuka->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_pertama', function($row) {
                return $row->tarikh_edaran_minit_ks_pertama ? $row->tarikh_edaran_minit_ks_pertama->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_edaran_minit_ks_kedua', function($row) {
                return $row->tarikh_edaran_minit_ks_kedua ? $row->tarikh_edaran_minit_ks_kedua->format('d/m/Y') : '-';
            })
            ->editColumn('arahan_minit_oleh_sio_tarikh', function($row) {
                return $row->arahan_minit_oleh_sio_tarikh ? $row->arahan_minit_oleh_sio_tarikh->format('d/m/Y') : '-';
            })
            ->editColumn('arahan_minit_ketua_bahagian_tarikh', function($row) {
                return $row->arahan_minit_ketua_bahagian_tarikh ? $row->arahan_minit_ketua_bahagian_tarikh->format('d/m/Y') : '-';
            })
            ->editColumn('arahan_minit_ketua_jabatan_tarikh', function($row) {
                return $row->arahan_minit_ketua_jabatan_tarikh ? $row->arahan_minit_ketua_jabatan_tarikh->format('d/m/Y') : '-';
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', function($row) {
                return $row->arahan_minit_oleh_ya_tpr_tarikh ? $row->arahan_minit_oleh_ya_tpr_tarikh->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_barang_kes_didaftarkan', function($row) {
                return $row->tarikh_barang_kes_didaftarkan ? $row->tarikh_barang_kes_didaftarkan->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_mps1', function($row) {
                return $row->tarikh_mps1 ? $row->tarikh_mps1->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_mps2', function($row) {
                return $row->tarikh_mps2 ? $row->tarikh_mps2->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_hebahan_media_massa', function($row) {
                return $row->tarikh_hebahan_media_massa ? $row->tarikh_hebahan_media_massa->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_pemakluman_ke_kedutaan', function($row) {
                return $row->tarikh_pemakluman_ke_kedutaan ? $row->tarikh_pemakluman_ke_kedutaan->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_permohonan_laporan_imigresen', function($row) {
                return $row->tarikh_permohonan_laporan_imigresen ? $row->tarikh_permohonan_laporan_imigresen->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_laporan_penuh_imigresen', function($row) {
                return $row->tarikh_laporan_penuh_imigresen ? $row->tarikh_laporan_penuh_imigresen->format('d/m/Y') : '-';
            })
            ->editColumn('tarikh_keputusan_akhir_mahkamah', function($row) {
                return $row->tarikh_keputusan_akhir_mahkamah ? $row->tarikh_keputusan_akhir_mahkamah->format('d/m/Y') : '-';
            })
            
            // Format boolean fields (render_boolean_radio fields)
            ->editColumn('arahan_minit_oleh_sio_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_sio_status);
            })
            ->editColumn('arahan_minit_ketua_bahagian_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_bahagian_status);
            })
            ->editColumn('arahan_minit_ketua_jabatan_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_ketua_jabatan_status);
            })
            ->editColumn('arahan_minit_oleh_ya_tpr_status', function($row) {
                return $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status);
            })
            ->editColumn('adakah_barang_kes_didaftarkan', function($row) {
                return $this->formatBoolean($row->adakah_barang_kes_didaftarkan);
            })
            ->editColumn('status_id_siasatan_dikemaskini', function($row) {
                return $this->formatBoolean($row->status_id_siasatan_dikemaskini);
            })
            ->editColumn('status_rajah_kasar_tempat_kejadian', function($row) {
                return $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian);
            })
            ->editColumn('status_gambar_tempat_kejadian', function($row) {
                return $this->formatBoolean($row->status_gambar_tempat_kejadian);
            })
            ->editColumn('status_gambar_barang_kes_am', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_am);
            })
            ->editColumn('status_gambar_barang_kes_berharga', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_berharga);
            })
            ->editColumn('status_gambar_orang_hilang', function($row) {
                return $this->formatBoolean($row->status_gambar_orang_hilang);
            })
            ->editColumn('status_mps1', function($row) {
                return $this->formatBoolean($row->status_mps1, 'Cipta', 'Tidak Cipta');
            })
            ->editColumn('status_mps2', function($row) {
                return $this->formatBoolean($row->status_mps2, 'Cipta', 'Tidak Cipta');
            })
            ->editColumn('hebahan_media_massa', function($row) {
                return $this->formatBoolean($row->hebahan_media_massa, 'Dibuat', 'Tidak Dibuat');
            })
            ->editColumn('semboyan_pemakluman_ke_kedutaan_bukan_warganegara', function($row) {
                return $this->formatBoolean($row->semboyan_pemakluman_ke_kedutaan_bukan_warganegara, 'Dibuat', 'Tidak Dibuat');
            })
            ->editColumn('status_permohonan_laporan_imigresen', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_imigresen);
            })
            // BAHAGIAN 7: New Imigresen fields
            ->editColumn('permohonan_laporan_permit_kerja', function($row) {
                return $this->formatBoolean($row->permohonan_laporan_permit_kerja, 'Ada', 'Tiada');
            })
            ->editColumn('permohonan_laporan_agensi_pekerjaan', function($row) {
                return $this->formatBoolean($row->permohonan_laporan_agensi_pekerjaan, 'Ada', 'Tiada');
            })
            ->editColumn('permohonan_status_kewarganegaraan', function($row) {
                return $this->formatBoolean($row->permohonan_status_kewarganegaraan, 'Ada', 'Tiada');
            })
            ->editColumn('adakah_ks_kus_fail_selesai', function($row) {
                return $row->adakah_ks_kus_fail_selesai ?? '-';
            })
            ->editColumn('adakah_muka_surat_4_keputusan_kes_dicatat', function($row) {
                return $this->formatBoolean($row->adakah_muka_surat_4_keputusan_kes_dicatat);
            })
            ->editColumn('orang_hilang_dijumpai_mati_mengejut_bukan_jenayah', function($row) {
                return $this->formatBoolean($row->orang_hilang_dijumpai_mati_mengejut_bukan_jenayah);
            })
            ->editColumn('orang_hilang_dijumpai_mati_mengejut_jenayah', function($row) {
                return $this->formatBoolean($row->orang_hilang_dijumpai_mati_mengejut_jenayah);
            })

            
            // Format JSON array fields (render_json_checkboxes fields)
            ->editColumn('status_pem', function($row) {
                return $this->formatArrayField($row->status_pem);
            })
            ->editColumn('keputusan_akhir_mahkamah', function($row) {
                return $this->formatArrayField($row->keputusan_akhir_mahkamah);
            })
            
            ->rawColumns([
                'action',
                'arahan_minit_oleh_sio_status',
                'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_jabatan_status',
                'arahan_minit_oleh_ya_tpr_status',
                'adakah_barang_kes_didaftarkan',
                'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian',
                'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga',
                'status_gambar_orang_hilang',
                'status_pem',
                'status_mps1',
                'status_mps2',
                'hebahan_media_massa',
                'semboyan_pemakluman_ke_kedutaan_bukan_warganegara',
                'status_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen',
                'permohonan_laporan_permit_kerja',
                'permohonan_laporan_agensi_pekerjaan',
                'permohonan_status_kewarganegaraan',
                'adakah_muka_surat_4_keputusan_kes_dicatat',
                'keputusan_akhir_mahkamah',
                'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah',
                'orang_hilang_dijumpai_mati_mengejut_jenayah'
            ])
            ->editColumn('created_at', function ($row) {
                return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->editColumn('updated_at', function ($row) {
                return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
            })
            ->make(true);
    }
// FILE: app/Http/Controllers/ProjectController.php

public function getLaporanMatiMengejutData(Project $project) {
    Gate::authorize('access-project', $project);
    $query = LaporanMatiMengejut::where('project_id', $project->id);
    
    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'LaporanMatiMengejut'))

        // VIRTUAL COLUMNS TO PREVENT ERROR WHEN SEARCHING
        ->addColumn('lewat_edaran_status', function($row) {
            return $row->lewat_edaran_status;
        })
        ->addColumn('terbengkalai_status_dc', function($row) {
            return $row->terbengkalai_status_dc;
        })
        ->addColumn('terbengkalai_status_da', function($row) {
            return $row->terbengkalai_status_da;
        })
        ->addColumn('baru_dikemaskini_status', function($row) {
            return $row->baru_dikemaskini_status;
        })

        ->editColumn('iprs_tarikh_ks', fn($r) => optional($r->iprs_tarikh_ks)->format('d/m/Y') ?? '-')
        
        ->editColumn('created_at', function ($row) {
            return optional($row->created_at)->format('d/m/Y H:i:s') ?? '-';
        })
        ->editColumn('updated_at', function ($row) {
            return optional($row->updated_at)->format('d/m/Y H:i:s') ?? '-';
        })


        // --- EXPLICIT DATE FORMATTING for all LMM date columns ---
        ->editColumn('tarikh_laporan_polis_dibuka', fn($r) => optional($r->tarikh_laporan_polis_dibuka)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_pertama', fn($r) => optional($r->tarikh_edaran_minit_ks_pertama)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_kedua', fn($r) => optional($r->tarikh_edaran_minit_ks_kedua)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_sebelum_akhir', fn($r) => optional($r->tarikh_edaran_minit_ks_sebelum_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_ks_akhir', fn($r) => optional($r->tarikh_edaran_minit_ks_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_semboyan_pemeriksaan_jips_ke_daerah', fn($r) => optional($r->tarikh_semboyan_pemeriksaan_jips_ke_daerah)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_fail_lmm_t_pertama', fn($r) => optional($r->tarikh_edaran_minit_fail_lmm_t_pertama)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_fail_lmm_t_kedua', fn($r) => optional($r->tarikh_edaran_minit_fail_lmm_t_kedua)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir', fn($r) => optional($r->tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_edaran_minit_fail_lmm_t_akhir', fn($r) => optional($r->tarikh_edaran_minit_fail_lmm_t_akhir)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_oleh_sio_tarikh', fn($r) => optional($r->arahan_minit_oleh_sio_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_ketua_bahagian_tarikh', fn($r) => optional($r->arahan_minit_ketua_bahagian_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_ketua_jabatan_tarikh', fn($r) => optional($r->arahan_minit_ketua_jabatan_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('arahan_minit_oleh_ya_tpr_tarikh', fn($r) => optional($r->arahan_minit_oleh_ya_tpr_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj2', fn($r) => optional($r->tarikh_rj2)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj2b', fn($r) => optional($r->tarikh_rj2b)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj9', fn($r) => optional($r->tarikh_rj9)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj99', fn($r) => optional($r->tarikh_rj99)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj10a', fn($r) => optional($r->tarikh_rj10a)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_rj10b', fn($r) => optional($r->tarikh_rj10b)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_post_mortem_mayat', fn($r) => optional($r->tarikh_permohonan_laporan_post_mortem_mayat)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_bedah_siasat', fn($r) => optional($r->tarikh_laporan_penuh_bedah_siasat)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_jabatan_kimia', fn($r) => optional($r->tarikh_permohonan_laporan_jabatan_kimia)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_jabatan_kimia', fn($r) => optional($r->tarikh_laporan_penuh_jabatan_kimia)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_jabatan_patalogi', fn($r) => optional($r->tarikh_permohonan_laporan_jabatan_patalogi)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_jabatan_patalogi', fn($r) => optional($r->tarikh_laporan_penuh_jabatan_patalogi)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_permohonan_laporan_imigresen', fn($r) => optional($r->tarikh_permohonan_laporan_imigresen)->format('d/m/Y') ?? '-')
        ->editColumn('tarikh_laporan_penuh_imigresen', fn($r) => optional($r->tarikh_laporan_penuh_imigresen)->format('d/m/Y') ?? '-')
        ->editColumn('permit_kerja_tarikh', fn($r) => optional($r->permit_kerja_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('permit_kerja_tarikh_tamat', fn($r) => optional($r->permit_kerja_tarikh_tamat)->format('d/m/Y') ?? '-')
        ->editColumn('agensi_pekerjaan_tarikh', fn($r) => optional($r->agensi_pekerjaan_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('agensi_pekerjaan_tarikh_tamat', fn($r) => optional($r->agensi_pekerjaan_tarikh_tamat)->format('d/m/Y') ?? '-')
        ->editColumn('status_kewarganegaraan_tarikh', fn($r) => optional($r->status_kewarganegaraan_tarikh)->format('d/m/Y') ?? '-')
        ->editColumn('status_kewarganegaraan_tarikh_tamat', fn($r) => optional($r->status_kewarganegaraan_tarikh_tamat)->format('d/m/Y') ?? '-')

        // --- BOOLEAN FORMATTING (Using the helper function) ---
        ->editColumn('adakah_ms_2_lmm_telah_disahkan_oleh_kpd', fn($row) => $this->formatBoolean($row->adakah_ms_2_lmm_telah_disahkan_oleh_kpd))
        ->editColumn('adakah_lmm_telah_di_rujuk_kepada_ya_koroner', fn($row) => $this->formatBoolean($row->adakah_lmm_telah_di_rujuk_kepada_ya_koroner))
        ->editColumn('arahan_minit_oleh_sio_status', fn($row) => $this->formatBoolean($row->arahan_minit_oleh_sio_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_ketua_bahagian_status', fn($row) => $this->formatBoolean($row->arahan_minit_ketua_bahagian_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_ketua_jabatan_status', fn($row) => $this->formatBoolean($row->arahan_minit_ketua_jabatan_status, 'Ada', 'Tiada'))
        ->editColumn('arahan_minit_oleh_ya_tpr_status', fn($row) => $this->formatBoolean($row->arahan_minit_oleh_ya_tpr_status, 'Ada', 'Tiada'))
        ->editColumn('adakah_barang_kes_didaftarkan', fn($row) => $this->formatBoolean($row->adakah_barang_kes_didaftarkan))
        ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan_io', fn($row) => $this->formatBoolean($row->adakah_borang_serah_terima_pegawai_tangkapan_io, 'Ada Dilampirkan', 'Tidak'))
        ->editColumn('adakah_borang_serah_terima_penyiasat_pemilik_saksi', fn($row) => $this->formatBoolean($row->adakah_borang_serah_terima_penyiasat_pemilik_saksi, 'Ada Dilampirkan', 'Tidak'))
        ->editColumn('adakah_sijil_surat_kebenaran_ipd', fn($row) => $this->formatBoolean($row->adakah_sijil_surat_kebenaran_ipd, 'Ada Dilampirkan', 'Tidak'))
        ->editColumn('adakah_gambar_pelupusan', fn($row) => $this->formatBoolean($row->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak'))
        ->editColumn('status_id_siasatan_dikemaskini', fn($row) => $this->formatBoolean($row->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak'))
        ->editColumn('status_rajah_kasar_tempat_kejadian', fn($row) => $this->formatBoolean($row->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_tempat_kejadian', fn($row) => $this->formatBoolean($row->status_gambar_tempat_kejadian, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_post_mortem_mayat_di_hospital', fn($row) => $this->formatBoolean($row->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_am', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_am, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_berharga', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_berharga, 'Ada', 'Tiada'))
        ->editColumn('status_gambar_barang_kes_darah', fn($row) => $this->formatBoolean($row->status_gambar_barang_kes_darah, 'Ada', 'Tiada'))
        
        // --- THREE-STATE RJ FIELDS (Using the new formatThreeState function) ---
        ->editColumn('status_rj2', fn($row) => $this->formatThreeState($row->status_rj2))
        ->editColumn('status_rj2b', fn($row) => $this->formatThreeState($row->status_rj2b))
        ->editColumn('status_rj9', fn($row) => $this->formatThreeState($row->status_rj9))
        ->editColumn('status_rj99', fn($row) => $this->formatThreeState($row->status_rj99))
        ->editColumn('status_rj10a', fn($row) => $this->formatThreeState($row->status_rj10a))
        ->editColumn('status_rj10b', fn($row) => $this->formatThreeState($row->status_rj10b))
        
        // --- OTHER BOOLEAN FIELDS ---
        ->editColumn('status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati', fn($row) => $this->formatBoolean($row->status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati, 'Dibuat', 'Tidak'))
        ->editColumn('status_permohonan_laporan_post_mortem_mayat', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_bedah_siasat', fn($row) => $this->formatBoolean($row->status_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_jabatan_kimia', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_jabatan_kimia', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak'))
        ->editColumn('status_permohonan_laporan_jabatan_patalogi', fn($row) => $this->formatBoolean($row->status_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak'))
        ->editColumn('status_laporan_penuh_jabatan_patalogi', fn($row) => $this->formatBoolean($row->status_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak'))
        ->editColumn('status_laporan_penuh_imigresen', fn($row) => $this->formatBoolean($row->status_laporan_penuh_imigresen, 'Diterima', 'Tidak'))
        // New simplified Imigresen fields formatting
        ->editColumn('permohonan_laporan_pengesahan_masuk_keluar_malaysia', fn($row) => $this->formatBoolean($row->permohonan_laporan_pengesahan_masuk_keluar_malaysia, 'Ada', 'Tiada'))
        ->editColumn('permohonan_laporan_permit_kerja_di_malaysia', fn($row) => $this->formatBoolean($row->permohonan_laporan_permit_kerja_di_malaysia, 'Ada', 'Tiada'))
        ->editColumn('permohonan_laporan_agensi_pekerjaan_di_malaysia', fn($row) => $this->formatBoolean($row->permohonan_laporan_agensi_pekerjaan_di_malaysia, 'Ada', 'Tiada'))
        ->editColumn('permohonan_status_kewarganegaraan', fn($row) => $this->formatBoolean($row->permohonan_status_kewarganegaraan, 'Ada', 'Tiada'))
        ->editColumn('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar', fn($row) => $this->formatBoolean($row->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar))
        ->editColumn('status_barang_kes_arahan_tpr', fn($row) => $this->formatBoolean($row->status_barang_kes_arahan_tpr))
        ->editColumn('adakah_muka_surat_4_keputusan_kes_dicatat', fn($row) => $this->formatBoolean($row->adakah_muka_surat_4_keputusan_kes_dicatat))
        ->editColumn('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan', fn($row) => $this->formatBoolean($row->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan))
        
        // --- SPECIAL STRING FIELD (KUS/FAIL) ---
        ->editColumn('adakah_ks_kus_fail_selesai', function($row) {
            $value = $row->adakah_ks_kus_fail_selesai;
            if (is_null($value) || $value === '') {
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
            }
            $color = $value === 'KUS' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $color . '">' . $value . '</span>';
        })
        
        // --- JSON ARRAY FORMATTING ---
        ->editColumn('status_pem', fn($row) => $this->formatArrayField($row->status_pem))
        ->editColumn('keputusan_akhir_mahkamah', fn($row) => $this->formatArrayField($row->keputusan_akhir_mahkamah))

        // Add additional columns needed for special fields (but not separate _lain columns)
        ->addColumn('ujian_makmal_details', function($row) {
            return $row->ujian_makmal_details ?? '';
        })
        ->addColumn('dilupuskan_perbendaharaan_amount', function($row) {
            return $row->dilupuskan_perbendaharaan_amount ?? '';
        })

        ->rawColumns([
            'action', 'status_pem', 'keputusan_akhir_mahkamah',
            'adakah_ms_2_lmm_telah_disahkan_oleh_kpd', 'adakah_lmm_telah_di_rujuk_kepada_ya_koroner',
            'arahan_minit_oleh_sio_status', 'arahan_minit_ketua_bahagian_status', 
            'arahan_minit_ketua_jabatan_status', 'arahan_minit_oleh_ya_tpr_status', 'adakah_barang_kes_didaftarkan',
            'adakah_borang_serah_terima_pegawai_tangkapan_io', 'adakah_borang_serah_terima_penyiasat_pemilik_saksi',
            'adakah_sijil_surat_kebenaran_ipd', 'adakah_gambar_pelupusan', 'status_id_siasatan_dikemaskini',
            'status_rajah_kasar_tempat_kejadian', 'status_gambar_tempat_kejadian', 'status_gambar_post_mortem_mayat_di_hospital',
            'status_gambar_barang_kes_am', 'status_gambar_barang_kes_berharga', 'status_gambar_barang_kes_darah',
            'status_rj2', 'status_rj2b', 'status_rj9', 'status_rj99', 'status_rj10a', 'status_rj10b',
            'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati',
            'status_permohonan_laporan_post_mortem_mayat', 'status_laporan_penuh_bedah_siasat',
            'status_permohonan_laporan_jabatan_kimia', 'status_laporan_penuh_jabatan_kimia',
            'status_permohonan_laporan_jabatan_patalogi', 'status_laporan_penuh_jabatan_patalogi',
            'status_permohonan_laporan_imigresen', 'status_laporan_penuh_imigresen',
            'permohonan_laporan_pengesahan_masuk_keluar_malaysia', 'permohonan_laporan_permit_kerja_di_malaysia', 
            'permohonan_laporan_agensi_pekerjaan_di_malaysia', 'permohonan_status_kewarganegaraan',
            'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar', 'status_barang_kes_arahan_tpr',
            'adakah_muka_surat_4_keputusan_kes_dicatat', 'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan',
            'adakah_ks_kus_fail_selesai'
        ])
        ->make(true);
}

    /**
     * Format boolean values for display in DataTables
     */
    private function formatBoolean($value, $trueText = 'Ya', $falseText = 'Tidak')
    {
        if (is_null($value)) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        return $value 
            ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' . $trueText . '</span>'
            : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . $falseText . '</span>';
    }

    /**
     * Format three-state integer values for display in DataTables (specifically for RJ fields)
     */
    private function formatThreeState($value, $adaText = 'Ada/Cipta', $tiadaText = 'Tiada/Tidak Cipta', $tidakBerkaitanText = 'Tidak Berkaitan')
    {
        if (is_null($value)) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        switch ((int)$value) {
            case 1:
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' . $adaText . '</span>';
            case 2:
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">' . $tidakBerkaitanText . '</span>';
            case 0:
            default:
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . $tiadaText . '</span>';
        }
    }

    private function formatThreeStateString($value, $adaText = 'Ada Dilampirkan', $tiadaText = 'Tidak Dilampirkan', $tidakBerkaitanText = 'Tidak Berkaitan')
    {
        if (is_null($value) || $value === '') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        $value = (string)$value;
        
        if (str_contains($value, 'Ada') || $value === $adaText) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' . htmlspecialchars($value) . '</span>';
        } elseif (str_contains($value, 'Tidak Berkaitan') || $value === $tidakBerkaitanText) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">' . htmlspecialchars($value) . '</span>';
        } else {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . htmlspecialchars($value) . '</span>';
        }
    }

        private function formatWantedPersonStatus($value)
    {
        if (is_null($value) || $value === '') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        if ($value === 'Ada / Cipta') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ada / Cipta</span>';
        } elseif ($value === 'Tidak Berkaitan') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Tidak Berkaitan</span>';
        } else {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tiada / Tidak Cipta</span>';
        }
    }

    private function formatPakarJudiLaporan($value)
    {
        if (is_null($value) || $value === '') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        if ($value === 'Diterima') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Diterima</span>';
        } elseif ($value === 'Masih Menunggu Laporan Pakar Judi') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Masih Menunggu Laporan Pakar Judi</span>';
        } else {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tidak Diterima</span>';
        }
    }

    
    /**
     * Format string status values for display in DataTables (specifically for E-FSA fields)
     */
    private function formatStringStatus($value, $trueText = 'Dibuat', $falseText = 'Tidak')
    {
        if (is_null($value) || $value === '') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        // Check if the value indicates a positive status
        $positiveValues = ['Dibuat', 'Diterima', 'Ya', 'Ada', 'Cipta', 'Dicipta', '1', 'true', 'YES'];
        $isPositive = in_array(strtolower($value), array_map('strtolower', $positiveValues)) || 
                      in_array($value, $positiveValues);
        
        return $isPositive 
            ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' . $trueText . '</span>'
            : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . $falseText . '</span>';
    }

    /**
     * Format string values for display in DataTables (specifically for E-FSA fields)
     */
    private function formatString($value, $trueText = 'Dibuat', $falseText = 'Tidak')
    {
        if (is_null($value) || $value === '') {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }

        // --- FIX ---
        // Add all possible "positive" string values from your forms to this array.
        $positiveValues = [
            'Permohonan Dibuat',
            'Dilampirkan',
            'Dibuat', 
            'Diterima', 
            'Ya', 
            'Ada', 
            'Cipta', 
            'Dicipta', 
            '1', 
            'true', 
            'YES'
        ];
        
        // Check case-insensitively if the value is in the positive list
        $isPositive = in_array(strtolower($value), array_map('strtolower', $positiveValues)) ||
                      (is_numeric($value) && $value > 0);

        // Display the actual value from the database, wrapped in htmlspecialchars for security.
        $displayText = htmlspecialchars($value);

        return $isPositive
            ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' . $displayText . '</span>'
            : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . $displayText . '</span>';
    }

    /**
     * Format array fields for display in DataTables
     */
    private function formatArrayField($value)
    {
        if (is_null($value) || empty($value)) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        // Ensure value is an array. If it's a JSON string from DB, decode it.
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } else {
                // If it's a string but not valid JSON, display it as a single badge
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">' . htmlspecialchars($value) . '</span>';
            }
        }
        
        if (!is_array($value)) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }
        
        $badges = [];
        foreach ($value as $item) {
            // Check for potential empty values or specific formatting needs within the array
            if (!empty($item) || $item === 0 || $item === false) { // include 0 or false if they are meaningful values
                $badges[] = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">' . htmlspecialchars($item) . '</span>';
            }
        }
        
        if (empty($badges)) {
            return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        }

        return implode(' ', $badges);
    }

    public function downloadTemplate($filename)
    {
        $filePath = storage_path('app/templates/' . $filename);
        
        if (!file_exists($filePath)) {
            abort(404, 'Template file not found.');
        }
        
        return response()->download($filePath);
    }
}