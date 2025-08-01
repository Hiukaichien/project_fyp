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
        
        // BAHAGIAN 4: Barang Kes - Boolean fields
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'dilupuskan_perbendaharaan_amount' => 'decimal:2',
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
        
        // System fields
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
        'fail_lmm_bahagian_pengurusan_pada_muka_surat_2_text',
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'adakah_barang_kes_didaftarkan_text',
        'adakah_borang_serah_terima_pegawai_tangkapan_io_text',
        'adakah_borang_serah_terima_penyiasat_pemilik_saksi_text',
        'adakah_sijil_surat_kebenaran_ipd_text',
        'adakah_gambar_pelupusan_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_gambar_post_mortem_mayat_di_hospital_text',
        'status_gambar_barang_kes_am_text',
        'status_gambar_barang_kes_berharga_text',
        'status_gambar_barang_kes_darah_text',
        'status_rj2_text',
        'status_rj2b_text',
        'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati_text',
        'status_permohonan_laporan_post_mortem_mayat_text',
        'status_laporan_penuh_bedah_siasat_text',
        'status_permohonan_laporan_jabatan_kimia_text',
        'status_laporan_penuh_jabatan_kimia_text',
        'status_permohonan_laporan_jabatan_patalogi_text',
        'status_laporan_penuh_jabatan_patalogi_text',
        'status_permohonan_laporan_imigresen_text',
        'status_laporan_penuh_imigresen_text',
        'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_text',
        'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr_text',
        'adakah_muka_surat_4_keputusan_kes_dicatat_text',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan_text',
        'adakah_ks_kus_fail_selesai_text',
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
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;
        $limitInHours = 24;

        if (!$tarikhA || !$tarikhB) {
            return null; // Cannot calculate if dates are missing
        }

        return $tarikhA->diffInHours($tarikhB) > $limitInHours ? 'LEWAT' : 'DALAM TEMPOH';
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

        // Fallback for general updates not related to JIPS
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
    
    public function getFailLmmBahagianPengurusanPadaMukaSurat2TextAttribute(): string {
        return $this->formatBooleanToMalay($this->fail_lmm_bahagian_pengurusan_pada_muka_surat_2, 'Ada', 'Tiada');
    }
    public function getArahanMinitOlehSioStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_oleh_sio_status, 'Ada', 'Tiada');
    }
    public function getArahanMinitKetuaBahagianStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_ketua_bahagian_status, 'Ada', 'Tiada');
    }
    public function getArahanMinitKetuaJabatanStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_ketua_jabatan_status, 'Ada', 'Tiada');
    }
    public function getArahanMinitOlehYaTprStatusTextAttribute(): string {
        return $this->formatBooleanToMalay($this->arahan_minit_oleh_ya_tpr_status, 'Ada', 'Tiada');
    }
    public function getAdakahBarangKesDidaftarkanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan);
    }
    public function getAdakahBorangSerahTerimaPegawaiTangkapanIoTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_borang_serah_terima_pegawai_tangkapan_io);
    }
    public function getAdakahBorangSerahTerimaPenyiasatPemilikSaksiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_borang_serah_terima_penyiasat_pemilik_saksi);
    }
    public function getAdakahSijilSuratKebenaranIpdTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_sijil_surat_kebenaran_ipd);
    }
    public function getAdakahGambarPelupusanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_gambar_pelupusan);
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
    public function getStatusGambarPostMortemMayatDiHospitalTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesAmTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_am, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesBerhargaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_berharga, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesDarahTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_darah, 'Ada', 'Tiada');
    }
    public function getStatusRj2TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj2, 'Cipta', 'Tidak');
    }
    public function getStatusRj2bTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj2b, 'Cipta', 'Tidak');
    }
    public function getStatusSemboyanPemaklumanKeKedutaanBagiKesMatiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati);
    }
    public function getStatusPermohonanLaporanPostMortemMayatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak');
    }
    public function getStatusLaporanPenuhBedahSiasatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak');
    }
    public function getStatusPermohonanLaporanJabatanKimiaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak');
    }
    public function getStatusLaporanPenuhJabatanKimiaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak');
    }
    public function getStatusPermohonanLaporanJabatanPatalogiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak');
    }
    public function getStatusLaporanPenuhJabatanPatalogiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak');
    }
    public function getStatusPermohonanLaporanImigresenTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_imigresen, 'Dibuat', 'Tidak');
    }
    public function getStatusLaporanPenuhImigresenTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_imigresen, 'Diterima', 'Tidak');
    }
    public function getStatusMukaSurat4BarangKesDitulisBersamaNoDaftarTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar);
    }
    public function getStatusMukaSurat4BarangKesDitulisBersamaNoDaftarDanTelahAdaArahanYaTprTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr);
    }
    public function getAdakahMukaSurat4KeputusanKesDicatatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_muka_surat_4_keputusan_kes_dicatat);
    }
    public function getAdakahFailLmmTAtauLmmTelahAdaKeputusanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan);
    }
    public function getAdakahKsKusFailSelesaiTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_ks_kus_fail_selesai);
    }
}