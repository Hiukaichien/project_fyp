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

        // Standardized Date Fields (ensure your DB table matches these names)
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',

        // Original Jenayah Specific Fields
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
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'lewat_edaran_status',
        'terbengkalai_status',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan',
        'tempoh_dikemaskini',
    ];

    /**
     * Get the project that this paper belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- ACCESSORS FOR DYNAMIC CALCULATION ---

    public function getLewatEdaranStatusAttribute(): ?string
    {
        // IMPORTANT: Jenayah uses the 24-hour rule
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;
        $limitInHours = 24;

        if (!$tarikhA || !$tarikhB) {
            return null; // Cannot calculate if dates are missing
        }

        return $tarikhA->diffInHours($tarikhB) > $limitInHours ? 'YA, LEWAT' : 'DALAM TEMPOH';
    }

    public function getTempohLewatEdaranDikesanAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;

        if ($tarikhA && $tarikhB) {
            $days = $tarikhA->diffInDays($tarikhB);
            return "{$days} HARI";
        }

        return null;
    }

    public function getTerbengkalaiStatusAttribute(): string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhC = $this->tarikh_edaran_minit_ks_sebelum_akhir;
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;
        $isTerbengkalai = false;

        // Rule 1: Check (D - C) if both dates exist.
        if ($tarikhD && $tarikhC) {
            if ($tarikhC->diffInMonths($tarikhD) >= 3) {
                $isTerbengkalai = true;
            }
        }

        // Rule 2: If not already flagged, check (D - A) if both dates exist.
        if (!$isTerbengkalai && $tarikhD && $tarikhA) {
            if ($tarikhA->diffInMonths($tarikhD) >= 3) {
                $isTerbengkalai = true;
            }
        }
        
        return $isTerbengkalai ? 'YA, TERBENGKALAI MELEBIHI 3 BULAN' : 'TIDAK TERBENGKALAI';
    }

    public function getBaruDikemaskiniStatusAttribute(): string
    {
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;
        $tarikhE = $this->tarikh_semboyan_pemeriksaan_jips_ke_daerah;

        if ($tarikhE && $tarikhD && $tarikhE->isAfter($tarikhD)) {
            return 'TERBENGKALAI / KS BARU DIKEMASKINI';
        }
        
        return 'TIADA PERGERAKAN BARU';
    }

    public function getTempohDikemaskiniAttribute(): ?string
    {
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;
        $tarikhE = $this->tarikh_semboyan_pemeriksaan_jips_ke_daerah;

        if ($tarikhD && $tarikhE) {
            $days = $tarikhD->diffInDays($tarikhE);
            return "{$days} HARI";
        }

        return null;
    }
}