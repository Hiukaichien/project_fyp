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
            $lewatItems = $allPapers->filter(fn ($paper) => property_exists($paper, 'lewat_edaran_48_jam_status') && $paper->lewat_edaran_48_jam_status === 'YA, LEWAT')->values();
            $terbengkalaiItems = $allPapers->filter(fn ($paper) => property_exists($paper, 'terbengkalai_status') && Str::contains($paper->terbengkalai_status, 'TERBENGKALAI'))->values();
            $kemaskiniItems = $allPapers->filter(fn ($paper) => property_exists($paper, 'baru_dikemaskini_status') && $paper->baru_dikemaskini_status === 'TERBENGKALAI / KS BARU DIKEMASKINI')->values();

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
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/csv|max:20480',
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'TrafikSeksyen', 'TrafikRule', 'OrangHilang', 'LaporanMatiMengejut'])],
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
            'paper_type' => ['required', 'string', Rule::in(['Jenayah', 'Narkotik', 'Komersil', 'TrafikSeksyen', 'TrafikRule', 'OrangHilang', 'LaporanMatiMengejut'])],
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
                    } elseif (is_bool($value)) { // Handle boolean values for CSV export
                        $row[] = $value ? 'Ya' : 'Tidak';
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
        $query = TrafikSeksyen::where('project_id', $project->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikSeksyen'))
            
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
                return $this->formatBoolean($row->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan');
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
                return $this->formatBoolean($row->status_semboyan_pertama_wanted_person);
            })
            ->editColumn('status_semboyan_kedua_wanted_person', function($row) {
                return $this->formatBoolean($row->status_semboyan_kedua_wanted_person);
            })
            ->editColumn('status_semboyan_ketiga_wanted_person', function($row) {
                return $this->formatBoolean($row->status_semboyan_ketiga_wanted_person);
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
            ->editColumn('resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', function($row) {
                return htmlspecialchars($row->resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan ?? '-');
            })
            ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan', function($row) {
                return htmlspecialchars($row->adakah_borang_serah_terima_pegawai_tangkapan ?? '-');
            })
            ->editColumn('keputusan_akhir_mahkamah', function($row) { // Now a single string
                return htmlspecialchars($row->keputusan_akhir_mahkamah ?? '-');
            })
            ->editColumn('lain_lain_permohonan_laporan', function($row) { // Ensure this is also handled for display
                return htmlspecialchars($row->lain_lain_permohonan_laporan ?? '-');
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
                'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail', // Now a boolean
                // Raw columns for JSON/complex string fields
                'status_pem',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', // Now a string
                'status_pergerakan_barang_kes', // Now a string
                'status_barang_kes_selesai_siasatan', // Now a string
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan', // Now a string
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', // Now a string
                'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', // Now a string
                'adakah_borang_serah_terima_pegawai_tangkapan', // Now a string
                'keputusan_akhir_mahkamah', // Now a string
                'lain_lain_permohonan_laporan', // String, ensuring it's in rawColumns if special chars might exist
            ]))
            ->make(true);
    }public function getTrafikRuleData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = TrafikRule::where('project_id', $project->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'TrafikRule'))
            
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
            ->editColumn('tarikh_rj10b', function($row) {
                return optional($row->tarikh_rj10b)->format('d/m/Y') ?? '-';
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
            ->editColumn('tarikh_laporan_penuh_jkjr', function($row) {
                return optional($row->tarikh_laporan_penuh_jkjr)->format('d/m/Y') ?? '-';
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
            ->editColumn('status_laporan_penuh_jkjr', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_jkjr, 'Dilampirkan', 'Tidak');
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

            // Format JSON array fields (status_pem)
            ->editColumn('status_pem', function($row) {
                return $this->formatArrayField($row->status_pem);
            })

            ->rawColumns([
                'action',
                'arahan_minit_oleh_sio_status',
                'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_jabatan_status',
                'arahan_minit_oleh_ya_tpr_status',
                'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian',
                'status_pem',
                'status_rj10b',
                'status_saman_pdrm_s_257',
                'status_saman_pdrm_s_167',
                'status_permohonan_laporan_jkr',
                'status_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj',
                'status_laporan_penuh_jkjr',
                'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_ks_kus_fail_selesai',
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan'
            ])
            ->make(true);
    }
    public function getOrangHilangData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = OrangHilang::where('project_id', $project->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'OrangHilang'))
            
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
            ->editColumn('status_laporan_penuh_imigresen', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_imigresen);
            })
            ->editColumn('adakah_ks_kus_fail_selesai', function($row) {
                return $this->formatBoolean($row->adakah_ks_kus_fail_selesai);
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
                'adakah_ks_kus_fail_selesai',
                'adakah_muka_surat_4_keputusan_kes_dicatat',
                'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah',
                'orang_hilang_dijumpai_mati_mengejut_jenayah'
            ])
            ->make(true);
    }
    public function getLaporanMatiMengejutData(Project $project) {
        Gate::authorize('access-project', $project);
        $query = LaporanMatiMengejut::where('project_id', $project->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->buildActionButtons($row, 'LaporanMatiMengejut'))
            ->editColumn('fail_lmm_bahagian_pengurusan_pada_muka_surat_2', function($row) {
                return $this->formatBoolean($row->fail_lmm_bahagian_pengurusan_pada_muka_surat_2);
            })
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
            ->editColumn('adakah_borang_serah_terima_pegawai_tangkapan_io', function($row) {
                return $this->formatBoolean($row->adakah_borang_serah_terima_pegawai_tangkapan_io);
            })
            ->editColumn('adakah_borang_serah_terima_penyiasat_pemilik_saksi', function($row) {
                return $this->formatBoolean($row->adakah_borang_serah_terima_penyiasat_pemilik_saksi);
            })
            ->editColumn('adakah_sijil_surat_kebenaran_ipd', function($row) {
                return $this->formatBoolean($row->adakah_sijil_surat_kebenaran_ipd);
            })
            ->editColumn('adakah_gambar_pelupusan', function($row) {
                return $this->formatBoolean($row->adakah_gambar_pelupusan);
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
            // 新增缺少的字段
            ->editColumn('status_gambar_barang_kes_am', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_am);
            })
            ->editColumn('status_gambar_barang_kes_berharga', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_berharga);
            })
            ->editColumn('status_gambar_barang_kes_darah', function($row) {
                return $this->formatBoolean($row->status_gambar_barang_kes_darah);
            })
            ->editColumn('status_rj2', function($row) {
                return $this->formatBoolean($row->status_rj2);
            })
            ->editColumn('status_rj2b', function($row) {
                return $this->formatBoolean($row->status_rj2b);
            })
            ->editColumn('status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati', function($row) {
                return $this->formatBoolean($row->status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati);
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
            ->editColumn('status_permohonan_laporan_imigresen', function($row) {
                return $this->formatBoolean($row->status_permohonan_laporan_imigresen);
            })
            ->editColumn('status_laporan_penuh_imigresen', function($row) {
                return $this->formatBoolean($row->status_laporan_penuh_imigresen);
            })
            ->editColumn('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar', function($row) {
                return $this->formatBoolean($row->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar);
            })
            ->editColumn('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr', function($row) {
                return $this->formatBoolean($row->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr);
            })
            ->editColumn('adakah_muka_surat_4_keputusan_kes_dicatat', function($row) {
                return $this->formatBoolean($row->adakah_muka_surat_4_keputusan_kes_dicatat);
            })
            ->editColumn('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan', function($row) {
                return $this->formatBoolean($row->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan);
            })
            ->editColumn('adakah_ks_kus_fail_selesai', function($row) {
                return $this->formatBoolean($row->adakah_ks_kus_fail_selesai);
            })
            ->rawColumns([
                'action', 
                'fail_lmm_bahagian_pengurusan_pada_muka_surat_2',
                'arahan_minit_oleh_sio_status',
                'arahan_minit_ketua_bahagian_status', 
                'arahan_minit_ketua_jabatan_status',
                'arahan_minit_oleh_ya_tpr_status',
                'adakah_barang_kes_didaftarkan',
                'adakah_borang_serah_terima_pegawai_tangkapan_io',
                'adakah_borang_serah_terima_penyiasat_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipd',
                'adakah_gambar_pelupusan',
                'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian',
                'status_gambar_post_mortem_mayat_di_hospital',
                'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_darah',
                'status_rj2',
                'status_rj2b',
                'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati',
                'status_permohonan_laporan_post_mortem_mayat',
                'status_laporan_penuh_bedah_siasat',
                'status_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi',
                'status_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen',
                'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar',
                'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr',
                'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan',
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
}