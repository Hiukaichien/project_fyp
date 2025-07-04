<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaperImport implements ToModel, WithHeadingRow, WithUpserts, SkipsOnFailure
{
    protected $projectId;
    protected $paperType;
    protected $modelClass;

    /**
     * Constructor to accept the project and paper type.
     *
     * @param int $projectId
     * @param string $paperType The class name of the model (e.g., 'JenayahPaper')
     */
    public function __construct(int $projectId, string $paperType)
    {
        $this->projectId = $projectId;
        $this->paperType = $paperType;
        $this->modelClass = 'App\\Models\\' . $paperType;

        if (!class_exists($this->modelClass)) {
            throw new \InvalidArgumentException("Model class not found for paper type: {$paperType}");
        }
    }

    /**
     * Process each row from the Excel file.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $columnMap = $this->getColumnMapping();
        $uniqueDbColumn = $this->uniqueBy();

        // Find the Excel header name for the unique DB column
        $uniqueExcelHeader = array_search($uniqueDbColumn, $columnMap);
        if (!$uniqueExcelHeader || !isset($row[$uniqueExcelHeader]) || empty($row[$uniqueExcelHeader])) {
            Log::warning("Skipping row due to missing or empty unique key '{$uniqueExcelHeader}' for paper type {$this->paperType}.", ['row' => $row]);
            return null; // Skip this row if the unique key is not present or empty
        }

        $data = ['project_id' => $this->projectId];

        // Dynamically build the data array from the Excel row using the map
        foreach ($columnMap as $dbColumn => $excelHeader) {
            $value = $row[$excelHeader] ?? null;

            // Check if the attribute is a date field and transform it
            $isDateColumn = str_contains($dbColumn, 'tarikh') || str_contains($dbColumn, '_at');
            if ($isDateColumn) {
                $data[$dbColumn] = $this->transformDate($value);
            } else {
                $data[$dbColumn] = $value;
            }
        }

        // Use the dynamic model class to create or update the record
        return $this->modelClass::updateOrCreate(
            [$uniqueDbColumn => $data[$uniqueDbColumn]], // Unique identifier
            $data                                       // Data to fill/update
        );
    }

    /**
     * Defines the unique database column for each paper type to handle upserts.
     * This must be a column in your database table.
     */
    public function uniqueBy(): string
    {
        switch ($this->paperType) {
            case 'TrafikRulePaper':
            case 'TrafikSeksyenPaper':
                return 'no_kst'; // From 'trafik.csv', the main identifier is 'no_kertas_siasatan'
            case 'LaporanMatiMengejutPaper':
                return 'no_lmm'; // From 'laporanmati.csv', the header is 'no_sdr_llm'
            case 'OrangHilangPaper':
                return 'no_ks_oh'; // From 'oranghilang.csv', the header is 'no_kertas_siasatan'
            case 'JenayahPaper':
            case 'NarkotikPaper':
            case 'KomersilPaper':
            default:
                return 'no_ks';
        }
    }

    /**
     * Gets the specific Excel-to-Database column mapping for each paper type.
     * The key is the database column name (model attribute).
     * The value is the snake_case version of the Excel header that `WithHeadingRow` generates.
     */
    private function getColumnMapping(): array
    {
        // Define base mappings that might be common, though specifics are safer
        $baseMapping = [
            'pegawai_penyiasat' => 'pegawai_penyiasat',
            'seksyen'           => 'seksyen',
        ];

        switch ($this->paperType) {
            case 'JenayahPaper':
                return [
                    'no_ks' => 'no_kertas_siasatan',
                    'io_aio' => 'pegawai_penyiasat',
                    'seksyen_dibuka' => 'seksyen',
                    'tarikh_laporan_polis' => 'tarikh_laporan_polis',
                    'pegawai_pemeriksa_jips' => 'pegawai_pemeriksa_jips_bukit_aman',
                    'tarikh_minit_a' => 'tarikh_edaran_pertama',
                    'tarikh_minit_d' => 'tarikh_edaran_akhir',
                    // Add other Jenayah specific mappings here
                ];

            case 'NarkotikPaper':
                return [
                    'no_ks' => 'no_k_siasatan',
                    'io_aio' => 'peg_penyiasat',
                    'tarikh_laporan_polis' => 'tarikh_laporan_polis',
                    'seksyen_dibuka' => 'seksyen',
                    'tarikh_minit_a' => 'tarikh_edaran_pertama',
                    'tarikh_minit_d' => 'tarikh_edaran_akhir',
                    // Add other Narkotik specific mappings here
                ];

            case 'KomersilPaper':
                return [
                    'no_ks' => 'no_kertas_siasatan',
                    'io_aio' => 'pegawai_siasatan',
                    'seksyen_dibuka' => 'seksyen',
                    'tarikh_ks_dibuka' => 'tarikh_kertas_siasatan_dibuka',
                    'pegawai_pemeriksa_jips' => 'pegawai_pemeriksa_jips',
                    'tarikh_minit_a' => 'tarikh_edaran_pertama',
                    'tarikh_minit_d' => 'tarikh_edaran_akhir',
                    // Add other Komersil specific mappings here
                ];

            case 'TrafikSeksyenPaper':
            case 'TrafikRulePaper':
                return [
                    'no_kst' => 'no_kertas_siasatan',
                    'io_aio' => 'pegawai_penyiasat',
                    'seksyen_dibuka' => 'seksyen',
                    'tarikh_kst_dibuka' => 'tarikh_daftar',
                    'no_saman' => 'no_saman',
                    'pegawai_pemeriksa_jips' => 'pegawai_pemeriksa_jips',
                    'tarikh_minit_a' => 'tarikh_edaran_pertama',
                    'tarikh_minit_d' => 'tarikh_minit_akhir',
                    // Add other Trafik specific mappings here
                ];

            case 'OrangHilangPaper':
                return [
                    'no_ks_oh' => 'no_kertas_siasatan',
                    'io_aio' => 'pegawai_penyiasat',
                    'tarikh_laporan_polis' => 'tarikh_laporan_polis',
                    'tarikh_ks_oh_dibuka' => 'tarikh_kertas_siasatan',
                    'tarikh_minit_a' => 'tarikh_edaran_pertama',
                    'tarikh_minit_d' => 'tarikh_edaran_akhir',
                    // Add other Orang Hilang specific mappings here
                ];
            
            case 'LaporanMatiMengejutPaper':
                return [
                    'no_lmm' => 'no_sdrllm', // Note the header is 'NO SDR/LLM', which becomes 'no_sdrllm'
                    'io_aio' => 'pegawai_penyiasat',
                    'no_repot_polis' => 'no_laporan_polis',
                    'tarikh_laporan_polis_header' => 'tarkh_laporan_polis',
                    'pegawai_pemeriksa_jips' => 'pegawai_pemeriksa_jips',
                    'tarikh_minit_a' => 'tarikh_edaran_pertama',
                    'tarikh_minit_d' => 'tarikh_edaran_akhir',
                    // Add other LMM specific mappings here
                ];

            default:
                // Fallback or default mapping if needed, e.g., for the original KertasSiasatan
                return [
                    'no_ks'             => 'no_kertas_siasatan',
                    'tarikh_ks'         => 'tarikh_ks',
                    'no_report'         => 'no_repot',
                    'pegawai_penyiasat'  => 'pegawai_penyiasat',
                    'status_ks'          => 'status_ks',
                    'status_kes'         => 'status_kes',
                    'seksyen'            => 'seksyen',
                ];
        }
    }

    /**
     * Helper function to parse dates from various common formats.
     */
    private function transformDate($value, $format = 'Y-m-d')
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel's numeric date format
        if (is_numeric($value)) {
            try {
                // The third parameter (timezone) is optional but good practice
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
            } catch (\Exception $e) {
                // It's a number but not a valid Excel date, so we can't process it as a date.
                return null;
            }
        }

        // Handle string formats
        $formatsToTry = [
            'd/m/Y', 'd.m.Y', 'd-m-Y',
            'm/d/Y', 'm.d.Y', 'm-d-Y',
            'Y-m-d H:i:s', 'Y-m-d',
        ];

        foreach ($formatsToTry as $inputFormat) {
            try {
                return Carbon::createFromFormat($inputFormat, $value)->format($format);
            } catch (\Exception $e) {
                continue; // Try next format
            }
        }
        
        // Final attempt with PHP's flexible parser
        try {
            return Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            Log::warning("Could not parse date format for value: '{$value}'. Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Handles validation failures.
     * This method is part of the SkipsOnFailure concern.
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error("Excel Import Failure", [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }
}