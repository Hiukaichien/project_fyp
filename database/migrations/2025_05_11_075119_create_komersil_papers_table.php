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
        Schema::create('komersil_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // Core Fields
            $table->string('no_ks')->unique();
            $table->date('tarikh_ks_dibuka')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('io_aio')->nullable();
            $table->string('status_ks')->nullable()->index();
            $table->string('status_kes')->nullable()->index();
            $table->string('kes_klasifikasi')->nullable();
            $table->string('seksyen_dibuka')->nullable();
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

            // KOMERSIL Specific Columns
            $table->string('edaran_minit_pertama_lebih_48jam_ab_flag')->nullable();
            $table->string('ks_terbengkalai_flag')->nullable();
            $table->text('pengiraan_ks_terbengkalai')->nullable();
            $table->string('ks_io_aio_renew_selepas_semboyan')->nullable();
            $table->string('rakam_okt_112_ktj')->nullable();
            $table->string('rakam_pengadu_saksi')->nullable();
            $table->string('diari_siasatan_dikemaskini')->nullable();
            $table->date('tarikh_akhir_diari_dikemaskini')->nullable();
            $table->string('no_daftar_ekshibit_kulit_ks')->nullable();
            $table->date('tarikh_daftar_bk_berharga_tunai')->nullable();
            $table->string('no_daftar_bk_berharga_er')->nullable();
            $table->date('tarikh_daftar_bk_am')->nullable();
            $table->string('no_daftar_bk_am_judi_er')->nullable();
            $table->date('tarikh_daftar_bk_kenderaan')->nullable();
            $table->string('no_daftar_bk_kenderaan_er')->nullable();
            $table->string('bk_tiada_rekod_daftar')->nullable();
            $table->string('gambar_bk_dilampirkan_ks')->nullable();
            $table->text('arahan_tpr_lucut_hak_ulasan')->nullable();
            $table->date('tarikh_arahan_tpr_lucut_hak')->nullable();
            $table->decimal('jumlah_wang_tunai_lucut_hak_rm', 15, 2)->nullable();
            $table->string('resit_kew38e_lucut_hak')->nullable();
            $table->text('arahan_tpr_pulang_bk_ulasan')->nullable();
            $table->date('tarikh_arahan_tpr_pulang_bk')->nullable();
            $table->decimal('wang_tunai_serah_semula_pemilik_rm', 15, 2)->nullable();
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
            $table->string('permohonan_efsa_bank_status')->nullable();
            $table->string('permohonan_efsa_bank_tarikh_info')->nullable();
            $table->string('permohonan_efsa_telco_status')->nullable();
            $table->string('permohonan_efsa_telco_tarikh_info')->nullable();
            $table->string('keputusan_efsa_bank_info')->nullable();
            $table->string('keputusan_efsa_telco_info')->nullable();
            $table->string('io_tiada_usaha_efsa_bank_2bulan_status')->nullable();
            $table->string('io_tiada_usaha_efsa_telco_2bulan_status')->nullable();
            $table->string('bk_telefon_hantar_forensik_status')->nullable();
            $table->date('bk_telefon_forensik_tarikh_hantar')->nullable();
            $table->string('keputusan_ujian_forensik_pdrm')->nullable();
            $table->string('ks_lengkap_io_gagal_rujuk_tpr_status')->nullable();
            $table->string('ks_ada_arahan_tuduh_io_tidak_laksana_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_tuduh')->nullable();
            $table->string('io_gagal_kesan_tangkap_suspek_status')->nullable();
            $table->string('io_tidak_mohon_waran_tangkap_status')->nullable();
            $table->string('io_tidak_wanted_okt_rj10a_status')->nullable();
            $table->string('tpr_beri_arahan_nfa_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_nfa')->nullable();
            $table->text('ulasan_kes_nfa')->nullable();
            $table->string('kes_nfa_tpr_rujuk_koroner_status')->nullable();
            $table->string('tpr_beri_arahan_dnaa_status')->nullable();
            $table->date('tarikh_tpr_beri_arahan_dnaa')->nullable();
            $table->text('ulasan_kes_dnaa')->nullable();
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
        Schema::dropIfExists('komersil_papers');
    }
};
