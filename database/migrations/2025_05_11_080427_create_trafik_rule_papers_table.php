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
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');

            // Common Core Fields
            $table->string('no_kst')->unique();
            $table->date('tarikh_kst_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_kst')->nullable()->index();
            $table->string('status_kes')->nullable()->index();
            $table->string('kes_klasifikasi')->nullable();
            $table->string('seksyen_dibuka')->nullable();
            $table->date('tarikh_laporan_polis')->nullable();
            $table->string('pegawai_pemeriksa_jips')->nullable();
            $table->date('tarikh_minit_a')->nullable();
            $table->date('tarikh_minit_d')->nullable();

            // Other fields
            $table->string('kst_terbengkalai_melebihi_3bulan')->nullable();
            $table->string('pengiraan_ks_terbengkalai_melebihi_3bulan')->nullable();
            $table->string('kst_io_aio_renew__tarikh_selepas_semboyan')->nullable();
            $table->string('rakam_pengadu_pihak_kemalangan_tuntutan_112ktj')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->string('no_daftar_ekshibit_kulit_ks')->nullable();
            $table->date('tarikh_io_aio_daftar_bk_kenderaan')->nullable();
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
            $table->string('pem1')->nullable();
            $table->string('pem2')->nullable();
            $table->string('pem3')->nullable();
            $table->string('pem4')->nullable();
            $table->string('rj9')->nullable();
            $table->date('tarikh_cipta_rj9')->nullable();
            $table->string('rj99')->nullable();
            $table->date('tarikh_cipta_rj99')->nullable();
            $table->string('rj10a')->nullable();
            $table->date('tarikh_cipta_rj10a')->nullable();
            $table->string('rj10b')->nullable();
            $table->date('tarikh_cipta_rj10b')->nullable();
            $table->string('rj2')->nullable();
            $table->date('tarikh_cipta_rj2')->nullable();
            $table->string('rj2b')->nullable();
            $table->date('tarikh_cipta_rj2b')->nullable();
            $table->string('rj21')->nullable();
            $table->date('tarikh_cipta_rj21')->nullable();
            $table->string('adakah_okt_surat_jamin_polis_pdrma43')->nullable();
            $table->string('adakah_pdrma43_okt_dijamin_penjamin')->nullable();
            $table->text('ulasan_surat_jamin_polis_pdrma43')->nullable();
            $table->string('ks_telah_lengkap_gagal_rujuk_tpr_arahan_tuduh')->nullable();
            $table->string('ks_telah_ada_arahan_tuduh_tidak_laksana')->nullable();
            $table->date('tarikh_tpr_beri_arahan_tuduh')->nullable();
            $table->string('io_aio_gagal_kesan_tangkap_suspek')->nullable();
            $table->string('io_aio_tidak_hantar_surat_panggilan_saksi')->nullable();
            $table->string('io_aio_tidak_mohon_waran_tangkap')->nullable();
            $table->string('io_aio_tidak_wanted_okt_rj10a')->nullable();
            $table->string('adakah_tpr_beri_arahan_nfa')->nullable();
            $table->date('tarikh_tpr_beri_arahan_nfa')->nullable();
            $table->text('ulasan_kes_di_nfa')->nullable();
            $table->string('adakah_kes_nfa_rujuk_semula_koroner')->nullable();
            $table->string('adakah_ks_berkeputusan_jatuh_hukum')->nullable();
            $table->date('tarikh_keputusan_jatuh_hukum')->nullable();
            $table->string('adakah_ks_difolio_susunan_kandungan')->nullable();
            $table->text('ulasan_penemuan_menarik1')->nullable();
            $table->text('ulasan_penemuan_menarik2')->nullable();
            $table->text('ulasan_lain_lain')->nullable();

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
