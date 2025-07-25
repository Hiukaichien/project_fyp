<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanMatiMengejut extends Model
{
    use HasFactory;

    protected $table = 'laporan_mati_mengejut';

    /**
     * All attributes are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'project_id' => 'integer',
        
        // BAHAGIAN 1: Maklumat Asas - Date fields
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        
        // BAHAGIAN 2: Pemeriksaan & Status - Date fields
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_akhir' => 'date:Y-m-d',
        'fail_lmm_bahagian_pengurusan_pada_muka_surat_2' => 'boolean',
        
        // BAHAGIAN 3: Arahan & Keputusan - Boolean and Date fields
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_oleh_sio_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_bahagian_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_ketua_jabatan_tarikh' => 'date:Y-m-d',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'date:Y-m-d',
        
        // BAHAGIAN 4: Barang Kes - Boolean and JSON fields
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'array',
        'status_barang_kes_selesai_siasatan' => 'array',
        'kaedah_pelupusan_barang_kes' => 'array',
        'arahan_pelupusan_barang_kes' => 'array',
        'adakah_borang_serah_terima_pegawai_tangkapan_io' => 'boolean',
        'adakah_borang_serah_terima_penyiasat_pemilik_saksi' => 'boolean',
        'adakah_sijil_surat_kebenaran_ipd' => 'boolean',
        'adakah_gambar_pelupusan' => 'boolean',
        
        // BAHAGIAN 5: Dokumen Siasatan - Boolean fields
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_post_mortem_mayat_di_hospital' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_berharga' => 'boolean',
        'status_gambar_barang_kes_darah' => 'boolean',
        
        // BAHAGIAN 6: Borang & Semakan - Boolean, Date and JSON fields
        'status_pem' => 'array',
        'status_rj2' => 'boolean',
        'tarikh_rj2' => 'date:Y-m-d',
        'status_rj2b' => 'boolean',
        'tarikh_rj2b' => 'date:Y-m-d',
        'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati' => 'boolean',
        
        // BAHAGIAN 7: Permohonan Laporan Agensi Luar - Boolean and Date fields
        'status_permohonan_laporan_post_mortem_mayat' => 'boolean',
        'tarikh_permohonan_laporan_post_mortem_mayat' => 'date:Y-m-d',
        'status_laporan_penuh_bedah_siasat' => 'boolean',
        'tarikh_laporan_penuh_bedah_siasat' => 'date:Y-m-d',
        'status_permohonan_laporan_jabatan_kimia' => 'boolean',
        'tarikh_permohonan_laporan_jabatan_kimia' => 'date:Y-m-d',
        'status_laporan_penuh_jabatan_kimia' => 'boolean',
        'tarikh_laporan_penuh_jabatan_kimia' => 'date:Y-m-d',
        'status_permohonan_laporan_jabatan_patalogi' => 'boolean',
        'tarikh_permohonan_laporan_jabatan_patalogi' => 'date:Y-m-d',
        'status_laporan_penuh_jabatan_patalogi' => 'boolean',
        'tarikh_laporan_penuh_jabatan_patalogi' => 'date:Y-m-d',
        'status_permohonan_laporan_imigresen' => 'boolean',
        'tarikh_permohonan_laporan_imigresen' => 'date:Y-m-d',
        'status_laporan_penuh_imigresen' => 'boolean',
        'tarikh_laporan_penuh_imigresen' => 'date:Y-m-d',
        
        // BAHAGIAN 8: Status Fail - Boolean fields
        'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar' => 'boolean',
        'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr' => 'boolean',
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'boolean',
        'adakah_ks_kus_fail_selesai' => 'boolean',
        
        // Legacy fields (keeping for backward compatibility)
        'tarikh_laporan_polis' => 'date:Y-m-d',
        'tarikh_minit_pertama' => 'date:Y-m-d',
        'tarikh_minit_akhir' => 'date:Y-m-d',
        'tarikh_permohonan_pm_dipohon' => 'date:Y-m-d',
        'tarikh_rujuk_tpr' => 'date:Y-m-d',
        'tarikh_rujuk_koroner' => 'date:Y-m-d',
        
        // System fields
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
        // Use the new field names from the form
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
        // Use the new field names from the form
        // If 'tarikh_edaran_minit_ks_pertama' is not set, we cannot determine the status.
        if (is_null($this->tarikh_edaran_minit_ks_pertama)) {
            $this->terbengkalai_3_bulan_status = null;
            return;
        }

        // A case is considered abandoned if it has a 'tarikh_edaran_minit_ks_pertama' but no 'tarikh_edaran_minit_ks_akhir',
        // and more than 3 months have passed since 'tarikh_edaran_minit_ks_pertama'.
        if (is_null($this->tarikh_edaran_minit_ks_akhir)) {
            $openingDate = $this->tarikh_edaran_minit_ks_pertama;

            // Check if more than 3 months have passed since the first minute date.
            if ($openingDate->diffInMonths(Carbon::now()) > 3) {
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            // If 'tarikh_edaran_minit_ks_akhir' is set, the case is not considered abandoned by this rule.
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
        }
    }

    /**
     * A simple logic to check if the record was recently updated.
     */
    public function calculateBaruKemaskini()
    {
        $this->baru_kemaskini_status = 'TIADA PERGERAKAN BARU';
        // Use the new field name from the form
        if ($this->tarikh_edaran_minit_ks_akhir && $this->updated_at) {
            if (Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIKEMASKINI';
            }
        }
    }
}