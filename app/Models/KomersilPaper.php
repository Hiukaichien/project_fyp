<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Kyslik\ColumnSortable\Sortable;

class KomersilPaper extends Model
{
    use HasFactory, Sortable;

    protected $table = 'komersil_papers';
    protected $guarded = [];

    protected $casts = [
        'project_id' => 'integer',
        'tarikh_ks_dibuka' => 'date:Y-m-d',
        'tarikh_laporan_polis' => 'date:Y-m-d',
        'tarikh_minit_a' => 'date:Y-m-d', // Mapped from "TARIKH EDARAN MINIT PERTAMA (A)"
        'tarikh_minit_b' => 'date:Y-m-d', // Mapped from "TARIKH EDARAN MINIT KEDUA (B)"
        'tarikh_minit_c' => 'date:Y-m-d', // Likely null for Komersil, standardized
        'tarikh_minit_d' => 'date:Y-m-d', // Mapped from "TARIKH EDARAN MINIT AKHIR"
        'tarikh_akhir_diari_dikemaskini' => 'date:Y-m-d',
        'tarikh_daftar_bk_berharga_tunai' => 'date:Y-m-d',
        'tarikh_daftar_bk_am' => 'date:Y-m-d',
        'tarikh_daftar_bk_kenderaan' => 'date:Y-m-d',
        'tarikh_arahan_tpr_lucut_hak' => 'date:Y-m-d',
        'jumlah_wang_tunai_lucut_hak_rm' => 'decimal:2',
        'tarikh_arahan_tpr_pulang_bk' => 'date:Y-m-d',
        'wang_tunai_serah_semula_pemilik_rm' => 'decimal:2',
        'tarikh_serahan_bk_pemilik' => 'date:Y-m-d',
        'rj9_tarikh_cipta' => 'date:Y-m-d',
        'rj99_tarikh_cipta' => 'date:Y-m-d',
        'rj10a_tarikh_cipta' => 'date:Y-m-d',
        'rj10b_tarikh_cipta' => 'date:Y-m-d',
        'rj2_tarikh_cipta' => 'date:Y-m-d',
        'rj2b_tarikh_cipta' => 'date:Y-m-d',
        'rj21_tarikh_cipta' => 'date:Y-m-d',
        'bk_telefon_forensik_tarikh_hantar' => 'date:Y-m-d',
        'tarikh_tpr_beri_arahan_tuduh' => 'date:Y-m-d',
        'tarikh_tpr_beri_arahan_nfa' => 'date:Y-m-d',
        'tarikh_tpr_beri_arahan_dnaa' => 'date:Y-m-d',
        'tarikh_keputusan_jatuh_hukum' => 'date:Y-m-d',

        // Added e-FSA related date casts (assuming up to 3 instances based on CSV structure)
        'efsa_bank_1_tarikh_dimohon' => 'date:Y-m-d',
        'efsa_bank_1_laporan_diterima_tarikh' => 'date:Y-m-d',
        'efsa_bank_2_tarikh_dimohon' => 'date:Y-m-d',
        'efsa_bank_2_laporan_diterima_tarikh' => 'date:Y-m-d',
        'efsa_bank_3_tarikh_dimohon' => 'date:Y-m-d',
        'efsa_bank_3_laporan_diterima_tarikh' => 'date:Y-m-d',
        'efsa_telco_1_tarikh_dimohon' => 'date:Y-m-d',
        'efsa_telco_1_laporan_diterima_tarikh' => 'date:Y-m-d',
        'efsa_telco_2_tarikh_dimohon' => 'date:Y-m-d',
        'efsa_telco_2_laporan_diterima_tarikh' => 'date:Y-m-d',
        'efsa_telco_3_tarikh_dimohon' => 'date:Y-m-d',
        'efsa_telco_3_laporan_diterima_tarikh' => 'date:Y-m-d',
        
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'id', 'no_ks', 'tarikh_ks_dibuka', 'status_ks', 'status_kes', 'pegawai_pemeriksa_jips', 'io_aio', 'created_at', 'updated_at'
        // Add other sortable columns specific to Komersil
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->applyClientSpecificCalculations();
        });
    }

    public function applyClientSpecificCalculations()
    {
        $this->calculateEdaranPertamaLebih24JamClientLogic();
        $this->calculateTerbengkalai3BulanClientLogic();
        $this->calculateBaruKemaskiniClientLogic();
    }

    // PASTE THE 3 CLIENT-SPECIFIC CALCULATION METHODS HERE
    public function calculateEdaranPertamaLebih24JamClientLogic()
    {
        if ($this->tarikh_minit_a && $this->tarikh_minit_b) {
            $tarikhA = Carbon::parse($this->tarikh_minit_a)->startOfDay();
            $tarikhB = Carbon::parse($this->tarikh_minit_b)->startOfDay();
            if ($tarikhB->isAfter($tarikhA) && $tarikhB->diffInHours($tarikhA) > 24) {
                $this->edar_lebih_24_jam_status = 'YA, EDARAN LEWAT 24 JAM';
            } else {
                $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH 24 JAM & KURANG';
            }
        } else {
            $this->edar_lebih_24_jam_status = null;
        }
    }

    public function calculateTerbengkalai3BulanClientLogic()
    {
        if ($this->tarikh_minit_a && $this->tarikh_minit_d) {
            $tarikhA = Carbon::parse($this->tarikh_minit_a);
            $tarikhD = Carbon::parse($this->tarikh_minit_d);
            if ($tarikhD->isAfter($tarikhA) && $tarikhA->diffInMonths($tarikhD) >= 3) {
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
        }
    }

    public function calculateBaruKemaskiniClientLogic() // THIS IS AN INTERPRETATION
    {
        $this->baru_kemaskini_status = 'TIADA PERGERAKAN BARU C-D';
        // For Komersil, tarikh_minit_c might often be null
        if ($this->tarikh_minit_c && $this->tarikh_minit_d) {
            $tarikhC = Carbon::parse($this->tarikh_minit_c);
            $tarikhD = Carbon::parse($this->tarikh_minit_d);
            if ($tarikhD->isAfter($tarikhC) && $this->updated_at && Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI';
            }
        } elseif ($this->tarikh_minit_d && !$this->tarikh_minit_c) { // If C is not applicable/null
             if ($this->updated_at && Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI (MINIT D)';
            }
        }
    }
}