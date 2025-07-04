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

            // == Core Fields from CSV ==
            $table->string('no_ks')->unique(); // From: NO KERTAS SIASATAN
            $table->string('io_aio')->nullable(); // From: PEGAWAI SIASATAN
            $table->string('seksyen')->nullable(); // From: SEKSYEN
            $table->date('tarikh_ks_dibuka')->nullable(); // From: TARIKH KERTAS SIASATAN DIBUKA
            $table->string('pegawai_pemeriksa_jips')->nullable(); // From: PEGAWAI PEMERIKSA (JIPS)
            $table->date('tarikh_minit_a')->nullable(); // From: TARIKH EDARAN PERTAMA
            $table->date('tarikh_minit_d')->nullable(); // From: TARIKH EDARAN AKHIR
            $table->string('edaran_pertama_melebihi_48jam')->nullable(); // From: EDARAN PERTAMA MELEBIHI 48JAM
            $table->string('terbengkalai_tb')->nullable(); // From: TERBENGKALAI (TB)
            $table->string('no_ext_brg_kes')->nullable(); // From: NO EXT BRG KES
            $table->string('brg_kes_tak_daftar')->nullable(); // From: BRG KES TAK DAFTAR
            $table->text('ulasan_isu_barang_kes')->nullable(); // From: ULASAN ISU BARANG KES
            $table->string('pem_1_2_3_4')->nullable(); // From: PEM 1,2,3,DAN 4
            $table->string('rj9')->nullable(); // From: RJ9 (REPORT TANGKAPAN)
            $table->string('rj99')->nullable(); // From: RJ 99 (KEPUTUSAN KES)
            $table->string('rj10a')->nullable(); // From: RJ 10A
            $table->string('rj10b')->nullable(); // From: RJ 10B
            $table->string('rj21')->nullable(); // From: RJ 21
            $table->string('permohonan_e_fsa')->nullable(); // From: PERMOHONAN e FSA (...)
            $table->string('permohonan_ulangan_e_fsa')->nullable(); // From: PERMOHONAN ULANGAN e FSA (...)
            $table->string('salinan_pdrm_s_43')->nullable(); // From: SALINAN PDRM (S) 43 (...)
            $table->date('tarikh_permohonan_telco')->nullable(); // From: TARIKH PERMOHONAN TELCO
            $table->string('arahan_tuduh_dilaksanakan_tidak')->nullable(); // From: ARAHAN TUDUH DILAKSANAKAN/TIDAK
            $table->string('diari_siasatan_tidak_dikemaskini')->nullable(); // From: DAIRI PENYIASATAN PDRM(S) 51B TIDAK DIKEMASKINI
            $table->string('permohonan_waran_tangkap')->nullable(); // From: PERMOHONAN WARAN TANGKAP
            $table->text('ulasan_keseluruhan_ks')->nullable(); // From: ULASAN KESELURUHAN KS

            // == System Calculated Statuses (can be removed if calculated on-the-fly) ==
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
        Schema::dropIfExists('komersil_papers');
    }
};