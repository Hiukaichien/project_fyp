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

            // Core Fields
            $table->string('no_lmm')->unique();
            $table->date('tarikh_lmm_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_lmm')->nullable()->index();
            $table->string('status_kes')->nullable()->index(); // CSV shows "STATUS KES" for this category too
            $table->string('kes_klasifikasi')->nullable(); // From CSV: KES
            // In CSV, 'SEKSYEN DIBUKA' is replaced by 'TARIKH LAPORAN POLIS' for this category
            $table->date('tarikh_laporan_polis_header')->nullable();
            $table->string('pegawai_pemeriksa_jips')->nullable();

            // Minit Dates (Standardized)
            // CSV for FAIL LAPORAN MATI MENGEJUT (SDR) has: "TARIKH EDARAN MINIT PERTAMA", "TARIKH EDARAN MINIT AKHIR"
            $table->date('tarikh_minit_a')->nullable();
            $table->date('tarikh_minit_b')->nullable();
            $table->date('tarikh_minit_c')->nullable();
            $table->date('tarikh_minit_d')->nullable();

            // Calculated Statuses
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();

            // FAIL LAPORAN MATI MENGEJUT (SDR) Specific Columns
            $table->string('lmm_terbengkalai_melebihi_3_bulan_flag')->nullable();
            $table->text('pengiraan_lmm_terbengkalai')->nullable();
            $table->string('lmm_io_aio_renew_selepas_semboyan')->nullable();
            $table->string('rakam_percakapan_pengadu_saksi')->nullable();
            $table->string('kematian_unsur_jenayah_status')->nullable();
            $table->string('kes_maklum_kbsjd_jika_jenayah_status')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->string('gambar_tempat_kejadian_lmm')->nullable();
            $table->string('lakaran_rajah_kasar_lmm')->nullable();
            $table->string('borang_mohon_post_mortem_hospital')->nullable();
            $table->string('gambar_post_mortem_hospital')->nullable();
            $table->string('permohonan_laporan_post_mortem')->nullable();
            $table->string('laporan_post_mortem_mayat')->nullable();
            $table->string('permohonan_laporan_spesimen_darah_kimia')->nullable();
            $table->string('laporan_spesimen_darah_kimia')->nullable();
            $table->string('pem1_status')->nullable();
            $table->string('pem2_status')->nullable();
            $table->string('pem3_status')->nullable();
            $table->string('pem4_status')->nullable();
            $table->string('butir_fail_lmm_dicatat_lengkap_status')->nullable();
            $table->string('dokumen_siasatan_dilampirkan_fail_lmmt_status')->nullable();
            $table->string('ms2_fail_lmmt_ditandatangan_kpd_status')->nullable();
            $table->string('fail_lmmt_rujuk_tpr_arahan_lanjut_status')->nullable();
            $table->string('fail_lmmt_rujuk_koroner_arahan_inkues_status')->nullable();
            $table->string('ks_difolio_ikut_susunan_status')->nullable(); // Note: CSV header is "ADAKAH KS DIFOLIO..." seems like a generic question applied here
            $table->string('tpr_beri_arahan_nfa_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_nfa')->nullable();
            $table->text('ulasan_kes_nfa')->nullable();
            $table->text('ulasan_penemuan_kes_menarik1')->nullable();
            $table->text('ulasan_penemuan_kes_menarik2')->nullable();
            $table->text('ulasan_lain_lain')->nullable();

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
