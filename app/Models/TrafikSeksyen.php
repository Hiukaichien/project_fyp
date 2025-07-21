<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrafikSeksyen extends Model
{
    use HasFactory;

    protected $table = 'trafik_seksyen';
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     * This is crucial. Laravel will convert 1/0 from DB to true/false PHP booleans.
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
        // B3 - ENSURE THESE ARE 'boolean'
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'array', // Keep as array for JSON
        // B4 - ENSURE THESE ARE 'boolean' or 'array' for JSON
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'array',
        'status_barang_kes_selesai_siasatan' => 'array',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'array',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'array',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',
        // B5 - ENSURE THESE ARE 'boolean'
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_post_mortem_mayat_di_hospital' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
        'status_gambar_barang_kes_darah' => 'boolean',
        'status_gambar_barang_kes_kontraban' => 'boolean',
        // B6 - ENSURE THESE ARE 'boolean' or 'array' for JSON
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
        // B7 - ENSURE THESE ARE 'boolean'
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
        // B8 - ENSURE THESE ARE 'boolean' or 'array' for JSON
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'keputusan_akhir_mahkamah' => 'array',
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
        'lewat_edaran_48_jam_status',
        'terbengkalai_status',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan',
        'tempoh_dikemaskini',

        // Appending text versions of boolean fields for display/export
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'adakah_barang_kes_didaftarkan_text',
        'adakah_sijil_surat_kebenaran_ipo_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
        'status_gambar_post_mortem_mayat_di_hospital_text',
        'status_gambar_barang_kes_am_text',
        'status_gambar_barang_kes_kenderaan_text',
        'status_gambar_barang_kes_darah_text',
        'status_gambar_barang_kes_kontraban_text',
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
        'status_permohonan_laporan_puspakom_text',
        'status_laporan_penuh_puspakom_text',
        'status_permohonan_laporan_jkr_text',
        'status_laporan_penuh_jkr_text',
        'status_permohonan_laporan_jpj_text',
        'status_laporan_penuh_jpj_text',
        'status_permohonan_laporan_imigresen_text',
        'status_laporan_penuh_imigresen_text',
        'muka_surat_4_barang_kes_ditulis_text',
        'muka_surat_4_dengan_arahan_tpr_text',
        'muka_surat_4_keputusan_kes_dicatat_text',
        'fail_lmm_ada_keputusan_koroner_text',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- ACCESSORS FOR DYNAMIC CALCULATION ---
    // (Keep your existing calculated accessors, e.g., getLewatEdaran48JamStatusAttribute)
    public function getLewatEdaran48JamStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;

        if (!$tarikhA || !$tarikhB) {
            return null; // Cannot calculate if dates are missing
        }

        return $tarikhA->diffInHours($tarikhB) > 48 ? 'YA, LEWAT' : 'DALAM TEMPOH';
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

    public function getTerbengkalaiStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;

        if ($tarikhA && !$tarikhD) {
            return $tarikhA->diffInMonths(Carbon::now()) > 3 ? 'YA, TERBENGKALAI' : 'TIDAK TERBENGKALAI';
        }

        return 'TIDAK BERKENAAN'; // Not considered abandoned if it has an end date or never started
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
     * This function receives a PHP boolean (true/false) because of the $casts.
     * It ensures consistent string output for display.
     */
    private function formatBooleanToMalay(?bool $value, string $trueText = 'Ya', string $falseText = 'Tidak', string $nullText = '-') : string
    {
        if (is_null($value)) {
            return $nullText;
        }
        return $value ? $trueText : $falseText;
    }

    // --- Accessors for Boolean Fields to display Malay Text ---
    // These methods return the Malay string which is then picked up by DataTables.

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

    // B4 Accessors
    public function getAdakahBarangKesDidaftarkanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan);
    }
    public function getAdakahSijilSuratKebenaranIpoTextAttribute(): string {
        return $this->formatBooleanToMalay($this->adakah_sijil_surat_kebenaran_ipo, 'Ada Dilampirkan', 'Tidak Dilampirkan');
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
    public function getStatusGambarPostMortemMayatDiHospitalTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_post_mortem_mayat_di_hospital, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesAmTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_am, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesKenderaanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kenderaan, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesDarahTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_darah, 'Ada', 'Tiada');
    }
    public function getStatusGambarBarangKesKontrabanTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kontraban, 'Ada', 'Tiada');
    }

    // B6 Accessors
    public function getStatusRj2TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj2, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj2bTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj2b, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj9TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj9, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj99TextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj99, 'Cipta', 'Tidak Cipta');
    }
    public function getStatusRj10aTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_rj10a, 'Cipta', 'Tidak Cipta');
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
    public function getStatusSemboyanPertamaWantedPersonTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_semboyan_pertama_wanted_person);
    }
    public function getStatusSemboyanKeduaWantedPersonTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_semboyan_kedua_wanted_person);
    }
    public function getStatusSemboyanKetigaWantedPersonTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_semboyan_ketiga_wanted_person);
    }
    public function getStatusPenandaanKelasWarnaTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_penandaan_kelas_warna);
    }

    // B7 Accessors
    public function getStatusPermohonanLaporanPuspakomTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_puspakom);
    }
    public function getStatusLaporanPenuhPuspakomTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tidak');
    }
    public function getStatusPermohonanLaporanJkrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jkr);
    }
    public function getStatusLaporanPenuhJkrTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jkr, 'Dilampirkan', 'Tidak');
    }
    public function getStatusPermohonanLaporanJpjTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jpj);
    }
    public function getStatusLaporanPenuhJpjTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jpj, 'Dilampirkan', 'Tidak');
    }
    public function getStatusPermohonanLaporanImigresenTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_imigresen);
    }
    public function getStatusLaporanPenuhImigresenTextAttribute(): string {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_imigresen, 'Dilampirkan', 'Tidak');
    }

    // B8 Accessors
    public function getMukaSurat4BarangKesDitulisTextAttribute(): string {
        return $this->formatBooleanToMalay($this->muka_surat_4_barang_kes_ditulis);
    }
    public function getMukaSurat4DenganArahanTprTextAttribute(): string {
        return $this->formatBooleanToMalay($this->muka_surat_4_dengan_arahan_tpr);
    }
    public function getMukaSurat4KeputusanKesDicatatTextAttribute(): string {
        return $this->formatBooleanToMalay($this->muka_surat_4_keputusan_kes_dicatat);
    }
    public function getFailLmmAdaKeputusanKoronerTextAttribute(): string {
        return $this->formatBooleanToMalay($this->fail_lmm_ada_keputusan_koroner);
    }
}