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
        Schema::create('trafik_rule_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // Core Fields
            $table->string('no_kst')->unique();
            $table->date('tarikh_kst_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_kst')->nullable()->index();
            $table->string('status_kes')->nullable()->index();
            $table->string('kes_klasifikasi')->nullable();
            // 'SEKSYEN DIBUKA' seems to be replaced by 'TARIKH LAPORAN POLIS' in this CSV section for this specific type
            // The next distinct field is PEGAWAI PEMERIKSA JIPS
            $table->date('tarikh_laporan_polis_header')->nullable(); // CSV Header: TARIKH LAPORAN POLIS (first instance for this category)
            $table->string('pegawai_pemeriksa_jips')->nullable();

            // Minit Dates (Standardized)
            // CSV for TRAFIK (KES RULE) has: "TARIKH EDARAN MINIT PERTAMA (A)", "TARIKH EDARAN MINIT AKHIR"
            $table->date('tarikh_minit_a')->nullable();
            $table->date('tarikh_minit_b')->nullable();
            $table->date('tarikh_minit_c')->nullable();
            $table->date('tarikh_minit_d')->nullable();

            // Calculated Statuses
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();

            // TRAFIK (KES RULE) Specific Columns
            $table->string('edaran_minit_pertama_lebih_48jam_ab_flag')->nullable();
            $table->string('kst_terbengkalai_flag')->nullable();
            $table->text('pengiraan_kst_terbengkalai')->nullable();
            $table->string('kst_io_aio_renew_selepas_semboyan')->nullable();
            $table->string('rakam_pengadu_pihak_kemalangan_tuntutan_112ktj')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->date('tarikh_daftar_bk_kenderaan')->nullable();
            $table->string('no_daftar_bk_kenderaan_er')->nullable();
            $table->string('bk_tiada_rekod_daftar')->nullable();
            $table->string('gambar_bk_dilampirkan_ks')->nullable();
            $table->string('surat_serah_terima_bk_penerima')->nullable();
            $table->date('tarikh_serahan_bk_pemilik')->nullable();
            $table->string('gambar_pelupusan_jika_diarah_tpr')->nullable();
            $table->string('jenis_bk_dilupuskan_selepas_tpr')->nullable();
            $table->string('lakaran_rajah_kasar_kemalangan')->nullable();
            $table->string('gambar_tempat_kejadian')->nullable();
            $table->string('gambar_kenderaan_kemalangan')->nullable();
            $table->string('kes_mati_lewat_hospital_rujuk_kbspt_dbs41_status')->nullable();
            $table->string('kes_cedera_parah_hospital_rujuk_kbspt_dbs43_status')->nullable();
            $table->string('saman_pol257_dikeluarkan_status')->nullable();
            $table->string('no_saman_pol257')->nullable();
            $table->string('pem1_status')->nullable();
            $table->string('pem2_status')->nullable();
            $table->string('pem3_status')->nullable();
            $table->string('pem4_status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trafik_rule_papers');
    }
};
