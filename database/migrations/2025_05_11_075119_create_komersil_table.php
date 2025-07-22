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
        Schema::create('komersil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // BAHAGIAN 1: Maklumat Asas (B1)
            $table->string('no_kertas_siasatan')->unique();
            $table->string('no_report_polis')->nullable();
            $table->string('pegawai_penyiasat')->nullable();
            $table->date('tarikh_laporan_polis_dibuka')->nullable();
            $table->string('seksyen')->nullable();

            // BAHAGIAN 2: Pemeriksaan JIPS (B2)
            $table->string('pegawai_pemeriksa')->nullable();
            $table->date('tarikh_edaran_minit_ks_pertama')->nullable();
            $table->date('tarikh_edaran_minit_ks_kedua')->nullable();
            $table->date('tarikh_edaran_minit_ks_sebelum_akhir')->nullable();
            $table->date('tarikh_edaran_minit_ks_akhir')->nullable();
            $table->date('tarikh_semboyan_pemeriksaan_jips_ke_daerah')->nullable();

            // BAHAGIAN 3: Arahan SIO & Ketua (B3)
            $table->boolean('arahan_minit_oleh_sio_status')->default(false);
            $table->date('arahan_minit_oleh_sio_tarikh')->nullable();
            $table->boolean('arahan_minit_ketua_bahagian_status')->default(false);
            $table->date('arahan_minit_ketua_bahagian_tarikh')->nullable();
            $table->boolean('arahan_minit_ketua_jabatan_status')->default(false);
            $table->date('arahan_minit_ketua_jabatan_tarikh')->nullable();
            $table->boolean('arahan_minit_oleh_ya_tpr_status')->default(false);
            $table->date('arahan_minit_oleh_ya_tpr_tarikh')->nullable();
            $table->string('keputusan_siasatan_oleh_ya_tpr')->nullable();
            $table->json('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan')->nullable();
            $table->text('ulasan_keputusan_siasatan_tpr')->nullable();
            $table->text('ulasan_keputusan_pegawai_pemeriksa')->nullable();

            // BAHAGIAN 4: Barang Kes (B4)
            $table->boolean('adakah_barang_kes_didaftarkan')->default(false);
            $table->string('no_daftar_barang_kes_am')->nullable();
            $table->string('no_daftar_barang_kes_berharga')->nullable();
            $table->string('no_daftar_barang_kes_kenderaan')->nullable();
            $table->json('status_pergerakan_barang_kes')->nullable();
            $table->json('status_barang_kes_selesai_siasatan')->nullable();
            $table->json('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan')->nullable();
            $table->json('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan')->nullable();
            $table->json('resit_kew_98e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbencaharaan')->nullable();
            $table->json('adakah_borang_serah_terima_pegawai_tangkapan')->nullable();
            $table->string('adakah_borang_serah_terima_pemilik_saksi')->nullable();
            $table->boolean('adakah_sijil_surat_kebenaran_ipo')->default(false);
            $table->string('adakah_gambar_pelupusan')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa')->nullable();

            // BAHAGIAN 5: Bukti & Rajah (B5)
            $table->boolean('status_id_siasatan_dikemaskini')->default(false);
            $table->boolean('status_rajah_kasar_tempat_kejadian')->default(false);
            $table->boolean('status_gambar_tempat_kejadian')->default(false);
            $table->boolean('status_gambar_barang_kes_am')->default(false);
            $table->boolean('status_gambar_barang_kes_berharga')->default(false);
            $table->boolean('status_gambar_barang_kes_kenderaan')->default(false);
            $table->boolean('status_gambar_barang_kes_darah')->default(false);
            $table->boolean('status_gambar_barang_kes_kontraban')->default(false);

            // BAHAGIAN 6: Laporan RJ & Semboyan (B6)
            $table->json('status_pem')->nullable();
            $table->boolean('status_rj2')->default(false);
            $table->date('tarikh_rj2')->nullable();
            $table->boolean('status_rj2b')->default(false);
            $table->date('tarikh_rj2b')->nullable();
            $table->boolean('status_rj9')->default(false);
            $table->date('tarikh_rj9')->nullable();
            $table->boolean('status_rj99')->default(false);
            $table->date('tarikh_rj99')->nullable();
            $table->boolean('status_rj10a')->default(false);
            $table->date('tarikh_rj10a')->nullable();
            $table->boolean('status_rj10b')->default(false);
            $table->date('tarikh_rj10b')->nullable();
            $table->text('lain_lain_rj_dikesan')->nullable();
            $table->boolean('status_semboyan_pertama_wanted_person')->default(false);
            $table->date('tarikh_semboyan_pertama_wanted_person')->nullable();
            $table->boolean('status_semboyan_kedua_wanted_person')->default(false);
            $table->date('tarikh_semboyan_kedua_wanted_person')->nullable();
            $table->boolean('status_semboyan_ketiga_wanted_person')->default(false);
            $table->date('tarikh_semboyan_ketiga_wanted_person')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_borang')->nullable();
            $table->boolean('status_penandaan_kelas_warna')->default(false);

            // BAHAGIAN 7: Laporan E-FSA, Puspakom, dll (B7)
            // BANK
            $table->boolean('status_permohonan_E_FSA_1_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_permohonan_E_FSA_1')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_1_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_laporan_E_FSA_1_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_2_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_permohonan_E_FSA_2_BANK')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_2_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_laporan_E_FSA_2_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_3_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_permohonan_E_FSA_3_BANK')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_3_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_laporan_E_FSA_3_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_4_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_permohonan_E_FSA_4_BANK')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_4_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_laporan_E_FSA_4_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_5_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_permohonan_E_FSA_5_BANK')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_5_oleh_IO_AIO')->default(false);
            $table->string('nama_bank_laporan_E_FSA_5_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO')->nullable();

            // TELCO
            $table->boolean('status_permohonan_E_FSA_1_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_permohonan_E_FSA_1_oleh_IO_AIO')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_laporan_E_FSA_1_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_2_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_permohonan_E_FSA_2_oleh_IO_AIO')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_laporan_E_FSA_2_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_3_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_permohonan_E_FSA_3_oleh_IO_AIO')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_laporan_E_FSA_3_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_4_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_permohonan_E_FSA_4_oleh_IO_AIO')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_laporan_E_FSA_4_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO')->nullable();

            $table->boolean('status_permohonan_E_FSA_5_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_permohonan_E_FSA_5_oleh_IO_AIO')->nullable();
            $table->boolean('status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO')->default(false);
            $table->string('nama_telco_laporan_E_FSA_5_oleh_IO_AIO')->nullable();
            $table->date('tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO')->nullable();

            // PUSPAKOM
            $table->boolean('status_permohonan_laporan_puspakom')->default(false);
            $table->date('tarikh_permohonan_laporan_puspakom')->nullable();
            $table->boolean('status_laporan_penuh_puspakom')->default(false);
            $table->date('tarikh_laporan_penuh_puspakom')->nullable();

            // IMIGRESEN
            $table->boolean('status_permohonan_laporan_imigresen')->default(false);
            $table->date('tarikh_permohonan_laporan_imigresen')->nullable();
            $table->boolean('status_laporan_penuh_imigresen')->default(false);
            $table->date('tarikh_laporan_penuh_imigresen')->nullable();

            // KASTAM
            $table->boolean('status_permohonan_laporan_kastam')->default(false);
            $table->date('tarikh_permohonan_laporan_kastam')->nullable();
            $table->boolean('status_laporan_penuh_kastam')->default(false);
            $table->date('tarikh_laporan_penuh_kastam')->nullable();

            // FORENSIK PDRM
            $table->boolean('status_permohonan_laporan_forensik_pdrm')->default(false);
            $table->date('tarikh_permohonan_laporan_forensik_pdrm')->nullable();
            $table->boolean('status_laporan_penuh_forensik_pdrm')->default(false);
            $table->date('tarikh_laporan_penuh_forensik_pdrm')->nullable();

            // Lain-lain
            $table->string('lain_lain_permohonan_laporan')->nullable();

            // BAHAGIAN 8: Penilaian Akhir (B8)
            $table->string('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar')->nullable();
            $table->string('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr')->nullable();
            $table->string('adakah_muka_surat_4_keputusan_kes_dicatat')->nullable();
            $table->string('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan')->nullable();
            $table->string('adakah_ks_kus_fail_selesai')->nullable();
            $table->json('keputusan_akhir_mahkamah')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_fail')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komersil');
    }
};