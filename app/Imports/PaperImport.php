<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PaperImport implements ToModel, WithHeadingRow, WithUpserts, SkipsOnFailure, WithEvents, WithValidation
{
    protected $projectId;
    protected $paperType;
    protected $modelClass;
    private $config;

    /**
     * Centralized configuration for all paper types.
     * This is the single source of truth for the import logic.
     * 'unique_by' is the database column.
     * 'column_map' is [ 'excel_header_snake_case' => 'database_column_name' ].
     */
    private static $paperConfig = [
        'Jenayah' => [
            'model'       => \App\Models\Jenayah::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_ks',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis',
            ],
        ],
        'Narkotik' => [
            'model'       => \App\Models\Narkotik::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_ksiasatan' => 'no_ks', // Note the header is 'NO K/SIASATAN'
                'peg_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis',
            ],
        ],
        'Komersil' => [
            'model'       => \App\Models\Komersil::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_ks',
                'pegawai_siasatan' => 'pegawai_siasatan',
                'seksyen' => 'seksyen',
                'tarikh_kertas_siasatan_dibuka' => 'tarikh_ks_dibuka',
            ],
        ],
        'Trafik' => [
            'model'       => \App\Models\Trafik::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_ks',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_daftar' => 'tarikh_daftar',
            ],
        ],
        'OrangHilang' => [
            'model'       => \App\Models\OrangHilang::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_ks',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_kertas_siasatan' => 'tarikh_ks',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis_sistem',
            ],
        ],
        'LaporanMatiMengejut' => [
            'model'       => \App\Models\LaporanMatiMengejut::class,
            'unique_by'   => 'no_sdr_lmm',
            'column_map'  => [
                'no_sdrllm' => 'no_sdr_lmm',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'no_laporan_polis' => 'no_laporan_polis',
                'tarkh_laporan_polis' => 'tarikh_laporan_polis', // Corrected common typo
                'tarikh_laporan_polis' => 'tarikh_laporan_polis', // Allow both
            ],
        ],
    ];

    public function __construct(int $projectId, string $paperType)
    {
        if (!isset(self::$paperConfig[$paperType])) {
            throw new \InvalidArgumentException("Invalid paper type specified: {$paperType}");
        }

        $this->projectId = $projectId;
        $this->paperType = $paperType;
        $this->config = self::$paperConfig[$paperType];
        $this->modelClass = $this->config['model'];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                // Expected headers are the keys of the column map (the Excel headers)
                $expectedHeaders = array_keys($this->config['column_map']);
                $actualHeaders = array_map(fn($h) => Str::snake(trim(strtolower($h))), $event->getReader()->getActiveSheet()->toArray()[0]);
                
                // We only check if at least one of the expected headers is present to allow flexibility.
                // A more robust check might be needed depending on requirements.
                $foundHeaders = array_intersect($expectedHeaders, $actualHeaders);

                if (empty($foundHeaders)) {
                    $message = 'Fail tidak dapat diimport. Pastikan fail Excel mempunyai sekurang-kurangnya satu lajur yang sepadan: ' . implode(', ', $expectedHeaders);
                    throw ValidationException::withMessages(['excel_file' => $message]);
                }
            },
        ];
    }

    public function model(array $row)
    {
        $data = ['project_id' => $this->projectId];
        $uniqueDbColumn = $this->config['unique_by'];
        $uniqueValue = null;

        foreach ($this->config['column_map'] as $excelHeader => $dbColumn) {
            if (!isset($row[$excelHeader])) continue; // Skip if header doesn't exist in the row

            $value = $row[$excelHeader];
            $isDateColumn = str_contains($dbColumn, 'tarikh') || str_contains($dbColumn, '_at');
            $data[$dbColumn] = $isDateColumn ? $this->transformDate($value) : (is_string($value) ? trim($value) : $value);
            
            if ($dbColumn === $uniqueDbColumn) {
                $uniqueValue = $data[$dbColumn];
            }
        }
        
        // Ensure the unique key has a value before trying to update/create
        if (empty($uniqueValue)) {
            return null; // Skip this row if the unique identifier is missing
        }

        return $this->modelClass::updateOrCreate(
            [$uniqueDbColumn => $uniqueValue],
            $data
        );
    }

    public function rules(): array
    {
        // Dynamically create validation rules based on the configuration.
        // This ensures that at least the unique identifier's column is required.
        $rules = [];
        $uniqueDbColumn = $this->config['unique_by'];
        
        // Find the Excel header that maps to the unique DB column
        $uniqueExcelHeader = array_search($uniqueDbColumn, $this->config['column_map']);

        if ($uniqueExcelHeader) {
            $rules[$uniqueExcelHeader] = 'required|string|max:255';
        }
        
        // You can add more general validation rules here if needed
        // e.g., 'seksyen' => 'nullable|string'

        return $rules;
    }

    public function customValidationMessages()
    {
        $messages = [];
        $uniqueDbColumn = $this->config['unique_by'];
        $uniqueExcelHeader = array_search($uniqueDbColumn, $this->config['column_map']);

        if ($uniqueExcelHeader) {
             $messages["{$uniqueExcelHeader}.required"] = "Lajur pengenalan unik '{$uniqueExcelHeader}' diperlukan untuk setiap baris.";
        }
       
        return $messages;
    }
    
    /**
     * This method is required by the WithUpserts interface.
     * It tells the package which column to use to find an existing record.
     */
    public function uniqueBy(): string
    {
        return $this->config['unique_by'];
    }

    private function transformDate($value, $format = 'Y-m-d')
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            try { return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format); } catch (\Exception $e) { return null; }
        }
        $formatsToTry = ['d/m/Y', 'd.m.Y', 'd-m-Y', 'm/d/Y', 'm.d.Y', 'm-d-Y', 'Y-m-d H:i:s', 'Y-m-d'];
        foreach ($formatsToTry as $inputFormat) {
            try { return Carbon::createFromFormat($inputFormat, $value)->format($format); } catch (\Exception $e) { continue; }
        }
        try { return Carbon::parse($value)->format($format); } catch (\Exception $e) { Log::warning("Could not parse date format for value: '{$value}'. Error: " . $e->getMessage()); return null; }
    }
    
    public function onFailure(Failure ...$failures)
    {
        // You can log these failures or pass them back to the controller
        Log::error("Excel Import Validation Failures for type: {$this->paperType}", ['failures' => $failures]);

        // To stop the import and show errors to the user, re-throw a ValidationException
        $errorMessages = [];
        foreach ($failures as $failure) {
            $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
        
        throw ValidationException::withMessages([
            'excel_file' => 'Terdapat ralat pada baris berikut dalam fail anda:',
            'excel_errors' => $errorMessages
        ]);
    }
}