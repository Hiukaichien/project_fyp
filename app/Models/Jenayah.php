<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jenayah extends Model
{
    use HasFactory;

    protected $table = 'jenayah';
    
    /**
     * The attributes that are not mass assignable.
     * An empty array means all attributes are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     * This ensures data consistency for dates, numbers, etc.
     */
    protected $casts = [
        'project_id' => 'integer',
        'tarikh_laporan_polis' => 'date:Y-m-d',
        'tarikh_minit_pertama' => 'date:Y-m-d',

        'tarikh_minit_akhir' => 'date:Y-m-d',
        'tarikh_akhir_diari_dikemaskini' => 'date:Y-m-d',
        'tarikh_daftar_bk_berharga_tunai' => 'date:Y-m-d',
        'tarikh_daftar_bk_am' => 'date:Y-m-d',
        'tarikh_daftar_bk_kenderaan' => 'date:Y-m-d',
        'tarikh_arahan_tpr_lucut_hak' => 'date:Y-m-d',
        'jumlah_lucut_hak_rm' => 'decimal:2',
        'wang_tunai_lucut_hak_judi' => 'decimal:2',
        'tarikh_arahan_tpr_pulang_bk' => 'date:Y-m-d',
        'jumlah_serah_semula_pemilik_rm' => 'decimal:2',
        'tarikh_serahan_bk_pemilik' => 'date:Y-m-d',
        'rj9_tarikh_cipta' => 'date:Y-m-d',
        'rj99_tarikh_cipta' => 'date:Y-m-d',
        'rj10a_tarikh_cipta' => 'date:Y-m-d',
        'rj10b_tarikh_cipta' => 'date:Y-m-d',
        'rj2_tarikh_cipta' => 'date:Y-m-d',
        'rj2b_tarikh_cipta' => 'date:Y-m-d',
        'rj21_tarikh_cipta' => 'date:Y-m-d',
        'tarikh_mohon_laporan_pakar_judi' => 'date:Y-m-d',
        'tarikh_terima_laporan_pakar_judi' => 'date:Y-m-d',
        'bk_telefon_forensik_tarikh_hantar' => 'date:Y-m-d',
        'tarikh_ks_rujuk_tpr' => 'date:Y-m-d',
        'tarikh_tpr_beri_arahan_tuduh' => 'date:Y-m-d',
        'tarikh_tpr_beri_arahan_nfa' => 'date:Y-m-d',
        'tarikh_tpr_beri_arahan_dnaa' => 'date:Y-m-d',
        'tarikh_keputusan_jatuh_hukum' => 'date:Y-m-d',
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
        // The logic for 'Baru Kemaskini' might need refinement based on business rules
        $this->calculateBaruKemaskini();
    }

    /**
     * Logic based on the 'jenayah.csv' which has 'TARIKH EDARAN PERTAMA' (minit_a)
     * and 'TARIKH EDARAN AKHIR' (minit_d).
     * The concept of "Minit Kedua (B)" does not exist in the new data.
     */
    public function calculateEdaranLebih48Jam()
    {
        // This logic might need adjustment. A common check is the time between
        // the report date and the first minute date.
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

    public function calculateBaruKemaskini()
    {
        $this->baru_kemaskini_status = 'TIADA PERGERAKAN BARU';
        if ($this->tarikh_minit_akhir && $this->updated_at) {
            // Check if the record was updated recently (e.g., within the last 7 days)
            if (Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIKEMASKINI';
            }
        }
    }
}