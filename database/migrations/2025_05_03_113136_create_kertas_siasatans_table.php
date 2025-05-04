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
        Schema::create('kertas_siasatans', function (Blueprint $table) {
            // Basic Info (Partially from Excel/IPRS Lookup)
            $table->id(); // Auto Generate Bil
            $table->string('no_ks')->unique(); // No. Kertas Siasatan - Primary Business Key
            $table->date('tarikh_ks')->nullable(); // Tarikh KS
            $table->string('no_report')->nullable(); // No. Repot
            $table->string('jenis_jabatan_ks')->nullable(); // Jenis Jabatan / KS
            $table->string('pegawai_penyiasat')->nullable(); // Pegawai Penyiasat
            $table->string('status_ks')->nullable(); // Status KS
            $table->string('status_kes')->nullable(); // Status Kes
            $table->string('seksyen')->nullable(); // Seksyen

            // Minit Edaran (User Input - Dates)
            $table->date('tarikh_minit_a')->nullable(); // Tarikh Edaran Minit Pertama (A)
            $table->date('tarikh_minit_b')->nullable(); // Tarikh Edaran Minit Kedua (B)
            $table->date('tarikh_minit_c')->nullable(); // Tarikh Edaran Sebelum Minit Terakhir (C)
            $table->date('tarikh_minit_d')->nullable(); // Tarikh Edaran Minit Terakhir (D)

            // Calculated Status Fields (System Calculated & Stored)
            $table->string('edar_lebih_24_jam_status')->nullable(); // YA, EDARAN LEWAT 24 JAM / EDARAN DALAM TEMPOH 24 JAM & KURANG
            $table->string('terbengkalai_3_bulan_status')->nullable(); // YA, TERBENGKALAI LEBIH 3 BULAN / TIDAK TERBENGKALAI
            $table->string('baru_kemaskini_status')->nullable(); // YA, BARU DIGERAKKAN UNTUK DIKEMASKINI / TIADA ISU

            // Status Semasa Diperiksa (User Input - Radio/Select + Conditional Date)
            $table->string('status_ks_semasa_diperiksa')->nullable(); // Stores the selected status text
            $table->date('tarikh_status_ks_semasa_diperiksa')->nullable(); // Date for the above status

            // Rakaman Percakapan (User Input - Radio)
            $table->enum('rakaman_pengadu', ['YA', 'TIADA'])->nullable(); // YA, PERCAKAPAN PENGADU DIRAKAM / TIADA RAKAMAN...
            $table->enum('rakaman_saspek', ['YA', 'TIADA'])->nullable(); // YA, PERCAKAPAN SUSPEK DIRAKAM / TIADA RAKAMAN...
            $table->enum('rakaman_saksi', ['YA', 'TIADA'])->nullable(); // YA, PERCAKAPAN SAKSI DIRAKAM / TIADA RAKAMAN...

            // ID Siasatan Lampiran (User Input - Radio + Conditional Date)
            $table->enum('id_siasatan_dilampirkan', ['YA', 'TIDAK'])->nullable();
            $table->date('tarikh_id_siasatan_dilampirkan')->nullable(); // Date if YA

            // Barang Kes (User Input - Various)
            $table->enum('barang_kes_am_didaftar', ['YA', 'TIDAK'])->nullable(); // Adakah Barang Kes AM Didaftar... (Radio)
            $table->string('no_daftar_kes_am')->nullable(); // No Daftar Barang Kes AM? (Text)
            $table->string('no_daftar_kes_senjata_api')->nullable(); // No Daftar Barang Kes Senjata Api? (Text)
            $table->string('no_daftar_kes_berharga')->nullable(); // No Daftar Barang Kes (Berharga)? (Text)
            $table->enum('gambar_rampasan_dilampirkan', ['YA', 'TIDAK'])->nullable(); // Adakah Gambar Rampasan... (Radio)
            $table->string('kedudukan_barang_kes')->nullable(); // Kedudukan Semasa Barang Kes (Radio/Select)
            $table->enum('surat_serah_terima_stor', ['ADA', 'TIADA'])->nullable(); // Surat Serah / Terima Barang Kes... (Radio)
            $table->enum('arahan_pelupusan', ['YA', 'TIDAK'])->nullable(); // Arahan Pelupusan Ekshibit... (Radio)
            $table->string('tatacara_pelupusan')->nullable(); // Tatacara Pelupusan Ekshibit (Radio/Select)
            $table->enum('resit_kew38e_dilampirkan', ['YA', 'TIDAK'])->nullable(); // Adakah Resit Kew.38E... (Radio)
            $table->enum('sijil_pelupusan_dilampirkan', ['YA', 'TIDAK'])->nullable(); // Adakah Surat Kuasa Atau Sijil... (Radio)
            $table->enum('gambar_pelupusan_dilampirkan', ['ADA', 'TIADA'])->nullable(); // Adakah Gambar Pelupusan... (Radio)
            $table->enum('surat_serah_terima_penuntut', ['YA', 'TIDAK'])->nullable(); // Sekiranya Ekshibit Serah... (Radio)
            $table->text('ulasan_barang_kes')->nullable(); // Ulasan Barang Kes (Textarea)

            // Pakar Judi / Forensik (User Input - Various)
            $table->enum('surat_mohon_pakar_judi', ['ADA', 'TIADA'])->nullable(); // Surat Permohonan Pakar Judi (Radio)
            $table->enum('laporan_pakar_judi', ['ADA', 'TIADA'])->nullable(); // Laporan Pakar Judi (Radio)
            $table->string('keputusan_pakar_judi')->nullable(); // Keputusan Laporan Pakar Judi (+ / -) (Radio/Select)
            $table->string('kategori_perjudian')->nullable(); // Kategori Perjudian Dimainkan (Text)
            $table->enum('surat_mohon_forensik', ['ADA', 'TIADA'])->nullable(); // Surat Permohonan Forensik PDRM (Radio)
            $table->enum('laporan_forensik', ['ADA', 'TIADA'])->nullable(); // Laporan Forensik PDRM (Radio)
            $table->string('keputusan_forensik')->nullable(); // Keputusan Laporan Forensik PDRM (Radio/Select)

            // Dokumen Lain (User Input - Various)
            $table->string('surat_jamin_polis')->nullable(); // Surat Jamin Polis... (Radio/Select: ADA, TIADA, Masih Guna Buku...)
            $table->enum('lakaran_lokasi', ['ADA', 'TIADA'])->nullable(); // Lakaran Rajah Kasar Lokasi... (Radio)
            $table->enum('gambar_lokasi', ['ADA', 'TIADA'])->nullable(); // Gambar Sebenar Lokasi... (Radio)

            // RJ Forms (User Input - Radio + Conditional Date)
            $table->enum('rj2_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->date('rj2_tarikh')->nullable();
            $table->enum('rj9_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->date('rj9_tarikh')->nullable();
            $table->enum('rj10a_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->date('rj10a_tarikh')->nullable();
            $table->enum('rj10b_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->date('rj10b_tarikh')->nullable();
            $table->enum('rj99_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->date('rj99_tarikh')->nullable();
            $table->enum('semboyan_kesan_tangkap_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->date('semboyan_kesan_tangkap_tarikh')->nullable();
            $table->enum('waran_tangkap_status', ['Mohon', 'Tidak Mohon'])->nullable();
            $table->date('waran_tangkap_tarikh')->nullable();
            $table->text('ulasan_isu_rj')->nullable(); // Ulasan Isu RJ Dikesan (Textarea)

             // Surat Pemberitahuan (User Input - Radio)
            $table->enum('pem1_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->enum('pem2_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->enum('pem3_status', ['Cipta', 'Tidak Cipta'])->nullable();
            $table->enum('pem4_status', ['Cipta', 'Tidak Cipta'])->nullable();

            // Isu-Isu (User Input - Radio)
            $table->enum('isu_tpr_tuduh', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_ks_lengkap_tiada_rujuk_tpr', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_tpr_arah_lupus_belum_laksana', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_tpr_arah_pulang_belum_laksana', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_tpr_arah_kesan_tangkap_tiada_tindakan', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_jatuh_hukum_barang_kes_tiada_rujuk_lupus', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_nfa_oleh_kbsjd_sahaja', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_selesai_jatuh_hukum_belum_kus_fail', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_ks_warisan_terbengkalai', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_kbsjd_simpan_ks', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_sio_simpan_ks', ['YA', 'TIADA ISU'])->nullable();
            $table->enum('isu_ks_pada_tpr', ['YA', 'TIADA ISU'])->nullable();

            // KS Hantar Status (User Input - Radio + Conditional Date)
            $table->enum('ks_hantar_tpr_status', ['YA', 'TIADA ISU'])->nullable();
            $table->date('ks_hantar_tpr_tarikh')->nullable();
            $table->enum('ks_hantar_kjsj_status', ['YA', 'TIADA ISU'])->nullable();
            $table->date('ks_hantar_kjsj_tarikh')->nullable();
            $table->enum('ks_hantar_d5_status', ['YA', 'TIADA ISU'])->nullable();
            $table->date('ks_hantar_d5_tarikh')->nullable();
            $table->enum('ks_hantar_kbsjd_status', ['YA', 'TIADA ISU'])->nullable();
            $table->date('ks_hantar_kbsjd_tarikh')->nullable();

            // Ulasan Pemeriksa (User Input - Textarea)
            $table->text('ulasan_isu_menarik')->nullable();
            $table->text('ulasan_keseluruhan')->nullable();

            // Timestamps
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kertas_siasatans');
    }
};
