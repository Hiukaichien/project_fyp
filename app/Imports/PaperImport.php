<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
     * The keys of 'column_map' MUST be the snake_case version of the Excel header.
     */
    private static $paperConfig = [
        'Jenayah' => [
            'model'       => \App\Models\Jenayah::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_ks' => 'no_ks', // CSV header is 'no_ks'
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis',
            ],
        ],
        'Narkotik' => [
            'model'       => \App\Models\Narkotik::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_ks' => 'no_ks', // CSV header is 'no_ks'
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis',
            ],
        ],
        'Komersil' => [
            'model'       => \App\Models\Komersil::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_ks' => 'no_ks', // CSV header is 'no_ks'
                'pegawai_siasatan' => 'pegawai_siasatan',
                'seksyen' => 'seksyen',
                'tarikh_ks_dibuka' => 'tarikh_ks_dibuka',
            ],
        ],
        'Trafik' => [
            'model'       => \App\Models\Trafik::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_ks' => 'no_ks', // CSV header is 'no_ks'
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_daftar' => 'tarikh_daftar',
            ],
        ],
        'OrangHilang' => [
            'model'       => \App\Models\OrangHilang::class,
            'unique_by'   => 'no_ks',
            'column_map'  => [
                'no_ks' => 'no_ks', // CSV header is 'no_ks'
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_sistem' => 'tarikh_laporan_polis_sistem',
                'tarikh_ks' => 'tarikh_ks',
            ],
        ],
        'LaporanMatiMengejut' => [
            'model'       => \App\Models\LaporanMatiMengejut::class,
            'unique_by'   => 'no_sdr_lmm',
            'column_map'  => [
                'no_sdr_lmm' => 'no_sdr_lmm', // CSV header is 'no_sdr_lmm'
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'no_laporan_polis' => 'no_laporan_polis',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis',
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
                $expectedHeaders = array_keys($this->config['column_map']);
                $actualHeaders = array_map(fn($h) => Str::snake(trim($h)), $event->getReader()->getActiveSheet()->toArray()[0]);
                
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

        foreach ($this->config['column_map'] as $excelHeaderSnake => $dbColumn) {
            if (!isset($row[$excelHeaderSnake])) continue; 

            $value = $row[$excelHeaderSnake];
            $isDateColumn = str_contains($dbColumn, 'tarikh') || str_contains($dbColumn, '_at');
            $data[$dbColumn] = $isDateColumn ? $this->transformDate($value) : (is_string($value) ? trim($value) : $value);
            
            if ($dbColumn === $uniqueDbColumn) {
                $uniqueValue = $data[$dbColumn];
            }
        }
        
        if (empty($uniqueValue)) {
            return null; 
        }

        return $this->modelClass::updateOrCreate(
            [$uniqueDbColumn => $uniqueValue],
            $data
        );
    }

    public function rules(): array
    {
        $rules = [];
        $uniqueDbColumn = $this->config['unique_by'];
        
        $uniqueExcelHeaderSnake = array_search($uniqueDbColumn, $this->config['column_map']);

        if ($uniqueExcelHeaderSnake) {
            $rules[$uniqueExcelHeaderSnake] = 'required|string|max:255';
        }

        return $rules;
    }

    public function customValidationMessages()
    {
        $messages = [];
        $uniqueDbColumn = $this->config['unique_by'];
        $uniqueExcelHeaderSnake = array_search($uniqueDbColumn, $this->config['column_map']);

        if ($uniqueExcelHeaderSnake) {
             $messages["{$uniqueExcelHeaderSnake}.required"] = "The unique identifier column '{$uniqueExcelHeaderSnake}' is required for every row.";
        }
       
        return $messages;
    }
    
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
        Log::error("Excel Import Validation Failures for type: {$this->paperType}", ['failures' => $failures]);

        $errorMessages = [];
        foreach ($failures as $failure) {
            $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        
        throw ValidationException::withMessages([
            'excel_file' => 'Terdapat ralat pada baris berikut dalam fail anda:',
            'excel_errors' => $errorMessages
        ]);
    }
}