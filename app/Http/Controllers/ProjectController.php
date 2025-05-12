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
    public function show(Project $project)
    {
        // Fetch unassigned Kertas Siasatan papers for the dropdown
        // Assuming KertasSiasatan model has Sortable trait
        $unassignedKertasSiasatan = KertasSiasatan::whereNull('project_id')
                                                ->sortable() // Apply default sort from KertasSiasatan model
                                                ->orderBy('no_ks') // Can be fallback or primary sort
                                                ->get();

        // Fetch other unassigned paper types as needed for their respective dropdowns
        // Apply sortable() assuming these models also use the Sortable trait
        $unassignedJenayahPapers = JenayahPaper::whereNull('project_id')->sortable()->get(); 
        $unassignedNarkotikPapers = NarkotikPaper::whereNull('project_id')->sortable()->get();
        $unassignedTrafikSeksyenPapers = TrafikSeksyenPaper::whereNull('project_id')->sortable()->get();
        $unassignedTrafikRulePapers = TrafikRulePaper::whereNull('project_id')->sortable()->orderBy('no_kst')->get(); // Keep specific orderBy if needed
        $unassignedKomersilPapers = KomersilPaper::whereNull('project_id')->sortable()->get();
        $unassignedLaporanMatiMengejutPapers = LaporanMatiMengejutPaper::whereNull('project_id')->sortable()->get();
        $unassignedOrangHilangPapers = OrangHilangPaper::whereNull('project_id')->sortable()->get();

        // Fetch paginated Kertas Siasatan associated with this project
        // Use a unique page name, e.g., 'ks_page', to avoid conflicts if other paginators are on the page
        $associatedKertasSiasatanPaginated = $project->kertasSiasatan()
                                                     ->sortable() // Assuming KertasSiasatan model uses Sortable trait
                                                     ->paginate(10, ['*'], 'ks_project_page');

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