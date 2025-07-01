<?php

namespace App\Imports;

use App\Models\KertasSiasatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// "WithHeadingRow" convert excel heading to snake_case and lowercase. 
// Eg: No. Kertas Siasatan > no_kertas_siasatan

class KertasSiasatanImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation, SkipsOnFailure
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            'no_ks'              => trim($row['no_kertas_siasatan'] ?? ''), //used trim for data consistency
            'tarikh_ks'          => $this->transformDate($row['tarikh_ks'] ?? null), 
            'no_report'          => $row['no_repot'] ?? null,         
            'jenis_jabatan_ks'   => $row['jenis_jabatan_ks'] ?? null,
            'pegawai_penyiasat'  => $row['pegawai_penyiasat'] ?? null, 
            'status_ks'          => $row['status_ks'] ?? null,        
            'status_kes'         => $row['status_kes'] ?? null,       
            'seksyen'            => $row['seksyen'] ?? null,
            'project_id'         => $this->projectId, // Associate with the selected project
        ];

        // Use updateOrCreate to update existing records or create new ones
        // The first argument is the array of attributes to find the record by.
        // The second argument is the array of attributes to update or create with.
        $kertasSiasatan = KertasSiasatan::updateOrCreate(
            ['no_ks' => $data['no_ks']], // Match existing record by 'no_ks'
            $data                       // Data to fill/update
        );
        
        // If you have model events (Observers) that calculate statuses,
        // they should trigger automatically on updateOrCreate.
        // If not, and you need to call them manually after upsert:
        // $kertasSiasatan->calculateEdarLebih24Jam();
        // $kertasSiasatan->calculateTerbengkalai3Bulan();
        // $kertasSiasatan->calculateBaruKemaskini();
        // $kertasSiasatan->save(); // Important if manual calculations modify the model

        return $kertasSiasatan;
    }

    /**
     * Specify the unique column(s) for upserting.
     */
    public function uniqueBy()
    {
        return 'no_ks';
    }

    /**
     * Helper function to parse dates from various common formats.
     */
    private function transformDate($value, $format = 'Y-m-d')
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric date format
        if (is_numeric($value)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
            } catch (\Exception $e) {
                // Fallback if it's not a valid Excel numeric date
            }
        }
        
        $formatsToTry = [
            'd/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'm-d-Y', 'Y/m/d',
            'd/m/Y H:i:s', 'Y-m-d H:i:s',
        ];

        foreach ($formatsToTry as $inputFormat) {
            try {
                return Carbon::createFromFormat($inputFormat, $value)->format($format);
            } catch (\Exception $e) {
                // Continue to next format if parsing fails
            }
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            Log::warning("Could not parse date: \"{$value}\". Error: {$e->getMessage()}");
            return null;
        }
    }

    
     // Validation rules for each row.

    public function rules(): array
    {
        return [
            // Key based on your CSV header "No. Kertas Siasatan"
            'no_kertas_siasatan' => 'required|string|max:255',

            // Add other rules as needed for your Excel columns. For example:
            // 'tarikh_ks' => 'nullable', // Validates the raw value from Excel before transformation
            // 'no_repot' => 'nullable|string|max:255',
        ];
    }

    
    // Handle validation failures
     
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error("Excel Import Validation Failure - Row: {$failure->row()}, Attribute: {$failure->attribute()}, Errors: " . implode(', ', $failure->errors()) . ", Values: " . implode(', ', $failure->values()));
        }
    }


    public function customValidationMessages()
    {
        return [
            'no_kertas_siasatan.required' => 'Lajur "No. Kertas Siasatan" diperlukan dan tidak boleh kosong.',
        ];
    }
}