<?php

namespace App\Imports;

use App\Models\KertasSiasatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation; // For basic validation
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KertasSiasatanImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // --- CRUCIAL: IPRS Data Lookup ---
        // Option A: Assume KS No. is provided in Excel and needs lookup for other fields
        $no_ks = $row['no_kertas_siasatan']; // Adjust heading name as per your Excel file
        // $iprsData = $this->lookupIprsData($no_ks); // Implement this lookup function

        // Option B: Assume all 7 fields are directly in the Excel file
        // Validate Tarikh KS format (Excel might have different formats)
        $tarikh_ks = null;
        if (!empty($row['tarikh_ks'])) {
             try {
                 // Try parsing common Excel date formats (numeric or string)
                 if (is_numeric($row['tarikh_ks'])) {
                     $tarikh_ks = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tarikh_ks'])->format('Y-m-d');
                 } else {
                      $tarikh_ks = Carbon::parse($row['tarikh_ks'])->format('Y-m-d');
                 }
             } catch (\Exception $e) {
                 // Handle invalid date format if necessary, maybe skip row or log error
                 // For now, we set it to null if parsing fails
                 Log::warning("Invalid date format for KS {$row['no_kertas_siasatan']}: {$row['tarikh_ks']}");
                 $tarikh_ks = null;
             }
        }


        // --- Create or Update Logic ---
        // Use updateOrCreate to handle existing KS numbers if the Excel might contain updates
        return KertasSiasatan::updateOrCreate(
            [
                'no_ks' => $row['no_kertas_siasatan'] // Key to find existing record
            ],
            [
                // Fields from Excel/IPRS Lookup
                // Adjust heading names ('tarikh_ks', 'no_repot' etc.) to match your Excel file exactly!
                'tarikh_ks'         => $tarikh_ks,
                'no_report'         => $row['no_repot'] ?? null,
                'jenis_jabatan_ks'  => $row['jenis_jabatan_ks'] ?? null, // Use actual column names from Excel
                'pegawai_penyiasat' => $row['pegawai_penyiasat'] ?? null,
                'status_ks'         => $row['status_ks'] ?? null,
                'status_kes'        => $row['status_kes'] ?? null,
                'seksyen'           => $row['seksyen'] ?? null,
                // Add defaults for other fields if needed, otherwise they remain null
            ]
        );
    }

    // Define validation rules for Excel columns
    public function rules(): array
    {
        return [
             // Adjust heading names to match your Excel file!
            '*.no_kertas_siasatan' => ['required', 'string', 'distinct'], // Ensure KS No. is present and unique within the file
            '*.tarikh_ks' => ['nullable'], // Allow empty, handle format in model()
            '*.no_repot' => ['nullable', 'string'],
            '*.jenis_jabatan_ks' => ['nullable', 'string'],
            '*.pegawai_penyiasat' => ['nullable', 'string'],
            '*.status_ks' => ['nullable', 'string'],
            '*.status_kes' => ['nullable', 'string'],
            '*.seksyen' => ['nullable', 'string'],
        ];
    }

    // Implement your IPRS data lookup if needed
    /*
    private function lookupIprsData(string $no_ks)
    {
        // Connect to IPRS DB, read file, or call API
        // Return an array or object with fields: tarikh_ks, no_report, ...
        // Example:
        // return IprsDataSource::find($no_ks); // Assuming an IprsDataSource model/service
        return [
            'tarikh_ks' => '2020-01-10', // Dummy data
            'no_report' => 'TRIANG/000118/20',
            // ... other fields
        ];
    }
    */
}