<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // For date calculations
use Kyslik\ColumnSortable\Sortable; 

class KertasSiasatan extends Model
{
    use HasFactory, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_ks',
        'tarikh_ks',
        'no_report',
        'jenis_jabatan_ks',
        'pegawai_penyiasat',
        'status_ks',
        'status_kes',
        'seksyen',
        'tarikh_minit_a',
        'tarikh_minit_b',
        'tarikh_minit_c',
        'tarikh_minit_d',
        'edar_lebih_24_jam_status', // System calculated, but allow mass assignment if set directly
        'terbengkalai_3_bulan_status', // System calculated
        'baru_kemaskini_status', // System calculated
        'status_ks_semasa_diperiksa',
        'tarikh_status_ks_semasa_diperiksa',
        'rakaman_pengadu',
        'rakaman_saspek',
        'rakaman_saksi',
        'id_siasatan_dilampirkan',
        'tarikh_id_siasatan_dilampirkan',
        'barang_kes_am_didaftar',
        'no_daftar_kes_am',
        'no_daftar_kes_senjata_api',
        'no_daftar_kes_berharga',
        'gambar_rampasan_dilampirkan',
        'kedudukan_barang_kes',
        'surat_serah_terima_stor',
        'arahan_pelupusan',
        'tatacara_pelupusan',
        'resit_kew38e_dilampirkan',
        'sijil_pelupusan_dilampirkan',
        'gambar_pelupusan_dilampirkan',
        'surat_serah_terima_penuntut',
        'ulasan_barang_kes',
        'surat_mohon_pakar_judi',
        'laporan_pakar_judi',
        'keputusan_pakar_judi',
        'kategori_perjudian',
        'surat_mohon_forensik',
        'laporan_forensik',
        'keputusan_forensik',
        'surat_jamin_polis',
        'lakaran_lokasi',
        'gambar_lokasi',
        'rj2_status', 'rj2_tarikh',
        'rj9_status', 'rj9_tarikh',
        'rj10a_status', 'rj10a_tarikh',
        'rj10b_status', 'rj10b_tarikh',
        'rj99_status', 'rj99_tarikh',
        'semboyan_kesan_tangkap_status', 'semboyan_kesan_tangkap_tarikh',
        'waran_tangkap_status', 'waran_tangkap_tarikh',
        'ulasan_isu_rj',
        'pem1_status', 'pem2_status', 'pem3_status', 'pem4_status',
        'isu_tpr_tuduh',
        'isu_ks_lengkap_tiada_rujuk_tpr',
        'isu_tpr_arah_lupus_belum_laksana',
        'isu_tpr_arah_pulang_belum_laksana',
        'isu_tpr_arah_kesan_tangkap_tiada_tindakan',
        'isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus',
        'isu_nfa_oleh_kbsjd_sahaja',
        'isu_selesai_jatuh_hukum_belum_kus_fail',
        'isu_ks_warisan_terbengkalai',
        'isu_kbsjd_simpan_ks',
        'isu_sio_simpan_ks',
        'isu_ks_pada_tpr',
        'ks_hantar_tpr_status', 'ks_hantar_tpr_tarikh',
        'ks_hantar_kjsj_status', 'ks_hantar_kjsj_tarikh',
        'ks_hantar_d5_status', 'ks_hantar_d5_tarikh',
        'ks_hantar_kbsjd_status', 'ks_hantar_kbsjd_tarikh',
        'ulasan_isu_menarik',
        'ulasan_keseluruhan',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tarikh_ks' => 'date:Y-m-d',
        'tarikh_minit_a' => 'date:Y-m-d',
        'tarikh_minit_b' => 'date:Y-m-d',
        'tarikh_minit_c' => 'date:Y-m-d',
        'tarikh_minit_d' => 'date:Y-m-d',
        'tarikh_status_ks_semasa_diperiksa' => 'date:Y-m-d',
        'tarikh_id_siasatan_dilampirkan' => 'date:Y-m-d',
        'rj2_tarikh' => 'date:Y-m-d',
        'rj9_tarikh' => 'date:Y-m-d',
        'rj10a_tarikh' => 'date:Y-m-d',
        'rj10b_tarikh' => 'date:Y-m-d',
        'rj99_tarikh' => 'date:Y-m-d',
        'semboyan_kesan_tangkap_tarikh' => 'date:Y-m-d',
        'waran_tangkap_tarikh' => 'date:Y-m-d',
        'ks_hantar_tpr_tarikh' => 'date:Y-m-d',
        'ks_hantar_kjsj_tarikh' => 'date:Y-m-d',
        'ks_hantar_d5_tarikh' => 'date:Y-m-d',
        'ks_hantar_kbsjd_tarikh' => 'date:Y-m-d',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Defines which columns are sortable.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'no_ks',
        'tarikh_ks',
        'no_report',
        'pegawai_penyiasat',
        'status_ks',
        'status_kes',
        'created_at',
        'updated_at'
    ];

    /**
     * Defines columns that are aliased for sorting or can be used as
     * a model-level default sort.
     * The controller's sortable() method parameters (e.g., ['created_at' => 'desc'])
     * will take precedence for the initial default sort of the table.
     * This array is more for aliasing or if sortable() is called without parameters.
     *
     * @var array
     */
    public $sortableAs = [
        // Example: If you had a 'user_full_name' that was a CONCAT in SQL,
        // you could alias it here. For standard columns, it's less common unless
        // you want to provide a model-level default sort if the controller doesn't.
        // 'no_ks', // If you wanted 'no_ks' to be a default sort candidate from the model.
    ];

    // --- Calculation Logic ---

    public function calculateEdarLebih24Jam()
    {
        if ($this->tarikh_ks && $this->tarikh_minit_a) {
            $tarikhKs = Carbon::parse($this->tarikh_ks);
            $tarikhMinitA = Carbon::parse($this->tarikh_minit_a);
            if ($tarikhMinitA->diffInHours($tarikhKs) > 24) {
                $this->edar_lebih_24_jam_status = 'YA, EDARAN LEWAT 24 JAM';
            } else {
                $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH 24 JAM & KURANG';
            }
        } else {
            $this->edar_lebih_24_jam_status = null;
        }
    }

    public function calculateTerbengkalai3Bulan()
    {
        // Determine the last significant activity date.
        // This could be tarikh_minit_d, or updated_at, or another specific date field
        // For this example, let's assume tarikh_minit_d is the most relevant.
        // If tarikh_minit_d is null, consider it not terbengkalai or use another logic.
        $lastActivityDate = null;
        if ($this->tarikh_minit_d) {
            $lastActivityDate = Carbon::parse($this->tarikh_minit_d);
        } elseif ($this->updated_at) { // Fallback to last update if no minit D
            $lastActivityDate = Carbon::parse($this->updated_at);
        }


        if ($lastActivityDate && $lastActivityDate->diffInMonths(Carbon::now()) >= 3) {
            $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
        } else {
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
        }
    }

    public function calculateBaruKemaskini()
    {
        // This logic might depend on previous status or specific actions.
        // Example: If it was 'YA, TERBENGKALAI LEBIH 3 BULAN' and updated_at is recent.
        // Let's assume if it was terbengkalai and updated recently, it's "baru dikemaskini"
        
        // Ensure updated_at is not null before calling methods on it.
        if ($this->updated_at && $this->terbengkalai_3_bulan_status === 'YA, TERBENGKALAI LEBIH 3 BULAN' && Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
            $this->baru_kemaskini_status = 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI';
        } else {
             // If it's not terbengkalai, or not recently updated after being terbengkalai
            $this->baru_kemaskini_status = 'TIADA ISU';
        }
    }

    /**
     * Handles nullifying dates if their corresponding status/enum is not 'YA' or 'Cipta' etc.
     * Call this before saving in the update method.
     */
    public function handleConditionalDates()
    {
        if ($this->status_ks_semasa_diperiksa == null || $this->status_ks_semasa_diperiksa == '') {
            $this->tarikh_status_ks_semasa_diperiksa = null;
        }
        if ($this->id_siasatan_dilampirkan !== 'YA') {
            $this->tarikh_id_siasatan_dilampirkan = null;
        }
        if ($this->rj2_status !== 'Cipta') {
            $this->rj2_tarikh = null;
        }
        if ($this->rj9_status !== 'Cipta') {
            $this->rj9_tarikh = null;
        }
        if ($this->rj10a_status !== 'Cipta') {
            $this->rj10a_tarikh = null;
        }
        if ($this->rj10b_status !== 'Cipta') {
            $this->rj10b_tarikh = null;
        }
        if ($this->rj99_status !== 'Cipta') {
            $this->rj99_tarikh = null;
        }
        if ($this->semboyan_kesan_tangkap_status !== 'Cipta') {
            $this->semboyan_kesan_tangkap_tarikh = null;
        }
        if ($this->waran_tangkap_status !== 'Mohon') {
            $this->waran_tangkap_tarikh = null;
        }
        if ($this->ks_hantar_tpr_status !== 'YA') {
            $this->ks_hantar_tpr_tarikh = null;
        }
        if ($this->ks_hantar_kjsj_status !== 'YA') {
            $this->ks_hantar_kjsj_tarikh = null;
        }
        if ($this->ks_hantar_d5_status !== 'YA') {
            $this->ks_hantar_d5_tarikh = null;
        }
        if ($this->ks_hantar_kbsjd_status !== 'YA') {
            $this->ks_hantar_kbsjd_tarikh = null;
        }
    }

    /**
     * Boot method to register model event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Calculate statuses before saving
        static::saving(function ($model) {
            $model->calculateEdarLebih24Jam();
            $model->calculateTerbengkalai3Bulan();
            $model->calculateBaruKemaskini();
            $model->handleConditionalDates(); 
        });
    }
}