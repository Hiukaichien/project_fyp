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
        Schema::create('narkotik_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // Core Fields (from NARKOTIK section)
            $table->string('no_ks')->unique();
            $table->date('tarikh_ks_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_ks')->nullable()->index();
            $table->string('status_kes')->nullable()->index();
            $table->string('kes_klasifikasi')->nullable(); // From CSV: KES
            $table->string('seksyen_dibuka')->nullable();
            $table->date('tarikh_laporan_polis')->nullable();
            $table->string('pegawai_pemeriksa_jips')->nullable();

            // Minit Dates (Standardized based on NARKOTIK section)
            // NARKOTIK CSV has: "TARIKH EDARAN MINIT PERTAMA", "TARIKH EDARAN MINIT AKHIR"
            $table->date('tarikh_minit_a')->nullable(); // Mapped from "TARIKH EDARAN MINIT PERTAMA"
            $table->date('tarikh_minit_b')->nullable(); // Likely null for Narkotik based on CSV
            $table->date('tarikh_minit_c')->nullable(); // Likely null for Narkotik based on CSV
            $table->date('tarikh_minit_d')->nullable(); // Mapped from "TARIKH EDARAN MINIT AKHIR"

            // Calculated Statuses
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();

            // NARKOTIK Specific Columns from CSV
            $table->string('ks_terbengkalai_flag')->nullable();
            $table->text('pengiraan_ks_terbengkalai')->nullable();
            $table->string('ks_io_aio_renew_selepas_semboyan')->nullable();
            $table->string('rakam_okt_37b1b_adb1952')->nullable();
            $table->string('rakam_pengadu_saksi_112ktj')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->string('ada_gambar_tempat_kejadian')->nullable();
            $table->text('ulasan_gambar_kejadian')->nullable();
            $table->string('no_daftar_ekshibit_kulit_ks')->nullable();
            $table->date('tarikh_daftar_bk_berharga_tunai')->nullable();
            $table->string('no_daftar_bk_berharga_er')->nullable();
            $table->date('tarikh_daftar_bk_dadah_am')->nullable();
            $table->string('no_daftar_bk_dadah_am_er')->nullable();
            $table->date('tarikh_daftar_bk_kenderaan')->nullable();
            $table->string('no_daftar_bk_kenderaan_er')->nullable();
            $table->string('bk_tiada_rekod_daftar')->nullable();
            $table->string('gambar_bk_dilampirkan_ks')->nullable();
            $table->string('gambar_botol_spesimen_urin_3d')->nullable();
            $table->string('gambar_strip_dadah_positif_plastik')->nullable();
            $table->date('tarikh_spesimen_urin_dipungut_a')->nullable();
            $table->string('spesimen_urin_hantar_patologi_kurang_48jam_status')->nullable();
            $table->date('tarikh_spesimen_urin_hantar_patologi_b')->nullable();
            $table->string('spesimen_urin_hantar_patologi_lebih_48jam_status_ab')->nullable();
            $table->text('alasan_lewat_hantar_spesimen_urin')->nullable();
            $table->string('keputusan_laporan_urin')->nullable();
            $table->string('borang_hantar_spesimen_dadah_kimia_status')->nullable();
            $table->string('sijil_pengesahan_terima_spesimen_dadah_kimia_status')->nullable();
            $table->date('tarikh_sah_terima_spesimen_dadah_kimia')->nullable();
            $table->string('keputusan_laporan_analisis_dadah_kimia')->nullable();
            $table->date('tarikh_keputusan_laporan_analisis_dadah_kimia')->nullable();
            $table->string('borang_hantar_spesimen_darah_ujian_dadah_status')->nullable();
            $table->string('keputusan_laporan_spesimen_darah_ujian_dadah')->nullable();
            $table->date('tarikh_keputusan_laporan_spesimen_darah_ujian_dadah')->nullable();
            $table->date('tarikh_tindakan_susulan_io_patologi_2bulan')->nullable();
            $table->date('tarikh_tindakan_susulan_io_kimia_2bulan')->nullable();
            $table->date('tarikh_tindakan_susulan_io_kimia_hospital_2bulan')->nullable();
            $table->string('io_tiada_usaha_dapatkan_keputusan_2bulan_status')->nullable();
            $table->text('arahan_tpr_pulang_bk_ulasan')->nullable();
            $table->date('tarikh_arahan_tpr_pulang_bk')->nullable();
            $table->decimal('nilai_wang_tunai_serah_semula_rm', 15,2)->nullable();
            $table->string('surat_serah_terima_bk_penerima')->nullable();
            $table->date('tarikh_serahan_bk_pemilik')->nullable();
            $table->string('gambar_pelupusan_jika_diarah_tpr')->nullable();
            $table->string('jenis_bk_dilupuskan_selepas_tpr')->nullable();
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
            $table->text('arahan_tpr_tuduh_39c_adb1952_ulasan')->nullable();
            $table->string('io_laksana_tindakan_tuduh_39c_status')->nullable();
            $table->date('tarikh_ks_rujuk_tpr')->nullable();
            $table->string('ks_ada_arahan_tuduh_tpr_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_tuduh')->nullable();
            $table->string('io_laksana_tuduh_selepas_arahan_tpr_status')->nullable();
            $table->string('tpr_beri_arahan_nfa_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_nfa')->nullable();
            $table->text('ulasan_kes_nfa')->nullable();
            $table->string('tpr_beri_arahan_dnaa_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_dnaa')->nullable();
            $table->string('okt_wanted_gagal_hadir_bicara_status')->nullable();
            $table->string('waran_tangkap_mohon_mahkamah_status')->nullable();
            $table->date('tarikh_waran_tangkap_dikeluarkan_mahkamah')->nullable();
            $table->text('ulasan_kes_dnaa_narkotik')->nullable(); // Renamed to avoid conflict if main ulasan_kes_dnaa is also needed
            $table->string('io_tuduh_semula_dnaa_status')->nullable();
            $table->string('ks_jatuh_hukum_status')->nullable();
            $table->date('tarikh_keputusan_jatuh_hukum')->nullable();
            $table->string('ks_difolio_ikut_susunan_status')->nullable();
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
        Schema::dropIfExists('narkotik_papers');
    }
};
