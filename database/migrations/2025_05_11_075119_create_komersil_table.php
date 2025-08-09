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
            $table->string('no_kertas_siasatan')->unique()->comment('B1');
            $table->string('no_repot_polis')->nullable()->comment('B1');
            $table->string('pegawai_penyiasat')->nullable()->comment('B1');
            $table->date('tarikh_laporan_polis_dibuka')->nullable()->comment('B1');
            $table->string('seksyen')->nullable()->comment('B1');

            // BAHAGIAN 2: Pemeriksaan JIPS (B2)
            $table->string('pegawai_pemeriksa')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_pertama')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_kedua')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_sebelum_akhir')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_akhir')->nullable()->comment('B2');
            $table->date('tarikh_semboyan_pemeriksaan_jips_ke_daerah')->nullable()->comment('B2');

            // BAHAGIAN 3: Arahan SIO & Ketua (B3)
            $table->boolean('arahan_minit_oleh_sio_status')->nullable()->comment('B3');
            $table->date('arahan_minit_oleh_sio_tarikh')->nullable()->comment('B3');
            $table->boolean('arahan_minit_ketua_bahagian_status')->nullable()->comment('B3');
            $table->date('arahan_minit_ketua_bahagian_tarikh')->nullable()->comment('B3');
            $table->boolean('arahan_minit_ketua_jabatan_status')->nullable()->comment('B3');
            $table->date('arahan_minit_ketua_jabatan_tarikh')->nullable()->comment('B3');
            $table->boolean('arahan_minit_oleh_ya_tpr_status')->nullable()->comment('B3');
            $table->date('arahan_minit_oleh_ya_tpr_tarikh')->nullable()->comment('B3');
            $table->string('keputusan_siasatan_oleh_ya_tpr')->nullable()->comment('B3');
            $table->json('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan')->nullable()->comment('B3');
            $table->text('ulasan_keputusan_siasatan_tpr')->nullable()->comment('B3');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa')->nullable()->comment('B3');

            // BAHAGIAN 4: Barang Kes (B4)
            $table->boolean('adakah_barang_kes_didaftarkan')->nullable()->comment('B4');
            $table->string('no_daftar_barang_kes_am')->nullable()->comment('B4');
            $table->string('no_daftar_barang_kes_berharga')->nullable()->comment('B4');
            $table->string('no_daftar_barang_kes_kenderaan')->nullable()->comment('B4');
            $table->string('no_daftar_botol_spesimen_urin')->nullable()->comment('no_daftar_botol_spesimen_urin: VARCHAR(255)');
            $table->string('jenis_barang_kes_am')->nullable()->comment('jenis_barang_kes_am: VARCHAR(255)');
            $table->string('jenis_barang_kes_berharga')->nullable()->comment('jenis_barang_kes_berharga: VARCHAR(255)');
            $table->string('jenis_barang_kes_kenderaan')->nullable()->comment('jenis_barang_kes_kenderaan: VARCHAR(255)');
            $table->string('status_pergerakan_barang_kes')->nullable()->comment('B4');
            $table->text('status_pergerakan_barang_kes_ujian_makmal')->nullable()->comment('B4');
            $table->string('status_barang_kes_selesai_siasatan')->nullable()->comment('B4');
            $table->text('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan')->nullable()->comment('B4');
            $table->text('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan')->nullable()->comment('B4');
            $table->text('resit_kew_38e_bagi_pelupusan')->nullable()->comment('B4,resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan');
            $table->text('adakah_borang_serah_terima_pegawai_tangkapan')->nullable()->comment('B4');
            $table->text('adakah_borang_serah_terima_pemilik_saksi')->nullable()->comment('B4');
            $table->tinyInteger('adakah_sijil_surat_kebenaran_ipd')->nullable()->comment('B4 - 0: Tidak Dilampirkan, 1: Ada Dilampirkan, 2: Tidak Berkaitan');
            $table->string('adakah_gambar_pelupusan')->nullable()->comment('B4');

            // BAHAGIAN 5: Bukti & Rajah (B5)
            $table->boolean('status_id_siasatan_dikemaskini')->nullable()->comment('B5');
            $table->boolean('status_rajah_kasar_tempat_kejadian')->nullable()->comment('B5');
            $table->boolean('status_gambar_tempat_kejadian')->nullable()->comment('B5');
            $table->boolean('status_gambar_barang_kes_am')->nullable()->comment('B5');
            $table->boolean('status_gambar_barang_kes_berharga')->nullable()->comment('B5');
            $table->boolean('status_gambar_barang_kes_kenderaan')->nullable()->comment('B5');
            $table->boolean('status_gambar_barang_kes_darah')->nullable()->comment('B5');
            $table->boolean('status_gambar_barang_kes_kontraban')->nullable()->comment('B5');

            // BAHAGIAN 6: Laporan RJ & Semboyan (B6)
            $table->json('status_pem')->nullable()->comment('B6');
            $table->boolean('status_rj2')->nullable()->comment('B6');
            $table->date('tarikh_rj2')->nullable()->comment('B6');
            $table->boolean('status_rj2b')->nullable()->comment('B6');
            $table->date('tarikh_rj2b')->nullable()->comment('B6');
            $table->boolean('status_rj9')->nullable()->comment('B6');
            $table->date('tarikh_rj9')->nullable()->comment('B6');
            $table->boolean('status_rj99')->nullable()->comment('B6');
            $table->date('tarikh_rj99')->nullable()->comment('B6');
            $table->boolean('status_rj10a')->nullable()->comment('B6');
            $table->date('tarikh_rj10a')->nullable()->comment('B6');
            $table->boolean('status_rj10b')->nullable()->comment('B6');
            $table->date('tarikh_rj10b')->nullable()->comment('B6');
            $table->text('lain_lain_rj_dikesan')->nullable()->comment('B6');
            $table->boolean('status_semboyan_pertama_wanted_person')->nullable()->comment('B6');
            $table->date('tarikh_semboyan_pertama_wanted_person')->nullable()->comment('B6');
            $table->boolean('status_semboyan_kedua_wanted_person')->nullable()->comment('B6');
            $table->date('tarikh_semboyan_kedua_wanted_person')->nullable()->comment('B6');
            $table->boolean('status_semboyan_ketiga_wanted_person')->nullable()->comment('B6');
            $table->date('tarikh_semboyan_ketiga_wanted_person')->nullable()->comment('B6');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_borang')->nullable()->comment('B6');
            $table->boolean('status_penandaan_kelas_warna')->nullable()->comment('B6');

            // Additional BAHAGIAN 6 fields from edit form
            $table->boolean('status_saman_pdrm_s_257')->nullable()->comment('B6 - Saman PDRM S257 status');
            $table->string('no_saman_pdrm_s_257')->nullable()->comment('B6 - Saman PDRM S257 number');
            $table->boolean('status_saman_pdrm_s_167')->nullable()->comment('B6 - Saman PDRM S167 status');
            $table->string('no_saman_pdrm_s_167')->nullable()->comment('B6 - Saman PDRM S167 number');

            // Additional BAHAGIAN 4 fields from edit form
            $table->text('status_pergerakan_barang_kes_lain')->nullable()->comment('B4 - Other movement status');
            $table->string('status_pergerakan_barang_kes_makmal')->nullable()->comment('B4 - Laboratory movement status');
            $table->string('status_barang_kes_selesai_siasatan_RM')->nullable()->comment('status_barang_kes_selesai_siasatan_lain');
            $table->text('status_barang_kes_selesai_siasatan_lain')->nullable()->comment('B4 - Other completion status');
            $table->text('kaedah_pelupusan_lain')->nullable()->comment('B4 - Other disposal method');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes')->nullable()->comment('B4 - Overall examiner comments on evidence');
            
            // BAHAGIAN 7: Laporan E-FSA, Puspakom, dll (B7)
            // Post Mortem fields
            $table->boolean('status_permohonan_laporan_post_mortem_mayat')->nullable()->comment('B7 - Post mortem request status');
            $table->date('tarikh_permohonan_laporan_post_mortem_mayat')->nullable()->comment('B7 - Post mortem request date');
            // BANK
            $table->string('status_permohonan_E_FSA_1_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_1', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_1_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_1_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_2_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_2_BANK', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_2_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_2_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_3_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_3_BANK', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_3_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_3_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_4_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_4_BANK', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_4_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_4_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_5_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_5_BANK', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_5_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_5_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO')->nullable(); // B7

            // TELCO
            $table->string('status_permohonan_E_FSA_1_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_1_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_1_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_2_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_2_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_2_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_3_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_3_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_3_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_4_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_4_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_4_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO')->nullable(); // B7

            $table->string('status_permohonan_E_FSA_5_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_5_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO', 100)->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_5_oleh_IO_AIO', 100)->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO')->nullable(); // B7

           
            // PUSPAKOM
            $table->boolean('status_permohonan_laporan_puspakom')->nullable(); // B7
            $table->date('tarikh_permohonan_laporan_puspakom')->nullable(); // B7
            $table->boolean('status_laporan_penuh_puspakom')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_puspakom')->nullable(); // B7

            //JKR & JPJ
            $table->boolean('status_permohonan_laporan_jkr')->nullable()->comment('status_permohonan_laporan_jkr: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_jkr')->nullable()->comment('tarikh_permohonan_laporan_jkr: DATE');
            $table->boolean('status_laporan_penuh_jkr')->nullable()->comment('status_laporan_penuh_jkr: BOOLEAN');
            $table->date('tarikh_laporan_penuh_jkr')->nullable()->comment('tarikh_laporan_penuh_jkr: DATE');
            $table->boolean('status_permohonan_laporan_jpj')->nullable()->comment('status_permohonan_laporan_jpj: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_jpj')->nullable()->comment('tarikh_permohonan_laporan_jpj: DATE');
            $table->boolean('status_laporan_penuh_jpj')->nullable()->comment('status_laporan_penuh_jpj: BOOLEAN');
            $table->date('tarikh_laporan_penuh_jpj')->nullable()->comment('tarikh_laporan_penuh_jpj: DATE');
            // IMIGRESEN
            $table->boolean('status_permohonan_laporan_imigresen')->nullable(); // B7
            $table->date('tarikh_permohonan_laporan_imigresen')->nullable(); // B7
            $table->boolean('status_laporan_penuh_imigresen')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_imigresen')->nullable(); // B7

            // KASTAM
            $table->boolean('status_permohonan_laporan_kastam')->nullable(); // B7
            $table->date('tarikh_permohonan_laporan_kastam')->nullable(); // B7
            $table->boolean('status_laporan_penuh_kastam')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_kastam')->nullable(); // B7

            // FORENSIK PDRM
            $table->boolean('status_permohonan_laporan_forensik_pdrm')->nullable(); // B7
            $table->date('tarikh_permohonan_laporan_forensik_pdrm')->nullable(); // B7
            $table->boolean('status_laporan_penuh_forensik_pdrm')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_forensik_pdrm')->nullable(); // B7
            $table->text('jenis_barang_kes_forensik')->nullable()->comment('jenis_barang_kes_forensik');
            // Lain-lain
            $table->text('lain_lain_permohonan_laporan')->nullable(); // B7

            // BAHAGIAN 8: Status Fail (B8)
            $table->boolean('muka_surat_4_barang_kes_ditulis')->nullable()->comment('ADAKAH MUKA SURAT 4 - BARANG KES DITULIS BERSAMA NO DAFTAR BARANG KES');
            $table->boolean('muka_surat_4_dengan_arahan_tpr')->nullable()->comment('ADAKAH MUKA SURAT 4 - BARANG KES DITULIS BERSAMA NO DAFTAR BARANG KES DAN TELAH ADA ARAHAN YA TPR UNTUK PELUPUSAN ATAU SERAHAN SEMULA KE PEMILIK');
            $table->boolean('muka_surat_4_keputusan_kes_dicatat')->nullable()->comment('ADAKAH MUKA SURAT 4 - KEPUTUSAN KES DICATAT SELENGKAPNYA...');
            $table->boolean('fail_lmm_ada_keputusan_koroner')->nullable()->comment('ADAKAH FAIL L.M.M (T) ATAU L.M.M TELAH ADA KEPUTUSAN SIASATAN OLEH YA KORONER');
            $table->boolean('status_kus_fail')->nullable()->comment('ADAKAH KERTAS SIASATAN TELAH DI KUS/FAIL...');
            $table->text('keputusan_akhir_mahkamah')->nullable()->comment('KEPUTUSAN AKHIR OLEH MAHKAMAH...');
            $table->text('ulasan_pegawai_pemeriksa_fail')->nullable()->comment('ULASAN KESELURUHAN PEGAWAI PEMERIKSA (JIKA ADA)');

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
