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
        Schema::create('laporan_mati_mengejut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');

            // BAHAGIAN 1: Maklumat Asas
            $table->string('no_kertas_siasatan')->unique();
            $table->string('no_fail_lmm_sdr')->nullable();
            $table->string('no_repot_polis')->nullable()->index();
            $table->string('pegawai_penyiasat')->nullable();
            $table->date('tarikh_laporan_polis_dibuka')->nullable();
            $table->string('seksyen')->nullable();

            // BAHAGIAN 2: Pemeriksaan & Status
            $table->string('pegawai_pemeriksa')->nullable();
            $table->date('tarikh_edaran_minit_ks_pertama')->nullable();
            $table->date('tarikh_edaran_minit_ks_kedua')->nullable();
            $table->date('tarikh_edaran_minit_ks_sebelum_akhir')->nullable();
            $table->date('tarikh_edaran_minit_ks_akhir')->nullable();
            $table->date('tarikh_semboyan_pemeriksaan_jips_ke_daerah')->nullable();
            
            // LMM(T) Dates
            $table->date('tarikh_edaran_minit_fail_lmm_t_pertama')->nullable();
            $table->date('tarikh_edaran_minit_fail_lmm_t_kedua')->nullable();
            $table->date('tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir')->nullable();
            $table->date('tarikh_edaran_minit_fail_lmm_t_akhir')->nullable();
            $table->boolean('fail_lmm_bahagian_pengurusan_pada_muka_surat_2')->nullable();

            // BAHAGIAN 3: Arahan & Keputusan
            $table->boolean('arahan_minit_oleh_sio_status')->nullable();
            $table->date('arahan_minit_oleh_sio_tarikh')->nullable();
            $table->boolean('arahan_minit_ketua_bahagian_status')->nullable();
            $table->date('arahan_minit_ketua_bahagian_tarikh')->nullable();
            $table->boolean('arahan_minit_ketua_jabatan_status')->nullable();
            $table->date('arahan_minit_ketua_jabatan_tarikh')->nullable();
            $table->boolean('arahan_minit_oleh_ya_tpr_status')->nullable();
            $table->date('arahan_minit_oleh_ya_tpr_tarikh')->nullable();
            $table->string('keputusan_siasatan_oleh_ya_tpr')->nullable();
            $table->string('arahan_tuduh_oleh_ya_tpr')->nullable();
            $table->text('ulasan_keputusan_siasatan_tpr')->nullable();
            $table->string('keputusan_siasatan_oleh_ya_koroner')->nullable();
            $table->string('ulasan_keputusan_oleh_ya_koroner')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa')->nullable();

            // BAHAGIAN 4: Barang Kes
            $table->boolean('adakah_barang_kes_didaftarkan')->nullable();
            $table->string('no_daftar_barang_kes_am')->nullable();
            $table->string('no_daftar_barang_kes_berharga')->nullable();
            $table->string('jenis_barang_kes_am')->nullable();
            $table->string('jenis_barang_kes_berharga')->nullable();
            $table->string('status_pergerakan_barang_kes')->nullable();
            $table->text('status_pergerakan_barang_kes_lain')->nullable();
            $table->text('ujian_makmal_details')->nullable();
            $table->string('status_barang_kes_selesai_siasatan')->nullable();
            $table->text('status_barang_kes_selesai_siasatan_lain')->nullable();
            $table->decimal('dilupuskan_perbendaharaan_amount', 10, 2)->nullable();
            $table->string('kaedah_pelupusan_barang_kes')->nullable();
            $table->text('kaedah_pelupusan_barang_kes_lain')->nullable();
            $table->string('arahan_pelupusan_barang_kes')->nullable();
            $table->boolean('adakah_borang_serah_terima_pegawai_tangkapan_io')->nullable();
            $table->boolean('adakah_borang_serah_terima_penyiasat_pemilik_saksi')->nullable();
            $table->boolean('adakah_sijil_surat_kebenaran_ipd')->nullable();
            $table->boolean('adakah_gambar_pelupusan')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_barang_kes')->nullable();

            // BAHAGIAN 5: Dokumen Siasatan
            $table->boolean('status_id_siasatan_dikemaskini')->nullable();
            $table->boolean('status_rajah_kasar_tempat_kejadian')->nullable();
            $table->boolean('status_gambar_tempat_kejadian')->nullable();
            $table->boolean('status_gambar_post_mortem_mayat_di_hospital')->nullable();
            $table->boolean('status_gambar_barang_kes_am')->nullable();
            $table->boolean('status_gambar_barang_kes_berharga')->nullable();
            $table->boolean('status_gambar_barang_kes_darah')->nullable();

            // BAHAGIAN 6: Borang & Semakan
            $table->json('status_pem')->nullable();
            $table->boolean('status_rj2')->nullable();
            $table->date('tarikh_rj2')->nullable();
            $table->boolean('status_rj2b')->nullable();
            $table->date('tarikh_rj2b')->nullable();
            $table->boolean('status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_borang')->nullable();

            // BAHAGIAN 7: Permohonan Laporan Agensi Luar
            // Post Mortem
            $table->boolean('status_permohonan_laporan_post_mortem_mayat')->nullable();
            $table->date('tarikh_permohonan_laporan_post_mortem_mayat')->nullable();
            $table->boolean('status_laporan_penuh_bedah_siasat')->nullable();
            $table->date('tarikh_laporan_penuh_bedah_siasat')->nullable();
            $table->text('keputusan_laporan_post_mortem')->nullable();
            
            // Jabatan Kimia
            $table->boolean('status_permohonan_laporan_jabatan_kimia')->nullable();
            $table->date('tarikh_permohonan_laporan_jabatan_kimia')->nullable();
            $table->boolean('status_laporan_penuh_jabatan_kimia')->nullable();
            $table->date('tarikh_laporan_penuh_jabatan_kimia')->nullable();
            $table->text('keputusan_laporan_jabatan_kimia')->nullable();
            
            // Jabatan Patalogi
            $table->boolean('status_permohonan_laporan_jabatan_patalogi')->nullable();
            $table->date('tarikh_permohonan_laporan_jabatan_patalogi')->nullable();
            $table->boolean('status_laporan_penuh_jabatan_patalogi')->nullable();
            $table->date('tarikh_laporan_penuh_jabatan_patalogi')->nullable();
            $table->text('keputusan_laporan_jabatan_patalogi')->nullable();
            
            // Imigresen
            $table->boolean('status_permohonan_laporan_imigresen')->nullable();
            $table->date('tarikh_permohonan_laporan_imigresen')->nullable();
            $table->boolean('status_laporan_penuh_imigresen')->nullable();
            $table->date('tarikh_laporan_penuh_imigresen')->nullable();
            
            $table->text('lain_lain_permohonan_laporan')->nullable();

            // BAHAGIAN 8: Status Fail
            $table->boolean('status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar')->nullable();
            $table->boolean('status_barang_kes_arahan_tpr')->nullable(); // M/S 4 - Barang Kes Ditulis Bersama No Daftar & Arahan TPR
            $table->boolean('adakah_muka_surat_4_keputusan_kes_dicatat')->nullable();
            $table->boolean('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan')->nullable();
            $table->boolean('adakah_ks_kus_fail_selesai')->nullable();
            $table->string('keputusan_akhir_mahkamah')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_fail')->nullable();

            // System Calculated Status Fields
            $table->string('edar_lebih_24_jam_status')->nullable();
            $table->string('terbengkalai_3_bulan_status')->nullable();
            $table->string('baru_kemaskini_status')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_mati_mengejut');
    }
};