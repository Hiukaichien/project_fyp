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
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'array',
        // B4
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'array',
        'status_barang_kes_selesai_siasatan' => 'array',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'array',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'array',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',
        // B5
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_post_mortem_mayat_di_hospital' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
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
        'status_saman_pdrm_s_257' => 'boolean',
        'status_saman_pdrm_s_167' => 'boolean',
        'status_semboyan_pertama_wanted_person' => 'boolean',
        'tarikh_semboyan_pertama_wanted_person' => 'date:Y-m-d',
        'status_semboyan_kedua_wanted_person' => 'boolean',
        'tarikh_semboyan_kedua_wanted_person' => 'date:Y-m-d',
        'status_semboyan_ketiga_wanted_person' => 'boolean',
        'tarikh_semboyan_ketiga_wanted_person' => 'date:Y-m-d',
        'status_penandaan_kelas_warna' => 'boolean',
        // B7
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
        // B8
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'keputusan_akhir_mahkamah' => 'array',
    ];
    
     /**
     * The accessors to append to the model's array form.
     * This makes the calculated values available in DataTables.
     */
    protected $appends = [
        'lewat_edaran_48_jam_status',
        'terbengkalai_status',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan', // ADDED
        'tempoh_dikemaskini',        // ADDED
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- ACCESSORS FOR DYNAMIC CALCULATION ---
    
    /**
     * Logic based on "Contoh 2 - LEWAT 48 JAM"
     */
    public function getLewatEdaran48JamStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;

        if (!$tarikhA || !$tarikhB) {
            return null; // Cannot calculate if dates are missing
        }

        return $tarikhA->diffInHours($tarikhB) > 48 ? 'YA, LEWAT' : 'DALAM TEMPOH';
    }

    /**
     * NEW Accessor based on "Contoh 2 - TEMPOH LEWAT EDARAN DIKESAN"
     */
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

    /**
     * Logic based on "Contoh 3 - TERBENGKALAI MELEBIHI 3 BULAN"
     */
    public function getTerbengkalaiStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhD = $this->tarikh_edaran_minit_ks_akhir;

        if ($tarikhA && !$tarikhD) {
            return $tarikhA->diffInMonths(Carbon::now()) > 3 ? 'YA, TERBENGKALAI' : 'TIDAK TERBENGKALAI';
        }
        
        return 'TIDAK BERKENAAN'; // Not considered abandoned if it has an end date or never started
    }

    /**
     * Logic based on "Contoh 4 - BARU DIKEMASKINI"
     */
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

    /**
     * NEW Accessor based on "Contoh 4 - TEMPOH DIKEMASKINI"
     */
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

        private function formatBooleanToMalay($value, $trueText = 'Ya', $falseText = 'Tidak')
    {
        if (is_null($value)) {
            return null; // Return null if the value is not set
        }
        return $value ? $trueText : $falseText;
    }

    // B3
    public function getArahanMinitOlehSioStatusAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getArahanMinitKetuaBahagianStatusAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getArahanMinitKetuaJabatanStatusAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getArahanMinitOlehYaTprStatusAttribute($value) { return $this->formatBooleanToMalay($value); }

    // B4
    public function getAdakahBarangKesDidaftarkanAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getAdakahSijilSuratKebenaranIpoAttribute($value) { return $this->formatBooleanToMalay($value); }

    // B5
    public function getStatusIdSiasatanDikemaskiniAttribute($value) { return $this->formatBooleanToMalay($value, 'Dikemaskini', 'Tidak Dikemaskini'); }
    public function getStatusRajahKasarTempatKejadianAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusGambarTempatKejadianAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusGambarPostMortemMayatDiHospitalAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusGambarBarangKesAmAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusGambarBarangKesKenderaanAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusGambarBarangKesDarahAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusGambarBarangKesKontrabanAttribute($value) { return $this->formatBooleanToMalay($value); }

    // B6
    public function getStatusRj2Attribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj2bAttribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj9Attribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj99Attribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj10aAttribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusRj10bAttribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusSamanPdrmS257Attribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusSamanPdrmS167Attribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusSemboyanPertamaWantedPersonAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusSemboyanKeduaWantedPersonAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusSemboyanKetigaWantedPersonAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusPenandaanKelasWarnaAttribute($value) { return $this->formatBooleanToMalay($value); }
    
    // B7
    public function getStatusPermohonanLaporanPuspakomAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusLaporanPenuhPuspakomAttribute($value) { return $this->formatBooleanToMalay($value, 'Dilampirkan', 'Tidak'); }
    public function getStatusPermohonanLaporanJkrAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusLaporanPenuhJkrAttribute($value) { return $this->formatBooleanToMalay($value, 'Dilampirkan', 'Tidak'); }
    public function getStatusPermohonanLaporanJpjAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusLaporanPenuhJpjAttribute($value) { return $this->formatBooleanToMalay($value, 'Dilampirkan', 'Tidak'); }
    public function getStatusPermohonanLaporanImigresenAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusLaporanPenuhImigresenAttribute($value) { return $this->formatBooleanToMalay($value, 'Dilampirkan', 'Tidak'); }

    // B8
    public function getMukaSurat4BarangKesDitulisAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getMukaSurat4DenganArahanTprAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getMukaSurat4KeputusanKesDicatatAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getFailLmmAdaKeputusanKoronerAttribute($value) { return $this->formatBooleanToMalay($value); }
}
