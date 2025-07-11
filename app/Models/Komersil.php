<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Komersil extends Model
{
    use HasFactory;

    protected $table = 'komersil';

    /**
     * An empty guarded array allows for mass assignment on all attributes.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'project_id' => 'integer',
        'tarikh_ks_dibuka' => 'date:Y-m-d',
        'tarikh_minit_pertama' => 'date:Y-m-d',
        'tarikh_minit_akhir' => 'date:Y-m-d',
        'tarikh_permohonan_telco' => 'date:Y-m-d', // Added from CSV analysis
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

  
   public function calculateEdaranLebih48Jam()
    {
        // If tarikh_minit_akhir is filled, the case is not considered late.
        if ($this->tarikh_minit_akhir) {
            $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH';
            return;
        }

        // If tarikh_minit_akhir is not filled, check against tarikh_minit_pertama.
        if ($this->tarikh_minit_pertama) {
            $tarikhPertama = $this->tarikh_minit_pertama;
            
            if ($tarikhPertama->diffInHours(Carbon::now()) > 48) {
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
        // Check if both minute dates are empty and there is an opening date.
        if (is_null($this->tarikh_minit_pertama) && is_null($this->tarikh_minit_akhir) && $this->tarikh_daftar) {
            $openingDate = $this->tarikh_daftar; 

            // Check if 3 months have passed since the opening date.
            if ($openingDate->diffInMonths(Carbon::now()) >= 3) {
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            // If any minute date is filled, or no opening date, it's not considered abandoned by this rule.
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
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