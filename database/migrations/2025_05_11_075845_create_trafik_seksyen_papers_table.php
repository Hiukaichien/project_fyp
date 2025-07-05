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
        Schema::create('trafik_seksyen_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // == Core Fields from CSV ==
            $table->string('no_ks')->unique(); // From: NO KERTAS SIASATAN
            $table->string('pegawai_penyiasat')->nullable(); // From: PEGAWAI PENYIASAT
            $table->string('seksyen')->nullable(); // From: SEKSYEN
            $table->date('tarikh_daftar')->nullable(); // From: TARIKH DAFTAR
            $table->string('no_saman')->nullable(); // From: NO SAMAN
            $table->string('pegawai_pemeriksa_jips')->nullable(); // From: PEGAWAI PEMERIKSA (JIPS)
            $table->date('tarikh_minit_pertama')->nullable(); // From: TARIKH EDARAN PERTAMA
            $table->date('tarikh_minit_akhir')->nullable(); // From: TARIKH MINIT AKHIR
            $table->string('lewat_edaran_pertama_48_jam')->nullable(); // From: LEWAT EDARAN PERTAMA 48 JAM
            $table->string('tiada_gambar_tempat_kejadian')->nullable(); // From: TIADA GAMBAR TEMPAT KEJADIAN
            $table->string('keputusan_kes_rule')->nullable(); // From: KEPUTUSAN KES (RULE)
            $table->string('no_sdr_sek_41')->nullable(); // From: NO SDR (SEK 41(1) APJ
            $table->string('ms_2_fail_lmm')->nullable(); // From: MS 2 FAIL LMM (T.T KPD)
            $table->string('rajah_kasar')->nullable(); // From: RAJAH KASAR
            $table->date('tarikh_hantar_puspakom')->nullable(); // From: TARIKH HANTAR PUSPAKOM
            $table->date('tarikh_hantar_patalogi')->nullable(); // From: TARIKH HANTAR PATALOGI
            $table->date('tarikh_hantar_kimia')->nullable(); // From: TARIKH HANTAR KIMIA
            $table->date('tarikh_terima_laporan_pakar')->nullable(); // From: TARIKH TERIMA LAPORAN PAKAR
            $table->string('slip_eba_sek_45a')->nullable(); // From: SLIP EBA (SEK 45A (1))
            $table->string('terbengkalai_tb')->nullable(); // From: TERBENGKALAI (TB)
            $table->string('arahan_tpr')->nullable(); // From: ARAHAN TPR→
            $table->string('kst_rujuk_tpr')->nullable(); // From: KST RUJUK TPR←
            $table->text('ulasan_pemeriksa')->nullable(); // From: ULASAN PEMERIKSA

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
        Schema::dropIfExists('trafik_seksyen_papers');
    }
};