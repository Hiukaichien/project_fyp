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
        Schema::create('orang_hilang_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // Core Fields
            $table->string('no_ks_oh')->unique();
            $table->date('tarikh_ks_oh_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_ks_oh')->nullable()->index();
            $table->string('status_kes')->nullable()->index();
            $table->string('kes_klasifikasi')->nullable();
            // In CSV, 'SEKSYEN DIBUKA' is replaced by 'TARIKH LAPORAN POLIS' for this category
            $table->date('tarikh_laporan_polis_header')->nullable();
            $table->string('pegawai_pemeriksa_jips')->nullable();

            // Minit Dates (Standardized)
            // CSV for PENGURUSAN (ORANG HILANG) has: "TARIKH EDARAN MINIT PERTAMA", "TARIKH EDARAN MINIT AKHIR"
            $table->date('tarikh_minit_a')->nullable();
            $table->date('tarikh_minit_b')->nullable();
            $table->date('tarikh_minit_c')->nullable();
            $table->date('tarikh_minit_d')->nullable();

            // Calculated Statuses
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();

            // PENGURUSAN (ORANG HILANG) Specific Columns
            $table->string('kst_terbengkalai_melebihi_3_bulan_flag')->nullable(); // Note: CSV header says KST, assuming it means KS(OH) here
            $table->text('pengiraan_ks_oh_terbengkalai')->nullable();
            $table->string('ks_oh_io_aio_renew_selepas_semboyan')->nullable();
            $table->string('rakam_percakapan_orang_hilang_dijumpai')->nullable();
            $table->string('laporan_polis_orang_hilang_dijumpai')->nullable();
            $table->string('unsur_jenayah_keatas_orang_hilang_status')->nullable();
            $table->string('orang_hilang_bawah_umur_18_status')->nullable();
            $table->string('semboyan_nur_alert_dihantar_status')->nullable();
            $table->string('gambar_orang_hilang')->nullable();
            $table->text('butiran_orang_hilang')->nullable();
            $table->string('penjanaan_sistem_mps1_status')->nullable();
            $table->string('penjanaan_sistem_mps2_status')->nullable();
            $table->string('hebahan_media_orang_hilang_status')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->string('gambar_tempat_kejadian_oh')->nullable();
            $table->string('lakaran_rajah_kasar_lokasi_oh')->nullable();
            $table->string('pem1_status')->nullable();
            $table->string('pem2_status')->nullable();
            $table->string('pem3_status')->nullable();
            $table->string('pem4_status')->nullable();
            $table->string('ks_rujuk_kbpd_arahan_lanjut_status')->nullable();
            $table->string('ks_oh_dijumpai_rujuk_tpr_nfa_status')->nullable();
            $table->string('ks_selesai_kus_fail_status')->nullable();
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
        Schema::dropIfExists('orang_hilang_papers');
    }
};
