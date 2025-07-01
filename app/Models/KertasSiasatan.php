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
        'project_id',
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
        //'id',
        //'no_ks',
        'tarikh_ks',
        //'no_report',
        //'pegawai_penyiasat',
        //'status_ks',
        //'status_kes',
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

    /**
     * Get the project that this Kertas Siasatan belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- Calculation Logic ---

    public function calculateEdarLebih24Jam() 
    {
        if ($this->tarikh_minit_a && $this->tarikh_minit_b) {
            $tarikhA = Carbon::parse($this->tarikh_minit_a)->startOfDay(); // Use startOfDay if time part is not relevant for this rule
            $tarikhB = Carbon::parse($this->tarikh_minit_b)->startOfDay();

            // Ensure Tarikh B is actually after Tarikh A for a meaningful "duration"
            if ($tarikhB->isAfter($tarikhA)) {
                if ($tarikhB->diffInHours($tarikhA) > 24) {
                    $this->edar_lebih_24_jam_status = 'YA, EDARAN LEWAT 24 JAM';
                } else {
                    $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH 24 JAM & KURANG';
                }
            } else {
                // Tarikh B is not after Tarikh A, so the condition "A minus B" for duration > 24h is not met
                $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH 24 JAM & KURANG'; // Or specific status like 'TARIKH B TIDAK SELEPAS A'
            }
        } else {
            $this->edar_lebih_24_jam_status = null; // Or 'TIADA DATA MINIT A/B'
        }
    }


    public function calculateTerbengkalai3Bulan() 
    {
        if ($this->tarikh_minit_a && $this->tarikh_minit_d) {
            $tarikhA = Carbon::parse($this->tarikh_minit_a);
            $tarikhD = Carbon::parse($this->tarikh_minit_d);

            // Ensure Tarikh D is after Tarikh A for the duration to be meaningful in this context
            if ($tarikhD->isAfter($tarikhA)) {
                if ($tarikhA->diffInMonths($tarikhD) >= 3) { // Duration between A and D
                    $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
                } else {
                    $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI (A-D < 3 BULAN)';
                }
            } else {
                // Tarikh D is not after Tarikh A, so the A-D duration condition is not met as specified
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI (D TIDAK SELEPAS A)';
            }
        } else {
            $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI (TIADA DATA A/D)';
        }
    }

    /**
     * Client Logic: KS Baru Kemaskini Setelah Semboyan JIPS
     * TARIKH C MINUS TARIKH D = KS BARU DIKEMASKINI PERGERAKAN
     *
     * INTERPRETATION: If tarikh_minit_d exists, is chronologically after tarikh_minit_c,
     * AND tarikh_minit_d is very recent (e.g., updated_at or tarikh_minit_d itself is within last 7 days from NOW).
     * "Setelah Semboyan JIPS" is not directly used here without more info.
     * THIS IS AN ASSUMPTION AND NEEDS CLIENT CLARIFICATION.
     */
    public function calculateBaruKemaskini() // Renamed
    {
        $this->baru_kemaskini_status = 'TIADA PERGERAKAN BARU C-D'; // Default status

        if ($this->tarikh_minit_c && $this->tarikh_minit_d) {
            $tarikhC = Carbon::parse($this->tarikh_minit_c);
            $tarikhD = Carbon::parse($this->tarikh_minit_d);

            // Interpretation: "Baru Dikemaskini Pergerakan" means D happened after C, AND D is recent.
            // The "recency" could be based on tarikh_minit_d itself or the record's updated_at.
            // Let's use updated_at as a general indicator of recent interaction with the record *after* D.
            // The client may need to specify if "Semboyan JIPS" refers to a specific flag or date.

            if ($tarikhD->isAfter($tarikhC)) {
                // Condition 1: Movement from C to D has occurred.
                // Condition 2: The record itself was updated recently (within last 7 days).
                // This implies that after the C->D movement, someone has touched/saved the record again recently.
                if ($this->updated_at && Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                    $this->baru_kemaskini_status = 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI';
                } else {
                   
                    $this->baru_kemaskini_status = 'PERGERAKAN C-D LAMA'; 
                }
            } else {
                // D is not after C, so no C->D "pergerakan" in that order.
                 $this->baru_kemaskini_status = 'TIADA PERGERAKAN C KE D';
            }
        } elseif ($this->tarikh_minit_d && !$this->tarikh_minit_c) {

             if ($this->updated_at && Carbon::parse($this->updated_at)->isAfter(Carbon::now()->subDays(7))) {
                $this->baru_kemaskini_status = 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI (MINIT D)';
            }
        }
        // Else, it remains the default 'TIADA PERGERAKAN BARU C-D'
    }
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