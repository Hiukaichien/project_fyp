<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\KertasSiasatan; // Keep for specific methods like destroy

class KertasSiasatanController extends Controller
{
    // A whitelist of valid models to prevent security issues and simplify class resolution.
    protected $validPaperTypes = [
        'KertasSiasatan' => \App\Models\KertasSiasatan::class,
        'JenayahPaper' => \App\Models\JenayahPaper::class,
        'NarkotikPaper' => \App\Models\NarkotikPaper::class,
        'KomersilPaper' => \App\Models\KomersilPaper::class,
        'TrafikSeksyenPaper' => \App\Models\TrafikSeksyenPaper::class,
        'TrafikRulePaper' => \App\Models\TrafikRulePaper::class,
        'OrangHilangPaper' => \App\Models\OrangHilangPaper::class,
        'LaporanMatiMengejutPaper' => \App\Models\LaporanMatiMengejutPaper::class,
    ];

    /**
     * --- MODIFIED: Generic show method for any paper type ---
     */
    public function show($paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        $paper = $modelClass::findOrFail($id);
        
        // Dynamically determine the view path (e.g., 'kertas_siasatan.show', 'jenayah_papers.show')
        $viewName = Str::snake($paperType, '_') . 's.show';
        
        // Pass a generic 'paper' variable and the type for the title
        return view($viewName, [
            'paper' => $paper,
            'paperType' => Str::headline(str_replace('Paper', '', $paperType))
        ]);
    }

    /**
     * --- MODIFIED: Generic edit method for any paper type ---
     */
    public function edit($paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        $paper = $modelClass::findOrFail($id);

        $viewName = Str::snake($paperType, '_') . 's.edit';

        return view($viewName, [
            'paper' => $paper,
            'paperType' => Str::headline(str_replace('Paper', '', $paperType))
        ]);
    }

    /**
     * --- MODIFIED: Generic update method for any paper type ---
     */
    public function update(Request $request, $paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        $paper = $modelClass::findOrFail($id);

        // Update all fillable fields from the request
        $paper->update($request->all());

        return Redirect::route('projects.show', $paper->project_id)
                       ->with('success', Str::headline(str_replace('Paper', '', $paperType)) . ' paper updated successfully.');
    }

    /**
     * Helper to get the model class and validate it.
     */
    private function getModelClass($paperType)
    {
        if (!array_key_exists($paperType, $this->validPaperTypes)) {
            abort(404, 'Invalid paper type specified.');
        }
        return $this->validPaperTypes[$paperType];
    }

    /**
     * The destroy method remains specific to the KertasSiasatan model as per the original route.
     */
    public function destroy(KertasSiasatan $kertasSiasatan)
    {
        try {
            $projectId = $kertasSiasatan->project_id;
            $kertasSiasatan->delete();
            $redirectRoute = $projectId ? route('projects.show', $projectId) : route('projects.index');
            return redirect($redirectRoute)->with('success', 'Kertas Siasatan ' . $kertasSiasatan->no_ks . ' berjaya dipadam.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memadam Kertas Siasatan: ' . $e->getMessage());
        }
    }
}