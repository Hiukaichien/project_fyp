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
        Schema::create('trafik_seksyen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // MAKLUMAT IPRS (8 Standard Fields)
            $table->string('iprs_no_kertas_siasatan')->nullable()->comment('IPRS: No. Kertas Siasatan');
            $table->date('iprs_tarikh_ks')->nullable()->comment('IPRS: Tarikh KS');
            $table->string('iprs_no_repot')->nullable()->comment('IPRS: No. Repot');
            $table->string('iprs_jenis_jabatan_ks')->nullable()->comment('IPRS: Jenis Jabatan/KS');
            $table->string('iprs_pegawai_penyiasat')->nullable()->comment('IPRS: Pegawai Penyiasat');
            $table->string('iprs_status_ks')->nullable()->comment('IPRS: Status KS');
            $table->string('iprs_status_kes')->nullable()->comment('IPRS: Status Kes');
            $table->string('iprs_seksyen')->nullable()->comment('IPRS: Seksyen');

            // BAHAGIAN 1: Maklumat Asas (B1)
            $table->string('no_kertas_siasatan')->unique()->comment('no_kertas_siasatan: VARCHAR(255)');
            $table->string('no_repot_polis')->nullable()->comment('no_repot_polis: VARCHAR(255)');
            $table->string('no_lmm_t')->nullable()->comment('No.L.M.M (T): VARCHAR(255)');
            $table->string('pegawai_penyiasat')->nullable()->comment('pegawai_penyiasat: VARCHAR(255)');
            $table->date('tarikh_laporan_polis_dibuka')->nullable()->comment('tarikh_laporan_polis_dibuka: DATE');
            $table->string('seksyen')->nullable()->comment('seksyen: VARCHAR(255)');

            // BAHAGIAN 2: Pemeriksaan & Status (B2)
            $table->string('pegawai_pemeriksa')->nullable()->comment('pegawai_pemeriksa: VARCHAR(255)');
            $table->date('tarikh_edaran_minit_ks_pertama')->nullable()->comment('tarikh_edaran_minit_ks_pertama: DATE');
            $table->date('tarikh_edaran_minit_ks_kedua')->nullable()->comment('tarikh_edaran_minit_ks_kedua: DATE');
            $table->date('tarikh_edaran_minit_ks_sebelum_akhir')->nullable()->comment('tarikh_edaran_minit_ks_sebelum_akhir: DATE');
            $table->date('tarikh_edaran_minit_ks_akhir')->nullable()->comment('tarikh_edaran_minit_ks_akhir: DATE');
            $table->date('tarikh_semboyan_pemeriksaan_jips_ke_daerah')->nullable()->comment('tarikh_semboyan_pemeriksaan_jips_ke_daerah: DATE');
            
            // BAHAGIAN 3: Arahan & Keputusan (B3)
            $table->boolean('arahan_minit_oleh_sio_status')->nullable()->comment('arahan_minit_oleh_sio_status: BOOLEAN');
            $table->date('arahan_minit_oleh_sio_tarikh')->nullable()->comment('arahan_minit_oleh_sio_tarikh: DATE');
            $table->boolean('arahan_minit_ketua_bahagian_status')->nullable()->comment('arahan_minit_ketua_bahagian_status: BOOLEAN');
            $table->date('arahan_minit_ketua_bahagian_tarikh')->nullable()->comment('arahan_minit_ketua_bahagian_tarikh: DATE');
            $table->boolean('arahan_minit_ketua_jabatan_status')->nullable()->comment('arahan_minit_ketua_jabatan_status: BOOLEAN');
            $table->date('arahan_minit_ketua_jabatan_tarikh')->nullable()->comment('arahan_minit_ketua_jabatan_tarikh: DATE');
            $table->boolean('arahan_minit_oleh_ya_tpr_status')->nullable()->comment('arahan_minit_oleh_ya_tpr_status: BOOLEAN');
            $table->date('arahan_minit_oleh_ya_tpr_tarikh')->nullable()->comment('arahan_minit_oleh_ya_tpr_tarikh: DATE');
            $table->string('keputusan_siasatan_oleh_ya_tpr')->nullable()->comment('keputusan_siasatan_oleh_ya_tpr: VARCHAR(255)');
            // CHANGED from JSON to STRING
            $table->string('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan')->nullable()->comment('adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan: VARCHAR(255)');
            $table->text('ulasan_keputusan_siasatan_tpr')->nullable()->comment('ulasan_keputusan_siasatan_tpr: TEXT');
            $table->string('keputusan_siasatan_oleh_ya_koroner')->nullable()->comment('keputusan_siasatan_oleh_ya_koroner: VARCHAR(255)');
            $table->string('ulasan_keputusan_oleh_ya_koroner')->nullable()->comment('ulasan_keputusan_oleh_ya_koroner: VARCHAR(255)');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa')->nullable()->comment('ulasan_keseluruhan_pegawai_pemeriksa: TEXT');
            
            // BAHAGIAN 3: Fail L.M.M (T) - Additional Fields
            $table->boolean('adakah_ms2_lmm_t_disahkan_oleh_kpd')->nullable()->comment('B3: ADAKAH M/S 2 L.M.M(T) TELAH DISAHKAN OLEH KPD');
            $table->boolean('adakah_lmm_t_dirujuk_kepada_ya_koroner')->nullable()->comment('B3: ADAKAH L.M.M(T) TELAH DI RUJUK KEPADA YA KORONER SETELAH ADA ARAHAN OLEH YA TPR');
            $table->text('keputusan_ya_koroner_lmm_t')->nullable()->comment('B3: KEPUTUSAN YA KORONER (L.M.M T)');
            
            // BAHAGIAN 4: Barang Kes (B4)
            $table->boolean('adakah_barang_kes_didaftarkan')->nullable()->comment('adakah_barang_kes_didaftarkan: BOOLEAN');
            $table->string('no_daftar_barang_kes_am')->nullable()->comment('no_daftar_barang_kes_am: VARCHAR(255)');
            $table->string('no_daftar_barang_kes_berharga')->nullable()->comment('no_daftar_barang_kes_berharga: VARCHAR(255)');
            $table->string('no_daftar_barang_kes_kenderaan')->nullable()->comment('no_daftar_barang_kes_kenderaan: VARCHAR(255)');
            $table->string('no_daftar_botol_spesimen_urin')->nullable()->comment('no_daftar_botol_spesimen_urin: VARCHAR(255)');
            $table->string('no_daftar_spesimen_darah')->nullable()->comment('no_daftar_spesimen_darah: VARCHAR(255)');
            $table->string('jenis_barang_kes_am')->nullable()->comment('jenis_barang_kes_am: VARCHAR(255)');
            $table->string('jenis_barang_kes_berharga')->nullable()->comment('jenis_barang_kes_berharga: VARCHAR(255)');
            $table->string('jenis_barang_kes_kenderaan')->nullable()->comment('jenis_barang_kes_kenderaan: VARCHAR(255)');
            $table->string('status_pergerakan_barang_kes')->nullable()->comment('status_pergerakan_barang_kes: VARCHAR(255)');
            $table->string('status_pergerakan_barang_kes_makmal')->nullable()->comment('status_pergerakan_barang_kes');
            $table->string('status_pergerakan_barang_kes_lain')->nullable()->comment('status_pergerakan_barang_kes_lain: VARCHAR(255)');
            $table->string('status_barang_kes_selesai_siasatan')->nullable()->comment('status_barang_kes_selesai_siasatan: VARCHAR(255)');
            $table->string('status_barang_kes_selesai_siasatan_RM')->nullable()->comment('status_barang_kes_selesai_siasatan_lain');
            $table->string('status_barang_kes_selesai_siasatan_lain')->nullable()->comment('status_barang_kes_selesai_siasatan_lain: VARCHAR(255)');
            $table->string('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan')->nullable()->comment('barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan: VARCHAR(255)');
            $table->string('kaedah_pelupusan_barang_kes_lain')->nullable()->comment('kaedah_pelupusan_barang_kes_lain: VARCHAR(255)'); // Named as per Blade for consistency
            $table->string('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan')->nullable()->comment('adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan: VARCHAR(255)');
            $table->string('resit_kew38e_pelupusan_wang_tunai')->nullable()->comment('resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan: VARCHAR(255)');
            $table->string('adakah_borang_serah_terima_pegawai_tangkapan')->nullable()->comment('adakah_borang_serah_terima_pegawai_tangkapan: VARCHAR(255)');
            $table->boolean('adakah_borang_serah_terima_pemilik_saksi')->nullable()->comment('adakah_borang_serah_terima_pemilik_saksi: BOOLEAN');
            $table->boolean('adakah_sijil_surat_kebenaran_ipo')->nullable()->comment('adakah_sijil_surat_kebenaran_ipo: BOOLEAN');
            $table->boolean('adakah_gambar_pelupusan')->nullable()->comment('adakah_gambar_pelupusan: BOOLEAN');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes')->nullable()->comment('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes: TEXT');
            
            // BAHAGIAN 5: Dokumen Siasatan (B5)
            $table->boolean('status_id_siasatan_dikemaskini')->nullable()->comment('status_id_siasatan_dikemaskini: BOOLEAN');
            $table->boolean('status_rajah_kasar_tempat_kejadian')->nullable()->comment('status_rajah_kasar_tempat_kejadian: BOOLEAN');
            $table->boolean('status_gambar_tempat_kejadian')->nullable()->comment('status_gambar_tempat_kejadian: BOOLEAN');
            $table->boolean('status_gambar_post_mortem_mayat_di_hospital')->nullable()->comment('status_gambar_post_mortem_mayat_di_hospital: BOOLEAN');
            $table->boolean('status_gambar_barang_kes_am')->nullable()->comment('status_gambar_barang_kes_am: BOOLEAN');
            $table->boolean('status_gambar_barang_kes_berharga')->nullable()->comment('status_gambar_barang_kes_berharga: BOOLEAN');
            $table->boolean('status_gambar_barang_kes_kenderaan')->nullable()->comment('status_gambar_barang_kes_kenderaan: BOOLEAN');
            $table->boolean('status_gambar_barang_kes_darah')->nullable()->comment('status_gambar_barang_kes_darah: BOOLEAN');
            $table->boolean('status_gambar_barang_kes_kontraban')->nullable()->comment('status_gambar_barang_kes_kontraban: BOOLEAN');

            // BAHAGIAN 6: Borang & Semakan (B6)
            $table->json('status_pem')->nullable()->comment('status_pem: JSON');
            $table->boolean('status_rj2')->nullable()->comment('status_rj2: BOOLEAN');
            $table->date('tarikh_rj2')->nullable()->comment('tarikh_rj2: DATE');
            $table->boolean('status_rj2b')->nullable()->comment('status_rj2b: BOOLEAN');
            $table->date('tarikh_rj2b')->nullable()->comment('tarikh_rj2b: DATE');
            $table->boolean('status_rj9')->nullable()->comment('status_rj9: BOOLEAN');
            $table->date('tarikh_rj9')->nullable()->comment('tarikh_rj9: DATE');
            $table->boolean('status_rj99')->nullable()->comment('status_rj99: BOOLEAN');
            $table->date('tarikh_rj99')->nullable()->comment('tarikh_rj99: DATE');
            $table->boolean('status_rj10a')->nullable()->comment('status_rj10a: BOOLEAN');
            $table->date('tarikh_rj10a')->nullable()->comment('tarikh_rj10a: DATE');
            $table->boolean('status_rj10b')->nullable()->comment('status_rj10b: BOOLEAN');
            $table->date('tarikh_rj10b')->nullable()->comment('tarikh_rj10b: DATE');
            $table->text('lain_lain_rj_dikesan')->nullable()->comment('lain_lain_rj_dikesan: TEXT');
            $table->boolean('status_saman_pdrm_s_257')->nullable()->comment('status_saman_pdrm_s_257: BOOLEAN');
            $table->string('no_saman_pdrm_s_257')->nullable()->comment('no_saman_pdrm_s_257: VARCHAR(255)');
            $table->boolean('status_saman_pdrm_s_167')->nullable()->comment('status_saman_pdrm_s_167: BOOLEAN');
            $table->string('no_saman_pdrm_s_167')->nullable()->comment('no_saman_pdrm_s_167: VARCHAR(255)');
            $table->string('status_semboyan_pertama_wanted_person')->nullable()->comment('status_semboyan_pertama_wanted_person: VARCHAR(255) (Ada / Cipta, Tiada / Tidak Cipta, Tidak Berkaitan)');
            $table->date('tarikh_semboyan_pertama_wanted_person')->nullable()->comment('tarikh_semboyan_pertama_wanted_person: DATE');
            $table->string('status_semboyan_kedua_wanted_person')->nullable()->comment('status_semboyan_kedua_wanted_person: VARCHAR(255) (Ada / Cipta, Tiada / Tidak Cipta, Tidak Berkaitan)');
            $table->date('tarikh_semboyan_kedua_wanted_person')->nullable()->comment('tarikh_semboyan_kedua_wanted_person: DATE');
            $table->string('status_semboyan_ketiga_wanted_person')->nullable()->comment('status_semboyan_ketiga_wanted_person: VARCHAR(255) (Ada / Cipta, Tiada / Tidak Cipta, Tidak Berkaitan)');
            $table->date('tarikh_semboyan_ketiga_wanted_person')->nullable()->comment('tarikh_semboyan_ketiga_wanted_person: DATE');
            $table->boolean('status_penandaan_kelas_warna')->nullable()->comment('status_penandaan_kelas_warna: BOOLEAN');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_bahagian_6')->nullable()->comment('B6: ULASAN KESELURUHAN PEGAWAI PEMERIKSA (JIKA ADA)');
            
            // BAHAGIAN 7: Permohonan Laporan Agensi Luar (B7)
            $table->boolean('status_permohonan_laporan_post_mortem_mayat')->nullable()->comment('B7: status_permohonan_laporan_post_mortem_mayat');
            $table->date('tarikh_permohonan_laporan_post_mortem_mayat')->nullable()->comment('B7: tarikh_permohonan_laporan_post_mortem_mayat');

            // Bedah Siasat
            $table->boolean('status_laporan_penuh_bedah_siasat')->nullable()->comment('B7: status_laporan_penuh_bedah_siasat');
            $table->date('tarikh_laporan_penuh_bedah_siasat')->nullable()->comment('B7: tarikh_laporan_penuh_bedah_siasat');

            // Jabatan Kimia
            $table->boolean('status_permohonan_laporan_jabatan_kimia')->nullable()->comment('B7: status_permohonan_laporan_jabatan_kimia');
            $table->date('tarikh_permohonan_laporan_jabatan_kimia')->nullable()->comment('B7: tarikh_permohonan_laporan_jabatan_kimia');
            $table->boolean('status_laporan_penuh_jabatan_kimia')->nullable()->comment('B7: status_laporan_penuh_jabatan_kimia');
            $table->date('tarikh_laporan_penuh_jabatan_kimia')->nullable()->comment('B7: tarikh_laporan_penuh_jabatan_kimia');
            $table->string('keputusan_laporan_jabatan_kimia')->nullable()->comment('B7: keputusan_laporan_jabatan_kimia');

            // Jabatan Patalogi
            $table->boolean('status_permohonan_laporan_jabatan_patalogi')->nullable()->comment('B7: status_permohonan_laporan_jabatan_patalogi');
            $table->date('tarikh_permohonan_laporan_jabatan_patalogi')->nullable()->comment('B7: tarikh_permohonan_laporan_jabatan_patalogi');
            $table->boolean('status_laporan_penuh_jabatan_patalogi')->nullable()->comment('B7: status_laporan_penuh_jabatan_patalogi');
            $table->date('tarikh_laporan_penuh_jabatan_patalogi')->nullable()->comment('B7: tarikh_laporan_penuh_jabatan_patalogi');
            $table->string('keputusan_laporan_jabatan_patalogi')->nullable()->comment('B7: keputusan_laporan_jabatan_patalogi');
            
            $table->boolean('status_permohonan_laporan_puspakom')->nullable()->comment('status_permohonan_laporan_puspakom: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_puspakom')->nullable()->comment('tarikh_permohonan_laporan_puspakom: DATE');
            $table->boolean('status_laporan_penuh_puspakom')->nullable()->comment('status_laporan_penuh_puspakom: BOOLEAN');
            $table->date('tarikh_laporan_penuh_puspakom')->nullable()->comment('tarikh_laporan_penuh_puspakom: DATE');
            $table->boolean('status_permohonan_laporan_jkr')->nullable()->comment('status_permohonan_laporan_jkr: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_jkr')->nullable()->comment('tarikh_permohonan_laporan_jkr: DATE');
            $table->boolean('status_laporan_penuh_jkr')->nullable()->comment('status_laporan_penuh_jkr: BOOLEAN');
            $table->date('tarikh_laporan_penuh_jkr')->nullable()->comment('tarikh_laporan_penuh_jkr: DATE');
            $table->boolean('status_permohonan_laporan_jpj')->nullable()->comment('status_permohonan_laporan_jpj: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_jpj')->nullable()->comment('tarikh_permohonan_laporan_jpj: DATE');
            $table->boolean('status_laporan_penuh_jpj')->nullable()->comment('status_laporan_penuh_jpj: BOOLEAN');
            $table->date('tarikh_laporan_penuh_jpj')->nullable()->comment('tarikh_laporan_penuh_jpj: DATE');
            
            // JKJR
            $table->boolean('status_permohonan_laporan_jkjr')->nullable()->comment('status_permohonan_laporan_jkjr: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_jkjr')->nullable()->comment('tarikh_permohonan_laporan_jkjr: DATE');
            $table->boolean('status_laporan_penuh_jkjr')->nullable()->comment('status_laporan_penuh_jkjr: BOOLEAN');
            $table->date('tarikh_laporan_penuh_jkjr')->nullable()->comment('tarikh_laporan_penuh_jkjr: DATE');
            
            // Kastam
            $table->boolean('status_permohonan_laporan_kastam')->nullable()->comment('status_permohonan_laporan_kastam: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_kastam')->nullable()->comment('tarikh_permohonan_laporan_kastam: DATE');
            $table->boolean('status_laporan_penuh_kastam')->nullable()->comment('status_laporan_penuh_kastam: BOOLEAN');
            $table->date('tarikh_laporan_penuh_kastam')->nullable()->comment('tarikh_laporan_penuh_kastam: DATE');

            // Forensik PDRM
            $table->boolean('status_permohonan_laporan_forensik_pdrm')->nullable()->comment('status_permohonan_laporan_forensik_pdrm: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_forensik_pdrm')->nullable()->comment('tarikh_permohonan_laporan_forensik_pdrm: DATE');
            $table->boolean('status_laporan_penuh_forensik_pdrm')->nullable()->comment('status_laporan_penuh_forensik_pdrm: BOOLEAN');
            $table->date('tarikh_laporan_penuh_forensik_pdrm')->nullable()->comment('tarikh_laporan_penuh_forensik_pdrm: DATE');
            $table->text('jenis_barang_kes_forensik')->nullable()->comment('jenis_barang_kes_forensik: TEXT');
            
            $table->boolean('status_permohonan_laporan_imigresen')->nullable()->comment('status_permohonan_laporan_imigresen: BOOLEAN');
            $table->date('tarikh_permohonan_laporan_imigresen')->nullable()->comment('tarikh_permohonan_laporan_imigresen: DATE');
            $table->boolean('status_laporan_penuh_imigresen')->nullable()->comment('status_laporan_penuh_imigresen: BOOLEAN');
            $table->date('tarikh_laporan_penuh_imigresen')->nullable()->comment('tarikh_laporan_penuh_imigresen: DATE');
            $table->string('lain_lain_permohonan_laporan')->nullable()->comment('lain_lain_permohonan_laporan: VARCHAR(255)');
            
            // BAHAGIAN 8: Status Fail (B8)
            $table->boolean('muka_surat_4_barang_kes_ditulis')->nullable()->comment('ADAKAH MUKA SURAT 4 - BARANG KES DITULIS BERSAMA NO DAFTAR BARANG KES');
            $table->boolean('muka_surat_4_dengan_arahan_tpr')->nullable()->comment('ADAKAH MUKA SURAT 4 - BARANG KES DITULIS BERSAMA NO DAFTAR BARANG KES DAN TELAH ADA ARAHAN YA TPR UNTUK PELUPUSAN ATAU SERAHAN SEMULA KE PEMILIK');
            $table->boolean('muka_surat_4_keputusan_kes_dicatat')->nullable()->comment('ADAKAH MUKA SURAT 4 - KEPUTUSAN KES DICATAT SELENGKAPNYA...');
            $table->boolean('fail_lmm_ada_keputusan_koroner')->nullable()->comment('ADAKAH FAIL L.M.M (T) ATAU L.M.M TELAH ADA KEPUTUSAN SIASATAN OLEH YA KORONER');
            // CHANGED from VARCHAR to BOOLEAN
            $table->boolean('status_kus_fail')->nullable()->comment('ADAKAH KERTAS SIASATAN TELAH DI KUS/FAIL...');
            // CHANGED from JSON to STRING
            $table->json('keputusan_akhir_mahkamah')->nullable()->comment('KEPUTUSAN AKHIR OLEH MAHKAMAH (Multi-select checkboxes)');
            $table->text('ulasan_pegawai_pemeriksa_fail')->nullable()->comment('ULASAN KESELURUHAN PEGAWAI PEMERIKSA (JIKA ADA)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trafik_seksyen');
    }
};