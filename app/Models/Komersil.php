<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Komersil extends Model
{
    use HasFactory;

    protected $table = 'komersil';
    protected $guarded = [];

 protected $casts = [
           // B1 - Dates
            'tarikh_laporan_polis_dibuka' => 'date:d/m/Y',
            
            // B2 - Dates
            'tarikh_edaran_minit_ks_pertama' => 'date:d/m/Y',
            'tarikh_edaran_minit_ks_kedua' => 'date:d/m/Y',
            'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:d/m/Y',
            'tarikh_edaran_minit_ks_akhir' => 'date:d/m/Y',
            'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:d/m/Y',
            
            // B3 - Arahan & Keputusan
            'arahan_minit_oleh_sio_status' => 'boolean',
            'arahan_minit_oleh_sio_tarikh' => 'date:d/m/Y',
            'arahan_minit_ketua_bahagian_status' => 'boolean',
            'arahan_minit_ketua_bahagian_tarikh' => 'date:d/m/Y',
            'arahan_minit_ketua_jabatan_status' => 'boolean',
            'arahan_minit_ketua_jabatan_tarikh' => 'date:d/m/Y',
            'arahan_minit_oleh_ya_tpr_status' => 'boolean',
            'arahan_minit_oleh_ya_tpr_tarikh' => 'date:d/m/Y',
            'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'array', // KEPT as array - still JSON in migration

            // B4 - Barang Kes
           'adakah_barang_kes_didaftarkan' => 'boolean',
            'status_pergerakan_barang_kes' => 'string',
            'status_pergerakan_barang_kes_ujian_makmal' => 'string',
            'status_pergerakan_barang_kes_lain' => 'string',
            'status_barang_kes_selesai_siasatan' => 'string', // CHANGED: From array to string
            'status_barang_kes_selesai_siasatan_lain' => 'string',
            'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'string', // CHANGED: From array to string
            'kaedah_pelupusan_lain' => 'string',
            'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'string', // CHANGED: From array to string
            'resit_kew_38e_bagi_pelupusan' => 'string', // CHANGED: From array to string
            'adakah_borang_serah_terima_pegawai_tangkapan' => 'string', // CHANGED: From array to string
            'adakah_borang_serah_terima_pemilik_saksi' => 'integer', 
            'adakah_sijil_surat_kebenaran_ipd' => 'integer',
            'adakah_gambar_pelupusan' => 'integer',

        // B5 - Dokumen Siasatan
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_berharga' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
        'status_gambar_barang_kes_darah' => 'boolean',
        'status_gambar_barang_kes_kontraban' => 'boolean',

        // B6 - Borang & Semakan
        'status_pem' => 'array', // This one is correct because it's for multiple checkboxes
        'status_rj2' => 'integer',
        'tarikh_rj2' => 'date:d/m/Y',
        'status_rj2b' => 'integer',
        'tarikh_rj2b' => 'date:d/m/Y',
        'status_rj9' => 'integer',
        'tarikh_rj9' => 'date:d/m/Y',
        'status_rj99' => 'integer',
        'tarikh_rj99' => 'date:d/m/Y',
        'status_rj10a' => 'integer',
        'tarikh_rj10a' => 'date:d/m/Y',
        'status_rj10b' => 'integer',
        'tarikh_rj10b' => 'date:d/m/Y',
        'status_saman_pdrm_s_257' => 'boolean',
        'status_saman_pdrm_s_167' => 'boolean',
        'status_semboyan_pertama_wanted_person' => 'boolean',
        'tarikh_semboyan_pertama_wanted_person' => 'date:d/m/Y',
        'status_semboyan_kedua_wanted_person' => 'boolean',
        'tarikh_semboyan_kedua_wanted_person' => 'date:d/m/Y',
        'status_semboyan_ketiga_wanted_person' => 'boolean',
        'tarikh_semboyan_ketiga_wanted_person' => 'date:d/m/Y',
        'status_penandaan_kelas_warna' => 'boolean',

        // B7 - E-FSA & Agensi Luar
        'status_permohonan_laporan_post_mortem_mayat' => 'boolean',
        'tarikh_permohonan_laporan_post_mortem_mayat' => 'date:d/m/Y',
        
        // E-FSA Bank fields - explicitly cast as strings
        'status_permohonan_E_FSA_1_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_2_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_2_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_3_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_3_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_4_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_4_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_5_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_5_oleh_IO_AIO' => 'string',
        
        // E-FSA Telco fields - explicitly cast as strings
        'status_permohonan_E_FSA_1_telco_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_2_telco_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_3_telco_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_4_telco_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO' => 'string',
        'status_permohonan_E_FSA_5_telco_oleh_IO_AIO' => 'string',
        'status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO' => 'string',
        
        // E-FSA dates still need casting
        'tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO' => 'date:d/m/Y',
        'tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO' => 'date:d/m/Y',
        
        // Puspakom
        'status_permohonan_laporan_puspakom' => 'boolean',
        'tarikh_permohonan_laporan_puspakom' => 'date:d/m/Y',
        'status_laporan_penuh_puspakom' => 'boolean',
        'tarikh_laporan_penuh_puspakom' => 'date:d/m/Y',
        
        // JKR
        //'status_permohonan_laporan_jkr' => 'boolean',
        //'tarikh_permohonan_laporan_jkr' => 'date:d/m/Y',
        //'status_laporan_penuh_jkr' => 'boolean',
        //'tarikh_laporan_penuh_jkr' => 'date:d/m/Y',
        
        // JPJ
        'status_permohonan_laporan_jpj' => 'boolean',
        'tarikh_permohonan_laporan_jpj' => 'date:d/m/Y',
        'status_laporan_penuh_jpj' => 'boolean',
        'tarikh_laporan_penuh_jpj' => 'date:d/m/Y',
        
        // Imigresen
        'status_permohonan_laporan_imigresen' => 'boolean',
        'tarikh_permohonan_laporan_imigresen' => 'date:d/m/Y',
        'status_laporan_penuh_imigresen' => 'boolean',
        'tarikh_laporan_penuh_imigresen' => 'date:d/m/Y',
        
        // Kastam
        'status_permohonan_laporan_kastam' => 'boolean',
        'tarikh_permohonan_laporan_kastam' => 'date:d/m/Y',
        'status_laporan_penuh_kastam' => 'boolean',
        'tarikh_laporan_penuh_kastam' => 'date:d/m/Y',
        
        // Forensik PDRM
        'status_permohonan_laporan_forensik_pdrm' => 'boolean',
        'tarikh_permohonan_laporan_forensik_pdrm' => 'date:d/m/Y',
        'status_laporan_penuh_forensik_pdrm' => 'boolean',
        'tarikh_laporan_penuh_forensik_pdrm' => 'date:d/m/Y',
        
        // B8 - Status Fail
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'status_kus_fail' => 'boolean',
        'keputusan_akhir_mahkamah' => 'array', // Changed to array for checkbox handling

        // Common timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_id' => 'integer',
    ];

    protected $appends = [
        // Calculated statuses
        'lewat_edaran_status',
        //'terbengkalai_status',
        'terbengkalai_status_dc',
        'terbengkalai_status_da',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan',
        'tempoh_dikemaskini',
        
        // B3 - Arahan & Keputusan text versions
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        
        // B4 - Barang Kes text versions
        'adakah_barang_kes_didaftarkan_text',
        'adakah_borang_serah_terima_pemilik_saksi_text',
        'adakah_sijil_surat_kebenaran_ipd_text',
        'adakah_gambar_pelupusan_text',
        
        // B5 - Dokumen Siasatan text versions
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_gambar_barang_kes_am_text',
        'status_gambar_barang_kes_berharga_text',
        'status_gambar_barang_kes_kenderaan_text',
        'status_gambar_barang_kes_darah_text',
        'status_gambar_barang_kes_kontraban_text',
        
        // B6 - Borang & Semakan text versions
        'status_rj2_text',
        'status_rj2b_text',
        'status_rj9_text',
        'status_rj99_text',
        'status_rj10a_text',
        'status_rj10b_text',
        'status_saman_pdrm_s_257_text',
        'status_saman_pdrm_s_167_text',
        'status_semboyan_pertama_wanted_person_text',
        'status_semboyan_kedua_wanted_person_text',
        'status_semboyan_ketiga_wanted_person_text',
        'status_penandaan_kelas_warna_text',
        
        // B7 - E-FSA & Agensi Luar text versions
        'status_permohonan_laporan_post_mortem_mayat_text',
        'status_permohonan_E_FSA_1_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_1_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_2_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_2_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_3_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_3_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_4_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_4_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_5_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_5_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_1_telco_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_2_telco_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_3_telco_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_4_telco_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO_text',
        'status_permohonan_E_FSA_5_telco_oleh_IO_AIO_text',
        'status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO_text',
        'status_permohonan_laporan_puspakom_text',
        'status_laporan_penuh_puspakom_text',
        //'status_permohonan_laporan_jkr_text',
        //'status_laporan_penuh_jkr_text',
        'status_permohonan_laporan_jpj_text',
        'status_laporan_penuh_jpj_text',
        'status_permohonan_laporan_imigresen_text',
        'status_laporan_penuh_imigresen_text',
        'status_permohonan_laporan_kastam_text',
        'status_laporan_penuh_kastam_text',
        'status_permohonan_laporan_forensik_pdrm_text',
        'status_laporan_penuh_forensik_pdrm_text',
        
        // B8 - Status Fail text versions
        'muka_surat_4_barang_kes_ditulis_text',
        'muka_surat_4_dengan_arahan_tpr_text',
        'muka_surat_4_keputusan_kes_dicatat_text',
        'fail_lmm_ada_keputusan_koroner_text',
        'status_kus_fail_text',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- Helper Methods ---
    private function formatBooleanToMalay(?bool $value, string $trueText = 'Ya', string $falseText = 'Tidak', string $nullText = '-'): string
    {
        if (is_null($value)) {
            return $nullText;
        }
        return $value ? $trueText : $falseText;
    }

    /**
     * Format three-value fields (0 = false, 1 = true, 2 = neutral)
     */
    private function formatTripleValueToMalay($value, string $trueText = 'Ya', string $falseText = 'Tidak', string $neutralText = 'Tidak Berkaitan', string $nullText = '-'): string
    {
        if (is_null($value)) {
            return $nullText;
        }
        
        // Handle three-value system
        if ($value === 2) {
            return $neutralText;
        }
        
        return $value ? $trueText : $falseText;
    }

    private function formatStringToMalay(?string $value, string $trueText = 'Ya', string $falseText = 'Tidak', string $nullText = '-'): string
    {
        if (is_null($value) || $value === '') {
            return $nullText;
        }
        
        // Check if the value indicates a positive status
        $positiveValues = ['Dibuat', 'Diterima', 'Ya', 'Ada', 'Cipta', 'Dicipta', '1', 'true', 'YES'];
        $isPositive = in_array(strtolower($value), array_map('strtolower', $positiveValues)) || 
                      in_array($value, $positiveValues) ||
                      (is_numeric($value) && $value > 0);
        
        return $isPositive ? $trueText : $falseText;
    }

    private function formatStringValue(?string $value, string $nullText = '-'): string
    {
        return $value ?: $nullText;
    }

    // --- Status Calculation Methods (Accessors) ---
    public function getLewatEdaranStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;
        $limitInHours = 48;

        if (!$tarikhA || !$tarikhB) {
            return 'TIDAK';
        }

        return $tarikhA->diffInHours($tarikhB) > $limitInHours 
            ? 'LEWAT' 
            : 'TIDAK';
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


    // --- Boolean Field Text Accessors ---
    // B3 Accessors
    public function getArahanMinitOlehSioStatusTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->arahan_minit_oleh_sio_status);
    }

    public function getArahanMinitKetuaBahagianStatusTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->arahan_minit_ketua_bahagian_status);
    }

    public function getArahanMinitKetuaJabatanStatusTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->arahan_minit_ketua_jabatan_status);
    }

    public function getArahanMinitOlehYaTprStatusTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->arahan_minit_oleh_ya_tpr_status);
    }

    // B4 Accessors
    public function getAdakahBarangKesDidaftarkanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan);
    }

    public function getAdakahBorangSerahTerimaPemilikSaksiTextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan');
    }

    public function getAdakahSijilSuratKebenaranIpdTextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->adakah_sijil_surat_kebenaran_ipd, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan');
    }

    public function getAdakahGambarPelupusanTextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan', 'Tidak Berkaitan');
    }

    // B5 Accessors
    public function getStatusIdSiasatanDikemaskiniTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini');
    }

    public function getStatusRajahKasarTempatKejadianTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada');
    }

    public function getStatusGambarTempatKejadianTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_tempat_kejadian, 'Ada', 'Tiada');
    }

    public function getStatusGambarBarangKesAmTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_am, 'Ada', 'Tiada');
    }

    public function getStatusGambarBarangKesBerhargaTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_berharga, 'Ada', 'Tiada');
    }

    public function getStatusGambarBarangKesKenderaanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada');
    }

    public function getStatusGambarBarangKesDarahTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_darah, 'Ada', 'Tiada');
    }

    public function getStatusGambarBarangKesKontrabanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada');
    }

    // B6 Accessors
    public function getStatusRj2TextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->status_rj2, 'Cipta', 'Tidak Cipta', 'Tidak Berkaitan');
    }

    public function getStatusRj2bTextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->status_rj2b, 'Cipta', 'Tidak Cipta', 'Tidak Berkaitan');
    }

    public function getStatusRj9TextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->status_rj9, 'Cipta', 'Tidak Cipta', 'Tidak Berkaitan');
    }

    public function getStatusRj99TextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->status_rj99, 'Cipta', 'Tidak Cipta', 'Tidak Berkaitan');
    }

    public function getStatusRj10aTextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->status_rj10a, 'Cipta', 'Tidak Cipta', 'Tidak Berkaitan');
    }

    public function getStatusRj10bTextAttribute(): string 
    {
        return $this->formatTripleValueToMalay($this->status_rj10b, 'Cipta', 'Tidak Cipta', 'Tidak Berkaitan');
    }

    public function getStatusSamanPdrmS257TextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_saman_pdrm_s_257, 'Dicipta', 'Tidak Dicipta');
    }

    public function getStatusSamanPdrmS167TextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_saman_pdrm_s_167, 'Dicipta', 'Tidak Dicipta');
    }

    public function getStatusSemboyanPertamaWantedPersonTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_semboyan_pertama_wanted_person);
    }

    public function getStatusSemboyanKeduaWantedPersonTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_semboyan_kedua_wanted_person);
    }

    public function getStatusSemboyanKetigaWantedPersonTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_semboyan_ketiga_wanted_person);
    }

    public function getStatusPenandaanKelasWarnaTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_penandaan_kelas_warna);
    }

    // B7 E-FSA & Agensi Luar Accessors
    public function getStatusPermohonanLaporanPostMortemMayatTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_post_mortem_mayat, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusPermohonanEFSA1OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_1_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA1OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_1_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA2OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_2_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA2OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_2_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA3OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_3_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA3OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_3_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA4OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_4_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA4OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_4_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA5OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_5_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA5OlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_5_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA1TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_1_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA1TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA2TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_2_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA2TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA3TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_3_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA3TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA4TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_4_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA4TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA5TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_permohonan_E_FSA_5_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA5TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatStringToMalay($this->status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanLaporanPuspakomTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_puspakom, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhPuspakomTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    /*public function getStatusPermohonanLaporanJkrTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jkr, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhJkrTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jkr, 'Dilampirkan', 'Tidak Dilampirkan');
    }
*/
    public function getStatusPermohonanLaporanJpjTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jpj, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhJpjTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jpj, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanLaporanImigresenTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_imigresen, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhImigresenTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_imigresen, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanLaporanKastamTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_kastam, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhKastamTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_kastam, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanLaporanForensikPdrmTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_forensik_pdrm, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhForensikPdrmTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_forensik_pdrm, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    // B8 - Status Fail Accessors
    public function getMukaSurat4BarangKesDitulisTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->muka_surat_4_barang_kes_ditulis, 'Ya', 'Tidak');
    }

    public function getMukaSurat4DenganArahanTprTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->muka_surat_4_dengan_arahan_tpr, 'Ya', 'Tidak');
    }

    public function getMukaSurat4KeputusanKesDicatatTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->muka_surat_4_keputusan_kes_dicatat, 'Ya', 'Tidak');
    }

    public function getFailLmmAdaKeputusanKoronerTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->fail_lmm_ada_keputusan_koroner, 'Ya', 'Tidak');
    }

    public function getStatusKusFailTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_kus_fail, 'Ya', 'Tidak');
    }
}