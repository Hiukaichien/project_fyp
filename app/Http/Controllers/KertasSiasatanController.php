<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\Komersil;
use App\Models\TrafikSeksyen;
use App\Models\TrafikRule; 
use App\Models\OrangHilang;
use App\Models\LaporanMatiMengejut;

class KertasSiasatanController extends Controller
{
    protected $validPaperTypes = [
        'Jenayah' => Jenayah::class,
        'Narkotik' => Narkotik::class,
        'Komersil' => Komersil::class,
        'TrafikSeksyen' => TrafikSeksyen::class,
        'TrafikRule' => TrafikRule::class,
        'OrangHilang' => OrangHilang::class,
        'LaporanMatiMengejut' => LaporanMatiMengejut::class,
    ];

    public function show($paperType, $id)
    {
        $modelClass = $this->getModelClass($paperType);
        // Eager load the project relationship for the authorization check
        $paper = $modelClass::with('project')->findOrFail($id);

        Gate::authorize('access-project', $paper->project);

        // This block is only relevant if you have calculateStatuses on the model.
        // As per the provided LaporanMatiMengejut model, it has applyClientSpecificCalculations in the boot method,
        // which means it runs automatically on saving, not necessarily on retrieval via a `calculateStatuses` method.
        // If your TrafikSeksyen model also uses the 'saving' event, this `if` block can be removed.
        // If you actually have a dedicated `calculateStatuses` method for display-time calculations, keep it.
        /*
        if (method_exists($paper, 'calculateStatuses')) {
            $paper->calculateStatuses();
        }
        */

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
        $paper = $modelClass::with('project')->findOrFail($id);

        Gate::authorize('access-project', $paper->project);

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
        $paper = $modelClass::findOrFail($id); // No need for with('project') here

        Gate::authorize('access-project', $paper->project);

        // Get all the data from the form.
        $data = $request->all();

        // --- START: SPECIAL HANDLING FOR SINGLE-SELECT RADIO FIELDS WITH 'Lain-lain' TEXT INPUT ---
        // This block now handles both LaporanMatiMengejut and TrafikSeksyen
        if ($paperType === 'LaporanMatiMengejut' || $paperType === 'TrafikSeksyen') {
            $singleSelectFieldsWithOtherInput = [];

            if ($paperType === 'LaporanMatiMengejut') {
                $singleSelectFieldsWithOtherInput = [
                    'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes_lain',
                    'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan_lain', 
                    'kaedah_pelupusan_barang_kes' => 'kaedah_pelupusan_barang_kes_lain'
                ];
            } elseif ($paperType === 'TrafikSeksyen') {
                $singleSelectFieldsWithOtherInput = [
                    'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes_lain',
                    'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan_lain',
                    'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'kaedah_pelupusan_barang_kes_lain' // Note: This field's 'lain' column is named 'kaedah_pelupusan_barang_kes_lain' in TrafikSeksyen migration
                ];
            }
            
            foreach ($singleSelectFieldsWithOtherInput as $mainField => $lainField) {
                if ($request->has($mainField)) {
                    $value = $request->input($mainField);
                    
                    // If "Lain-lain" is selected for the main radio option
                    if ($value === 'Lain-lain') {
                        // Use the content of the `_lain` text field if it's provided and not empty
                        if ($request->has($lainField) && !empty($request->input($lainField))) {
                            $data[$mainField] = $request->input($lainField);
                        } else {
                            // Otherwise, store "Lain-lain" (or an empty string if you prefer empty if no text)
                            $data[$mainField] = $value;
                        }
                        // Remove the separate text field from the data array to prevent it being saved under its own column IF the main field now stores the text.
                        // However, if your migration adds `_lain` columns specifically for this, you should keep it in $data.
                        // Based on your migration, you *do* have `_lain` columns, so we set them properly.
                        $data[$lainField] = $request->input($lainField) ?? null; // Ensure `_lain` column is saved
                    } else {
                        // If a specific option (not "Lain-lain") is selected
                        $data[$mainField] = $value;
                        // Clear the corresponding `_lain` field
                        $data[$lainField] = null;
                    }
                } else {
                    // If the main radio group is not present in the request (e.g., no option selected),
                    // ensure both main and lain fields are null.
                    $data[$mainField] = null;
                    $data[$lainField] = null;
                }
            }
        }
        // --- END: SPECIAL HANDLING ---


        if ($paperType === 'Narkotik') {
            // Special handling for Narkotik fields that have multiple options with 'Lain-lain' and special cases.
            $singleSelectFieldsWithOtherInput = [
                'status_pergerakan_barang_kes' => [
                    'lain' => 'status_pergerakan_barang_kes_lain',
                    'special' => [ // Special cases for Narkotik
                        'Ujian Makmal' => 'status_pergerakan_barang_kes_makmal',
                    ],
                ],
                'status_barang_kes_selesai_siasatan' => [
                    'lain' => 'status_barang_kes_selesai_siasatan_lain',
                    'special' => [
                        'Dilupuskan ke Perbendaharaan' => 'status_barang_kes_selesai_siasatan_RM',
                    ],
                ],
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => [
                    'lain' => 'kaedah_pelupusan_barang_kes_lain',
                ],
            ];

            foreach ($singleSelectFieldsWithOtherInput as $mainField => $config) {
                $lainField = $config['lain'] ?? null;
                $specials = $config['special'] ?? [];

                $value = $request->input($mainField);

                // 1. handle 'Lain-Lain' option (note: uppercase L)
                if ($value === 'Lain-Lain') {
                    if ($lainField) {
                        // Only save if 'Lain-Lain' is selected and content is not empty
                        $data[$lainField] = $request->input($lainField) ?? null;
                    }
                } else {
                    // Clear lain field for other options
                    if ($lainField) {
                        $data[$lainField] = null;
                    }
                }

                // 2. handle special cases
                // This will only set the special field if the value matches one of the special options.
                foreach ($specials as $option => $specialField) {
                    if ($value === $option) {
                        $data[$specialField] = $request->input($specialField) ?? null;
                    } else {
                        $data[$specialField] = null;
                    }
                }

                // Finally, set the main field value.
                $data[$mainField] = $value ?? null;
            }
        }


        
        // --- START: DEFINITIVE FIX FOR BOOLEAN FIELDS ---
        // Manually process all boolean fields to remove any ambiguity caused by unchecked radios/checkboxes.
        foreach ($paper->getCasts() as $field => $type) {
            if ($type === 'boolean') {
                // If the field exists in the request (i.e., a radio button was selected
                // or a checkbox was checked), explicitly cast its value to a boolean.
                // This correctly handles both "1" (true) and "0" (false).
                if ($request->has($field)) {
                    $data[$field] = (bool)$request->input($field);
                } else {
                    // If the field is NOT in the request (i.e., an unchecked checkbox or a radio group
                    // where '0' is not explicitly sent but no '1' is, or the '0' value of a radio button
                    // if it's the default and not interacted with), we force it to be 'false'.
                    $data[$field] = false;
                }
            }
        }
        // --- END: DEFINITIVE FIX ---

        // Now, update the model with the clean and explicit data.
        $paper->update($data);

        return Redirect::route('projects.show', $paper->project_id)
                       ->with('success', Str::headline($paperType) . ' berjaya dikemaskini.');
    }

    /**
     * Helper to get the model class and validate it.
     */
    private function getModelClass($paperType)
    {
        if (!array_key_exists($paperType, $this->validPaperTypes)) {
            abort(404, 'Jenis kertas yang dinyatakan tidak sah.');
        }
        return $this->validPaperTypes[$paperType];
    }
}