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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithValidation;


class PaperImport implements ToModel, WithHeadingRow, WithUpserts, SkipsOnFailure, WithEvents, WithValidation
{
    protected $projectId;
    protected $paperType;
    protected $modelClass;

    public function __construct(int $projectId, string $paperType)
    {
        $this->projectId = $projectId;
        $this->paperType = $paperType;
        $this->modelClass = 'App\\Models\\' . $paperType;

        if (!class_exists($this->modelClass)) {
            throw new \InvalidArgumentException("Model class not found for paper type: {$paperType}");
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $expectedHeaders = array_values($this->getColumnMapping());
                $actualHeaders = $event->getReader()->getActiveSheet()->toArray()[0];
                $cleanedActualHeaders = array_map(fn($h) => Str::snake(trim(strtolower($h))), $actualHeaders);
                $missingHeaders = array_diff($expectedHeaders, $cleanedActualHeaders);

                if (!empty($missingHeaders)) {
                    $message = 'Fail tidak dapat diimport. Lajur berikut tiada atau salah eja: ' . implode(', ', $missingHeaders);
                    throw ValidationException::withMessages(['excel_file' => $message]);
                }
            },
        ];
    }

    public function model(array $row)
    {
        $columnMap = $this->getColumnMapping();
        $uniqueDbColumn = $this->uniqueBy();
        $uniqueExcelHeader = array_search($uniqueDbColumn, $columnMap);

        $data = ['project_id' => $this->projectId];

        foreach ($columnMap as $dbColumn => $excelHeader) {
            $value = $row[$excelHeader] ?? null;
            $isDateColumn = str_contains($dbColumn, 'tarikh') || str_contains($dbColumn, '_at');
            $data[$dbColumn] = $isDateColumn ? $this->transformDate($value) : (is_string($value) ? trim($value) : $value);
        }

        return $this->modelClass::updateOrCreate(
            [$uniqueDbColumn => $data[$uniqueDbColumn]],
            $data
        );
    }
    
    /**
     * --- FIX: Reverting to the explicit, hardcoded validation logic from your original file ---
     * This is the most reliable way and directly mirrors the code that worked.
     */
    public function rules(): array
    {
        switch ($this->paperType) {
            case 'JenayahPaper':
                return ['no_kertas_siasatan' => 'required|string|max:255'];
            case 'NarkotikPaper':
                return ['no_k_siasatan' => 'required|string|max:255'];
            case 'KomersilPaper':
                return ['no_kertas_siasatan' => 'required|string|max:255'];
            case 'TrafikSeksyenPaper':
            case 'TrafikRulePaper':
                return ['no_kertas_siasatan' => 'required|string|max:255'];
            case 'OrangHilangPaper':
                return ['no_kertas_siasatan' => 'required|string|max:255'];
            case 'LaporanMatiMengejutPaper':
                return ['no_sdrllm' => 'required|string|max:255'];
            case 'KertasSiasatan':
            default:
                return ['no_kertas_siasatan' => 'required|string|max:255'];
        }
    }

    public function customValidationMessages()
    {
        switch ($this->paperType) {
            case 'JenayahPaper':
                return ['no_kertas_siasatan.required' => 'Lajur "no_kertas_siasatan" diperlukan.'];
            case 'NarkotikPaper':
                return ['no_k_siasatan.required' => 'Lajur "no_k_siasatan" diperlukan.'];
            case 'KomersilPaper':
                return ['no_kertas_siasatan.required' => 'Lajur "no_kertas_siasatan" diperlukan.'];
            case 'TrafikSeksyenPaper':
            case 'TrafikRulePaper':
                return ['no_kertas_siasatan.required' => 'Lajur "no_kertas_siasatan" diperlukan.'];
            case 'OrangHilangPaper':
                return ['no_kertas_siasatan.required' => 'Lajur "no_kertas_siasatan" diperlukan.'];
            case 'LaporanMatiMengejutPaper':
                return ['no_sdrllm.required' => 'Lajur "no_sdrllm" diperlukan.'];
            case 'KertasSiasatan':
            default:
                return ['no_kertas_siasatan.required' => 'Lajur "no_kertas_siasatan" diperlukan.'];
        }
    }

    public function uniqueBy(): string
    {
        switch ($this->paperType) {
            case 'TrafikRulePaper':
            case 'TrafikSeksyenPaper':
                return 'no_kst';
            case 'LaporanMatiMengejutPaper':
                return 'no_lmm';
            case 'OrangHilangPaper':
                return 'no_ks_oh';
            default:
                return 'no_ks';
        }
    }

    private function getColumnMapping(): array
    {
        // The list of columns to import. 
        // The database column and Excel header are the SAME for each.
        $columns = [
            'no_ks',
            'pegawai_penyiasat',
            'seksyen',
            'tarikh_laporan_polis', // This will be the primary date column for most
        ];

        // Add specific columns for each paper type if they exist in their respective Excel files
        switch ($this->paperType) {
            case 'KomersilPaper':
                // If Komersil Excel has 'tarikh_ks_dibuka' instead of 'tarikh_laporan_polis'
                $columns = array_diff($columns, ['tarikh_laporan_polis']); // Remove the default date
                $columns[] = 'tarikh_ks_dibuka'; // Add the specific one
                break;

            case 'TrafikSeksyenPaper':
                $columns = array_diff($columns, ['tarikh_laporan_polis']);
                $columns[] = 'tarikh_daftar';
                break;

            case 'OrangHilangPaper':
                $columns = array_diff($columns, ['tarikh_laporan_polis_sistem']);
                $columns[] = 'tarikh_ks';
                $columns[] = 'tarikh_laporan_polis_sistem'; 
                break;
                
            case 'LaporanMatiMengejutPaper':
                // LMM is completely different
                $columns = [
                    'no_sdr_lmm',
                    'pegawai_penyiasat',
                    'no_repot_polis',
                    'tarikh_laporan_polis',
                ];
                break;
        }

        // The array_flip() trick creates the mapping where key equals value.
        // E.g., ['no_ks', 'seksyen'] becomes ['no_ks' => 'no_ks', 'seksyen' => 'seksyen']
        return array_flip($columns);
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
        foreach ($failures as $failure) { 
            Log::error("Excel Import Validation Failure", [
                'row' => $failure->row(), 
                'attribute' => $failure->attribute(), 
                'errors' => $failure->errors(), 
                'values' => $failure->values()
            ]); 
        }
    }
}