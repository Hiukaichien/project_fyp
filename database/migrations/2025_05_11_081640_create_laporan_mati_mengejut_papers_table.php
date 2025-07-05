<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_mati_mengejut_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // == Core Fields from CSV ==
            $table->string('no_sdr_lmm')->unique(); // From: NO SDR/LLM
            $table->string('pegawai_penyiasat')->nullable(); // From: PEGAWAI PENYIASAT
            $table->string('no_laporan_polis')->nullable()->index(); // From: NO LAPORAN POLIS
            $table->date('tarikh_laporan_polis')->nullable(); // From: TARKH LAPORAN POLIS
            $table->string('pegawai_pemeriksa_jips')->nullable(); // From: PEGAWAI PEMERIKSA (JIPS)
            $table->date('tarikh_minit_pertama')->nullable(); // From: TARIKH EDARAN PERTAMA
            $table->date('tarikh_minit_akhir')->nullable(); // From: TARIKH EDARAN AKHIR
            $table->string('pem_1_2_3_4')->nullable(); // From: PEM 1/2/3/4
            $table->string('terbengkalai_tb')->nullable(); // From: TERBENGKALAI (TB)
            $table->date('tarikh_permohonan_pm_dipohon')->nullable(); // From: TARIKH PERMOHONAN P/MORTEM DI POHON
            $table->string('laporan_pm_diterima_status')->nullable(); // From: LAPORAN P/MORTEM DITERIMA/TIDAK DITERIMA/FOLOW UP
            $table->string('status_sdr')->nullable(); // From: STATUS SDR
            $table->string('gambar_post_mortem')->nullable(); // From: GAMBAR POST-MORTEM SI MATI
            $table->string('gambar_tempat_kejadian')->nullable(); // From: GAMBAR TEMPAT KEJADIAN
            $table->string('tandatangan_kpd_ms2_sdr')->nullable(); // From: TANDATANGAN KPD DI M/S 2 SDR
            $table->date('tarikh_rujuk_tpr')->nullable(); // From: TARIKH RUJUK TPR
            $table->string('arahan_tpr')->nullable(); // From: ARAHAN TPR
            $table->date('tarikh_rujuk_koroner')->nullable(); // From: TARIKH RUJUK SDR KEPADA KORONER
            $table->string('arahan_koroner')->nullable(); // From: ARAHAN KORONER
            $table->string('status_sdr_final')->nullable(); // From: STATUS SDR (second instance)
            $table->text('ulasan_keseluruhan')->nullable(); // From: ULASAN KESELURUHAN

            // == System Calculated Statuses (can be removed if calculated on-the-fly) ==
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_mati_mengejut_papers');
    }
};