<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Trafik_Seksyen extends Model
{
    use HasFactory;

    protected $table = 'trafik_seksyen';
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        // B1
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        // B2
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        // B3
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_oleh_sio_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_bahagian_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_ketua_jabatan_tarikh' => 'date:Y-m-d',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'date:Y-m-d',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'array',
        // B4
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'array',
        'status_barang_kes_selesai_siasatan' => 'array',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'array',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'array',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',
        // B5
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_post_mortem_mayat_di_hospital' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
        'status_gambar_barang_kes_darah' => 'boolean',
        'status_gambar_barang_kes_kontraban' => 'boolean',
        // B6
        'status_pem' => 'array',
        'status_rj2' => 'boolean',
        'tarikh_rj2' => 'date:Y-m-d',
        'status_rj2b' => 'boolean',
        'tarikh_rj2b' => 'date:Y-m-d',
        'status_rj9' => 'boolean',
        'tarikh_rj9' => 'date:Y-m-d',
        'status_rj99' => 'boolean',
        'tarikh_rj99' => 'date:Y-m-d',
        'status_rj10a' => 'boolean',
        'tarikh_rj10a' => 'date:Y-m-d',
        'status_rj10b' => 'boolean',
        'tarikh_rj10b' => 'date:Y-m-d',
        'status_saman_pdrm_s_257' => 'boolean',
        'status_saman_pdrm_s_167' => 'boolean',
        'status_semboyan_pertama_wanted_person' => 'boolean',
        'tarikh_semboyan_pertama_wanted_person' => 'date:Y-m-d',
        'status_semboyan_kedua_wanted_person' => 'boolean',
        'tarikh_semboyan_kedua_wanted_person' => 'date:Y-m-d',
        'status_semboyan_ketiga_wanted_person' => 'boolean',
        'tarikh_semboyan_ketiga_wanted_person' => 'date:Y-m-d',
        'status_penandaan_kelas_warna' => 'boolean',
        // B7
        'status_permohonan_laporan_puspakom' => 'boolean',
        'tarikh_permohonan_laporan_puspakom' => 'date:Y-m-d',
        'status_laporan_penuh_puspakom' => 'boolean',
        'tarikh_laporan_penuh_puspakom' => 'date:Y-m-d',
        'status_permohonan_laporan_jkr' => 'boolean',
        'tarikh_permohonan_laporan_jkr' => 'date:Y-m-d',
        'status_laporan_penuh_jkr' => 'boolean',
        'tarikh_laporan_penuh_jkr' => 'date:Y-m-d',
        'status_permohonan_laporan_jpj' => 'boolean',
        'tarikh_permohonan_laporan_jpj' => 'date:Y-m-d',
        'status_laporan_penuh_jpj' => 'boolean',
        'tarikh_laporan_penuh_jpj' => 'date:Y-m-d',
        'status_permohonan_laporan_imigresen' => 'boolean',
        'tarikh_permohonan_laporan_imigresen' => 'date:Y-m-d',
        'status_laporan_penuh_imigresen' => 'boolean',
        'tarikh_laporan_penuh_imigresen' => 'date:Y-m-d',
        // B8
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'keputusan_akhir_mahkamah' => 'array',
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
     * Calculates if the case is abandoned.
     * A case is abandoned if both first and last minute dates are empty,
     * and it has been over 3 months since the case was opened.
     */
    public function calculateTerbengkalai3Bulan()
    {
        // A case is considered abandoned if it has a 'tarikh_minit_pertama' but no 'tarikh_minit_akhir',
        // and more than 3 months have passed since 'tarikh_minit_pertama'.
        if ($this->tarikh_minit_pertama && is_null($this->tarikh_minit_akhir)) {
            $openingDate = $this->tarikh_minit_pertama;

            // Check if more than 3 months have passed since the first minute date.
            if ($openingDate->diffInMonths(Carbon::now()) > 3) {
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            // If 'tarikh_minit_pertama' is not set, or if 'tarikh_minit_akhir' is set,
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
        if ($this->tarikh_minit_akhir && $this->updated_at) {
            if (Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIKEMASKINI';
            }
        }
    }
}