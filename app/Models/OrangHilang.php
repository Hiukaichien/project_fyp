<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrangHilang extends Model
{
    use HasFactory;

    protected $table = 'orang_hilang';

    /**
     * All attributes are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'project_id' => 'integer',
        'tarikh_ks' => 'date:Y-m-d',
        'tarikh_laporan_polis_sistem' => 'date:Y-m-d',
        'tarikh_minit_pertama' => 'date:Y-m-d',
        'tarikh_minit_akhir' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that this paper belongs to.
     */
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
        $this->calculateEdaranLebih48Jam();
        $this->calculateTerbengkalai3Bulan();
        $this->calculateBaruKemaskini();
    }

    /**
     * Calculates if the first minute distribution was late.
     */
    public function calculateEdaranLebih48Jam()
    {
        if ($this->tarikh_ks && $this->tarikh_minit_pertama) {
            $tarikhBuka = $this->tarikh_ks->startOfDay();
            $tarikhA = $this->tarikh_minit_pertama->startOfDay();
            
            if ($tarikhA->isAfter($tarikhBuka) && $tarikhA->diffInHours($tarikhBuka) > 48) {
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
            $tarikhA = $this->tarikh_minit_pertama;
            $tarikhD = $this->tarikh_minit_akhir;

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