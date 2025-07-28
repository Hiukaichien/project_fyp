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
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        // Calculated statuses
        'lewat_edaran_status',
        'terbengkalai_status',
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
        'status_laporan_penuh_imigresen_text',
        'adakah_muka_surat_4_keputusan_kes_dicatat_text',
        'adakah_ks_kus_fail_selesai_text',
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
    public function getStatusLaporanPenuhImigresenTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_imigresen, 'Diterima', 'Tidak');
    }
    public function getAdakahMukaSurat4KeputusanKesDicatatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_muka_surat_4_keputusan_kes_dicatat);
    }
    public function getAdakahKsKusFailSelesaiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_ks_kus_fail_selesai);
    }
}