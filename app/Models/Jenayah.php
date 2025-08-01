<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jenayah extends Model
{
    use HasFactory;

    protected $table = 'jenayah';
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     * This is crucial for data integrity and consistency.
     */
    protected $casts = [
        // BAHAGIAN 1 & 2
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        
        // BAHAGIAN 3
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_oleh_sio_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_bahagian_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_ketua_jabatan_tarikh' => 'date:Y-m-d',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'date:Y-m-d',

        // BAHAGIAN 4
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'adakah_borang_serah_terima_pemilik_saksi' => 'boolean',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',
        'adakah_gambar_pelupusan' => 'boolean',

        // BAHAGIAN 5
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_post_mortem_mayat_di_hospital' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_berharga' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
        'status_gambar_barang_kes_darah' => 'boolean',
        'status_gambar_barang_kes_kontraban' => 'boolean',

        // BAHAGIAN 6
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
        'status_semboyan_pertama_wanted_person' => 'boolean',
        'tarikh_semboyan_pertama_wanted_person' => 'date:Y-m-d',
        'status_semboyan_kedua_wanted_person' => 'boolean',
        'tarikh_semboyan_kedua_wanted_person' => 'date:Y-m-d',
        'status_semboyan_ketiga_wanted_person' => 'boolean',
        'tarikh_semboyan_ketiga_wanted_person' => 'date:Y-m-d',
        'status_penandaan_kelas_warna' => 'boolean',

        // BAHAGIAN 7
        'status_permohonan_laporan_pakar_judi' => 'boolean',
        'tarikh_permohonan_laporan_pakar_judi' => 'date:Y-m-d',
        'status_laporan_penuh_pakar_judi' => 'boolean',
        'tarikh_laporan_penuh_pakar_judi' => 'date:Y-m-d',
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
        'status_permohonan_laporan_puspakom' => 'boolean',
        'tarikh_permohonan_laporan_puspakom' => 'date:Y-m-d',
        'status_laporan_penuh_puspakom' => 'boolean',
        'tarikh_laporan_penuh_puspakom' => 'date:Y-m-d',
        'status_permohonan_laporan_jpj' => 'boolean',
        'tarikh_permohonan_laporan_jpj' => 'date:Y-m-d',
        'status_laporan_penuh_jpj' => 'boolean',
        'tarikh_laporan_penuh_jpj' => 'date:Y-m-d',
        'status_permohonan_laporan_imigresen' => 'boolean',
        'tarikh_permohonan_laporan_imigresen' => 'date:Y-m-d',
        'status_laporan_penuh_imigresen' => 'boolean',
        'tarikh_laporan_penuh_imigresen' => 'date:Y-m-d',
        'status_permohonan_laporan_kastam' => 'boolean',
        'tarikh_permohonan_laporan_kastam' => 'date:Y-m-d',
        'status_laporan_penuh_kastam' => 'boolean',
        'tarikh_laporan_penuh_kastam' => 'date:Y-m-d',
        'status_permohonan_laporan_forensik_pdrm' => 'boolean',
        'tarikh_permohonan_laporan_forensik_pdrm' => 'date:Y-m-d',
        'status_laporan_penuh_forensik_pdrm' => 'boolean',
        'tarikh_laporan_penuh_forensik_pdrm' => 'date:Y-m-d',

        // BAHAGIAN 8
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'status_kus_fail' => 'boolean',
    ];

    protected $appends = [
        // Calculated Statuses for Dashboard
        'lewat_edaran_status',
        //'terbengkalai_status',
        'terbengkalai_status_dc',
        'terbengkalai_status_da',
        'baru_dikemaskini_status',

        // Text Accessors for Boolean Fields
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'adakah_barang_kes_didaftarkan_text',
        'adakah_borang_serah_terima_pemilik_saksi_text',
        'adakah_sijil_surat_kebenaran_ipo_text',
        'adakah_gambar_pelupusan_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_gambar_post_mortem_mayat_di_hospital_text',
        'status_gambar_barang_kes_am_text',
        'status_gambar_barang_kes_berharga_text',
        'status_gambar_barang_kes_kenderaan_text',
        'status_gambar_barang_kes_darah_text',
        'status_gambar_barang_kes_kontraban_text',
        'status_rj2_text',
        'status_rj2b_text',
        'status_rj9_text',
        'status_rj99_text',
        'status_rj10a_text',
        'status_rj10b_text',
        'status_semboyan_pertama_wanted_person_text',
        'status_semboyan_kedua_wanted_person_text',
        'status_semboyan_ketiga_wanted_person_text',
        'status_penandaan_kelas_warna_text',
        'status_permohonan_laporan_pakar_judi_text',
        'status_laporan_penuh_pakar_judi_text',
        'status_permohonan_laporan_post_mortem_mayat_text',
        'status_laporan_penuh_bedah_siasat_text',
        'status_permohonan_laporan_jabatan_kimia_text',
        'status_laporan_penuh_jabatan_kimia_text',
        'status_permohonan_laporan_jabatan_patalogi_text',
        'status_laporan_penuh_jabatan_patalogi_text',
        'status_permohonan_laporan_puspakom_text',
        'status_laporan_penuh_puspakom_text',
        'status_permohonan_laporan_jpj_text',
        'status_laporan_penuh_jpj_text',
        'status_permohonan_laporan_imigresen_text',
        'status_laporan_penuh_imigresen_text',
        'status_permohonan_laporan_kastam_text',
        'status_laporan_penuh_kastam_text',
        'status_permohonan_laporan_forensik_pdrm_text',
        'status_laporan_penuh_forensik_pdrm_text',
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

    // --- ACCESSORS FOR DYNAMIC CALCULATION ---

    public function getLewatEdaranStatusAttribute(): ?string
    {
        $dateA = $this->tarikh_edaran_minit_ks_pertama;
        $dateB = $this->tarikh_edaran_minit_ks_kedua;

        if (!$dateA || !$dateB) {
            return null;
        }

        // Jenayah rule is > 24 hours
        return $dateA->diffInHours($dateB) > 24 ? 'LEWAT' : 'DALAM TEMPOH';
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

    // --- HELPER & TEXT ACCESSORS ---

    private function formatBooleanToMalay(?bool $value, string $trueText = 'Ya', string $falseText = 'Tidak', string $nullText = '-') : string
    {
        if (is_null($value)) return $nullText;
        return $value ? $trueText : $falseText;
    }

    // --- B3 Accessors ---
    public function getArahanMinitOlehSioStatusTextAttribute(): string { return $this->formatBooleanToMalay($this->arahan_minit_oleh_sio_status, 'Ada', 'Tiada'); }
    public function getArahanMinitKetuaBahagianStatusTextAttribute(): string { return $this->formatBooleanToMalay($this->arahan_minit_ketua_bahagian_status, 'Ada', 'Tiada'); }
    public function getArahanMinitKetuaJabatanStatusTextAttribute(): string { return $this->formatBooleanToMalay($this->arahan_minit_ketua_jabatan_status, 'Ada', 'Tiada'); }
    public function getArahanMinitOlehYaTprStatusTextAttribute(): string { return $this->formatBooleanToMalay($this->arahan_minit_oleh_ya_tpr_status, 'Ada', 'Tiada'); }

    // --- B4 Accessors ---
    public function getAdakahBarangKesDidaftarkanTextAttribute(): string { return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan); }
    public function getAdakahBorangSerahTerimaPemilikSaksiTextAttribute(): string { return $this->formatBooleanToMalay($this->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan'); }
    public function getAdakahSijilSuratKebenaranIpoTextAttribute(): string { return $this->formatBooleanToMalay($this->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan'); }
    public function getAdakahGambarPelupusanTextAttribute(): string { return $this->formatBooleanToMalay($this->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan'); }

    // --- B5 Accessors ---
    public function getStatusIdSiasatanDikemaskiniTextAttribute(): string { return $this->formatBooleanToMalay($this->status_id_siasatan_dikemaskini, 'Dikemaskini', 'Tidak Dikemaskini'); }
    public function getStatusRajahKasarTempatKejadianTextAttribute(): string { return $this->formatBooleanToMalay($this->status_rajah_kasar_tempat_kejadian, 'Ada', 'Tiada'); }
    public function getStatusGambarTempatKejadianTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_tempat_kejadian, 'Ada', 'Tiada'); }
    public function getStatusGambarPostMortemMayatDiHospitalTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada'); }
    public function getStatusGambarBarangKesAmTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_barang_kes_am, 'Ada', 'Tiada'); }
    public function getStatusGambarBarangKesBerhargaTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_barang_kes_berharga, 'Ada', 'Tiada'); }
    public function getStatusGambarBarangKesKenderaanTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada'); }
    public function getStatusGambarBarangKesDarahTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_barang_kes_darah, 'Ada', 'Tiada'); }
    public function getStatusGambarBarangKesKontrabanTextAttribute(): string { return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada'); }

    // --- B6 Accessors ---
    public function getStatusRj2TextAttribute(): string { return $this->formatBooleanToMalay($this->status_rj2, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj2bTextAttribute(): string { return $this->formatBooleanToMalay($this->status_rj2b, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj9TextAttribute(): string { return $this->formatBooleanToMalay($this->status_rj9, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj99TextAttribute(): string { return $this->formatBooleanToMalay($this->status_rj99, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj10aTextAttribute(): string { return $this->formatBooleanToMalay($this->status_rj10a, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj10bTextAttribute(): string { return $this->formatBooleanToMalay($this->status_rj10b, 'Cipta', 'Tidak Cipta'); }
    public function getStatusSemboyanPertamaWantedPersonTextAttribute(): string { return $this->formatBooleanToMalay($this->status_semboyan_pertama_wanted_person, 'Ada', 'Tiada'); }
    public function getStatusSemboyanKeduaWantedPersonTextAttribute(): string { return $this->formatBooleanToMalay($this->status_semboyan_kedua_wanted_person, 'Ada', 'Tiada'); }
    public function getStatusSemboyanKetigaWantedPersonTextAttribute(): string { return $this->formatBooleanToMalay($this->status_semboyan_ketiga_wanted_person, 'Ada', 'Tiada'); }
    public function getStatusPenandaanKelasWarnaTextAttribute(): string { return $this->formatBooleanToMalay($this->status_penandaan_kelas_warna); }

    // --- B7 Accessors ---
    public function getStatusPermohonanLaporanPakarJudiTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_pakar_judi, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhPakarJudiTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_pakar_judi, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanPostMortemMayatTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_post_mortem_mayat, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhBedahSiasatTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_bedah_siasat, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanJabatanKimiaTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_jabatan_kimia, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhJabatanKimiaTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_jabatan_kimia, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanJabatanPatalogiTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_jabatan_patalogi, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhJabatanPatalogiTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_jabatan_patalogi, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanPuspakomTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_puspakom, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhPuspakomTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_puspakom, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanJpjTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_jpj, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhJpjTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_jpj, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanImigresenTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_imigresen, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhImigresenTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_imigresen, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanKastamTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_kastam, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhKastamTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_kastam, 'Diterima', 'Tidak'); }
    public function getStatusPermohonanLaporanForensikPdrmTextAttribute(): string { return $this->formatBooleanToMalay($this->status_permohonan_laporan_forensik_pdrm, 'Dibuat', 'Tidak'); }
    public function getStatusLaporanPenuhForensikPdrmTextAttribute(): string { return $this->formatBooleanToMalay($this->status_laporan_penuh_forensik_pdrm, 'Diterima', 'Tidak'); }

    // --- B8 Accessors ---
    public function getMukaSurat4BarangKesDitulisTextAttribute(): string { return $this->formatBooleanToMalay($this->muka_surat_4_barang_kes_ditulis); }
    public function getMukaSurat4DenganArahanTprTextAttribute(): string { return $this->formatBooleanToMalay($this->muka_surat_4_dengan_arahan_tpr); }
    public function getMukaSurat4KeputusanKesDicatatTextAttribute(): string { return $this->formatBooleanToMalay($this->muka_surat_4_keputusan_kes_dicatat); }
    public function getFailLmmAdaKeputusanKoronerTextAttribute(): string { return $this->formatBooleanToMalay($this->fail_lmm_ada_keputusan_koroner); }
    public function getStatusKusFailTextAttribute(): string { return $this->formatBooleanToMalay($this->status_kus_fail); }
}