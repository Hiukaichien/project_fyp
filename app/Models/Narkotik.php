<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Narkotik extends Model
{
    use HasFactory;

    protected $table = 'narkotik';

    /**
     * The attributes that are not mass assignable.
     * An empty array means all attributes are mass assignable.
     */
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
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'string',
        // B4
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'string',
        'status_pergerakan_barang_kes_makmal' => 'string',
        'status_pergerakan_barang_kes_lain' => 'string',
        'status_barang_kes_selesai_siasatan' => 'string',
        'status_barang_kes_selesai_siasatan_RM' => 'string',
        'status_barang_kes_selesai_siasatan_lain' => 'string',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'string',
        'kaedah_pelupusan_barang_kes_lain' => 'string',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'string',
        'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'boolean',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'boolean',
        'adakah_borang_serah_terima_pemilik_saksi' => 'boolean',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',
        'adakah_gambar_pelupusan' => 'boolean',
        // B5
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_botol_spesimen_urin_3_dimensi_dan_berseal_merah' => 'boolean',
        'status_gambar_pembalut_botol_spesimen_urin_bernombor_siri_dan_test_strip_dadah_positif' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_berharga' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
        'status_gambar_barang_kes_dadah' => 'boolean',
        'status_gambar_barang_kes_ketum' => 'boolean',
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
        'status_semboyan_pertama_wanted_person' => 'boolean',
        'tarikh_semboyan_pertama_wanted_person' => 'date:Y-m-d',
        'status_semboyan_kedua_wanted_person' => 'boolean',
        'tarikh_semboyan_kedua_wanted_person' => 'date:Y-m-d',
        'status_semboyan_ketiga_wanted_person' => 'boolean',
        'tarikh_semboyan_ketiga_wanted_person' => 'date:Y-m-d',
        'status_penandaan_kelas_warna' => 'boolean',

        // B7
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
        // B8
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'keputusan_akhir_mahkamah' => 'string',
        // Common timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_id' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     * This makes the calculated values available in DataTables and exports.
     * It uses the name `original_db_column_name_text`.
     */
    protected $appends = [
        // Calculated statuses (already string outputs)
        'lewat_edaran_status',
        //'terbengkalai_status',
        'terbengkalai_status_dc',
        'terbengkalai_status_da',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan',
        'tempoh_dikemaskini',

        // Appending text versions of boolean fields for display/export
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'adakah_barang_kes_didaftarkan_text',
        'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan_text',
        'adakah_borang_serah_terima_pegawai_tangkapan_text',
        'adakah_borang_serah_terima_pemilik_saksi_text',
        'adakah_sijil_surat_kebenaran_ipo_text',
        'adakah_gambar_pelupusan_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_gambar_botol_spesimen_urin_3_dimensi_dan_berseal_merah_text',
        'status_gambar_pembalut_botol_spesimen_urin_bernombor_siri_dan_test_strip_dadah_positif_text',
        'status_gambar_barang_kes_am_text',
        'status_gambar_barang_kes_berharga_text',
        'status_gambar_barang_kes_kenderaan_text',
        'status_gambar_barang_kes_dadah_text',
        'status_gambar_barang_kes_ketum_text',
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

    public function getLewatEdaranStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;

        if (!$tarikhA || !$tarikhB) {
            return 'TIDAK';
        }

        return $tarikhA->diffInHours($tarikhB) > 48 ? 'LEWAT' : 'DALAM TEMPOH';
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
     * This function receives a PHP boolean (true/false) because of the $casts.
     * It ensures consistent string output for display.
     */

    private function formatBooleanToMalay(?bool $value, string $trueText = 'Ya', string $falseText = 'Tidak', string $nullText = '-'): string
    {
        if (is_null($value)) {
            return $nullText;
        }
        return $value ? $trueText : $falseText;
    }

    //B3
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

    //B4
    public function getAdakahBarangKesDidaftarkanTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan);
    }
    public function getResitKew38eBagiPelupusanBarangKesWangTunaiKePerbendaharaanTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan, 'Ada Dilampirkan', 'Tidak Dilampirkan');
    }
    public function getAdakahBorangSerahTerimaPegawaiTangkapanTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->adakah_borang_serah_terima_pegawai_tangkapan, 'Ada Dilampirkan', 'Tidak Dilampirkan');
    }
    public function getAdakahBorangSerahTerimaPemilikSaksiTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->adakah_borang_serah_terima_pemilik_saksi, 'Ada Dilampirkan', 'Tidak Dilampirkan');
    }
    public function getAdakahSijilSuratKebenaranIpoTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan');
    }
    public function getAdakahGambarPelupusanTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->adakah_gambar_pelupusan, 'Ada Dilampirkan', 'Tidak Dilampirkan');
    }

    //B5
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
    public function getStatusGambarBotolSpesimenUrin3DimensiDanBersealMerahTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_gambar_botol_spesimen_urin_3_dimensi_dan_berseal_merah, 'Ada', 'Tiada');
    }
    public function getStatusGambarPembalutBotolSpesimenUrinBernomborSiriDanTestStripDadahPositifTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_gambar_pembalut_botol_spesimen_urin_bernombor_siri_dan_test_strip_dadah_positif, 'Ada', 'Tiada');
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
    public function getStatusGambarBarangKesDadahTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_dadah, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesKetumTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_ketum, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesDarahTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_darah, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesKontrabanTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada');
    }

    //B6
    public function getStatusRj2TextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_rj2, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj2bTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_rj2b, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj9TextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_rj9, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj99TextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_rj99, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj10aTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_rj10a, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj10bTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_rj10b, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusSemboyanPertamaWantedPersonTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_semboyan_pertama_wanted_person, 'Ada', 'Tiada');
    }
    public function getStatusSemboyanKeduaWantedPersonTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_semboyan_kedua_wanted_person, 'Ada', 'Tiada');
    }
    public function getStatusSemboyanKetigaWantedPersonTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_semboyan_ketiga_wanted_person, 'Ada', 'Tiada');
    }
    public function getStatusPenandaanKelasWarnaTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_penandaan_kelas_warna);
    }

    //B7
    public function getStatusPermohonanLaporanJabatanKimiaTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jabatan_kimia, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJabatanKimiaTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jabatan_kimia, 'Dilampirkan', 'Tidak Dilampirkan');
    }
    public function getStatusPermohonanLaporanJabatanPatalogiTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jabatan_patalogi, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJabatanPatalogiTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jabatan_patalogi, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanPuspakomTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_puspakom, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhPuspakomTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanJpjTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jpj, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhJpjTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jpj, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanImigresenTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_imigresen, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhImigresenTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_imigresen, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanKastamTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_kastam, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhKastamTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_kastam, 'Dilampirkan', 'Tiada');
    }
    public function getStatusPermohonanLaporanForensikPdrmTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_forensik_pdrm, 'Ada', 'Tiada');
    }
    public function getStatusLaporanPenuhForensikPdrmTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_forensik_pdrm, 'Dilampirkan', 'Tiada');
    }

    //B8
    public function getMukaSurat4BarangKesDitulisTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->muka_surat_4_barang_kes_ditulis);
    }
    public function getMukaSurat4DenganArahanTprTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->muka_surat_4_dengan_arahan_tpr);
    }
    public function getMukaSurat4KeputusanKesDicatatTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->muka_surat_4_keputusan_kes_dicatat);
    }
    public function getFailLmmAdaKeputusanKoronerTextAttribute(): string
    {
        return $this->formatBooleanToMalay($this->fail_lmm_ada_keputusan_koroner);
    }
    public function getStatusKusFailTextAttribute(): string
    {
        return $this->status_kus_fail_text ?? '-';
    }
}