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
        // Dates
        'tarikh_laporan_polis_dibuka' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_pertama' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_kedua' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'date:Y-m-d',
        'tarikh_edaran_minit_ks_akhir' => 'date:Y-m-d',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'date:Y-m-d',
        
        // B3 - Booleans
        'arahan_minit_oleh_sio_status' => 'boolean',
        'arahan_minit_ketua_bahagian_status' => 'boolean',
        'arahan_minit_ketua_jabatan_status' => 'boolean',
        'arahan_minit_oleh_ya_tpr_status' => 'boolean',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'array',

        // B4 - Booleans and Arrays
        'adakah_barang_kes_didaftarkan' => 'boolean',
        'status_pergerakan_barang_kes' => 'array',
        'status_barang_kes_selesai_siasatan' => 'array',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'array',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'array',
        'resit_kew_98e_pelupusan_tunai_perbendaharaan' => 'array',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'array',
        'adakah_sijil_surat_kebenaran_ipo' => 'boolean',

        // B5 - Booleans
        'status_id_siasatan_dikemaskini' => 'boolean',
        'status_rajah_kasar_tempat_kejadian' => 'boolean',
        'status_gambar_tempat_kejadian' => 'boolean',
        'status_gambar_barang_kes_am' => 'boolean',
        'status_gambar_barang_kes_berharga' => 'boolean',
        'status_gambar_barang_kes_kenderaan' => 'boolean',
        'status_gambar_barang_kes_darah' => 'boolean',
        'status_gambar_barang_kes_kontraban' => 'boolean',

        // B6 - Booleans, Arrays and Dates
        'status_pem' => 'array',
        'status_rj2' => 'boolean',
        'status_rj2b' => 'boolean',
        'status_rj9' => 'boolean',
        'status_rj99' => 'boolean',
        'status_rj10a' => 'boolean',
        'status_rj10b' => 'boolean',

        // B7 - E-FSA Related Booleans and Dates
        'status_permohonan_E_FSA_1_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'boolean',
        'status_permohonan_E_FSA_1_telco_oleh_IO_AIO' => 'boolean',
        'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'boolean',
        'status_permohonan_E_FSA_2_oleh_IO_AIO' => 'boolean',
        'status_permohonan_E_FSA_3_oleh_IO_AIO' => 'boolean',
        'status_permohonan_E_FSA_4_oleh_IO_AIO' => 'boolean',
        'status_permohonan_E_FSA_5_oleh_IO_AIO' => 'boolean',
        
        // B8 - Status Fail Booleans and Arrays
        'muka_surat_4_barang_kes_ditulis' => 'boolean',
        'muka_surat_4_dengan_arahan_tpr' => 'boolean',
        'muka_surat_4_keputusan_kes_dicatat' => 'boolean',
        'fail_lmm_ada_keputusan_koroner' => 'boolean',
        'keputusan_akhir_mahkamah' => 'array',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_id' => 'integer',
    ];

    protected $appends = [
        'lewat_edaran_48_jam_status',
        'terbengkalai_status',
        'baru_dikemaskini_status',
        
        // Boolean field text representations
        'arahan_minit_oleh_sio_status_text',
        'arahan_minit_ketua_bahagian_status_text',
        'arahan_minit_ketua_jabatan_status_text',
        'arahan_minit_oleh_ya_tpr_status_text',
        'adakah_barang_kes_didaftarkan_text',
        'adakah_sijil_surat_kebenaran_ipo_text',
        'status_id_siasatan_dikemaskini_text',
        'status_rajah_kasar_tempat_kejadian_text',
        'status_gambar_tempat_kejadian_text',
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
        'status_permohonan_lain_lain_oleh_IO_AIO_text',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan_text',
        'adakah_ks_kus_fail_selesai_text',
        'adakah_muka_surat_4_keputusan_kes_dicatat_text',
        'status_semboyan_pertama_wanted_person_text',
        'status_semboyan_kedua_wanted_person_text',
        'status_semboyan_ketiga_wanted_person_text',
        'status_penandaan_kelas_warna_text',
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

    // --- Status Calculation Methods ---
    public function getLewatEdaran48JamStatusAttribute(): ?string
    {
        if (!$this->tarikh_edaran_minit_ks_pertama || !$this->tarikh_edaran_minit_ks_kedua) {
            return null;
        }

        return $this->tarikh_edaran_minit_ks_pertama->diffInHours($this->tarikh_edaran_minit_ks_kedua) > 48 
            ? 'YA, LEWAT EDARAN' 
            : 'DALAM TEMPOH';
    }

    public function getTerbengkalaiStatusAttribute(): string
    {
        if (!$this->tarikh_edaran_minit_ks_pertama) {
            return 'TIDAK DAPAT DITENTUKAN';
        }

        if (!$this->tarikh_edaran_minit_ks_akhir) {
            return $this->tarikh_edaran_minit_ks_pertama->diffInMonths(now()) > 3 
                ? 'YA, TERBENGKALAI' 
                : 'TIDAK TERBENGKALAI';
        }

        return 'TIDAK TERBENGKALAI';
    }

    public function getBaruDikemaskiniStatusAttribute(): string
    {
        if ($this->updated_at && $this->updated_at->isAfter(now()->subDays(7))) {
            return 'YA, BARU DIKEMASKINI';
        }

        return 'TIADA PERGERAKAN BARU';
    }

    // --- Boolean Field Text Accessors ---
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

    public function getAdakahBarangKesDidaftarkanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->adakah_barang_kes_didaftarkan);
    }

    public function getAdakahSijilSuratKebenaranIpoTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->adakah_sijil_surat_kebenaran_ipo);
    }

    public function getStatusIdSiasatanDikemaskiniTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_id_siasatan_dikemaskini);
    }

    public function getStatusRajahKasarTempatKejadianTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rajah_kasar_tempat_kejadian);
    }

    public function getStatusGambarTempatKejadianTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_tempat_kejadian);
    }

    public function getStatusGambarBarangKesAmTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_am);
    }

    public function getStatusGambarBarangKesBerhargaTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_berharga);
    }

    public function getStatusGambarBarangKesKenderaanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kenderaan);
    }

    public function getStatusGambarBarangKesDarahTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_darah);
    }

    public function getStatusGambarBarangKesKontrabanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_gambar_barang_kes_kontraban);
    }

    public function getStatusRj2TextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rj2);
    }

    public function getStatusRj2bTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rj2b);
    }

    public function getStatusRj9TextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rj9);
    }

    public function getStatusRj99TextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rj99);
    }

    public function getStatusRj10aTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rj10a);
    }

    public function getStatusRj10bTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_rj10b);
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

    public function getAdakahMukaSurat4KeputusanKesDicatatTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->adakah_muka_surat_4_keputusan_kes_dicatat);
    }

    public function getAdakahFailLmmTAtauLmmTelahAdaKeputusanTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan);
    }

    public function getAdakahKsKusFailSelesaiTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->adakah_ks_kus_fail_selesai);
    }

    public function getStatusPermohonanLainLainOlehIOAIOTextAttribute(): string 
    {
        return $this->formatBooleanToMalay($this->status_permohonan_lain_lain_oleh_IO_AIO);
    }
}