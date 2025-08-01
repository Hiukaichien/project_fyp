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
        // --- ADDED: Casts for new B7 fields ---
        'status_laporan_penuh_jpj' => 'boolean',
        'tarikh_laporan_penuh_jpj' => 'date:Y-m-d',
        'status_permohonan_laporan_jkjr' => 'boolean',
        'tarikh_permohonan_laporan_jkjr' => 'date:Y-m-d',
        'status_laporan_penuh_jkjr' => 'boolean',
        'tarikh_laporan_penuh_jkjr' => 'date:Y-m-d',

        // B8
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'adakah_ks_kus_fail_selesai' => 'boolean',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'boolean',
        'keputusan_akhir_mahkamah' => 'string',
        
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
        // 'terbengkalai_status', // This is now removed
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan', 
        'tempoh_dikemaskini',
        'terbengkalai_status_dc',
        'terbengkalai_status_da',

        // Text versions of boolean fields for display
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_rj10b_text',
        'status_saman_pdrm_s_257_text',
        'status_saman_pdrm_s_167_text',
        'status_permohonan_laporan_jkr_text',
        'status_laporan_penuh_jkr_text',
        'status_permohonan_laporan_jpj_text',
        'status_laporan_penuh_jkjr_text',
        'adakah_muka_surat_4_keputusan_kes_dicatat_text',
        'adakah_ks_kus_fail_selesai_text',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan_text',
        'fail_lmm_t_muka_surat_2_disahkan_kpd_text',
        'status_laporan_penuh_jpj_text',
        'status_permohonan_laporan_jkjr_text',
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

        return $tarikhA->diffInHours($tarikhB) > 24 ? 'LEWAT' : 'DALAM TEMPOH';
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

        if ($tarikhD && $tarikhC) {
            if ($tarikhD->diffInMonths($tarikhC) >= 3) {
                return 'TERBENGKALAI MELEBIHI 3 BULAN';
            }
        }
        
        return 'TIDAK';
    }

    public function getTerbengkalaiStatusDaAttribute(): string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;

        if ($tarikhD && $tarikhA) {
            if ($tarikhD->diffInMonths($tarikhA) >= 3) {
                return 'TERBENGKALAI MELEBIHI 3 BULAN';
            }
        }
        
        return 'TIDAK';
    }

    public function getBaruDikemaskiniStatusAttribute(): string
    {
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;
        $tarikhE = $this->tarikh_semboyan_pemeriksaan_jips_ke_daerah;

        if ($tarikhE && $tarikhD && $tarikhE->isAfter($tarikhD)) {
            return 'TERBENGKALAI / KS BARU DIKEMASKINI';
        }

        if ($this->updated_at && $this->updated_at->isAfter(Carbon::now()->subDays(7))) {
            return 'BARU DIKEMASKINI';
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
    // --- ADDED: Text accessors for new B7 fields ---
    public function getStatusLaporanPenuhJpjTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jpj, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanJkjrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jkjr, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJkjrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jkjr, 'Dilampirkan', 'Tiada');
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