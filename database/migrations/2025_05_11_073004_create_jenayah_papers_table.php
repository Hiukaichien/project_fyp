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
        Schema::create('jenayah_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // == Core Fields from CSV ==
            $table->string('no_ks')->unique(); // From: NO KERTAS SIASATAN
            $table->string('io_aio')->nullable(); // From: PEGAWAI PENYIASAT
            $table->string('seksyen')->nullable(); // From: SEKSYEN
            $table->date('tarikh_laporan_polis')->nullable(); // From: TARIKH LAPORAN POLIS
            $table->string('pegawai_pemeriksa_jips')->nullable(); // From: PEGAWAI PEMERIKSA JIPS BUKIT AMAN
            $table->date('tarikh_minit_a')->nullable(); // From: TARIKH EDARAN PERTAMA
            $table->date('tarikh_minit_d')->nullable(); // From: TARIKH EDARAN AKHIR
            $table->string('terbengkalai_tb')->nullable(); // From: TERBENGKALAI (TB)
            $table->string('no_ext_brg_kes')->nullable(); // From: NO EXT BRG KES
            $table->string('brg_kes_tak_daftar')->nullable(); // From: BRG KES TAK DAFTAR
            $table->string('gambar_brg_kes')->nullable(); // From: GAMBAR BRG KES
            $table->decimal('wang_tunai_lucut_hak_judi', 15, 2)->nullable(); // From: WANG TUNAI DILUCUT HAK (JUDI)
            $table->text('ulasan_barang_kes')->nullable(); // From: ULASAN BARANG KES
            $table->string('pem_1_2_3_4')->nullable(); // From: PEM 1/2/3/4
            $table->string('rj9')->nullable(); // From: RJ9
            $table->string('rj99')->nullable(); // From: RJ 99
            $table->string('rj10a')->nullable(); // From: RJ 10A
            $table->string('rj10b')->nullable(); // From: RJ 10B
            $table->string('rj21')->nullable(); // From: RJ 21
            $table->string('pdrma43_jamin_polis')->nullable(); // From: PDRM(A)43 JAMIN POLIS
            $table->string('laporan_pakar')->nullable(); // From: LAPORAN PAKAR
            $table->string('arahan_ya_tpr')->nullable(); // From: ARAHAN YA TPR
            $table->string('arahan_tuduh_ya_tpr')->nullable(); // From: ARAHAN TUDUH YA TPR
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
        Schema::dropIfExists('jenayah_papers');
    }
};