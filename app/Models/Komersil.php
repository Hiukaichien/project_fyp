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
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        
        // B2 - Dates
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        
        // B3 - Arahan & Keputusan
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_oleh_sio_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_bahagian_tarikh' => 'date:Y-m-d',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_ketua_jabatan_tarikh' => 'date:Y-m-d',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'date:Y-m-d',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'array',

        // B4 - Barang Kes
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'array',
        'status_barang_kes_selesai_siasatan' => 'array',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'array',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'resit_kew_38e_pelupusan_tunai_perbendaharaan' => 'array',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'array',
        'adakah_borang_serah_terima_pemilik_saksi' => 'boolean',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',
        'adakah_gambar_pelupusan' => 'boolean',

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

        // B7 - E-FSA & Agensi Luar
        'status_permohonan_laporan_post_mortem_mayat' => 'boolean',
        'tarikh_permohonan_laporan_post_mortem_mayat' => 'date:Y-m-d',
        'status_permohonan_E_FSA_1_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_2_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_2_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_3_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_3_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_4_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_4_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_5_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_5_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_1_telco_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_2_telco_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_3_telco_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_4_telco_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO' => 'date:Y-m-d',
        'status_permohonan_E_FSA_5_telco_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO' => 'boolean',
        'tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO' => 'date:Y-m-d',
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

        // Common timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_id' => 'integer',
    ];

    protected $appends = [
        // Calculated statuses
        'lewat_edaran_status',
        'terbengkalai_status',
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
        'adakah_sijil_surat_kebenaran_ipo_text',
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
        'status_permohonan_laporan_jkr_text',
        'status_laporan_penuh_jkr_text',
        'status_permohonan_laporan_jpj_text',
        'status_laporan_penuh_jpj_text',
        'status_permohonan_laporan_imigresen_text',
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

    // --- Status Calculation Methods (Accessors) ---
    public function getLewatEdaranStatusAttribute(): ?string
    {
        $tarikhA = $this->tarikh_edaran_minit_ks_pertama;
        $tarikhB = $this->tarikh_edaran_minit_ks_kedua;
        $limitInHours = 48;

        if (!$tarikhA || !$tarikhB) {
            return null;
        }

        return $tarikhA->diffInHours($tarikhB) > $limitInHours 
            ? 'YA, LEWAT' 
            : 'DALAM TEMPOH';
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
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_1_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA1OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_1_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA2OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_2_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA2OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_2_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA3OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_3_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA3OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_3_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA4OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_4_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA4OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_4_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA5OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_5_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA5OlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_5_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA1TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_1_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA1TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA2TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_2_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA2TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA3TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_3_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA3TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA4TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_4_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA4TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanEFSA5TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_E_FSA_5_telco_oleh_IO_AIO, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhEFSA5TelcoOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanLaporanPuspakomTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_puspakom, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhPuspakomTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_puspakom, 'Dilampirkan', 'Tidak Dilampirkan');
    }

    public function getStatusPermohonanLaporanJkrTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_laporan_jkr, 'Permohonan Dibuat', 'Tiada Permohonan');
    }

    public function getStatusLaporanPenuhJkrTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_laporan_penuh_jkr, 'Dilampirkan', 'Tidak Dilampirkan');
    }

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
}