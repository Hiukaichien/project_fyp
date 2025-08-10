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
        Schema::create('orang_hilang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');

            // Auto-generated field
            $table->string('no_kertas_siasatan')->unique();

                        // MAKLUMAT IPRS (8 Standard Fields)
            $table->string('iprs_no_kertas_siasatan')->nullable()->comment('IPRS: No. Kertas Siasatan');
            $table->date('iprs_tarikh_ks')->nullable()->comment('IPRS: Tarikh KS');
            $table->string('iprs_no_repot')->nullable()->comment('IPRS: No. Repot');
            $table->string('iprs_jenis_jabatan_ks')->nullable()->comment('IPRS: Jenis Jabatan/KS');
            $table->string('iprs_pegawai_penyiasat')->nullable()->comment('IPRS: Pegawai Penyiasat');
            $table->string('iprs_status_ks')->nullable()->comment('IPRS: Status KS');
            $table->string('iprs_status_kes')->nullable()->comment('IPRS: Status Kes');
            $table->string('iprs_seksyen')->nullable()->comment('IPRS: Seksyen');
            
            // == BAHAGIAN 1: Maklumat Asas ==
            $table->string('no_repot_polis')->nullable();
            $table->string('pegawai_penyiasat')->nullable();
            $table->date('tarikh_laporan_polis_dibuka')->nullable();
            $table->string('seksyen')->nullable();

            // == BAHAGIAN 2: Pemeriksaan & Status ==
            $table->string('pegawai_pemeriksa')->nullable();
            $table->date('tarikh_edaran_minit_ks_pertama')->nullable();
            $table->date('tarikh_edaran_minit_ks_kedua')->nullable();
            $table->date('tarikh_edaran_minit_ks_sebelum_akhir')->nullable();
            $table->date('tarikh_edaran_minit_ks_akhir')->nullable();
            $table->date('tarikh_semboyan_pemeriksaan_jips_ke_daerah')->nullable();

            // == BAHAGIAN 3: Arahan & Keputusan ==
            // Arahan Minit SIO
            $table->boolean('arahan_minit_oleh_sio_status')->nullable();
            $table->date('arahan_minit_oleh_sio_tarikh')->nullable();
            
            // Arahan Minit Ketua Bahagian
            $table->boolean('arahan_minit_ketua_bahagian_status')->nullable();
            $table->date('arahan_minit_ketua_bahagian_tarikh')->nullable();
            
            // Arahan Minit Ketua Jabatan
            $table->boolean('arahan_minit_ketua_jabatan_status')->nullable();
            $table->date('arahan_minit_ketua_jabatan_tarikh')->nullable();
            
            // Arahan Minit YA TPR
            $table->boolean('arahan_minit_oleh_ya_tpr_status')->nullable();
            $table->date('arahan_minit_oleh_ya_tpr_tarikh')->nullable();
            
            // Keputusan & Ulasan
            $table->string('keputusan_siasatan_oleh_ya_tpr')->nullable();
            $table->string('arahan_tuduh_oleh_ya_tpr')->nullable(); // Changed from enum to string
            $table->text('ulasan_keputusan_siasatan_tpr')->nullable();
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa')->nullable();

            // == BAHAGIAN 4: Barang Kes ==
            $table->boolean('adakah_barang_kes_didaftarkan')->nullable();
            $table->string('no_daftar_barang_kes_am')->nullable();
            $table->string('no_daftar_barang_kes_berharga')->nullable();

            // == BAHAGIAN 5: Dokumen Siasatan ==
            $table->boolean('status_id_siasatan_dikemaskini')->nullable();
            $table->boolean('status_rajah_kasar_tempat_kejadian')->nullable();
            $table->boolean('status_gambar_tempat_kejadian')->nullable();
            $table->boolean('status_gambar_barang_kes_am')->nullable();
            $table->boolean('status_gambar_barang_kes_berharga')->nullable();
            $table->boolean('status_gambar_orang_hilang')->nullable();

            // == BAHAGIAN 6: Borang & Semakan ==
            // Status PEM (JSON array for multiple selections)
            $table->json('status_pem')->nullable();
            
            // MPS 1 & MPS 2
            $table->boolean('status_mps1')->nullable();
            $table->date('tarikh_mps1')->nullable();
            $table->boolean('status_mps2')->nullable();
            $table->date('tarikh_mps2')->nullable();
            
            // NUR-Alert JSJ
            $table->string('pemakluman_nur_alert_jsj_bawah_18_tahun')->nullable();
            
            // Rakaman Percakapan
            $table->string('rakaman_percakapan_orang_hilang')->nullable();
            
            // Laporan Polis
            $table->string('laporan_polis_orang_hilang_dijumpai')->nullable();
            
            // Hebahan Media Massa
            $table->boolean('hebahan_media_massa')->nullable();
            
            // Orang Hilang Dijumpai (Mati Mengejut)
            $table->boolean('orang_hilang_dijumpai_mati_mengejut_bukan_jenayah')->nullable();
            $table->text('alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah')->nullable();
            $table->boolean('orang_hilang_dijumpai_mati_mengejut_jenayah')->nullable();
            $table->text('alasan_orang_hilang_dijumpai_mati_mengejut_jenayah')->nullable();
            
            // Semboyan Pemakluman ke Kedutaan
            $table->boolean('semboyan_pemakluman_ke_kedutaan_bukan_warganegara')->nullable();
            
            // Ulasan Borang
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_borang')->nullable();

            // == BAHAGIAN 7: Permohonan Laporan Agensi Luar ==
            // Imigresen
            $table->boolean('status_permohonan_laporan_imigresen')->nullable();
            $table->date('tarikh_permohonan_laporan_imigresen')->nullable();
            
            // New fields added for BAHAGIAN 7
            $table->boolean('permohonan_laporan_permit_kerja')->nullable();
            $table->boolean('permohonan_laporan_agensi_pekerjaan')->nullable();
            $table->boolean('permohonan_status_kewarganegaraan')->nullable();

            // == BAHAGIAN 8: Status Fail ==
            $table->boolean('adakah_muka_surat_4_keputusan_kes_dicatat')->nullable();
            $table->string('adakah_ks_kus_fail_selesai')->nullable(); // Changed from boolean to string for KUS/FAIL dropdown
            $table->json('keputusan_akhir_mahkamah')->nullable(); // Changed from string to json for multiple checkboxes
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_fail')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orang_hilang');
    }
};