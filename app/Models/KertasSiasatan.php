<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KertasSiasatan extends Model
{
    use HasFactory;

    protected $fillable = [
        // Basic Info (Some might be non-editable, adjust as needed)
        // 'no_ks', // Usually not editable after creation
        'tarikh_ks',
        'no_report',
        'jenis_jabatan_ks',
        'pegawai_penyiasat',
        'status_ks',
        'status_kes',
        'seksyen',

        // Minit Edaran
        'tarikh_minit_a',
        'tarikh_minit_b',
        'tarikh_minit_c',
        'tarikh_minit_d',

        // Status Semasa Diperiksa
        'status_ks_semasa_diperiksa',
        'tarikh_status_ks_semasa_diperiksa',

        // Rakaman Percakapan
        'rakaman_pengadu',
        'rakaman_saspek',
        'rakaman_saksi',

        // ID Siasatan Lampiran
        'id_siasatan_dilampirkan',
        'tarikh_id_siasatan_dilampirkan',

        // Barang Kes
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

        // Pakar Judi / Forensik
        'surat_mohon_pakar_judi',
        'laporan_pakar_judi',
        'keputusan_pakar_judi',
        'kategori_perjudian',
        'surat_mohon_forensik',
        'laporan_forensik',
        'keputusan_forensik',

        // Dokumen Lain
        'surat_jamin_polis',
        'lakaran_lokasi',
        'gambar_lokasi',

        // RJ Forms
        'rj2_status', 'rj2_tarikh',
        'rj9_status', 'rj9_tarikh',
        'rj10a_status', 'rj10a_tarikh',
        'rj10b_status', 'rj10b_tarikh',
        'rj99_status', 'rj99_tarikh',
        'semboyan_kesan_tangkap_status', 'semboyan_kesan_tangkap_tarikh',
        'waran_tangkap_status', 'waran_tangkap_tarikh',
        'ulasan_isu_rj',

        // Surat Pemberitahuan
        'pem1_status', 'pem2_status', 'pem3_status', 'pem4_status',

        // Isu-Isu
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

        // KS Hantar Status
        'ks_hantar_tpr_status', 'ks_hantar_tpr_tarikh',
        'ks_hantar_kjsj_status', 'ks_hantar_kjsj_tarikh',
        'ks_hantar_d5_status', 'ks_hantar_d5_tarikh',
        'ks_hantar_kbsjd_status', 'ks_hantar_kbsjd_tarikh',

        // Ulasan Pemeriksa
        'ulasan_isu_menarik',
        'ulasan_keseluruhan',

        // Note: Calculated fields are usually NOT in fillable as they are set by the system
        // 'edar_lebih_24_jam_status',
        // 'terbengkalai_3_bulan_status',
        // 'baru_kemaskini_status',
    ];

    // Cast date fields to Carbon instances
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
    ];

    // --- Auto-Calculation Logic (Example using Mutators/Events) ---
    // Option 1: Using Model Observers (Recommended for complex logic/side effects)
    // php artisan make:observer KertasSiasatanObserver --model=KertasSiasatan
    // Register in AppServiceProvider or create a dedicated service provider.
    // In KertasSiasatanObserver.php:
    /*
    public function saving(KertasSiasatan $ks)
    {
        $ks->calculateEdarLebih24Jam();
        $ks->calculateTerbengkalai3Bulan();
        $ks->calculateBaruKemaskini();
        // Ensure conditional dates are nulled if the condition is false
        $ks->handleConditionalDates();
    }
    */

    // Option 2: Using Mutators (Simpler for direct attribute changes)
    // Note: Mutators run *before* saving. Calculations might need latest data.
    // Observers (triggered by saving/updating events) are generally better for this.

    // Helper methods for calculation (can be called from Observer or Controller)
    public function calculateEdarLebih24Jam()
    {
        if ($this->tarikh_minit_a && $this->tarikh_minit_b) {
            $dateA = Carbon::parse($this->tarikh_minit_a);
            $dateB = Carbon::parse($this->tarikh_minit_b);
            // Assuming B should be within 24 hours *after* A
            if ($dateB->diffInHours($dateA) > 24) {
                $this->edar_lebih_24_jam_status = 'YA, EDARAN LEWAT 24 JAM';
            } else {
                $this->edar_lebih_24_jam_status = 'EDARAN DALAM TEMPOH 24 JAM & KURANG';
            }
        } else {
            $this->edar_lebih_24_jam_status = null; // Reset if dates are missing
        }
    }

    public function calculateTerbengkalai3Bulan()
    {
        if ($this->tarikh_minit_a && $this->tarikh_minit_d) {
            $dateA = Carbon::parse($this->tarikh_minit_a);
            $dateD = Carbon::parse($this->tarikh_minit_d);
             // Assuming D should be within 3 months *after* A
            if ($dateD->diffInMonths($dateA) >= 3) { // Use >= 3 for "more than or equal to 3 months"
                $this->terbengkalai_3_bulan_status = 'YA, TERBENGKALAI LEBIH 3 BULAN';
            } else {
                $this->terbengkalai_3_bulan_status = 'TIDAK TERBENGKALAI';
            }
        } else {
            $this->terbengkalai_3_bulan_status = null; // Reset
        }
    }

     public function calculateBaruKemaskini()
     {
         // Logic needs clarification based on C & D dates comparison
         // Example: Check if D is very recent compared to C, or if D exists and C doesn't?
         // Placeholder logic:
         if ($this->tarikh_minit_c && $this->tarikh_minit_d) {
              $dateC = Carbon::parse($this->tarikh_minit_c);
              $dateD = Carbon::parse($this->tarikh_minit_d);
              // Example: If D is after C and within a short timeframe (e.g., 7 days)? Adjust as needed.
              if ($dateD->isAfter($dateC) /* && $dateD->diffInDays($dateC) <= 7 */ ) {
                 $this->baru_kemaskini_status = 'YA, BARU DIGERAKKAN UNTUK DIKEMASKINI';
              } else {
                 $this->baru_kemaskini_status = 'TIADA ISU';
              }
         } else {
             $this->baru_kemaskini_status = 'TIADA ISU'; // Default or null
         }
     }

    // Helper to nullify dates when conditions are not met
    public function handleConditionalDates() {
        if ($this->status_ks_semasa_diperiksa == null || $this->status_ks_semasa_diperiksa == '') {
            $this->tarikh_status_ks_semasa_diperiksa = null;
        }
        if ($this->id_siasatan_dilampirkan != 'YA') {
            $this->tarikh_id_siasatan_dilampirkan = null;
        }
        if ($this->rj2_status != 'Cipta') $this->rj2_tarikh = null;
        if ($this->rj9_status != 'Cipta') $this->rj9_tarikh = null;
        if ($this->rj10a_status != 'Cipta') $this->rj10a_tarikh = null;
        if ($this->rj10b_status != 'Cipta') $this->rj10b_tarikh = null;
        if ($this->rj99_status != 'Cipta') $this->rj99_tarikh = null;
        if ($this->semboyan_kesan_tangkap_status != 'Cipta') $this->semboyan_kesan_tangkap_tarikh = null;
        if ($this->waran_tangkap_status != 'Mohon') $this->waran_tangkap_tarikh = null;
        if ($this->ks_hantar_tpr_status != 'YA') $this->ks_hantar_tpr_tarikh = null;
        if ($this->ks_hantar_kjsj_status != 'YA') $this->ks_hantar_kjsj_tarikh = null;
        if ($this->ks_hantar_d5_status != 'YA') $this->ks_hantar_d5_tarikh = null;
        if ($this->ks_hantar_kbsjd_status != 'YA') $this->ks_hantar_kbsjd_tarikh = null;

    }
}