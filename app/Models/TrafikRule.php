<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrafikRule extends Model
{
    use HasFactory;

    protected $table = 'trafik_rule';
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        // IPRS Standard Fields
        'iprs_tarikh_ks' => 'datetime',

        // B1
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        
        // B2
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_fail_lmm_t_akhir' => 'date:Y-m-d',
        'fail_lmm_t_muka_surat_2_disahkan_kpd' => 'boolean',

        // B3
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_oleh_sio_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_bahagian_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_ketua_jabatan_tarikh' => 'date:Y-m-d',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'date:Y-m-d',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'string',

        // B5
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',

        // B6
        'status_pem' => 'array',
        
        // RJ Fields - Added as per client requirements
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

        // B7
        'status_permohonan_laporan_jkr' => 'boolean',
        'tarikh_permohonan_laporan_jkr' => 'date:Y-m-d',
        'status_laporan_penuh_jkr' => 'boolean',
        'tarikh_laporan_penuh_jkr' => 'date:Y-m-d',
        'status_permohonan_laporan_jpj' => 'boolean',
        'tarikh_permohonan_laporan_jpj' => 'date:Y-m-d',
        'status_laporan_penuh_jpj' => 'boolean',
        'tarikh_laporan_penuh_jpj' => 'date:Y-m-d',
        'status_permohonan_laporan_jkjr' => 'boolean',
        'tarikh_permohonan_laporan_jkjr' => 'date:Y-m-d',
        'status_laporan_penuh_jkjr' => 'boolean',
        'tarikh_laporan_penuh_jkjr' => 'date:Y-m-d',
        
        // PUSPAKOM - Added as per client requirements
        'status_permohonan_laporan_puspakom' => 'boolean',
        'tarikh_permohonan_laporan_puspakom' => 'date:Y-m-d',
        'status_laporan_penuh_puspakom' => 'boolean',
        'tarikh_laporan_penuh_puspakom' => 'date:Y-m-d',
        
        // HOSPITAL - Added as per client requirements
        'status_permohonan_laporan_hospital' => 'boolean',
        'tarikh_permohonan_laporan_hospital' => 'date:Y-m-d',
        'status_laporan_penuh_hospital' => 'boolean',
        'tarikh_laporan_penuh_hospital' => 'date:Y-m-d',

        // B8
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'adakah_ks_kus_fail_selesai' => 'boolean',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'boolean',
        'keputusan_akhir_mahkamah' => 'array',
        
        // Common Timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
       
    ];

    
     /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        // Calculated statuses
        'lewat_edaran_status',
        //'terbengkalai_status',
        'terbengkalai_status_dc',
        'terbengkalai_status_da',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan', 
        'tempoh_dikemaskini',


        // Text versions of boolean fields for display
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        
        // RJ Fields - Added accessors
        'status_rj2_text',
        'status_rj2b_text', 
        'status_rj9_text',
        'status_rj99_text',
        'status_rj10a_text',
        
        'status_rj10b_text',
        'status_saman_pdrm_s_257_text',
        'status_saman_pdrm_s_167_text',
        'status_permohonan_laporan_jkr_text',
        'status_laporan_penuh_jkr_text',
        'status_permohonan_laporan_jpj_text',
        'status_laporan_penuh_jpj_text',
        'status_permohonan_laporan_jkjr_text',
        'status_laporan_penuh_jkjr_text',
        
        // PUSPAKOM - Added accessors
        'status_permohonan_laporan_puspakom_text',
        'status_laporan_penuh_puspakom_text',
        
        // HOSPITAL - Added accessors  
        'status_permohonan_laporan_hospital_text',
        'status_laporan_penuh_hospital_text',
        
        'adakah_muka_surat_4_keputusan_kes_dicatat_text',
        'adakah_ks_kus_fail_selesai_text',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan_text',
        'fail_lmm_t_muka_surat_2_disahkan_kpd_text',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- ACCESSORS FOR DYNAMIC CALCULATION ---
    
    public function getLewatEdaranStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;

        if (!$tarikhA || !$tarikhB) {
            return null;
        }

        return $tarikhA->diffInHours($tarikhB) > 24 ? 'LEWAT' : 'TIDAK';
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


 public function getTerbengkalaiStatusDcAttribute(): string
    {
        $tarikhC = $this->tarikh_edaran_minit_ks_sebelum_akhir;
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;

        // Check if both dates exist to perform the calculation.
        if ($tarikhD && $tarikhC) {
            // If D is 3 or more months after C, it is terbengkalai.
            if ($tarikhD->gte($tarikhC->copy()->addMonths(3))) {
                return 'TERBENGKALAI MELEBIHI 3 BULAN';
            }
        }
        
        // Otherwise, it is not considered terbengkalai by this specific rule.
        return 'TIDAK';
    }

    public function getTerbengkalaiStatusDaAttribute(): string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;

        // Check if both dates exist to perform the calculation.
        if ($tarikhD && $tarikhA) {
            // If D is 3 or more months after A, it is terbengkalai.
            if ($tarikhD->gte($tarikhA->copy()->addMonths(3))) {
                return 'TERBENGKALAI MELEBIHI 3 BULAN';
            }
        }
        
        // Otherwise, it is not considered terbengkalai by this specific rule.
        return 'TIDAK';
    }

        public function getBaruDikemaskiniStatusAttribute(): string
    {
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;
        $tarikhE = $this->tarikh_semboyan_pemeriksaan_jips_ke_daerah;

        if ($tarikhE && $tarikhD && $tarikhE->isAfter($tarikhD)) {
            return 'TERBENGKALAI / KS BARU DIKEMASKINI';
        }

        // The only other possibility is 'TIDAK' (or 'TIADA PERGERAKAN BARU')
        return 'TIDAK'; 
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

    /**
     * Helper function to format boolean values into Malay text.
     */
    private function formatBooleanToMalay(?bool $value, string $trueText = 'Ya', string $falseText = 'Tidak', string $nullText = '-') : string
    {
        if (is_null($value)) {
            return $nullText;
        }
        return $value ? $trueText : $falseText;
    }

    // --- Accessors for Boolean Fields to display Malay Text ---
    
    // --- ADDED: New B2 Accessor ---
    public function getFailLmmTMukaSurat2DisahkanKpdTextAttribute(): string {
        return $this->formatBooleanToMalay($this->fail_lmm_t_muka_surat_2_disahkan_kpd, 'Telah Disahkan', 'Belum Disahkan');
    }

    // B3 Accessors
    public function getArahanMinitOlehSioStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_oleh_sio_status);
    }
    public function getArahanMinitKetuaBahagianStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_ketua_bahagian_status);
    }
    public function getArahanMinitKetuaJabatanStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_ketua_jabatan_status);
    }
    public function getArahanMinitOlehYaTprStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_oleh_ya_tpr_status);
    }

    // B5 Accessors
    public function getStatusIdSiasatanDikemaskiniTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini');
    }
    public function getStatusRajahKasarTempatKejadianTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada');
    }
    public function getStatusGambarTempatKejadianTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_tempat_kejadian, 'Ada', 'Tiada');
    }
    
    // B6 Accessors
    
    // RJ Fields - Added accessors
    public function getStatusRj2TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj2, 'Diterima', 'Tidak');
    }
    public function getStatusRj2bTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj2b, 'Diterima', 'Tidak');
    }
    public function getStatusRj9TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj9, 'Diterima', 'Tidak');
    }
    public function getStatusRj99TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj99, 'Diterima', 'Tidak');
    }
    public function getStatusRj10aTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj10a, 'Diterima', 'Tidak');
    }
    
    public function getStatusRj10bTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj10b, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusSamanPdrmS257TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_saman_pdrm_s_257, 'Dicipta', 'Tidak Dicipta');
    }
    public function getStatusSamanPdrmS167TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_saman_pdrm_s_167, 'Dicipta', 'Tidak Dicipta');
    }

    // B7 Accessors
    public function getStatusPermohonanLaporanJkrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jkr, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJkrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jkr, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanJpjTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jpj, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJpjTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jpj, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanJkjrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jkjr, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJkjrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jkjr, 'Dilampirkan', 'Tiada');
    }
    
    // PUSPAKOM - Added accessors
    public function getStatusPermohonanLaporanPuspAkomTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_puspakom, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhPuspAkomTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tiada');
    }
    
    // HOSPITAL - Added accessors
    public function getStatusPermohonanLaporanHospitalTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_hospital, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhHospitalTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_hospital, 'Dilampirkan', 'Tiada');
    }

    // B8 Accessors
    public function getAdakahMukaSurat4KeputusanKesDicatatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_muka_surat_4_keputusan_kes_dicatat);
    }
    public function getAdakahKsKusFailSelesaiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_ks_kus_fail_selesai);
    }
    public function getAdakahFailLmmTAtauLmmTelahAdaKeputusanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan);
    }
}