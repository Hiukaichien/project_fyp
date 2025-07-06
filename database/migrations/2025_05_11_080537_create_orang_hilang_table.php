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
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // == Core Fields from CSV ==
            $table->string('no_ks')->unique(); // From: NO KERTAS SIASATAN
            $table->string('pegawai_penyiasat')->nullable(); // From: PEGAWAI PENYIASAT
            $table->string('tarikh_laporan_polis_sistem')->nullable(); // From: the first 'TARIKH LAPORAN POLIS' which is a string
            $table->date('tarikh_ks')->nullable(); // From: TARIKH KERTAS SIASATAN
            $table->date('tarikh_laporan_polis')->nullable(); // From: the second 'TARIKH LAPORAN POLIS' which is a date
            $table->date('tarikh_minit_a')->nullable(); // From: TARIKH EDARAN PERTAMA
            $table->date('tarikh_minit_d')->nullable(); // From: TARIKH EDARAN AKHIR
            $table->string('terbengkalai_tb')->nullable(); // From: TERBENGKALAI (TB)
            $table->string('mps1_butiran_oh')->nullable(); // From: MPS 1 (BUTIRAN OH)
            $table->string('mps2_oh_dijumpai')->nullable(); // From: MPS 2 (OH DIJUMPAI)
            $table->string('percakapan_mangsa_dijumpai')->nullable(); // From: PERCAKAPAN MANGSA DIJUMPAI /BALIK)
            $table->string('kategori_umur_oh')->nullable(); // From: KATEGORI UMUR OH
            $table->string('jantina_oh')->nullable(); // From: JANTINA OH
            $table->string('kewarganegaraan')->nullable(); // From: KEWARGANERAAN
            $table->string('kedutaan')->nullable(); // From: KEDUTAAN
            $table->string('pem1_status')->nullable(); // From: PEM 1
            $table->string('pem2_status')->nullable(); // From: PEM 2
            $table->string('pem3_status')->nullable(); // From: PEM 3
            $table->string('pem4_status')->nullable(); // From: PEM 4
            $table->string('gambar_orang_hilang')->nullable(); // From: GAMBAR ORANG HILANG
            $table->string('hebahan_oh')->nullable(); // From: HEBAHAN OH
            $table->string('pemakluman_ciq_jsj')->nullable(); // From: PEMAKLUMAN CIQ JSJ
            $table->string('pemakluman_ke_nur_kasih')->nullable(); // From: PEMAKLUMAN KE NUR KASIH
            $table->string('status_oh')->nullable(); // From: STATUS OH
            $table->string('kep_dibuka_ks_jenayah')->nullable(); // From: ADAKAH KEP(OH) DIBUKA KS JIKA MELIBATKAN KES JENAYAH
            $table->string('sitrep_warga_asing')->nullable(); // From: SITREP (WARGA ASING)
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
        Schema::dropIfExists('orang_hilang');
    }
};