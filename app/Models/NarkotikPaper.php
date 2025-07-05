<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NarkotikPaper extends Model
{
    use HasFactory;

    protected $table = 'narkotik_papers';

    /**
     * The attributes that are not mass assignable.
     * An empty array means all attributes are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'project_id' => 'integer',
        'tarikh_laporan_polis' => 'date:Y-m-d',
        'tarikh_minit_pertama' => 'date:Y-m-d',
        'tarikh_minit_akhir' => 'date:Y-m-d',
        'tarikh_urine_dipungut' => 'date:Y-m-d',
        'tarikh_urine_dihantar_ke_patalogi' => 'date:Y-m-d',
        'tarikh_laporan_patalogi_diterima' => 'date:Y-m-d',
        'tarikh_brg_kes_dihantar_kimia' => 'date:Y-m-d',
        'tarikh_laporan_kimia_diterima' => 'date:Y-m-d',
        'tarikh_arahan_tuduh_oleh_tpr' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->applyClientSpecificCalculations();
        });
    }

    /**
     * Apply all business logic calculations before saving.
     */
    public function applyClientSpecificCalculations()
    {
        // This logic uses the available date columns
        $this->calculateEdaranLebih48Jam();
        $this->calculateTerbengkalai3Bulan();
        $this->calculateBaruKemaskini();
    }

    /**
     * Calculates if the first minute distribution was late.
     */
    public function calculateEdaranLebih48Jam()
    {
        if ($this->tarikh_laporan_polis && $this->tarikh_minit_pertama) {
            $tarikhLaporan = Carbon::parse($this->tarikh_laporan_polis)->startOfDay();
            $tarikhA = Carbon::parse($this->tarikh_minit_pertama)->startOfDay();
            
            if ($tarikhA->isAfter($tarikhLaporan) && $tarikhA->diffInHours($tarikhLaporan) > 48) {
                $this->edar_lebih_24_jam_status = 'YA, EDARAN LEWAT 48 JAM';
            } else {
                $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH 48 JAM';
            }
        } else {
            $this->edar_lebih_24_jam_status = null; // Cannot calculate
        }
    }
    
    /**
     * Calculates if the case is abandoned based on the first and last minute dates.
     */
    public function calculateTerbengkalai3Bulan()
    {
        if ($this->tarikh_minit_pertama && $this->tarikh_minit_akhir) {
            $tarikhA = Carbon::parse($this->tarikh_minit_pertama);
            $tarikhD = Carbon::parse($this->tarikh_minit_akhir);

            if ($tarikhD->isAfter($tarikhA) && $tarikhA->diffInMonths($tarikhD) >= 3) {
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI (TIADA DATA)';
        }
    }

    /**
     * A simple logic to check if the record was recently updated.
     */
    public function calculateBaruKemaskini()
    {
        $this->baru_kemaskini_status = 'TIADA PERGERAKAN BARU';
        if ($this->tarikh_minit_akhir && $this->updated_at) {
            if (Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIKEMASKINI';
            }
        }
    }
}