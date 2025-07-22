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
            $table->string('no_fail_lmm_t')->nullable()->comment('B1');
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
            $table->text('ulasan_keputusan_pegawai_pemeriksa')->nullable()->comment('B3');

            // BAHAGIAN 4: Barang Kes (B4)
            $table->boolean('adakah_barang_kes_didaftarkan')->nullable()->comment('B4');
            $table->string('no_daftar_barang_kes_am')->nullable()->comment('B4');
            $table->string('no_daftar_barang_kes_berharga')->nullable()->comment('B4');
            $table->string('no_daftar_barang_kes_kenderaan')->nullable()->comment('B4');
            $table->json('status_pergerakan_barang_kes')->nullable()->comment('B4');
            $table->json('status_barang_kes_selesai_siasatan')->nullable()->comment('B4');
            $table->json('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan')->nullable()->comment('B4');
            $table->json('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan')->nullable()->comment('B4');
            $table->json('resit_kew_98e_pelupusan_tunai_perbendaharaan')->nullable()->comment('B4,resit_kew_98e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbencaharaan');
            $table->json('adakah_borang_serah_terima_pegawai_tangkapan')->nullable()->comment('B4');
            $table->string('adakah_borang_serah_terima_pemilik_saksi')->nullable()->comment('B4');
            $table->boolean('adakah_sijil_surat_kebenaran_ipo')->nullable()->comment('B4');
            $table->string('adakah_gambar_pelupusan')->nullable()->comment('B4');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa')->nullable()->comment('B4');

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

            // BAHAGIAN 7: Laporan E-FSA, Puspakom, dll (B7)
            // BANK
            $table->boolean('status_permohonan_E_FSA_1_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_1')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_1_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_1_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_2_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_2_BANK')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_2_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_2_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_3_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_3_BANK')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_3_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_3_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_4_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_4_BANK')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_4_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_4_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_5_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_permohonan_E_FSA_5_BANK')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_5_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_bank_laporan_E_FSA_5_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO')->nullable(); // B7

            // TELCO
            $table->boolean('status_permohonan_E_FSA_1_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_1_oleh_IO_AIO')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_1_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_2_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_2_oleh_IO_AIO')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_2_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_3_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_3_oleh_IO_AIO')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_3_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_4_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_4_oleh_IO_AIO')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_4_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO')->nullable(); // B7

            $table->boolean('status_permohonan_E_FSA_5_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_permohonan_E_FSA_5_oleh_IO_AIO')->nullable(); // B7
            $table->boolean('status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO')->nullable(); // B7
            $table->string('nama_telco_laporan_E_FSA_5_oleh_IO_AIO')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO')->nullable(); // B7

            // PUSPAKOM
            $table->boolean('status_permohonan_laporan_puspakom')->nullable(); // B7
            $table->date('tarikh_permohonan_laporan_puspakom')->nullable(); // B7
            $table->boolean('status_laporan_penuh_puspakom')->nullable(); // B7
            $table->date('tarikh_laporan_penuh_puspakom')->nullable(); // B7

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

            // Lain-lain
            $table->string('lain_lain_permohonan_laporan')->nullable(); // B7

            // BAHAGIAN 8: Status Fail (B8)
            $table->boolean('muka_surat_4_barang_kes_ditulis')->nullable()->comment('ADAKAH MUKA SURAT 4 - BARANG KES DITULIS BERSAMA NO DAFTAR BARANG KES');
            $table->boolean('muka_surat_4_dengan_arahan_tpr')->nullable()->comment('ADAKAH MUKA SURAT 4 - BARANG KES DITULIS BERSAMA NO DAFTAR BARANG KES DAN TELAH ADA ARAHAN YA TPR UNTUK PELUPUSAN ATAU SERAHAN SEMULA KE PEMILIK');
            $table->boolean('muka_surat_4_keputusan_kes_dicatat')->nullable()->comment('ADAKAH MUKA SURAT 4 - KEPUTUSAN KES DICATAT SELENGKAPNYA...');
            $table->boolean('fail_lmm_ada_keputusan_koroner')->nullable()->comment('ADAKAH FAIL L.M.M (T) ATAU L.M.M TELAH ADA KEPUTUSAN SIASATAN OLEH YA KORONER');
            $table->string('status_kus_fail')->nullable()->comment('ADAKAH KERTAS SIASATAN TELAH DI KUS/FAIL...');
            $table->json('keputusan_akhir_mahkamah')->nullable()->comment('KEPUTUSAN AKHIR OLEH MAHKAMAH...');
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
