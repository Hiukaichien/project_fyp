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

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderBy('project_date', 'desc')->get();
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
    public function show(Project $project)
    {
        // Fetch unassigned Kertas Siasatan papers for the dropdown
        $unassignedKertasSiasatan = KertasSiasatan::whereNull('project_id')
                                                ->orderBy('no_ks')
                                                ->get();

        // Fetch other unassigned paper types as needed for their respective dropdowns
        $unassignedJenayahPapers = JenayahPaper::whereNull('project_id')->get(); 
        $unassignedNarkotikPapers = NarkotikPaper::whereNull('project_id')->get();
        $unassignedTrafikSeksyenPapers = TrafikSeksyenPaper::whereNull('project_id')->get();
        $unassignedTrafikRulePapers = TrafikRulePaper::whereNull('project_id')->orderBy('no_kst')->get();
        $unassignedKomersilPapers = KomersilPaper::whereNull('project_id')->get();
        $unassignedLaporanMatiMengejutPapers = LaporanMatiMengejutPaper::whereNull('project_id')->get(); // Corrected model name
        $unassignedOrangHilangPapers = OrangHilangPaper::whereNull('project_id')->get();

        // Fetch paginated Kertas Siasatan associated with this project
        // Use a unique page name, e.g., 'ks_page', to avoid conflicts if other paginators are on the page
        $associatedKertasSiasatanPaginated = $project->kertasSiasatan()
                                                     ->sortable() // Assuming KertasSiasatan model uses Sortable trait
                                                     ->paginate(15, ['*'], 'ks_project_page');

        return view('projects.show', compact(
            'project',
            'unassignedKertasSiasatan',
            'unassignedJenayahPapers',
            'unassignedNarkotikPapers',
            'unassignedTrafikSeksyenPapers',
            'unassignedTrafikRulePapers',
            'unassignedKomersilPapers',
            'unassignedLaporanMatiMengejutPapers', // Corrected variable name
            'unassignedOrangHilangPapers',
            'associatedKertasSiasatanPaginated' // Pass paginated associated KS
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * (Stub - implement if needed)
     */
    public function edit(Project $project)
    {
        // return view('projects.edit', compact('project'));
        // For now, redirect or show a message if not implemented
        return Redirect::route('projects.show', $project)->with('info', 'Edit functionality not yet implemented.');
    }

    /**
     * Update the specified resource in storage.
     * (Stub - implement if needed)
     */
    public function update(Request $request, Project $project)
    {
        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'project_date' => 'required|date',
        //     'description' => 'nullable|string',
        // ]);
        // $project->update($validatedData);
        // return Redirect::route('projects.index')->with('success', 'Project updated successfully.');
        return Redirect::route('projects.show', $project)->with('info', 'Update functionality not yet implemented.');
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
}