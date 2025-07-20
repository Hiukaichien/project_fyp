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
        Schema::create('trafik_rule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // BAHAGIAN 1: Maklumat Asas (B1)
            $table->string('no_kertas_siasatan')->unique()->comment('B1');
            $table->string('no_fail_lmm_t')->nullable()->comment('B1');
            $table->string('no_repot_polis')->nullable()->comment('B1');
            $table->string('pegawai_penyiasat')->nullable()->comment('B1');
            $table->date('tarikh_laporan_polis_dibuka')->nullable()->comment('B1');
            $table->string('seksyen')->nullable()->comment('B1');

            // BAHAGIAN 2: Pemeriksaan & Status (B2)
            $table->string('pegawai_pemeriksa')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_pertama')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_kedua')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_sebelum_akhir')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_ks_akhir')->nullable()->comment('B2');
            $table->date('tarikh_semboyan_pemeriksaan_jips_ke_daerah')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_fail_lmm_t_pertama')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_fail_lmm_t_kedua')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir')->nullable()->comment('B2');
            $table->date('tarikh_edaran_minit_fail_lmm_t_akhir')->nullable()->comment('B2');
            $table->date('fail_lmm_bahagian_pengurusan_pada_muka_surat_2')->nullable()->comment('B2');

            // BAHAGIAN 3: Arahan & Keputusan (B3)
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

            // BAHAGIAN 5: Dokumen Siasatan (B5)
            $table->boolean('status_id_siasatan_dikemaskini')->nullable()->comment('B5');
            $table->boolean('status_rajah_kasar_tempat_kejadian')->nullable()->comment('B5');
            $table->boolean('status_gambar_tempat_kejadian')->nullable()->comment('B5');

            // BAHAGIAN 6: Borang & Semakan (B6)
            $table->json('status_pem')->nullable()->comment('B6');
            $table->boolean('status_rj10b')->nullable()->comment('B6');
            $table->date('tarikh_rj10b')->nullable()->comment('B6');
            $table->text('lain_lain_rj_dikesan')->nullable()->comment('B6');
            $table->boolean('status_saman_pdrm_s_257')->nullable()->comment('B6');
            $table->string('no_saman_pdrm_s_257')->nullable()->comment('B6');
            $table->boolean('status_saman_pdrm_s_167')->nullable()->comment('B6');
            $table->string('no_saman_pdrm_s_167')->nullable()->comment('B6');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_borang')->nullable()->comment('B6');

            // BAHAGIAN 7: Permohonan Laporan Agensi Luar (B7)
            $table->boolean('status_permohonan_laporan_jkr')->nullable()->comment('B7');
            $table->date('tarikh_permohonan_laporan_jkr')->nullable()->comment('B7');
            $table->boolean('status_laporan_penuh_jkr')->nullable()->comment('B7');
            $table->date('tarikh_laporan_penuh_jkr')->nullable()->comment('B7');
            $table->boolean('status_permohonan_laporan_jpj')->nullable()->comment('B7');
            $table->date('tarikh_permohonan_laporan_jpj')->nullable()->comment('B7');
            $table->boolean('status_laporan_penuh_jkjr')->nullable()->comment('B7'); // Name from diagram
            $table->date('tarikh_laporan_penuh_jkjr')->nullable()->comment('B7'); // Name from diagram
            $table->string('lain_lain_permohonan_laporan')->nullable()->comment('B7');

            // BAHAGIAN 8: Status Fail (B8)
            $table->string('adakah_muka_surat_4_keputusan_kes_dicatat')->nullable()->comment('B8');
            $table->string('adakah_ks_kus_fail_selesai')->nullable()->comment('B8');
            $table->string('adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan')->nullable()->comment('B8');
            $table->json('keputusan_akhir_mahkamah')->nullable()->comment('B8');
            $table->text('ulasan_keseluruhan_pegawai_pemeriksa_fail')->nullable()->comment('B8');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trafik_rule');
    }
};