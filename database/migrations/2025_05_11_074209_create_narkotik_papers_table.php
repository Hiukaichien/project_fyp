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

            // == Core Fields from CSV ==
            $table->string('no_ks')->unique(); // From: NO K/SIASATAN
            $table->string('io_aio')->nullable(); // From: PEG. PENYIASAT
            $table->date('tarikh_laporan_polis')->nullable(); // From: TARIKH LAPORAN POLIS
            $table->string('seksyen')->nullable(); // From: SEKSYEN
            $table->date('tarikh_minit_a')->nullable(); // From: TARIKH EDARAN PERTAMA
            $table->date('tarikh_minit_d')->nullable(); // From: TARIKH EDARAN AKHIR
            $table->string('terbengkalai_tb')->nullable(); // From: TERBENGKALAI (TB)
            $table->string('pegawai_pemeriksa')->nullable(); // From: PEGAWAI PEMERIKSA
            $table->string('no_ext_brg_kes')->nullable(); // From: NO EXT BRG KES
            $table->string('tidak_daftar_barang_kes')->nullable(); // From: TIDAK DAFTAR BARANG KES (TD)
            $table->text('ulasan_barang_kes')->nullable(); // From: ULASAN BARANG KES
            $table->string('gambar_barang_kes')->nullable(); // From: GAMBAR BARANG KES
            $table->date('tarikh_urine_dipungut')->nullable(); // From: TARIKH URINE DIPUNGUT
            $table->date('tarikh_urine_dihantar_ke_patalogi')->nullable(); // From: TARIKH URINE DIHANTAR KE PATALOGI
            $table->string('urine_dihantar_lewat')->nullable(); // From: URINE DIHANTAR LEWAT
            $table->date('tarikh_laporan_patalogi_diterima')->nullable(); // From: Tarikh laporan patalogi diterima
            $table->string('keputusan_urine')->nullable(); // From: KEPUTUSAN URINE
            $table->date('tarikh_brg_kes_dihantar_kimia')->nullable(); // From: TARIKH BRG KES DIHANTAR KIMIA (MEMILIKI)
            $table->date('tarikh_laporan_kimia_diterima')->nullable(); // From: TARIKH LAPORAN KIMIA DITERIMA
            $table->date('tarikh_arahan_tuduh_oleh_tpr')->nullable(); // From: TARIKH ARAHAN TUDUH OLEH TPR
            $table->string('waran_tangkap_dibuat_atau_tidak')->nullable(); // From: WARAN TANGKAP DIBUAT ATAU TIDAK
            $table->string('rj10a_dijana_atau_tidak')->nullable(); // From: RJ 10A DI JANA ATAU TIDAK
            $table->string('rj9')->nullable(); // From: RJ9
            $table->string('rj99')->nullable(); // From: RJ99
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
        Schema::dropIfExists('narkotik_papers');
    }
};