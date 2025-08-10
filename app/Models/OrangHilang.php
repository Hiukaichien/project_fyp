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
        'iprs_tarikh_ks' => 'date:Y-m-d',
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
        // New BAHAGIAN 7 fields
        'permohonan_laporan_permit_kerja' => 'boolean',
        'permohonan_laporan_agensi_pekerjaan' => 'boolean',
        'permohonan_status_kewarganegaraan' => 'boolean',
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        // Updated BAHAGIAN 8 field types
        // 'adakah_ks_kus_fail_selesai' is now string (KUS/FAIL dropdown), not boolean
        
        // JSON fields
        'status_pem' => 'array',
        'keputusan_akhir_mahkamah' => 'array', // Changed from string to array for multiple checkboxes
        
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

        // Text versions of boolean fields
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'adakah_barang_kes_didaftarkan_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_gambar_barang_kes_am_text',
        'status_gambar_barang_kes_berharga_text',
        'status_gambar_orang_hilang_text',
        'status_mps1_text',
        'status_mps2_text',
        'hebahan_media_massa_text',
        'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah_text',
        'orang_hilang_dijumpai_mati_mengejut_jenayah_text',
        'semboyan_pemakluman_ke_kedutaan_bukan_warganegara_text',
        'status_permohonan_laporan_imigresen_text',
        // New BAHAGIAN 7 field text versions
        'permohonan_laporan_permit_kerja_text',
        'permohonan_laporan_agensi_pekerjaan_text',
        'permohonan_status_kewarganegaraan_text',
        'adakah_muka_surat_4_keputusan_kes_dicatat_text',
        // Updated BAHAGIAN 8 field (adakah_ks_kus_fail_selesai is now string, no boolean text needed)
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
    
    // --- ACCESSORS FOR DYNAMIC CALCULATION ---

    public function getLewatEdaranStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;
        $limitInHours = 24;

        if (!$tarikhA || !$tarikhB) {
            return 'TIDAK'; // Cannot calculate if dates are missing
        }

        return $tarikhA->diffInHours($tarikhB) > $limitInHours ? 'LEWAT' : 'TIDAK';
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
    public function getAdakahBarangKesDidaftarkanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan);
    }
    public function getStatusIdSiasatanDikemaskiniTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak');
    }
    public function getStatusRajahKasarTempatKejadianTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada');
    }
    public function getStatusGambarTempatKejadianTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_tempat_kejadian, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesAmTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_am, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesBerhargaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_berharga, 'Ada', 'Tiada');
    }
    public function getStatusGambarOrangHilangTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_orang_hilang, 'Ada', 'Tiada');
    }
    public function getStatusMps1TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_mps1, 'Cipta', 'Tidak');
    }
    public function getStatusMps2TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_mps2, 'Cipta', 'Tidak');
    }
    public function getHebahanMediaMassaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->hebahan_media_massa, 'Dibuat', 'Tidak');
    }
    public function getOrangHilangDijumpaiMatiMengejutBukanJenayahTextAttribute(): string {
        return $this->formatBooleanToMalay($this->orang_hilang_dijumpai_mati_mengejut_bukan_jenayah);
    }
    public function getOrangHilangDijumpaiMatiMengejutJenayahTextAttribute(): string {
        return $this->formatBooleanToMalay($this->orang_hilang_dijumpai_mati_mengejut_jenayah);
    }
    public function getSemboyanPemaklumanKeKedutaanBukanWarganegaraTextAttribute(): string {
        return $this->formatBooleanToMalay($this->semboyan_pemakluman_ke_kedutaan_bukan_warganegara, 'Dibuat', 'Tidak');
    }
    public function getStatusPermohonanLaporanImigresenTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_imigresen);
    }
    // New BAHAGIAN 7 field text accessors
    public function getPermohonanLaporanPermitKerjaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->permohonan_laporan_permit_kerja, 'Ada', 'Tiada');
    }
    public function getPermohonanLaporanAgensiPekerjaanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->permohonan_laporan_agensi_pekerjaan, 'Ada', 'Tiada');
    }
    public function getPermohonanStatusKewarganegaraanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->permohonan_status_kewarganegaraan, 'Ada', 'Tiada');
    }
    public function getAdakahMukaSurat4KeputusanKesDicatatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_muka_surat_4_keputusan_kes_dicatat);
    }
    // Note: adakah_ks_kus_fail_selesai is now a string field (KUS/FAIL), so no boolean text accessor needed
}