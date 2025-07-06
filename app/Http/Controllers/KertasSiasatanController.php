<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\Komersil;
use App\Models\Trafik; 
use App\Models\OrangHilang;
use App\Models\LaporanMatiMengejut;

class KertasSiasatanController extends Controller
{

    protected $validPaperTypes = [
        'Jenayah' => Jenayah::class,
        'Narkotik' => Narkotik::class,
        'Komersil' => Komersil::class,
        'Trafik' => Trafik::class,
        'OrangHilang' => OrangHilang::class,
        'LaporanMatiMengejut' => LaporanMatiMengejut::class,
    ];


    public function show($paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        $paper = $modelClass::findOrFail($id);

        $viewFolderName = Str::snake($paperType); 
        $viewName = 'kertas_siasatan.' . $viewFolderName . '.show';
        
        return view($viewName, [
            'paper' => $paper,
            'paperType' => Str::headline($paperType)
        ]);
    }

    public function edit($paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        $paper = $modelClass::findOrFail($id);

        $viewFolderName = Str::snake($paperType);
        $viewName = 'kertas_siasatan.' . $viewFolderName . '.edit';

        return view($viewName, [
            'paper' => $paper,
            'paperType' => Str::headline($paperType)
        ]);
    }


    public function update(Request $request, $paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        $paper = $modelClass::findOrFail($id);

        $paper->update($request->all());

        return Redirect::route('projects.show', $paper->project_id)
                       ->with('success', Str::headline($paperType) . ' paper updated successfully.');
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
}