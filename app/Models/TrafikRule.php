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
        'fail_lmm_bahagian_pengurusan_pada_muka_surat_2' => 'date:Y-m-d',
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
        'status_laporan_penuh_jkjr' => 'boolean',
        'tarikh_laporan_penuh_jkjr' => 'date:Y-m-d',
        // B8
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'string',
        'adakah_ks_kus_fail_selesai' => 'string',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'string',
        'keputusan_akhir_mahkamah' => 'array',
        'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'string',
    ];

    
     /**
     * The accessors to append to the model's array form.
     * This makes the calculated values available in DataTables.
     */
    protected $appends = [
        'lewat_edaran_48_jam_status',
        'terbengkalai_status',
        'baru_dikemaskini_status',
        'tempoh_lewat_edaran_dikesan', 
        'tempoh_dikemaskini',      
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

    /**
     * Helper function to format boolean values into Malay text.
     */
    private function formatBooleanToMalay($value, $trueText = 'Ya', $falseText = 'Tidak')
    {
        if (is_null($value)) {
            return null; // Or return 'Tidak Diketahui' if you prefer
        }
        return $value ? $trueText : $falseText;
    }

    // B3 Accessors
    public function getArahanMinitOlehSioStatusAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getArahanMinitKetuaBahagianStatusAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getArahanMinitKetuaJabatanStatusAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getArahanMinitOlehYaTprStatusAttribute($value) { return $this->formatBooleanToMalay($value); }

    // B5 Accessors
    public function getStatusIdSiasatanDikemaskiniAttribute($value) { return $this->formatBooleanToMalay($value, 'Dikemaskini', 'Tidak Dikemaskini'); }
    public function getStatusRajahKasarTempatKejadianAttribute($value) { return $this->formatBooleanToMalay($value, 'Ada', 'Tiada'); }
    public function getStatusGambarTempatKejadianAttribute($value) { return $this->formatBooleanToMalay($value, 'Ada', 'Tiada'); }
    
    // B6 Accessors
    public function getStatusRj10bAttribute($value) { return $this->formatBooleanToMalay($value, 'Cipta', 'Tidak Cipta'); }
    public function getStatusSamanPdrmS257Attribute($value) { return $this->formatBooleanToMalay($value, 'Dicipta', 'Tidak Dicipta'); }
    public function getStatusSamanPdrmS167Attribute($value) { return $this->formatBooleanToMalay($value, 'Dicipta', 'Tidak Dicipta'); }

    // B7 Accessors
    public function getStatusPermohonanLaporanJkrAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusLaporanPenuhJkrAttribute($value) { return $this->formatBooleanToMalay($value, 'Dilampirkan', 'Tidak'); }
    public function getStatusPermohonanLaporanJpjAttribute($value) { return $this->formatBooleanToMalay($value); }
    public function getStatusLaporanPenuhJkjrAttribute($value) { return $this->formatBooleanToMalay($value, 'Dilampirkan', 'Tidak'); }
}
