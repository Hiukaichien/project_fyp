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
        $paper = $modelClass::findOrFail($id);

        Gate::authorize('access-project', $paper->project);

        // Get all data from the form request.
        $data = $request->all();

        // --- START: SPECIAL HANDLING FOR RADIO BUTTONS WITH EXTRA INPUTS ---

    if ($paperType === 'TrafikSeksyen' || $paperType === 'Narkotik') {
        $config = [];
        
        if ($paperType === 'TrafikSeksyen') {
            $config = [
                'status_pergerakan_barang_kes' => [
                    'lain' => 'status_pergerakan_barang_kes_lain',
                    'special' => [
                        'Ujian Makmal' => 'status_pergerakan_barang_kes_makmal'
                    ]
                ],
                'status_barang_kes_selesai_siasatan' => [
                    'lain' => 'status_barang_kes_selesai_siasatan_lain',
                    'special' => [
                        'Dilupuskan ke Perbendaharaan' => 'status_barang_kes_selesai_siasatan_RM'
                    ]
                ],
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => [
                    'lain' => 'kaedah_pelupusan_barang_kes_lain'
                ],
            ];
        } 
        elseif ($paperType === 'Narkotik') {
            $config = [
                'status_pergerakan_barang_kes' => [
                    'lain' => 'status_pergerakan_barang_kes_lain',
                    'special' => [ 'Ujian Makmal' => 'status_pergerakan_barang_kes_makmal' ],
                ],
                'status_barang_kes_selesai_siasatan' => [
                    'lain' => 'status_barang_kes_selesai_siasatan_lain',
                    'special' => [ 'Dilupuskan ke Perbendaharaan' => 'status_barang_kes_selesai_siasatan_RM' ],
                ],
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => [
                    'lain' => 'kaedah_pelupusan_barang_kes_lain',
                ],
            ];
        }

        foreach ($config as $mainField => $fieldConfig) {
            $lainField = $fieldConfig['lain'] ?? null;
            $specials = $fieldConfig['special'] ?? [];
            $value = $request->input($mainField);

            // Handle "Lain-Lain"
            if ($lainField) {
                $data[$lainField] = ($value === 'Lain-Lain') ? ($request->input($lainField) ?? null) : null;
            }

            // Handle special fields
            foreach ($specials as $option => $specialField) {
                $data[$specialField] = ($value === $option) ? ($request->input($specialField) ?? null) : null;
            }

            $data[$mainField] = $value ?? null;
        }
    }
        // --- END: SPECIAL HANDLING ---


        // --- START: DATE FORMAT CONVERSION DD/MM/YYYY to Y-m-d ---
        // Convert DD/MM/YYYY format to Y-m-d format for database storage
        foreach ($data as $field => $value) {
            // Check if it's a date field (ends with 'tarikh' or contains specific date field names)
            if ((str_ends_with($field, '_tarikh') || in_array($field, [
                'tarikh_laporan_polis_dibuka',
                'tarikh_edaran_minit_ks_pertama', 
                'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah'
            ])) && !empty($value)) {
                // Check if the date is in DD/MM/YYYY format
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                    // Convert DD/MM/YYYY to Y-m-d
                    $dateParts = explode('/', $value);
                    $data[$field] = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                }
            }
        }
        // --- END: DATE FORMAT CONVERSION ---

        // --- START: DEFINITIVE FIX FOR ALL BOOLEAN FIELDS ---
        // This loop ensures that any boolean field (from radio buttons or checkboxes)
        // is explicitly saved as true (1) or false (0), preventing nulls from
        // unchecked boxes or unselected radio options.
        foreach ($paper->getCasts() as $field => $type) {
            if ($type === 'boolean') {
                // If the field is present in the request (value "1" or "0" was sent)
                if ($request->has($field)) {
                    $data[$field] = (bool)$request->input($field);
                } else {
                    // If the field is NOT in the request (e.g., an unchecked checkbox),
                    // force its value to be false.
                    $data[$field] = false;
                }
            }
        }
        // --- END: BOOLEAN FIX ---

        // Update the model with the processed data.
        $paper->update($data);

        // Redirect back to the project page with a success message.
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