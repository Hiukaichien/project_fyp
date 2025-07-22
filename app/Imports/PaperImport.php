<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class PaperImport implements ToCollection, WithHeadingRow, WithEvents
{
    protected $projectId;
    protected $userId;
    protected $paperType;
    protected $modelClass;
    private $config;
    
    private $successCount = 0;
    private $skippedRows = [];

    private static $paperConfig = [
        'Jenayah' => [
            'model'       => \App\Models\Jenayah::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
            ],
        ],
        'Narkotik' => [
            'model'       => \App\Models\Narkotik::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
            ],
        ],
        'Komersil' => [
            'model'       => \App\Models\Komersil::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
            ],
        ],
        'TrafikSeksyen' => [ 
            'model'       => \App\Models\TrafikSeksyen::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
            ],
        ],
            'TrafikRule' => [ 
            'model'       => \App\Models\TrafikRule::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
            ],
        ],
        'OrangHilang' => [
            'model'       => \App\Models\OrangHilang::class,
           'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'seksyen' => 'seksyen',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
            ],
        ],
        'LaporanMatiMengejut' => [
            'model'       => \App\Models\LaporanMatiMengejut::class,
            'unique_by'   => 'no_sdr_lmm',
            'column_map'  => [
                'no_sdr_lmm' => 'no_sdr_lmm',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'no_laporan_polis' => 'no_laporan_polis',
                'tarikh_laporan_polis' => 'tarikh_laporan_polis',
            ],
        ],
    ];

    public function __construct(int $projectId, int $userId, string $paperType)
    {
        if (!isset(self::$paperConfig[$paperType])) {
            throw new \InvalidArgumentException("Invalid paper type specified: {$paperType}");
        }
        $this->projectId = $projectId;
        $this->userId = $userId;
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
                    $message = 'Import failed. Please ensure the Excel file has the required columns.';
                    throw ValidationException::withMessages(['excel_file' => $message]);
                }
            },
        ];
    }

    public function collection(Collection $rows)
    {
        $uniqueDbColumn = $this->config['unique_by'];
        $uniqueExcelHeaderSnake = array_search($uniqueDbColumn, $this->config['column_map']);
        
        if (!$uniqueExcelHeaderSnake) {
             throw new \Exception("Configuration error for {$this->paperType}.");
        }

        // Get all existing unique keys for this user to check against in memory
        $existingKeys = $this->modelClass::query()
            ->whereHas('project', fn($query) => $query->where('user_id', $this->userId))
            ->pluck($uniqueDbColumn)
            ->all();

        $dataToInsert = [];
        $rowNumber = 2;

        foreach ($rows as $row) {
            $uniqueValue = $row[$uniqueExcelHeaderSnake] ?? null;

            if (empty($uniqueValue)) {
                $this->skippedRows[] = "Row {$rowNumber}: Skipped because the unique identifier is missing.";
                $rowNumber++;
                continue;
            }

            // Check against both DB records and records in the current file to prevent duplicates
            if (in_array($uniqueValue, $existingKeys)) {
                $this->skippedRows[] = "Row {$rowNumber}: Skipped because record '{$uniqueValue}' already exists in your projects.";
                $rowNumber++;
                continue;
            }

            $data = [
                'project_id' => $this->projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            foreach ($this->config['column_map'] as $excelHeaderSnake => $dbColumn) {
                if (isset($row[$excelHeaderSnake])) {
                    $value = $row[$excelHeaderSnake];
                    $isDateColumn = str_contains($dbColumn, 'tarikh') || str_contains($dbColumn, '_at');
                    $data[$dbColumn] = $isDateColumn ? $this->transformDate($value) : (is_string($value) ? trim($value) : $value);
                }
            }
            
            $dataToInsert[] = $data;
            $existingKeys[] = $uniqueValue; // Add to our in-memory list
            $this->successCount++;
            $rowNumber++;
        }

        if (!empty($dataToInsert)) {
            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                $this->modelClass::insert($chunk);
            }
        }
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

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }
}