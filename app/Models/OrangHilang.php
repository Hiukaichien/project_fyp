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
        
        // Date fields
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        'arahan_minit_oleh_sio_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_bahagian_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_jabatan_tarikh' => 'date:Y-m-d',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'date:Y-m-d',
        'tarikh_mps1' => 'date:Y-m-d',
        'tarikh_mps2' => 'date:Y-m-d',
        'tarikh_permohonan_laporan_imigresen' => 'date:Y-m-d',
        'tarikh_laporan_penuh_imigresen' => 'date:Y-m-d',
        
        // Boolean fields
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_berharga' => 'boolean',
        'status_gambar_orang_hilang' => 'boolean',
        'status_mps1' => 'boolean',
        'status_mps2' => 'boolean',
        'hebahan_media_massa' => 'boolean',
        'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => 'boolean',
        'orang_hilang_dijumpai_mati_mengejut_jenayah' => 'boolean',
        'semboyan_pemakluman_ke_kedutaan_bukan_warganegara' => 'boolean',
        'status_permohonan_laporan_imigresen' => 'boolean',
        'status_laporan_penuh_imigresen' => 'boolean',
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'adakah_ks_kus_fail_selesai' => 'boolean',
        
        // JSON fields
        'status_pem' => 'array',
        
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
     * Get the user who created this paper.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Generate unique no_kertas_siasatan when creating
            if (empty($model->no_kertas_siasatan)) {
                $model->no_kertas_siasatan = $model->generateNoKertasSiasatan();
            }
        });
    }

    /**
     * Generate unique no_kertas_siasatan
     */
    public function generateNoKertasSiasatan()
    {
        $year = now()->year;
        $prefix = "OH/{$year}/";
        
        // Get the latest number for this year
        $latestRecord = static::where('no_kertas_siasatan', 'LIKE', $prefix . '%')
                            ->orderBy('no_kertas_siasatan', 'desc')
                            ->first();
        
        if ($latestRecord) {
            $latestNumber = (int) str_replace($prefix, '', $latestRecord->no_kertas_siasatan);
            $nextNumber = $latestNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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
        // If tarikh_edaran_minit_ks_akhir is filled, the case is not considered late.
        if ($this->tarikh_edaran_minit_ks_akhir) {
            $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH';
            return;
        }

        // If tarikh_edaran_minit_ks_akhir is not filled, check against tarikh_edaran_minit_ks_pertama.
        if ($this->tarikh_edaran_minit_ks_pertama) {
            $tarikhPertama = $this->tarikh_edaran_minit_ks_pertama;
            
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
        // A case is considered abandoned if it has a 'tarikh_edaran_minit_ks_pertama' but no 'tarikh_edaran_minit_ks_akhir',
        // and more than 3 months have passed since 'tarikh_edaran_minit_ks_pertama'.
        if ($this->tarikh_edaran_minit_ks_pertama && is_null($this->tarikh_edaran_minit_ks_akhir)) {
            $openingDate = $this->tarikh_edaran_minit_ks_pertama;

            // Check if more than 3 months have passed since the first minute date.
            if ($openingDate->diffInMonths(Carbon::now()) > 3) {
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            // If 'tarikh_edaran_minit_ks_pertama' is not set, or if 'tarikh_edaran_minit_ks_akhir' is set,
            // the case is not considered abandoned by this rule.
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
        }
    }

    /**
     * A simple logic to check if the record was recently updated.
     */
    public function calculateBaruKemaskini()
    {
        $this->baru_kemaskini_status = 'TIADA PERGERAKAN BARU';
        if ($this->tarikh_edaran_minit_ks_akhir && $this->updated_at) {
            if (Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIKEMASKINI';
            }
        }
    }
}