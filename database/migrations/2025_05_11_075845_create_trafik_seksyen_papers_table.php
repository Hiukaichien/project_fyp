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

            // Core Fields
            $table->string('no_kst')->unique();
            $table->date('tarikh_kst_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_kst')->nullable()->index();
            $table->string('status_kes')->nullable()->index();
            $table->text('kes_klasifikasi')->nullable();
            $table->text('seksyen_dibuka')->nullable();
            $table->date('tarikh_laporan_polis')->nullable();
            $table->string('pegawai_pemeriksa_jips')->nullable();

            // Minit Dates (Standardized)
            $table->date('tarikh_minit_a')->nullable();
            $table->date('tarikh_minit_b')->nullable();
            $table->date('tarikh_minit_c')->nullable();
            $table->date('tarikh_minit_d')->nullable();

            // Calculated Statuses
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();

            // TRAFIK (KES SEKSYEN) Specific Columns
            $table->string('edaran_minit_pertama_lebih_48jam_ab_flag')->nullable();
            $table->string('kst_terbengkalai_flag')->nullable();
            $table->text('pengiraan_kst_terbengkalai')->nullable();
            $table->string('kst_io_aio_renew_selepas_semboyan')->nullable();
            $table->text('rakam_okt_112_ktj')->nullable();
            $table->text('rakam_pengadu_saksi')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->string('no_daftar_ekshibit_kulit_ks')->nullable();
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
            $table->string('borang_mohon_post_mortem_hospital')->nullable();
            $table->string('gambar_post_mortem_hospital')->nullable();
            $table->string('permohonan_laporan_post_mortem')->nullable();
            $table->text('laporan_post_mortem_mayat')->nullable();
            $table->string('permohonan_laporan_spesimen_darah_kimia')->nullable();
            $table->text('laporan_spesimen_darah_kimia')->nullable();
            $table->string('permohonan_laporan_puspakom')->nullable();
            $table->text('laporan_puspakom')->nullable();
            $table->string('permohonan_laporan_jkr')->nullable();
            $table->text('laporan_jkr')->nullable();
            $table->string('pem1_status')->nullable();
            $table->string('pem2_status')->nullable();
            $table->string('pem3_status')->nullable();
            $table->string('pem4_status')->nullable();
            $table->string('rj9_status')->nullable();
            $table->date('rj9_tarikh_cipta')->nullable();
            $table->string('rj99_status')->nullable();
            $table->date('rj99_tarikh_cipta')->nullable();
            $table->string('rj10a_status')->nullable();
            $table->date('rj10a_tarikh_cipta')->nullable();
            $table->string('rj10b_status')->nullable();
            $table->date('rj10b_tarikh_cipta')->nullable();
            $table->string('rj2_status')->nullable();
            $table->date('rj2_tarikh_cipta')->nullable();
            $table->string('rj2b_status')->nullable();
            $table->date('rj2b_tarikh_cipta')->nullable();
            $table->string('rj21_status')->nullable();
            $table->date('rj21_tarikh_cipta')->nullable();
            $table->string('surat_jamin_polis_pdrma43_status')->nullable();
            $table->string('pdrma43_jamin_diri_sendiri_status')->nullable();
            $table->string('pdrma43_dijamin_penjamin_status')->nullable();
            $table->text('ulasan_surat_jamin_polis_pdrma43')->nullable();
            $table->string('io_tiada_usaha_laporan_post_mortem_2bulan_status')->nullable();
            $table->string('io_tiada_usaha_laporan_kimia_2bulan_status')->nullable();
            $table->string('io_tiada_usaha_laporan_puspakom_2bulan_status')->nullable();
            $table->string('io_tiada_usaha_laporan_jkr_2bulan_status')->nullable();
            $table->string('ks_lengkap_io_gagal_rujuk_tpr_status')->nullable();
            $table->string('ks_ada_arahan_tuduh_io_tidak_laksana_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_tuduh')->nullable();
            $table->string('io_gagal_kesan_tangkap_suspek_status')->nullable();
            $table->string('io_tidak_hantar_surat_panggilan_saksi_status')->nullable();
            $table->string('io_tidak_mohon_waran_tangkap_status')->nullable();
            $table->string('io_tidak_wanted_okt_rj10a_status')->nullable();
            $table->string('tpr_beri_arahan_nfa_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_nfa')->nullable();
            $table->text('ulasan_kes_nfa')->nullable();
            $table->string('kes_nfa_tpr_rujuk_koroner_status')->nullable();
            $table->string('ks_jatuh_hukum_status')->nullable();
            $table->date('tarikh_keputusan_jatuh_hukum')->nullable();
            $table->string('ks_difolio_ikut_susunan_status')->nullable();
            $table->text('ulasan_penemuan_menarik1')->nullable();
            $table->text('ulasan_penemuan_menarik2')->nullable();
            $table->text('ulasan_lain_lain')->nullable();
            $table->string('fail_lmm_dibuka_bersama_kst_status')->nullable();
            $table->string('ada_no_fail_lmm_bersama_kst')->nullable();
            $table->string('salinan_dokumen_siasatan_lmm_status')->nullable();
            $table->string('ms2_fail_lmm_ditandatangan_kpd_status')->nullable();
            $table->string('fail_lmm_rujuk_koroner_status')->nullable();

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
