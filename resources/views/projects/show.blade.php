{{-- FILE: resources/views/projects/show.blade.php (Part 1 of 5) --}}
@php
    // --- DYNAMIC CONFIGURATION SETUP ---
    use App\Models\Jenayah;
    use App\Models\Narkotik;
    use App\Models\Komersil;
    use App\Models\TrafikSeksyen;
    use App\Models\TrafikRule;
    use App\Models\OrangHilang;
    use App\Models\LaporanMatiMengejut;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    // A single source of truth for all table configurations.
    // Keys are PascalCase to match the $dashboardData array from the controller.
    $paperTypes = [
        'Jenayah' => ['model' => new Jenayah(), 'route' => 'projects.jenayah_data', 'title' => 'JSJ'],
        'Narkotik' => ['model' => new Narkotik(), 'route' => 'projects.narkotik_data', 'title' => 'JSJN'],
        'Komersil' => ['model' => new Komersil(), 'route' => 'projects.komersil_data', 'title' => 'JSJK'],
        'TrafikSeksyen' => ['model' => new TrafikSeksyen(), 'route' => 'projects.trafik_seksyen_data', 'title' => 'JSPT (APJ 1987 - AKTA 333) '],
        'TrafikRule' => ['model' => new TrafikRule(), 'route' => 'projects.trafik_rule_data', 'title' => 'JSPT (KKLJ 1969 - LN 166/1959)'],
        'OrangHilang' => ['model' => new OrangHilang(), 'route' => 'projects.orang_hilang_data', 'title' => 'JP (ORANG HILANG)'],
        'LaporanMatiMengejut' => ['model' => new LaporanMatiMengejut(), 'route' => 'projects.laporan_mati_mengejut_data', 'title' => 'JP (MATI MENGEJUT)'],
    ];

    $ignoreColumns = ['id', 'user_id', 'project_id'];

    $jenayahColumns = [

        // IPRS Standard Fields (8 columns for standardization)
        'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
        'iprs_tarikh_ks' => 'IPRS Tarikh KS',
        'iprs_no_repot' => 'IPRS No. Repot',
        'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
        'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
        'iprs_status_ks' => 'IPRS Status KS',
        'iprs_status_kes' => 'IPRS Status Kes',
        'iprs_seksyen' => 'IPRS Seksyen',

        // BAHAGIAN 1: Maklumat Asas
        'no_kertas_siasatan' => 'No. Kertas Siasatan',
        'no_repot_polis' => 'No. Repot Polis',
        'pegawai_penyiasat' => 'Pegawai Penyiasat',
        'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
        'seksyen' => 'Seksyen',
        
        // BAHAGIAN 2: Pemeriksaan & Status
        'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
        'tarikh_edaran_minit_ks_pertama' => 'Tarikh Minit KS Pertama (A)',
        'tarikh_edaran_minit_ks_kedua' => 'Tarikh Minit KS Kedua (B)',
        'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 24 Jam',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Minit KS Sebelum Akhir (C)',
        'tarikh_edaran_minit_ks_akhir' => 'Tarikh Minit KS Akhir (D)',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan JIPS (E)',
        'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
        'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
        'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',
        
        // BAHAGIAN 3: Arahan & Keputusan
        'arahan_minit_oleh_sio_status' => 'Arahan Minit SIO',
        'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan SIO',
        'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Ketua Bahagian',
        'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
        'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Ketua Jabatan',
        'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit YA TPR',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan YA TPR',
        'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan YA TPR',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'Arahan Tuduh Diambil Tindakan',
        'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan TPR',
        'ulasan_keseluruhan_pegawai_pemeriksa' => 'Ulasan Pemeriksa (B3)',
        
        // BAHAGIAN 4: Barang Kes
        'adakah_barang_kes_didaftarkan' => 'Barang Kes Didaftarkan',
        'no_daftar_barang_kes_am' => 'No. Daftar BK Am',
        'no_daftar_barang_kes_berharga' => 'No. Daftar BK Berharga',
        'no_daftar_barang_kes_kenderaan' => 'No. Daftar BK Kenderaan',
        'no_daftar_botol_spesimen_urin' => 'No. Daftar Spesimen Urin',
        'no_daftar_spesimen_darah' => 'No. Daftar Spesimen Darah',
        'no_daftar_kontraban' => 'No. Daftar Kontraban',
        'jenis_barang_kes_am' => 'Jenis BK Am',
        'jenis_barang_kes_berharga' => 'Jenis BK Berharga',
        'jenis_barang_kes_kenderaan' => 'Jenis BK Kenderaan',
        'jenis_barang_kes_kontraban' => 'Jenis BK Kontraban',
        'status_pergerakan_barang_kes' => 'Status Pergerakan BK',
        'status_barang_kes_selesai_siasatan' => 'Status BK Selesai Siasatan',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'Kaedah Pelupusan BK',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'Arahan Pelupusan Wang Tunai',
        'resit_kew38e_pelupusan_wang_tunai' => 'Resit Kew.38e',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'Borang Serah/Terima (Pegawai Tangkapan)',
        'adakah_borang_serah_terima_pemilik_saksi' => 'Borang Serah/Terima (Pemilik/Saksi)',
        'adakah_sijil_surat_kebenaran_ipo' => 'Sijil/Surat Kebenaran IPD',
        'adakah_gambar_pelupusan' => 'Gambar Pelupusan',
        'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'Ulasan Pemeriksa (Barang Kes)',
        
        // BAHAGIAN 5: Dokumen Siasatan
        'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
        'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar',
        'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
        'status_gambar_post_mortem_mayat_di_hospital' => 'Gambar Post Mortem',
        'status_gambar_barang_kes_am' => 'Gambar BK Am',
        'status_gambar_barang_kes_berharga' => 'Gambar BK Berharga',
        'status_gambar_barang_kes_kenderaan' => 'Gambar BK Kenderaan',
        'status_gambar_barang_kes_darah' => 'Gambar BK Darah',
        'status_gambar_barang_kes_kontraban' => 'Gambar BK Kontraban',
        
        // BAHAGIAN 6: Borang & Semakan
        'status_pem' => 'Borang PEM',
        'status_rj2' => 'Status RJ2',
        'tarikh_rj2' => 'Tarikh RJ2',
        'status_rj2b' => 'Status RJ2B',
        'tarikh_rj2b' => 'Tarikh RJ2B',
        'status_rj9' => 'Status RJ9',
        'tarikh_rj9' => 'Tarikh RJ9',
        'status_rj99' => 'Status RJ99',
        'tarikh_rj99' => 'Tarikh RJ99',
        'status_rj10a' => 'Status RJ10A',
        'tarikh_rj10a' => 'Tarikh RJ10A',
        'status_rj10b' => 'Status RJ10B',
        'tarikh_rj10b' => 'Tarikh RJ10B',
        'lain_lain_rj_dikesan' => 'Lain-lain RJ Dikesan',
        'status_semboyan_pertama_wanted_person' => 'SEMBOYAN PEMAKLUMAN PERTAMA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_pertama_wanted_person' => 'Tarikh Semboyan WP 1',
        'status_semboyan_kedua_wanted_person' => 'SEMBOYAN PEMAKLUMAN KEDUA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_kedua_wanted_person' => 'Tarikh Semboyan WP 2',
        'status_semboyan_ketiga_wanted_person' => 'SEMBOYAN PEMAKLUMAN KETIGA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_ketiga_wanted_person' => 'Tarikh Semboyan WP 3',
        'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'Ulasan Pemeriksa (Borang)',
        'status_penandaan_kelas_warna' => 'Penandaan Kelas Warna',
        
        // BAHAGIAN 7: Permohonan Laporan Agensi Luar
        'status_permohonan_laporan_pakar_judi' => 'Mohon Laporan Pakar Judi',
        'tarikh_permohonan_laporan_pakar_judi' => 'Tarikh Mohon Pakar Judi',
        'status_laporan_penuh_pakar_judi' => 'Laporan Penuh Pakar Judi',
        'tarikh_laporan_penuh_pakar_judi' => 'Tarikh Laporan Pakar Judi',
        'keputusan_laporan_pakar_judi' => 'Keputusan Laporan Pakar Judi',
        'status_permohonan_laporan_post_mortem_mayat' => 'Mohon Laporan Post Mortem',
        'tarikh_permohonan_laporan_post_mortem_mayat' => 'Tarikh Mohon Post Mortem',
        'status_laporan_penuh_bedah_siasat' => 'Laporan Penuh Bedah Siasat',
        'tarikh_laporan_penuh_bedah_siasat' => 'Tarikh Laporan Bedah Siasat',
        'status_permohonan_laporan_jabatan_kimia' => 'Mohon Laporan Kimia',
        'tarikh_permohonan_laporan_jabatan_kimia' => 'Tarikh Mohon Kimia',
        'status_laporan_penuh_jabatan_kimia' => 'Laporan Penuh Kimia',
        'tarikh_laporan_penuh_jabatan_kimia' => 'Tarikh Laporan Kimia',
        'keputusan_laporan_jabatan_kimia' => 'Keputusan Laporan Kimia',
        'status_permohonan_laporan_jabatan_patalogi' => 'Mohon Laporan Patalogi',
        'tarikh_permohonan_laporan_jabatan_patalogi' => 'Tarikh Mohon Patalogi',
        'status_laporan_penuh_jabatan_patalogi' => 'Laporan Penuh Patalogi',
        'tarikh_laporan_penuh_jabatan_patalogi' => 'Tarikh Laporan Patalogi',
        'keputusan_laporan_jabatan_patalogi' => 'Keputusan Laporan Patalogi',
        'status_permohonan_laporan_puspakom' => 'Mohon Laporan Puspakom',
        'tarikh_permohonan_laporan_puspakom' => 'Tarikh Mohon Puspakom',
        'status_laporan_penuh_puspakom' => 'Laporan Penuh Puspakom',
        'tarikh_laporan_penuh_puspakom' => 'Tarikh Laporan Puspakom',
        'status_permohonan_laporan_jpj' => 'Mohon Laporan JPJ',
        'tarikh_permohonan_laporan_jpj' => 'Tarikh Mohon JPJ',
        'status_laporan_penuh_jpj' => 'Laporan Penuh JPJ',
        'tarikh_laporan_penuh_jpj' => 'Tarikh Laporan JPJ',
        'status_permohonan_laporan_imigresen' => 'Mohon Laporan Imigresen',
        'tarikh_permohonan_laporan_imigresen' => 'Tarikh Mohon Imigresen',
        'status_laporan_penuh_imigresen' => 'Laporan Penuh Imigresen',
        'tarikh_laporan_penuh_imigresen' => 'Tarikh Laporan Imigresen',
        'status_permohonan_laporan_kastam' => 'Mohon Laporan Kastam',
        'tarikh_permohonan_laporan_kastam' => 'Tarikh Mohon Kastam',
        'status_laporan_penuh_kastam' => 'Laporan Penuh Kastam',
        'tarikh_laporan_penuh_kastam' => 'Tarikh Laporan Kastam',
        'status_permohonan_laporan_forensik_pdrm' => 'Mohon Laporan Forensik',
        'tarikh_permohonan_laporan_forensik_pdrm' => 'Tarikh Mohon Forensik',
        'status_laporan_penuh_forensik_pdrm' => 'Laporan Penuh Forensik',
        'tarikh_laporan_penuh_forensik_pdrm' => 'Tarikh Laporan Forensik',
        'keputusan_laporan_forensik_pdrm' => 'Keputusan Laporan Forensik',
        'jenis_ujian_analisis_forensik' => 'Jenis Ujian Analisis Forensik',
        'lain_lain_permohonan_laporan' => 'Lain-lain Permohonan Laporan',
        
        // BAHAGIAN 8: Status Fail
        'muka_surat_4_barang_kes_ditulis' => 'M/S 4 - BK Ditulis',
        'muka_surat_4_dengan_arahan_tpr' => 'M/S 4 - Arahan TPR',
        'muka_surat_4_keputusan_kes_dicatat' => 'M/S 4 - Keputusan Kes Dicatat',
        'fail_lmm_ada_keputusan_koroner' => 'Fail LMM Ada Keputusan Koroner',
        'status_kus_fail' => 'Status KUS/FAIL',
        'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
        'ulasan_pegawai_pemeriksa_fail' => 'Ulasan Pemeriksa (Fail)',    

        // Timestamps
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Terakhir Dikemaskini',
    ];

    // Define custom columns for Narkotik based on actual form fields in edit.blade.php
    $narkotikColumns = [

                // IPRS Standard Fields (8 columns for standardization)
        'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
        'iprs_tarikh_ks' => 'IPRS Tarikh KS',
        'iprs_no_repot' => 'IPRS No. Repot',
        'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
        'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
        'iprs_status_ks' => 'IPRS Status KS',
        'iprs_status_kes' => 'IPRS Status Kes',
        'iprs_seksyen' => 'IPRS Seksyen',

    // BAHAGIAN 1: Maklumat Asas
    'no_kertas_siasatan' => 'No. Kertas Siasatan',
    'no_repot_polis' => 'No. Repot Polis',
    'pegawai_penyiasat' => 'Pegawai Penyiasat',
    'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
    'seksyen' => 'Seksyen',

    // BAHAGIAN 2: Pemeriksaan & Status
    'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
    'tarikh_edaran_minit_ks_pertama' => 'Tarikh Minit KS Pertama (A)',
    'tarikh_edaran_minit_ks_kedua' => 'Tarikh Minit KS Kedua (B)',
    'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 48 Jam',
    'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Minit KS Sebelum Akhir (C)',
    'tarikh_edaran_minit_ks_akhir' => 'Tarikh Minit KS Akhir (D)',
    'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan JIPS ke Daerah (E)',
    'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
    'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
    'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',

    // BAHAGIAN 3: Arahan & Keputusan
    'arahan_minit_oleh_sio_status' => 'Arahan Minit SIO',
    'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan SIO',
    'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
    'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Ketua Bahagian',
    'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
    'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Ketua Jabatan',
    'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit YA TPR',
    'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan YA TPR',
    'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan YA TPR',
    'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'Arahan Tuduh YA TPR',
    'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan Siasatan TPR',
    'ulasan_keseluruhan_pegawai_pemeriksa_b3' => 'Ulasan Pegawai Pemeriksa B3',

    // BAHAGIAN 4: Barang Kes
    'adakah_barang_kes_didaftarkan' => 'Barang Kes Didaftarkan',
    'no_daftar_barang_kes_am' => 'No. Daftar Barang Kes Am',
    'no_daftar_barang_kes_berharga' => 'No. Daftar Barang Kes Berharga',
    'no_daftar_barang_kes_kenderaan' => 'No. Daftar Barang Kes Kenderaan',
    'no_daftar_botol_spesimen_urin' => 'No. Daftar Botol Spesimen Urin',
    'no_daftar_barang_kes_dadah' => 'No. Daftar Barang Kes Dadah',
    'no_daftar_spesimen_darah' => 'No. Daftar Spesimen Darah',
    'jenis_barang_kes_am' => 'Jenis Barang Kes Am',
    'jenis_barang_kes_berharga' => 'Jenis Barang Kes Berharga',
    'jenis_barang_kes_kenderaan' => 'Jenis Barang Kes Kenderaan',
    'jenis_barang_kes_dadah' => 'Jenis Barang Kes Dadah',
    'status_pergerakan_barang_kes' => 'Status Pergerakan Barang Kes',
    //'status_pergerakan_barang_kes_makmal' => 'Pergerakan Barang Kes Makmal',
    //'status_pergerakan_barang_kes_lain' => 'Pergerakan Barang Kes Lain',
    'status_barang_kes_selesai_siasatan' => 'Status Barang Kes Selesai Siasatan',
    //'status_barang_kes_selesai_siasatan_RM' => 'Siasatan Selesai RM',
    //'status_barang_kes_selesai_siasatan_lain' => 'Siasatan Selesai Lain',
    'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'Kaedah Pelupusan',
    //'kaedah_pelupusan_barang_kes_lain' => 'Kaedah Pelupusan Lain',
    'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'Arahan Pelupusan',
    'resit_kew38e_pelupusan_wang_tunai' => 'Resit Kew.38e',
    'adakah_borang_serah_terima_pegawai_tangkapan' => 'Borang Serah Terima Pegawai Tangkapan',
    'adakah_borang_serah_terima_pemilik_saksi' => 'Borang Serah Terima Pemilik Saksi',
    'adakah_sijil_surat_kebenaran_ipo' => 'Sijil Surat Kebenaran IPO',
    'adakah_gambar_pelupusan' => 'Gambar Pelupusan',
    'ulasan_keseluruhan_pegawai_pemeriksa_b4' => 'Ulasan Pegawai Pemeriksa B4',

    // BAHAGIAN 5: Dokumen Siasatan
    'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
    'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar Tempat Kejadian',
    'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
    'gambar_botol_urin_3d_berseal' => 'Gambar Botol Spesimen Urin 3D',
    'gambar_pembalut_urin_dan_test_strip' => 'Gambar Pembalut Urin dan Test Strip Dadah Positif',
    'status_gambar_barang_kes_am' => 'Gambar Barang Kes Am',
    'status_gambar_barang_kes_berharga' => 'Gambar Barang Kes Berharga',
    'status_gambar_barang_kes_kenderaan' => 'Gambar Barang Kes Kenderaan',
    'status_gambar_barang_kes_dadah' => 'Gambar Barang Kes Dadah',
    'status_gambar_barang_kes_ketum' => 'Gambar Barang Kes Ketum',
    'status_gambar_barang_kes_darah' => 'Gambar Barang Kes Darah',
    'status_gambar_barang_kes_kontraban' => 'Gambar Barang Kes Kontraban',

    // BAHAGIAN 6: Borang & Semakan
    'status_pem' => 'Status PEM',
    'status_rj2' => 'Status RJ2',
    'tarikh_rj2' => 'Tarikh RJ2',
    'status_rj2b' => 'Status RJ2B',
    'tarikh_rj2b' => 'Tarikh RJ2B',
    'status_rj9' => 'Status RJ9',
    'tarikh_rj9' => 'Tarikh RJ9',
    'status_rj99' => 'Status RJ99',
    'tarikh_rj99' => 'Tarikh RJ99',
    'status_rj10a' => 'Status RJ10A',
    'tarikh_rj10a' => 'Tarikh RJ10A',
    'status_rj10b' => 'Status RJ10B',
    'tarikh_rj10b' => 'Tarikh RJ10B',
    'lain_lain_rj_dikesan' => 'Lain-lain RJ Dikesan',
    'status_semboyan_pertama_wanted_person' => 'SEMBOYAN PEMAKLUMAN PERTAMA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
    'tarikh_semboyan_pertama_wanted_person' => 'Tarikh Semboyan Pertama',
    'status_semboyan_kedua_wanted_person' => 'SEMBOYAN PEMAKLUMAN KEDUA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
    'tarikh_semboyan_kedua_wanted_person' => 'Tarikh Semboyan Kedua',
    'status_semboyan_ketiga_wanted_person' => 'SEMBOYAN PEMAKLUMAN KETIGA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
    'tarikh_semboyan_ketiga_wanted_person' => 'Tarikh Semboyan Ketiga',
    'status_penandaan_kelas_warna' => 'Penandaan Kelas Warna',
    'ulasan_keseluruhan_pegawai_pemeriksa_b6' => 'Ulasan Pegawai Pemeriksa B6',

    // BAHAGIAN 7: Permohonan Laporan Agensi Luar
    'status_permohonan_laporan_jabatan_kimia' => 'Permohonan Laporan Jabatan Kimia',
    'tarikh_permohonan_laporan_jabatan_kimia' => 'Tarikh Permohonan Jabatan Kimia',
    'status_laporan_penuh_jabatan_kimia' => 'Laporan Penuh Jabatan Kimia',
    'tarikh_laporan_penuh_jabatan_kimia' => 'Tarikh Laporan Penuh Jabatan Kimia',
    'keputusan_laporan_jabatan_kimia' => 'Keputusan Laporan Jabatan Kimia',
    'status_permohonan_laporan_jabatan_patalogi' => 'Permohonan Laporan Jabatan Patalogi',
    'tarikh_permohonan_laporan_jabatan_patalogi' => 'Tarikh Permohonan Jabatan Patalogi',
    'status_laporan_penuh_jabatan_patalogi' => 'Laporan Penuh Jabatan Patalogi',
    'tarikh_laporan_penuh_jabatan_patalogi' => 'Tarikh Laporan Penuh Jabatan Patalogi',
    'keputusan_laporan_jabatan_patalogi' => 'Keputusan Laporan Jabatan Patalogi',
    'status_permohonan_laporan_puspakom' => 'Permohonan Laporan PUSPAKOM',
    'tarikh_permohonan_laporan_puspakom' => 'Tarikh Permohonan PUSPAKOM',
    'status_laporan_penuh_puspakom' => 'Laporan Penuh PUSPAKOM',
    'tarikh_laporan_penuh_puspakom' => 'Tarikh Laporan Penuh PUSPAKOM',
    'status_permohonan_laporan_jpj' => 'Permohonan Laporan JPJ',
    'tarikh_permohonan_laporan_jpj' => 'Tarikh Permohonan JPJ',
    'status_laporan_penuh_jpj' => 'Laporan Penuh JPJ',
    'tarikh_laporan_penuh_jpj' => 'Tarikh Laporan Penuh JPJ',
    'status_permohonan_laporan_imigresen' => 'Permohonan Laporan Imigresen',
    'tarikh_permohonan_laporan_imigresen' => 'Tarikh Permohonan Imigresen',
    'status_laporan_penuh_imigresen' => 'Laporan Penuh Imigresen',
    'tarikh_laporan_penuh_imigresen' => 'Tarikh Laporan Penuh Imigresen',
    'status_permohonan_laporan_kastam' => 'Permohonan Laporan Kastam',
    'tarikh_permohonan_laporan_kastam' => 'Tarikh Permohonan Kastam',
    'status_laporan_penuh_kastam' => 'Laporan Penuh Kastam',
    'tarikh_laporan_penuh_kastam' => 'Tarikh Laporan Penuh Kastam',
    'status_permohonan_laporan_forensik_pdrm' => 'Permohonan Laporan Forensik PDRM',
    'tarikh_permohonan_laporan_forensik_pdrm' => 'Tarikh Permohonan Forensik PDRM',
    'status_laporan_penuh_forensik_pdrm' => 'Laporan Penuh Forensik PDRM',
    'tarikh_laporan_penuh_forensik_pdrm' => 'Tarikh Laporan Penuh Forensik PDRM',
    'jenis_barang_kes_di_hantar' => 'Jenis Barang Kes Di Hantar',
    'lain_lain_permohonan_laporan' => 'Lain-lain Permohonan Laporan',

    // BAHAGIAN 8: Status Fail
    'muka_surat_4_barang_kes_ditulis' => 'Muka Surat 4 Barang Kes Ditulis',
    'muka_surat_4_dengan_arahan_tpr' => 'Muka Surat 4 Dengan Arahan TPR',
    'muka_surat_4_keputusan_kes_dicatat' => 'Muka Surat 4 Keputusan Kes Dicatat',
    'fail_lmm_ada_keputusan_koroner' => 'Fail LMM Ada Keputusan Koroner',
    'status_kus_fail' => 'Status KUS/FAIL',
    'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
    'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'Ulasan Pegawai Pemeriksa Fail',

    // Date columns
    'created_at' => 'Tarikh Dicipta',
    'updated_at' => 'Tarikh Dikemaskini',
    ];
    // Define custom columns for OrangHilang based on actual form fields in edit.blade.php
    $orangHilangColumns = [

        // IPRS Standard Fields (8 columns for standardization)
        'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
        'iprs_tarikh_ks' => 'IPRS Tarikh KS',
        'iprs_no_repot' => 'IPRS No. Repot',
        'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
        'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
        'iprs_status_ks' => 'IPRS Status KS',
        'iprs_status_kes' => 'IPRS Status Kes',
        'iprs_seksyen' => 'IPRS Seksyen',

        // BAHAGIAN 1: Maklumat Asas
        'no_kertas_siasatan' => 'No. Kertas Siasatan',
        'no_repot_polis' => 'No. Repot Polis',
        'pegawai_penyiasat' => 'Pegawai Penyiasat',
        'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
        'seksyen' => 'Seksyen',
        
        // BAHAGIAN 2: Pemeriksaan & Status
        'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
        'tarikh_edaran_minit_ks_pertama' => 'Tarikh Edaran Minit KS Pertama (A)',
        'tarikh_edaran_minit_ks_kedua' => 'Tarikh Edaran Minit KS Kedua (B)',
        'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 24 Jam',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Edaran Minit KS Sebelum Akhir (C)',
        'tarikh_edaran_minit_ks_akhir' => 'Tarikh Edaran Minit KS Akhir (D)',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)',
        'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
        'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
        'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',
        
        // BAHAGIAN 3: Arahan & Keputusan
        'arahan_minit_oleh_sio_status' => 'Arahan Minit Oleh SIO',
        'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan Minit SIO',
        'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
        'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Minit Ketua Jabatan',
        'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit Oleh YA TPR',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan Minit YA TPR',
        'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan Oleh YA TPR',
        'arahan_tuduh_oleh_ya_tpr' => 'Arahan Tuduh Oleh YA TPR',
        'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan Siasatan TPR',
        'ulasan_keseluruhan_pegawai_pemeriksa' => 'Ulasan Keseluruhan Pegawai Pemeriksa',
        
        // BAHAGIAN 4: Barang Kes
        'adakah_barang_kes_didaftarkan' => 'Adakah Barang Kes Didaftarkan',
        'no_daftar_barang_kes_am' => 'No. Daftar Barang Kes Am',
        'no_daftar_barang_kes_berharga' => 'No. Daftar Barang Kes Berharga',
        
        // BAHAGIAN 5: Dokumen Siasatan
        'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
        'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar Tempat Kejadian',
        'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
        'status_gambar_barang_kes_am' => 'Gambar Barang Kes Am',
        'status_gambar_barang_kes_berharga' => 'Gambar Barang Kes Berharga',
        'status_gambar_orang_hilang' => 'Gambar Orang Hilang',
        
        // BAHAGIAN 6: Borang & Semakan
        'status_pem' => 'Borang PEM',
        'status_mps1' => 'Status MPS1',
        'tarikh_mps1' => 'Tarikh MPS1',
        'status_mps2' => 'Status MPS2',
        'tarikh_mps2' => 'Tarikh MPS2',
        'pemakluman_nur_alert_jsj_bawah_18_tahun' => 'Pemakluman NUR-Alert JSJ (Bawah 18 Tahun)',
        'rakaman_percakapan_orang_hilang' => 'Rakaman Percakapan Orang Hilang(OH) Dijumpai Semula',
        'laporan_polis_orang_hilang_dijumpai' => 'Laporan Polis Orang Hilang(OH) Dijumpai Semula',
        'hebahan_media_massa' => 'Hebahan Media Massa',
        'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => 'Orang Hilang Dijumpai (Mati Mengejut Bukan Jenayah)',
        'alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => 'Alasan Mati Mengejut Bukan Jenayah',
        'orang_hilang_dijumpai_mati_mengejut_jenayah' => 'Orang Hilang Dijumpai (Mati Mengejut Jenayah)',
        'alasan_orang_hilang_dijumpai_mati_mengejut_jenayah' => 'Alasan Mati Mengejut Jenayah',
        'semboyan_pemakluman_ke_kedutaan_bukan_warganegara' => 'Semboyan Pemakluman ke Kedutaan (Bukan Warganegara)',
        'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'Ulasan Keseluruhan Pegawai Pemeriksa (Borang)',
        
        // BAHAGIAN 7: Imigresen
        'status_permohonan_laporan_imigresen' => 'Permohonan Laporan Pengesahan Masuk / Keluar Malaysia',
        'tarikh_permohonan_laporan_imigresen' => 'Tarikh Permohonan Laporan Pengesahan Masuk / Keluar Malaysia',
        'permohonan_laporan_permit_kerja' => 'Permohonan Laporan Permit Kerja di Malaysia',
        'permohonan_laporan_agensi_pekerjaan' => 'Permohonan Laporan Agensi Pekerjaan di Malaysia',
        'permohonan_status_kewarganegaraan' => 'Permohonan Status Kewarganegaraan',
        
        // BAHAGIAN 8: Status Fail
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'M/S 4 - Keputusan Kes Dicatat',
        'adakah_ks_kus_fail_selesai' => 'KS Telah di KUS/FAIL',
        'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
        'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'Ulasan Keseluruhan Pegawai Pemeriksa (Fail)',

        // Date columns
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Tarikh Dikemaskini',
    ];

    // Define custom columns for LaporanMatiMengejut based on BAHAGIAN 1-8 order from show.blade.php
        $laporanMatiMengejutColumns = [

            // IPRS Standard Fields (8 columns for standardization)
            'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
            'iprs_tarikh_ks' => 'IPRS Tarikh KS',
            'iprs_no_repot' => 'IPRS No. Repot',
            'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
            'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
            'iprs_status_ks' => 'IPRS Status KS',
            'iprs_status_kes' => 'IPRS Status Kes',
            'iprs_seksyen' => 'IPRS Seksyen',

            // BAHAGIAN 1: Maklumat Asas
            'no_kertas_siasatan' => 'No. Kertas Siasatan',
            'no_fail_lmm_sdr' => 'No. Fail LMM/SDR',
            'no_repot_polis' => 'No. Repot Polis',
            'pegawai_penyiasat' => 'Pegawai Penyiasat',
            'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
            'seksyen' => 'Seksyen',
            
            // New LMM fields
            'adakah_ms_2_lmm_telah_disahkan_oleh_kpd' => 'M/S 2 LMM Disahkan KPD',
            'adakah_lmm_telah_di_rujuk_kepada_ya_koroner' => 'LMM Rujuk Kepada YA Koroner',
            'keputusan_ya_koroner' => 'Keputusan YA Koroner',

            // BAHAGIAN 2: Pemeriksaan & Status
            'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
            'tarikh_edaran_minit_ks_pertama' => 'Tarikh Minit KS Pertama (A)',
            'tarikh_edaran_minit_ks_kedua' => 'Tarikh Minit KS Kedua (B)',
            'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 24 Jam',
            'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Minit KS Sebelum Akhir (C)',
            'tarikh_edaran_minit_ks_akhir' => 'Tarikh Minit KS Akhir (D)',
            'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan JIPS (E)',
            'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
            'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
            'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',
            'tarikh_edaran_minit_fail_lmm_t_pertama' => 'Tarikh Minit LMM(T) Pertama',
            'tarikh_edaran_minit_fail_lmm_t_kedua' => 'Tarikh Minit LMM(T) Kedua',
            'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => 'Tarikh Minit LMM(T) Sebelum Akhir',
            'tarikh_edaran_minit_fail_lmm_t_akhir' => 'Tarikh Minit LMM(T) Akhir',

            // BAHAGIAN 3: Arahan & Keputusan
            'arahan_minit_oleh_sio_status' => 'Arahan Minit SIO',
            'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan SIO',
            'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
            'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Ketua Bahagian',
            'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
            'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Ketua Jabatan',
            'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit YA TPR',
            'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan YA TPR',
            'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan YA TPR',
            'arahan_tuduh_oleh_ya_tpr' => 'Arahan Tuduh YA TPR',
            'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan Siasatan TPR',
            'keputusan_siasatan_oleh_ya_koroner' => 'Keputusan Siasatan YA Koroner',
            'ulasan_keputusan_oleh_ya_koroner' => 'Ulasan Keputusan YA Koroner',
            'ulasan_keseluruhan_pegawai_pemeriksa' => 'Ulasan Pegawai Pemeriksa',

            // BAHAGIAN 4: Barang Kes
            'adakah_barang_kes_didaftarkan' => 'Barang Kes Didaftarkan',
            'no_daftar_barang_kes_am' => 'No. Daftar Barang Kes Am',
            'no_daftar_barang_kes_berharga' => 'No. Daftar Barang Kes Berharga',
            'jenis_barang_kes_am' => 'Jenis Barang Kes Am',
            'jenis_barang_kes_berharga' => 'Jenis Barang Kes Berharga',
            'status_pergerakan_barang_kes' => 'Status Pergerakan Barang Kes',
            //'status_pergerakan_barang_kes_lain' => 'Status Pergerakan Barang Kes (Lain-lain)',
            //'ujian_makmal_details' => 'Ujian Makmal',
            'status_barang_kes_selesai_siasatan' => 'Status Barang Kes Selesai Siasatan',
            //'status_barang_kes_selesai_siasatan_lain' => 'Status Barang Kes Selesai Siasatan (Lain-lain)',
            //'dilupuskan_perbendaharaan_amount' => 'Jumlah Dilupuskan ke Perbendaharaan',
            'kaedah_pelupusan_barang_kes' => 'Kaedah Pelupusan Barang Kes',
            //'kaedah_pelupusan_barang_kes_lain' => 'Kaedah Pelupusan Barang Kes (Lain-lain)',
            'arahan_pelupusan_barang_kes' => 'Arahan Pelupusan Barang Kes',
            'adakah_borang_serah_terima_pegawai_tangkapan_io' => 'Borang Serah/Terima (Pegawai Tangkapan)',
            'adakah_borang_serah_terima_penyiasat_pemilik_saksi' => 'Borang Serah/Terima (Penyiasat/Pemilik)',
            'adakah_sijil_surat_kebenaran_ipd' => 'Sijil/Surat Kebenaran IPD',
            'adakah_gambar_pelupusan' => 'Gambar Pelupusan',
            'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'Ulasan Pegawai Pemeriksa (Barang Kes)',

            // BAHAGIAN 5: Dokumen Siasatan
            'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
            'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar Tempat Kejadian',
            'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
            'status_gambar_post_mortem_mayat_di_hospital' => 'Gambar Post Mortem Mayat',
            'status_gambar_barang_kes_am' => 'Gambar Barang Kes Am',
            'status_gambar_barang_kes_berharga' => 'Gambar Barang Kes Berharga',
            'status_gambar_barang_kes_darah' => 'Gambar Barang Kes Darah',

            // BAHAGIAN 6: Borang & Semakan
            'status_pem' => 'Status PEM',
            'status_rj2' => 'Status RJ2',
            'tarikh_rj2' => 'Tarikh RJ2',
            'status_rj2b' => 'Status RJ2B',
            'tarikh_rj2b' => 'Tarikh RJ2B',
            'status_rj9' => 'Status RJ9',
            'tarikh_rj9' => 'Tarikh RJ9',
            'status_rj99' => 'Status RJ99',
            'tarikh_rj99' => 'Tarikh RJ99',
            'status_rj10a' => 'Status RJ10A',
            'tarikh_rj10a' => 'Tarikh RJ10A',
            'status_rj10b' => 'Status RJ10B',
            'tarikh_rj10b' => 'Tarikh RJ10B',
            'lain_lain_rj_dikesan' => 'Lain-lain RJ Dikesan',
            'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati' => 'Semboyan Pemakluman Kedutaan',
            'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'Ulasan Pegawai Pemeriksa (Borang)',

            // BAHAGIAN 7: Permohonan Laporan Agensi Luar
            'status_permohonan_laporan_post_mortem_mayat' => 'Permohonan Post Mortem',
            'tarikh_permohonan_laporan_post_mortem_mayat' => 'Tarikh Permohonan Post Mortem',
            'status_laporan_penuh_bedah_siasat' => 'Laporan Penuh Bedah Siasat',
            'tarikh_laporan_penuh_bedah_siasat' => 'Tarikh Laporan Penuh Bedah Siasat',
            'keputusan_laporan_post_mortem' => 'Keputusan Laporan Post Mortem',
            'status_permohonan_laporan_jabatan_kimia' => 'Permohonan Jabatan Kimia',
            'tarikh_permohonan_laporan_jabatan_kimia' => 'Tarikh Permohonan Jabatan Kimia',
            'status_laporan_penuh_jabatan_kimia' => 'Laporan Penuh Jabatan Kimia',
            'tarikh_laporan_penuh_jabatan_kimia' => 'Tarikh Laporan Penuh Jabatan Kimia',
            'keputusan_laporan_jabatan_kimia' => 'Keputusan Laporan Jabatan Kimia',
            'status_permohonan_laporan_jabatan_patalogi' => 'Permohonan Jabatan Patalogi',
            'tarikh_permohonan_laporan_jabatan_patalogi' => 'Tarikh Permohonan Jabatan Patalogi',
            'status_laporan_penuh_jabatan_patalogi' => 'Laporan Penuh Jabatan Patalogi',
            'tarikh_laporan_penuh_jabatan_patalogi' => 'Tarikh Laporan Penuh Jabatan Patalogi',
            'keputusan_laporan_jabatan_patalogi' => 'Keputusan Laporan Patalogi',
            'permohonan_laporan_pengesahan_masuk_keluar_malaysia' => 'Permohonan Laporan Pengesahan Masuk/Keluar Malaysia',
            'tarikh_permohonan_laporan_imigresen' => 'Tarikh Permohonan Laporan Pengesahan Masuk/Keluar Malaysia',
            'status_laporan_penuh_imigresen' => 'Laporan Penuh Imigresen',
            'tarikh_laporan_penuh_imigresen' => 'Tarikh Laporan Penuh Imigresen',
            
            // New simplified Imigresen fields
            'permohonan_laporan_permit_kerja_di_malaysia' => 'Permohonan Laporan Permit Kerja Di Malaysia',
            'permohonan_laporan_agensi_pekerjaan_di_malaysia' => 'Permohonan Laporan Agensi Pekerjaan Di Malaysia',
            'permohonan_status_kewarganegaraan' => 'Permohonan Status Kewarganegaraan',
            
            'lain_lain_permohonan_laporan' => 'Lain-lain Permohonan Laporan',

            // BAHAGIAN 8: Status Fail
            'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar' => 'M/S 4 - Barang Kes Ditulis',
            'status_barang_kes_arahan_tpr' => 'M/S 4 - Dengan Arahan TPR',
            'adakah_muka_surat_4_keputusan_kes_dicatat' => 'M/S 4 - Keputusan Kes Dicatat',
            'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'Fail LMM(T) Ada Keputusan',
            'adakah_ks_kus_fail_selesai' => 'KS KUS/FAIL Selesai',
            'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
            'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'Ulasan Pegawai Pemeriksa (Fail)',

            // Date columns
            'created_at' => 'Tarikh Dicipta',
            'updated_at' => 'Tarikh Dikemaskini',
        ];

    // Define custom columns for TrafikSeksyen based on migration structure
    $trafikSeksyenColumns = [
        // IPRS Standard Fields (8 columns for standardization)
        'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
        'iprs_tarikh_ks' => 'IPRS Tarikh KS',
        'iprs_no_repot' => 'IPRS No. Repot',
        'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
        'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
        'iprs_status_ks' => 'IPRS Status KS',
        'iprs_status_kes' => 'IPRS Status Kes',
        'iprs_seksyen' => 'IPRS Seksyen',
        
        // BAHAGIAN 1: Maklumat Asas
        'no_kertas_siasatan' => 'No. Kertas Siasatan',
        'no_repot_polis' => 'No. Repot Polis',
        'no_lmm_t' => 'No.L.M.M (T)',
        'pegawai_penyiasat' => 'Pegawai Penyiasat',
        'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
        'seksyen' => 'Seksyen',
        
        // BAHAGIAN 2: Pemeriksaan & Status
        'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
        'tarikh_edaran_minit_ks_pertama' => 'Tarikh Edaran Minit KS Pertama (A)',
        'tarikh_edaran_minit_ks_kedua' => 'Tarikh Edaran Minit KS Kedua (B)',
        'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 48 Jam',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Edaran Minit KS Sebelum Akhir (C)',
        'tarikh_edaran_minit_ks_akhir' => 'Tarikh Edaran Minit KS Akhir (D)',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)',
        'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
        'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
        'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',
        
        // BAHAGIAN 3: Arahan & Keputusan
        'arahan_minit_oleh_sio_status' => 'Arahan Minit Oleh SIO',
        'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan Minit SIO',
        'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
        'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Minit Ketua Jabatan',
        'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit Oleh YA TPR',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan Minit YA TPR',
        'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan Oleh YA TPR',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'Arahan Tuduh YA TPR Diambil Tindakan',
        'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan Siasatan TPR',
        'keputusan_siasatan_oleh_ya_koroner' => 'Keputusan Siasatan YA Koroner',
        'ulasan_keputusan_oleh_ya_koroner' => 'Ulasan Keputusan YA Koroner',
        'ulasan_keseluruhan_pegawai_pemeriksa' => 'Ulasan Keseluruhan Pegawai Pemeriksa',
        
        // BAHAGIAN 4: Barang Kes
        'adakah_barang_kes_didaftarkan' => 'Barang Kes Didaftarkan',
        'no_daftar_barang_kes_am' => 'No. Daftar Barang Kes Am',
        'no_daftar_barang_kes_berharga' => 'No. Daftar Barang Kes Berharga',
        'no_daftar_barang_kes_kenderaan' => 'No. Daftar Barang Kes Kenderaan',
        'no_daftar_botol_spesimen_urin' => 'No. Daftar Botol Spesimen Urin',
        'no_daftar_spesimen_darah' => 'No. Daftar Spesimen Darah',
        'jenis_barang_kes_am' => 'Jenis Barang Kes Am',
        'jenis_barang_kes_berharga' => 'Jenis Barang Kes Berharga',
        'jenis_barang_kes_kenderaan' => 'Jenis Barang Kes Kenderaan',
        'status_pergerakan_barang_kes' => 'Status Pergerakan Barang Kes',
        //'status_pergerakan_barang_kes_makmal' => 'Status Pergerakan Barang Kes (Makmal)',
        //'status_pergerakan_barang_kes_lain' => 'Status Pergerakan Barang Kes (Lain-lain)',
        'status_barang_kes_selesai_siasatan' => 'Status Barang Kes Selesai Siasatan',
        //'status_barang_kes_selesai_siasatan_RM' => 'Status Barang Kes Selesai Siasatan (RM)',
        //'status_barang_kes_selesai_siasatan_lain' => 'Status Barang Kes Selesai Siasatan (Lain-lain)',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'Kaedah Pelupusan Barang Kes',
        //'kaedah_pelupusan_barang_kes_lain' => 'Kaedah Pelupusan Barang Kes (Lain-lain)',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'Arahan Pelupusan ke Perbendaharaan',
        'resit_kew38e_pelupusan_wang_tunai' => 'Resit Kew.38e Pelupusan',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'Borang Serah Terima Pegawai Tangkapan',
        'adakah_borang_serah_terima_pemilik_saksi' => 'Borang Serah Terima Pemilik Saksi',
        'adakah_sijil_surat_kebenaran_ipo' => 'Sijil Surat Kebenaran IPO',
        'adakah_gambar_pelupusan' => 'Gambar Pelupusan',
        'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'Ulasan Keseluruhan Pegawai Pemeriksa (Barang Kes)',
        
        // BAHAGIAN 5: Dokumen Siasatan
        'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
        'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar Tempat Kejadian',
        'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
        'status_gambar_post_mortem_mayat_di_hospital' => 'Gambar Post-Mortem Mayat Di Hospital',
        'status_gambar_barang_kes_am' => 'Gambar Barang Kes Am',
        'status_gambar_barang_kes_berharga' => 'Gambar Barang Kes Berharga',
        'status_gambar_barang_kes_kenderaan' => 'Gambar Barang Kes Kenderaan',
        'status_gambar_barang_kes_darah' => 'Gambar Barang Kes Darah',
        'status_gambar_barang_kes_kontraban' => 'Gambar Barang Kes Kontraban',
        
        // BAHAGIAN 6: Borang & Semakan
        'status_pem' => 'Borang PEM',
        'status_rj2' => 'RJ 2',
        'tarikh_rj2' => 'Tarikh RJ 2',
        'status_rj2b' => 'RJ 2B',
        'tarikh_rj2b' => 'Tarikh RJ 2B',
        'status_rj9' => 'RJ 9',
        'tarikh_rj9' => 'Tarikh RJ 9',
        'status_rj99' => 'RJ 99',
        'tarikh_rj99' => 'Tarikh RJ 99',
        'status_rj10a' => 'RJ 10A',
        'tarikh_rj10a' => 'Tarikh RJ 10A',
        'status_rj10b' => 'RJ 10B',
        'tarikh_rj10b' => 'Tarikh RJ 10B',
        'lain_lain_rj_dikesan' => 'Lain-lain RJ Dikesan',
        'status_saman_pdrm_s_257' => 'Status Saman PDRM S.257',
        'no_saman_pdrm_s_257' => 'No. Saman PDRM S.257',
        'status_saman_pdrm_s_167' => 'Status Saman PDRM S.167',
        'no_saman_pdrm_s_167' => 'No. Saman PDRM S.167',
        'status_semboyan_pertama_wanted_person' => 'SEMBOYAN PEMAKLUMAN PERTAMA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_pertama_wanted_person' => 'Tarikh Semboyan Pertama Wanted Person',
        'status_semboyan_kedua_wanted_person' => 'SEMBOYAN PEMAKLUMAN KEDUA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_kedua_wanted_person' => 'Tarikh Semboyan Kedua Wanted Person',
        'status_semboyan_ketiga_wanted_person' => 'SEMBOYAN PEMAKLUMAN KETIGA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_ketiga_wanted_person' => 'Tarikh Semboyan Ketiga Wanted Person',
        'status_penandaan_kelas_warna' => 'Penandaan Kelas Warna',
        'ulasan_keseluruhan_pegawai_pemeriksa_bahagian_6' => 'Ulasan Keseluruhan Pegawai Pemeriksa (Borang)',
        
        // BAHAGIAN 7: Permohonan Laporan Agensi Luar
        'status_permohonan_laporan_post_mortem_mayat' => 'Permohonan Laporan Post Mortem Mayat',
        'tarikh_permohonan_laporan_post_mortem_mayat' => 'Tarikh Permohonan Laporan Post Mortem Mayat',
        'status_laporan_penuh_bedah_siasat' => 'Laporan Penuh Bedah Siasat',
        'tarikh_laporan_penuh_bedah_siasat' => 'Tarikh Laporan Penuh Bedah Siasat',
        'status_permohonan_laporan_jabatan_kimia' => 'Permohonan Laporan Jabatan Kimia',
        'tarikh_permohonan_laporan_jabatan_kimia' => 'Tarikh Permohonan Laporan Jabatan Kimia',
        'status_laporan_penuh_jabatan_kimia' => 'Laporan Penuh Jabatan Kimia',
        'tarikh_laporan_penuh_jabatan_kimia' => 'Tarikh Laporan Penuh Jabatan Kimia',
        'keputusan_laporan_jabatan_kimia' => 'Keputusan Laporan Jabatan Kimia',
        'status_permohonan_laporan_jabatan_patalogi' => 'Permohonan Laporan Jabatan Patalogi',
        'tarikh_permohonan_laporan_jabatan_patalogi' => 'Tarikh Permohonan Laporan Jabatan Patalogi',
        'status_laporan_penuh_jabatan_patalogi' => 'Laporan Penuh Jabatan Patalogi',
        'tarikh_laporan_penuh_jabatan_patalogi' => 'Tarikh Laporan Penuh Jabatan Patalogi',
        'keputusan_laporan_jabatan_patalogi' => 'Keputusan Laporan Jabatan Patalogi',
        'status_permohonan_laporan_puspakom' => 'Permohonan Laporan PUSPAKOM',
        'tarikh_permohonan_laporan_puspakom' => 'Tarikh Permohonan PUSPAKOM',
        'status_laporan_penuh_puspakom' => 'Laporan Penuh PUSPAKOM',
        'tarikh_laporan_penuh_puspakom' => 'Tarikh Laporan Penuh PUSPAKOM',
        'status_permohonan_laporan_jkr' => 'Permohonan Laporan JKR',
        'tarikh_permohonan_laporan_jkr' => 'Tarikh Permohonan JKR',
        'status_laporan_penuh_jkr' => 'Laporan Penuh JKR',
        'tarikh_laporan_penuh_jkr' => 'Tarikh Laporan Penuh JKR',
        'status_permohonan_laporan_jpj' => 'Permohonan Laporan JPJ',
        'tarikh_permohonan_laporan_jpj' => 'Tarikh Permohonan JPJ',
        'status_laporan_penuh_jpj' => 'Laporan Penuh JPJ',
        'tarikh_laporan_penuh_jpj' => 'Tarikh Laporan Penuh JPJ',
        'status_permohonan_laporan_jkjr' => 'Permohonan Laporan JKJR',
        'tarikh_permohonan_laporan_jkjr' => 'Tarikh Permohonan Laporan JKJR',
        'status_laporan_penuh_jkjr' => 'Laporan Penuh JKJR',
        'tarikh_laporan_penuh_jkjr' => 'Tarikh Laporan Penuh JKJR',
        'status_permohonan_laporan_kastam' => 'Permohonan Laporan Kastam',
        'tarikh_permohonan_laporan_kastam' => 'Tarikh Permohonan Laporan Kastam',
        'status_laporan_penuh_kastam' => 'Laporan Penuh Kastam',
        'tarikh_laporan_penuh_kastam' => 'Tarikh Laporan Penuh Kastam',
        'status_permohonan_laporan_forensik_pdrm' => 'Permohonan Laporan Forensik PDRM',
        'tarikh_permohonan_laporan_forensik_pdrm' => 'Tarikh Permohonan Laporan Forensik PDRM',
        'status_laporan_penuh_forensik_pdrm' => 'Laporan Penuh Forensik PDRM',
        'tarikh_laporan_penuh_forensik_pdrm' => 'Tarikh Laporan Penuh Forensik PDRM',
        'jenis_barang_kes_forensik' => 'Jenis Barang Kes Forensik',
        'status_permohonan_laporan_imigresen' => 'Permohonan Laporan Imigresen',
        'tarikh_permohonan_laporan_imigresen' => 'Tarikh Permohonan Laporan Imigresen',
        'status_laporan_penuh_imigresen' => 'Laporan Penuh Imigresen',
        'tarikh_laporan_penuh_imigresen' => 'Tarikh Laporan Penuh Imigresen',
        'lain_lain_permohonan_laporan' => 'Lain-lain Permohonan Laporan',
        
        // BAHAGIAN 8: Status Fail
        'muka_surat_4_barang_kes_ditulis' => 'M/S 4 - Barang Kes Ditulis Bersama No Daftar',
        'muka_surat_4_dengan_arahan_tpr' => 'M/S 4 - Dengan Arahan TPR Untuk Pelupusan/Serahan',
        'muka_surat_4_keputusan_kes_dicatat' => 'M/S 4 - Keputusan Kes Dicatat',
        'fail_lmm_ada_keputusan_koroner' => 'Fail L.M.M Ada Keputusan Siasatan Oleh YA Koroner',
        'status_kus_fail' => 'Status KS di KUS/FAIL',
        'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
        'ulasan_pegawai_pemeriksa_fail' => 'Ulasan Pegawai Pemeriksa (Fail)',
        
        // Date columns
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Tarikh Dikemaskini',
    ];

    // Define custom columns for TrafikRule based on migration structure
    $trafikRuleColumns = [

        // IPRS Standard Fields (8 columns for standardization)
        'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
        'iprs_tarikh_ks' => 'IPRS Tarikh KS',
        'iprs_no_repot' => 'IPRS No. Repot',
        'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
        'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
        'iprs_status_ks' => 'IPRS Status KS',
        'iprs_status_kes' => 'IPRS Status Kes',
        'iprs_seksyen' => 'IPRS Seksyen',
        
        // BAHAGIAN 1: Maklumat Asas
        'no_kertas_siasatan' => 'No. Kertas Siasatan',
        'no_fail_lmm_t' => 'No. Fail LMM(T)',
        'no_repot_polis' => 'No. Repot Polis',
        'pegawai_penyiasat' => 'Pegawai Penyiasat',
        'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
        'seksyen' => 'Seksyen',
        
        // BAHAGIAN 2: Pemeriksaan & Status
        'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
        'tarikh_edaran_minit_ks_pertama' => 'Tarikh Edaran Minit KS Pertama (A)',
        'tarikh_edaran_minit_ks_kedua' => 'Tarikh Edaran Minit KS Kedua (B)',
        'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 24 Jam',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Edaran Minit KS Sebelum Akhir (C)',
        'tarikh_edaran_minit_ks_akhir' => 'Tarikh Edaran Minit KS Akhir (D)',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)',
        'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
        'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
        'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',
        'tarikh_edaran_minit_fail_lmm_t_pertama' => 'Tarikh Minit LMM(T) Pertama',
        'tarikh_edaran_minit_fail_lmm_t_kedua' => 'Tarikh Minit LMM(T) Kedua',
        'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => 'Tarikh Minit LMM(T) Sebelum Akhir',
        'tarikh_edaran_minit_fail_lmm_t_akhir' => 'Tarikh Minit LMM(T) Akhir',
        'fail_lmm_t_muka_surat_2_disahkan_kpd' => 'Fail LMM(T) Disahkan KPD',

        // BAHAGIAN 3: Arahan & Keputusan
        'arahan_minit_oleh_sio_status' => 'Arahan Minit Oleh SIO',
        'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan Minit SIO',
        'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
        'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Minit Ketua Jabatan',
        'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit Oleh YA TPR',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan Minit YA TPR',
        'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan Oleh YA TPR',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'Arahan Tuduh YA TPR Diambil Tindakan',
        'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan Siasatan TPR',
        'ulasan_keseluruhan_pegawai_pemeriksa' => 'Ulasan Keseluruhan Pegawai Pemeriksa',
        
        // BAHAGIAN 5: Dokumen Siasatan
        'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
        'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar Tempat Kejadian',
        'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
        
        // BAHAGIAN 6: Borang & Semakan
        'status_pem' => 'Borang PEM',
        'status_rj2' => 'Status RJ2',
        'tarikh_rj2' => 'Tarikh RJ2',
        'status_rj2b' => 'Status RJ2B',
        'tarikh_rj2b' => 'Tarikh RJ2B',
        'status_rj9' => 'Status RJ9',
        'tarikh_rj9' => 'Tarikh RJ9',
        'status_rj99' => 'Status RJ99',
        'tarikh_rj99' => 'Tarikh RJ99',
        'status_rj10a' => 'Status RJ10A',
        'tarikh_rj10a' => 'Tarikh RJ10A',
        'status_rj10b' => 'Status RJ10B',
        'tarikh_rj10b' => 'Tarikh RJ10B',
        'status_saman_pdrm_s_257' => 'Status Saman PDRM S.257',
        'no_saman_pdrm_s_257' => 'No Saman PDRM S.257',
        'status_saman_pdrm_s_167' => 'Status Saman PDRM S.167',
        'no_saman_pdrm_s_167' => 'No Saman PDRM S.167',
        'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'Ulasan Pemeriksa (Borang)',
        
        // BAHAGIAN 7: Permohonan Laporan Agensi Luar
        'status_permohonan_laporan_jkr' => 'Permohonan Laporan JKR',
        'tarikh_permohonan_laporan_jkr' => 'Tarikh Permohonan JKR',
        'status_laporan_penuh_jkr' => 'Laporan Penuh JKR',
        'tarikh_laporan_penuh_jkr' => 'Tarikh Laporan Penuh JKR',
        'status_permohonan_laporan_jpj' => 'Permohonan Laporan JPJ',
        'tarikh_permohonan_laporan_jpj' => 'Tarikh Permohonan JPJ',
        'status_laporan_penuh_jpj' => 'Laporan Penuh JPJ',
        'tarikh_laporan_penuh_jpj' => 'Tarikh Laporan Penuh JPJ',
        'status_permohonan_laporan_jkjr' => 'Permohonan Laporan JKJR',
        'tarikh_permohonan_laporan_jkjr' => 'Tarikh Permohonan JKJR',
        'status_laporan_penuh_jkjr' => 'Laporan Penuh JKJR',
        'tarikh_laporan_penuh_jkjr' => 'Tarikh Laporan Penuh JKJR',
        'status_permohonan_laporan_puspakom' => 'Permohonan Laporan PUSPAKOM',
        'tarikh_permohonan_laporan_puspakom' => 'Tarikh Permohonan PUSPAKOM',
        'status_laporan_penuh_puspakom' => 'Laporan Penuh PUSPAKOM',
        'tarikh_laporan_penuh_puspakom' => 'Tarikh Laporan Penuh PUSPAKOM',
        'status_permohonan_laporan_hospital' => 'Permohonan Laporan HOSPITAL',
        'tarikh_permohonan_laporan_hospital' => 'Tarikh Permohonan HOSPITAL',
        'status_laporan_penuh_hospital' => 'Laporan Penuh HOSPITAL',
        'tarikh_laporan_penuh_hospital' => 'Tarikh Laporan Penuh HOSPITAL',
        'lain_lain_permohonan_laporan' => 'Lain-lain Permohonan Laporan',
        
        // BAHAGIAN 8: Status Fail
        'adakah_muka_surat_4_keputusan_kes_dicatat' => 'M/S 4 - Keputusan Kes Dicatat',
        'adakah_ks_kus_fail_selesai' => 'KS KUS/FAIL Selesai',
        'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'Fail LMM(T) Ada Keputusan',
        'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
        'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'Ulasan Pegawai Pemeriksa (Fail)',
        
        // Date columns
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Tarikh Dikemaskini',
    ];

    // Define custom columns for Komersil based on migration structure
    $komersilColumns = [

        // IPRS Standard Fields (8 columns for standardization)
        'iprs_no_kertas_siasatan' => 'IPRS No. Kertas Siasatan',
        'iprs_tarikh_ks' => 'IPRS Tarikh KS',
        'iprs_no_repot' => 'IPRS No. Repot',
        'iprs_jenis_jabatan_ks' => 'IPRS Jenis Jabatan KS',
        'iprs_pegawai_penyiasat' => 'IPRS Pegawai Penyiasat',
        'iprs_status_ks' => 'IPRS Status KS',
        'iprs_status_kes' => 'IPRS Status Kes',
        'iprs_seksyen' => 'IPRS Seksyen',

        // BAHAGIAN 1: Maklumat Asas
        'no_kertas_siasatan' => 'No. Kertas Siasatan',
        'no_repot_polis' => 'No. Repot Polis',
        'pegawai_penyiasat' => 'Pegawai Penyiasat',
        'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
        'seksyen' => 'Seksyen',
        
        // BAHAGIAN 2: Pemeriksaan JIPS
        'pegawai_pemeriksa' => 'Pegawai Pemeriksa',
        'tarikh_edaran_minit_ks_pertama' => 'Tarikh Edaran Minit KS Pertama (A)',
        'tarikh_edaran_minit_ks_kedua' => 'Tarikh Edaran Minit KS Kedua (B)',
        'lewat_edaran_status' => 'Sistem Calculate (B - A): KS Lewat Edaran 48 Jam',
        'tarikh_edaran_minit_ks_sebelum_akhir' => 'Tarikh Edaran Minit KS Sebelum Akhir (C)',
        'tarikh_edaran_minit_ks_akhir' => 'Tarikh Edaran Minit KS Akhir (D)',
        'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'Tarikh Semboyan Pemeriksaan JIPS ke Daerah (E)',
        'terbengkalai_status_dc' => 'Sistem Calculate (D - C): Terbengkalai Melebihi 3 Bulan',
        'baru_dikemaskini_status' => 'Sistem Calculate (E - D): Terbengkalai / Baru Dikemaskini',
        'terbengkalai_status_da' => 'Sistem Calculate (D - A): Terbengkalai Melebihi 3 Bulan',
        
        // BAHAGIAN 3: Arahan SIO & Ketua
        'arahan_minit_oleh_sio_status' => 'Arahan Minit Oleh SIO',
        'arahan_minit_oleh_sio_tarikh' => 'Tarikh Arahan Minit SIO',
        'arahan_minit_ketua_bahagian_status' => 'Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_bahagian_tarikh' => 'Tarikh Arahan Minit Ketua Bahagian',
        'arahan_minit_ketua_jabatan_status' => 'Arahan Minit Ketua Jabatan',
        'arahan_minit_ketua_jabatan_tarikh' => 'Tarikh Arahan Minit Ketua Jabatan',
        'arahan_minit_oleh_ya_tpr_status' => 'Arahan Minit Oleh YA TPR',
        'arahan_minit_oleh_ya_tpr_tarikh' => 'Tarikh Arahan Minit YA TPR',
        'keputusan_siasatan_oleh_ya_tpr' => 'Keputusan Siasatan Oleh YA TPR',
        'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'Arahan Tuduh YA TPR Diambil Tindakan',
        'ulasan_keputusan_siasatan_tpr' => 'Ulasan Keputusan Siasatan TPR',
        'ulasan_keseluruhan_pegawai_pemeriksa' => 'Ulasan Keseluruhan Pegawai Pemeriksa',
        
        // BAHAGIAN 4: Barang Kes
        'adakah_barang_kes_didaftarkan' => 'Barang Kes Didaftarkan',
        'no_daftar_barang_kes_am' => 'No. Daftar Barang Kes Am',
        'no_daftar_barang_kes_berharga' => 'No. Daftar Barang Kes Berharga',
        'no_daftar_barang_kes_kenderaan' => 'No. Daftar Barang Kes Kenderaan',
        'no_daftar_botol_spesimen_urin' => 'No. Daftar Botol Spesimen Urin',
        'jenis_barang_kes_am' => 'Jenis Barang Kes Am',
        'jenis_barang_kes_berharga' => 'Jenis Barang Kes Berharga',
        'jenis_barang_kes_kenderaan' => 'Jenis Barang Kes Kenderaan',
        'status_pergerakan_barang_kes' => 'Status Pergerakan Barang Kes',
        //'status_pergerakan_barang_kes_ujian_makmal' => 'Status Pergerakan Barang Kes Ujian Makmal',
        //'status_pergerakan_barang_kes_lain' => 'Status Pergerakan Barang Kes Lain',
        //'status_pergerakan_barang_kes_makmal' => 'Status Pergerakan Barang Kes Makmal',
        'status_barang_kes_selesai_siasatan' => 'Status Barang Kes Selesai Siasatan',
        //'status_barang_kes_selesai_siasatan_RM' => 'Status Barang Kes Selesai Siasatan RM',
        //'status_barang_kes_selesai_siasatan_lain' => 'Status Barang Kes Selesai Siasatan Lain',
        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'Kaedah Pelupusan Barang Kes',
        //'kaedah_pelupusan_lain' => 'Kaedah Pelupusan Lain',
        'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'Arahan Pelupusan ke Perbendaharaan',
        'resit_kew_38e_bagi_pelupusan' => 'Resit Kew.38e Pelupusan',
        'adakah_borang_serah_terima_pegawai_tangkapan' => 'Borang Serah Terima Pegawai Tangkapan',
        'adakah_borang_serah_terima_pemilik_saksi' => 'Borang Serah Terima Pemilik Saksi',
        'adakah_sijil_surat_kebenaran_ipd' => 'Sijil Surat Kebenaran IPD',
        'adakah_gambar_pelupusan' => 'Gambar Pelupusan',
        'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'Ulasan Pegawai Pemeriksa (Barang Kes)',
        
        // BAHAGIAN 5: Bukti & Rajah
        'status_id_siasatan_dikemaskini' => 'ID Siasatan Dikemaskini',
        'status_rajah_kasar_tempat_kejadian' => 'Rajah Kasar Tempat Kejadian',
        'status_gambar_tempat_kejadian' => 'Gambar Tempat Kejadian',
        'status_gambar_barang_kes_am' => 'Gambar Barang Kes Am',
        'status_gambar_barang_kes_berharga' => 'Gambar Barang Kes Berharga',
        'status_gambar_barang_kes_kenderaan' => 'Gambar Barang Kes Kenderaan',
        'status_gambar_barang_kes_darah' => 'Gambar Barang Kes Darah',
        'status_gambar_barang_kes_kontraban' => 'Gambar Barang Kes Kontraban',
        
        // BAHAGIAN 6: Laporan RJ & Semboyan
        'status_pem' => 'Status PEM',
        'status_rj2' => 'Status RJ2',
        'tarikh_rj2' => 'Tarikh RJ2',
        'status_rj2b' => 'Status RJ2B',
        'tarikh_rj2b' => 'Tarikh RJ2B',
        'status_rj9' => 'Status RJ9',
        'tarikh_rj9' => 'Tarikh RJ9',
        'status_rj99' => 'Status RJ99',
        'tarikh_rj99' => 'Tarikh RJ99',
        'status_rj10a' => 'Status RJ10A',
        'tarikh_rj10a' => 'Tarikh RJ10A',
        'status_rj10b' => 'Status RJ10B',
        'tarikh_rj10b' => 'Tarikh RJ10B',
        'lain_lain_rj_dikesan' => 'Lain-lain RJ Dikesan',
        'status_semboyan_pertama_wanted_person' => 'SEMBOYAN PEMAKLUMAN PERTAMA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_pertama_wanted_person' => 'Tarikh Semboyan Pertama',
        'status_semboyan_kedua_wanted_person' => 'SEMBOYAN PEMAKLUMAN KEDUA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_kedua_wanted_person' => 'Tarikh Semboyan Kedua',
        'status_semboyan_ketiga_wanted_person' => 'SEMBOYAN PEMAKLUMAN KETIGA WANTED PERSON KE DAERAH UNTUK KESAN / TANGKAP',
        'tarikh_semboyan_ketiga_wanted_person' => 'Tarikh Semboyan Ketiga',
        'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'Ulasan Pegawai Pemeriksa (Borang)',
        'status_penandaan_kelas_warna' => 'Penandaan Kelas Warna',
        
        // BAHAGIAN 6 (Saman): Missing fields from migration  
        'status_saman_pdrm_s_257' => 'Status Saman PDRM S.257',
        'no_saman_pdrm_s_257' => 'No Saman PDRM S.257',
        'status_saman_pdrm_s_167' => 'Status Saman PDRM S.167',
        'no_saman_pdrm_s_167' => 'No Saman PDRM S.167',
        
        // BAHAGIAN 7: Permohonan Laporan Agensi Luar
        'status_permohonan_E_FSA_1_oleh_IO_AIO' => 'Status Mohon E-FSA Bank 1',
        'nama_bank_permohonan_E_FSA_1' => 'Nama Bank Mohon E-FSA 1',
        'status_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'Status Laporan E-FSA Bank 1',
        'nama_bank_laporan_E_FSA_1_oleh_IO_AIO' => 'Nama Bank Laporan E-FSA 1',
        'tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO' => 'Tarikh Laporan E-FSA Bank 1',
        'status_permohonan_E_FSA_1_telco_oleh_IO_AIO' => 'Status Mohon E-FSA Telco 1',
        'nama_telco_permohonan_E_FSA_1_oleh_IO_AIO' => 'Nama Telco Mohon E-FSA 1',
        'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'Status Laporan E-FSA Telco 1',
        'nama_telco_laporan_E_FSA_1_oleh_IO_AIO' => 'Nama Telco Laporan E-FSA 1',
        'tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => 'Tarikh Laporan E-FSA Telco 1',
        'status_permohonan_laporan_puspakom' => 'Mohon Laporan Puspakom',
        'tarikh_permohonan_laporan_puspakom' => 'Tarikh Mohon Puspakom',
        'status_laporan_penuh_puspakom' => 'Laporan Penuh Puspakom',
        'tarikh_laporan_penuh_puspakom' => 'Tarikh Laporan Puspakom',
        //'status_permohonan_laporan_jkr' => 'Mohon Laporan JKR',
       // 'tarikh_permohonan_laporan_jkr' => 'Tarikh Mohon JKR',
       // 'status_laporan_penuh_jkr' => 'Laporan Penuh JKR',
       // 'tarikh_laporan_penuh_jkr' => 'Tarikh Laporan JKR',
        'status_permohonan_laporan_jpj' => 'Mohon Laporan JPJ',
        'tarikh_permohonan_laporan_jpj' => 'Tarikh Mohon JPJ',
        'status_laporan_penuh_jpj' => 'Laporan Penuh JPJ',
        'tarikh_laporan_penuh_jpj' => 'Tarikh Laporan JPJ',
        'status_permohonan_laporan_imigresen' => 'Mohon Laporan Imigresen',
        'tarikh_permohonan_laporan_imigresen' => 'Tarikh Mohon Imigresen',
        'status_laporan_penuh_imigresen' => 'Laporan Penuh Imigresen',
        'tarikh_laporan_penuh_imigresen' => 'Tarikh Laporan Imigresen',
        'status_permohonan_laporan_kastam' => 'Mohon Laporan Kastam',
        'tarikh_permohonan_laporan_kastam' => 'Tarikh Mohon Kastam',
        'status_laporan_penuh_kastam' => 'Laporan Penuh Kastam',
        'tarikh_laporan_penuh_kastam' => 'Tarikh Laporan Kastam',
        'status_permohonan_laporan_forensik_pdrm' => 'Mohon Laporan Forensik',
        'tarikh_permohonan_laporan_forensik_pdrm' => 'Tarikh Mohon Forensik',
        'status_laporan_penuh_forensik_pdrm' => 'Laporan Penuh Forensik',
        'tarikh_laporan_penuh_forensik_pdrm' => 'Tarikh Laporan Forensik',
        'jenis_barang_kes_forensik' => 'Jenis BK Forensik',
        'lain_lain_permohonan_laporan' => 'Lain-lain Permohonan Laporan',

        // BAHAGIAN 8: Status Fail
        'muka_surat_4_barang_kes_ditulis' => 'M/S 4 - Barang Kes Ditulis Bersama No Daftar',
        'muka_surat_4_dengan_arahan_tpr' => 'M/S 4 - Dengan Arahan TPR Untuk Pelupusan/Serahan',
        'muka_surat_4_keputusan_kes_dicatat' => 'M/S 4 - Keputusan Kes Dicatat',
        'fail_lmm_ada_keputusan_koroner' => 'Fail L.M.M Ada Keputusan Siasatan Oleh YA Koroner',
        'status_kus_fail' => 'Status KS di KUS/FAIL',
        'keputusan_akhir_mahkamah' => 'Keputusan Akhir Mahkamah',
        'ulasan_pegawai_pemeriksa_fail' => 'Ulasan Pegawai Pemeriksa (Fail)',

        // Date columns
        'created_at' => 'Tarikh Dicipta',
        'updated_at' => 'Tarikh Dikemaskini',
    ];
@endphp

<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.1/css/fixedColumns.dataTables.css">
    <style>
        table.dataTable th.dt-ordering-asc::after,
        table.dataTable th.dt-ordering-desc::after {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-left: 0.5em;
        }

        table.dataTable th.dt-ordering-asc::after {
            content: "\f0de";
        }

        table.dataTable th.dt-ordering-desc::after {
            content: "\f0dd";
        }

        .is-restoring-scroll {
            visibility: hidden;
        }

        .datatable-container-loading {
            min-height: 400px;
        }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-black-200 leading-tight">{{ __('Dashboard Projek: ') }} {{ $project->name }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-blue-100 underline">← {{ __('Kembali ke Senarai Projek') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success')) 
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {!! session('success') !!}
                </div> 
            @endif

            @if (session('error'))
                 <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ralat!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Project Details Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-1">
                    <div>
                        <h3 class="text-2xl dark:text-white font-semibold">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            @php
                                $date = \Carbon\Carbon::parse($project->project_date);
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
                                    5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
                                ];
                                $malayMonth = $months[$date->month];
                            @endphp
                            {{ $date->day }} {{ $malayMonth }} {{ $date->year }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-4">
                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-papers-modal')"><i class="fas fa-file-upload mr-2"></i> {{ __('muat naik') }}</x-primary-button>
                    
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'export-papers-modal')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-file-download mr-2"></i> {{ __('Eksport') }}
                            </button>

                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'destroy-papers-modal')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" title="Padam Kertas Siasatan">
                                <i class="fas fa-trash-alt mr-2"></i> Padam Kertas
                            </button>

                            <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500" title="{{ __('Edit Projek') }}"><i class="fas fa-edit fa-lg"></i></a>
                        </div>
                </div>
                @if($project->description)<p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mt-4 border-t pt-4">{{ $project->description }}</p>@endif
            </div>

{{-- FILE: resources/views/projects/show.blade.php (Part 3 of 5) --}}
            {{-- Unified Tabbed Interface --}}
<div x-data="{ activeTab: sessionStorage.getItem('activeProjectTab') || 'Jenayah' }" 
     x-init="
        // Initialize the DataTable for the starting tab (either from memory or the default).
        initDataTable(activeTab); 
        
        // Watch for when the activeTab changes.
        $watch('activeTab', value => {
            // 1. **Save the new tab's name to session storage.
            sessionStorage.setItem('activeProjectTab', value);

            // 2. Initialize the DataTable for the newly selected tab.
            initDataTable(value);
        });
     "
     class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- SINGLE Tab Header Navigation --}}
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                        @foreach($paperTypes as $key => $config)
                            <a href="#" @click.prevent="activeTab = '{{ $key }}'"
                               :class="{ 'border-indigo-500 text-indigo-600': activeTab === '{{ $key }}', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== '{{ $key }}' }"
                               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                {{ $config['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                {{-- Tab Content Panels --}}
                <div class="mt-6">
                    @foreach($paperTypes as $key => $config)
                        @php 
                            // Get the dashboard data for the current tab. This now works because keys match.
                            $data = $dashboardData[$key] ?? null;
                        @endphp
                        <div x-show="activeTab === '{{ $key }}'" x-cloak>
                            @if($data)
                                {{-- Dashboard Section (Charts & Summaries) is now INSIDE each tab --}}
                                <div class="mb-12">
                                    @php
                                        // Define which paper types use the 24-hour rule.
                                        $typesWith24HourRule = ['Jenayah', 'TrafikRule', 'OrangHilang', 'LaporanMatiMengejut'];

                                        // Determine the correct title dynamically based on the current paper type ($key).
                                        $lewatTitle = in_array($key, $typesWith24HourRule) 
                                            ? 'Edaran Kertas Siasatan Lewat (> 24 Jam)' 
                                            : 'Edaran Kertas Siasatan Lewat (> 48 Jam)';
                                    @endphp
                                    <x-dashboard-section :key="$key" :data="$data" :lewat-title="$lewatTitle" />
                                </div>
                                
                                <div class="my-8 border-t border-gray-200 dark:border-gray-700"></div>
                                <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Butiran Terperinci: {{ $config['title'] }}</h4>
                            @endif

                            {{-- DataTable Section --}}
                            <div class="overflow-auto">
                                <table id="{{ $key }}-datatable" class="w-full text-sm text-left" style="width:100%">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-20">
                                        <tr>
                                            <th class="px-4 py-3 sticky left-0 bg-gray-50 dark:bg-gray-700 z-30 border-r border-gray-200 dark:border-gray-600">Tindakan</th>
                                            <th class="px-4 py-3">No.</th>
                                            @if($key === 'Jenayah')
                                                    {{-- Use custom columns for Jenayah --}}
                                                    @foreach($jenayahColumns as $column => $label)
                                                        <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                    @endforeach
                                                @elseif($key === 'Narkotik')
                                                    {{-- Use custom columns for Narkotik --}}
                                                    @foreach($narkotikColumns as $column => $label)
                                                        <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                    @endforeach
                                                @elseif($key === 'Komersil')
                                                {{-- Use custom columns for Komersil --}}
                                                @foreach($komersilColumns as $column => $label)
                                                    <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                @endforeach
                                            @elseif($key === 'OrangHilang')
                                                {{-- Use custom columns for OrangHilang --}}
                                                @foreach($orangHilangColumns as $column => $label)
                                                    <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                @endforeach
                                            @elseif($key === 'LaporanMatiMengejut')
                                                {{-- Use custom columns for LaporanMatiMengejut --}}
                                                @foreach($laporanMatiMengejutColumns as $column => $label)
                                                    <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                @endforeach
                                            @elseif($key === 'TrafikSeksyen')
                                                {{-- Use custom columns for TrafikSeksyen --}}
                                                @foreach($trafikSeksyenColumns as $column => $label)
                                                    <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                @endforeach
                                            @elseif($key === 'TrafikRule')
                                                {{-- Use custom columns for TrafikRule --}}
                                                @foreach($trafikRuleColumns as $column => $label)
                                                    <th scope="col" class="px-4 py-3">{{ $label }}</th>
                                                @endforeach
                                            @else
                                                {{-- Use the table's actual columns for other models --}}
                                                @php $columns = array_diff(Schema::getColumnListing($config['model']->getTable()), $ignoreColumns); @endphp
                                                @foreach($columns as $column)
                                                    <th scope="col" class="px-4 py-3">{{ Str::of($column)->replace('_', ' ')->title() }}</th>
                                                @endforeach
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


{{-- FILE: resources/views/projects/show.blade.php (Part 4 of 5) --}}
{{-- FILE: resources/views/projects/show.blade.php --}}

{{-- Import Modal --}}
<x-modal name="import-papers-modal" :show="$errors->has('excel_file') || $errors->has('excel_errors')" focusable>
    <form action="{{ route('projects.import', $project) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900 dark:text-black-100">Muat Naik Kertas Siasatan ke: {{ $project->name }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-grey-600">Sila pilih kategori kertas dan muat naik fail Excel yang sepadan.</p>

        {{-- Error Display --}}
        @if ($errors->has('excel_file') || $errors->has('excel_errors'))
            <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p class="font-bold">{{ $errors->first('excel_file') }}</p>
                @if ($errors->has('excel_errors'))
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->get('excel_errors') as $errorMessage)
                            <li>{{ $errorMessage }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        {{-- Paper Type Dropdown --}}
        <div class="mt-6">
            <label for="paper_type_modal" class="block text-sm font-medium text-gray-700 dark:text-black-200">Kategori Kertas</label>
            <select name="paper_type" id="paper_type_modal" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                <option value="Jenayah" @if(old('paper_type') == 'Jenayah') selected @endif>JSJ (Jenayah)</option>
                <option value="Narkotik" @if(old('paper_type') == 'Narkotik') selected @endif>JSJN (Narkotik)</option>
                <option value="Komersil" @if(old('paper_type') == 'Komersil') selected @endif>JSJK (Komersil)</option>
                <option value="TrafikSeksyen" @if(old('paper_type') == 'TrafikSeksyen') selected @endif>JSPT (APJ 1987 - AKTA 333)</option>
                <option value="TrafikRule" @if(old('paper_type') == 'TrafikRule') selected @endif>JSPT (KKLJ 1969 - LN 166/1959)</option>
                <option value="OrangHilang" @if(old('paper_type') == 'OrangHilang') selected @endif>JP (Orang Hilang)</option>
                <option value="LaporanMatiMengejut" @if(old('paper_type') == 'LaporanMatiMengejut') selected @endif>JP (Mati Mengejut)</option>
            </select>
        </div>

        {{-- File Input --}}
        <div class="mt-6">
            <label for="excel_file_modal" class="block text-sm font-medium text-gray-700 dark:text-black-300">Pilih Fail Excel</label>
            <div class="mt-1 flex items-center">
                <input type="file" name="excel_file" id="excel_file_modal" required accept=".xlsx,.xls,.csv" class="hidden">
                <button type="button" id="file-select-btn" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 opacity-50 cursor-not-allowed">
                    <i class="fas fa-file-upload mr-2"></i>Pilih Fail
                </button>
                <span id="file-name" class="ml-3 text-sm text-gray-500">Sila pilih kategori kertas dahulu</span>
            </div>
         <!--   <p class="mt-2 text-xs text-blue-600">
                <a id="template-download-link" href="#" download class="underline hover:text-blue-800" style="display: none;">
                    Klik di sini untuk muat turun templat <span id="template-type"></span>
                </a>
                <span id="template-placeholder" class="text-gray-500">Sila pilih kategori kertas untuk muat turun templat</span>
            </p>    !-->
        </div>

        {{-- *** NEW DYNAMIC INFO BOX *** --}}
     <!--   <div id="column-info" class="mt-4 p-3 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-800 text-sm rounded" style="display: none;">
            <p class="font-bold">Pastikan fail anda mempunyai lajur berikut:</p>
            <code id="column-list" class="block mt-2 text-xs break-words"></code>
        </div> !-->
        {{-- *** END OF NEW DYNAMIC INFO BOX *** --}}

        {{-- Action Buttons --}}
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
            <x-primary-button id="import-submit-btn" class="ms-3" disabled>{{ __('Muat Naik Fail') }}</x-primary-button>
        </div>

        {{-- JavaScript for Modal Interactivity --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paperTypeSelect = document.getElementById('paper_type_modal');
                const fileInput = document.getElementById('excel_file_modal');
                const fileNameSpan = document.getElementById('file-name');
                const submitBtn = document.getElementById('import-submit-btn');
                const fileSelectBtn = document.getElementById('file-select-btn');
                
                // Get the new info box elements
                const columnInfoDiv = document.getElementById('column-info');
                const columnListEl = document.getElementById('column-list');
                
                // Get template download elements
                const templateDownloadLink = document.getElementById('template-download-link');
                const templateTypeSpan = document.getElementById('template-type');
                const templatePlaceholder = document.getElementById('template-placeholder');

                // Store the required columns for each paper type
                const columnData = {
                    'Jenayah': 'no_kertas_siasatan, no_repot_polis, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen',
                    'Narkotik': 'no_kertas_siasatan, no_repot_polis, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen',
                    'Komersil': 'no_kertas_siasatan, no_repot_polis, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen',
                    'TrafikSeksyen': 'no_kertas_siasatan, no_repot_polis, no_lmm_t, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen',
                    'TrafikRule': 'no_kertas_siasatan, no_fail_lmm_t, no_repot_polis, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen',
                    'OrangHilang': 'no_kertas_siasatan, no_repot_polis, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen',
                    'LaporanMatiMengejut': 'no_kertas_siasatan, no_fail_lmm_sdr, no_repot_polis, pegawai_penyiasat, tarikh_laporan_polis_dibuka, seksyen'
                };

                // Map paper types to their corresponding template files
                const templateFiles = {
                    'Jenayah': 'templat_jsj.csv',
                    'Narkotik': 'templat_jsjn.csv',
                    'Komersil': 'templat_jsjk.csv',
                    'TrafikSeksyen': 'templat_trafikseksyen.csv',
                    'TrafikRule': 'templat_trafikrule.csv',
                    'OrangHilang': 'templat_oh.csv',
                    'LaporanMatiMengejut': 'templat_lmm.csv'
                };

                // Map paper types to their display names
                const paperTypeNames = {
                    'Jenayah': 'JSJ (Jenayah)',
                    'Narkotik': 'JSJN (Narkotik)',
                    'Komersil': 'JSJK (Komersil)',
                    'TrafikSeksyen': 'JSPT (APJ 1987 - AKTA 333)',
                    'TrafikRule': 'JSPT (KKLJ 1969 - LN 166/1959)',
                    'OrangHilang': 'JP (Orang Hilang)',
                    'LaporanMatiMengejut': 'JP (Mati Mengejut)'
                };

                // Function to show/hide and update the column info box
                function updateColumnInfo() {
                    const selectedType = paperTypeSelect.value;
                    const requiredColumns = columnData[selectedType];

                    if (requiredColumns) {
                        columnListEl.textContent = requiredColumns;
                        columnInfoDiv.style.display = 'block';
                    } else {
                        columnInfoDiv.style.display = 'none';
                    }
                }

                // Function to update template download link
                function updateTemplateDownload() {
                    const selectedType = paperTypeSelect.value;
                    const templateFile = templateFiles[selectedType];
                    const typeName = paperTypeNames[selectedType];

                    if (templateFile && typeName) {
                        // Construct the URL properly using Laravel route
                        const baseUrl = "{{ route('templates.download', ['filename' => 'PLACEHOLDER']) }}";
                        templateDownloadLink.href = baseUrl.replace('PLACEHOLDER', templateFile);
                        templateTypeSpan.textContent = typeName;
                        templateDownloadLink.style.display = 'inline';
                        templatePlaceholder.style.display = 'none';
                    } else {
                        templateDownloadLink.style.display = 'none';
                        templatePlaceholder.style.display = 'inline';
                    }
                }

                function updateFileSelectButton() {
                    const paperTypeSelected = paperTypeSelect.value !== '';
                    if (paperTypeSelected) {
                        fileSelectBtn.disabled = false;
                        fileSelectBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        fileNameSpan.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : 'Tiada fail dipilih';
                        fileSelectBtn.onclick = () => fileInput.click();
                    } else {
                        fileSelectBtn.disabled = true;
                        fileSelectBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        fileNameSpan.textContent = 'Sila pilih kategori kertas dahulu';
                        fileInput.value = '';
                        fileSelectBtn.onclick = null;
                    }
                }

                function checkFormValidity() {
                    const paperTypeSelected = paperTypeSelect.value !== '';
                    const fileSelected = fileInput.files.length > 0;
                    submitBtn.disabled = !(paperTypeSelected && fileSelected);
                }

                paperTypeSelect.addEventListener('change', function() {
                    updateFileSelectButton();
                    updateColumnInfo(); // <-- Call the new function
                    updateTemplateDownload(); // <-- Call the template download function
                    checkFormValidity();
                });
                
                fileInput.addEventListener('change', function(e) {
                    fileNameSpan.textContent = e.target.files.length > 0 ? e.target.files[0].name : 'Tiada fail dipilih';
                    checkFormValidity();
                });

                // Initial checks when the modal is opened
                updateFileSelectButton();
                updateColumnInfo();
                updateTemplateDownload(); // <-- Call the template download function initially
                checkFormValidity();
            });
        </script>
    </form>
</x-modal>

    <!-- Duplicate Confirmation Modal -->
    @if(session('duplicates_found'))
    <x-modal name="duplicate-confirmation-modal" :show="true" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-red-900">Rekod Pendua Dijumpai</h2>
            <p class="mt-1 text-sm text-red-600">Sistem telah mengesan rekod pendua dalam fail yang dimuat naik.</p>

            <!-- Import Summary -->
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-sm font-medium text-blue-900 mb-2">Ringkasan Muat Naik:</h3>
                <div class="grid grid-cols-3 gap-4 text-xs">
                    <div class="text-center">
                        <div class="text-lg font-bold text-green-600">{{ session('new_records_count', 0) }}</div>
                        <div class="text-green-700">Rekod Baharu</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-red-600">{{ count(session('duplicate_records', [])) }}</div>
                        <div class="text-red-700">Rekod Pendua</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-blue-600">{{ session('total_records_count', 0) }}</div>
                        <div class="text-blue-700">Jumlah Rekod</div>
                    </div>
                </div>
            </div>

            <!-- Duplicate Records List -->
            <div class="mt-4 max-h-64 overflow-y-auto">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Senarai Rekod Pendua:</h3>
                <ul class="space-y-1 text-xs text-gray-700">
                    @foreach(session('duplicate_records', []) as $duplicate)
                        <li class="p-2 bg-red-50 rounded border-l-2 border-red-200">
                            <strong>Baris {{ $duplicate['row_number'] }}:</strong> 
                            {{ $duplicate['unique_column'] }} "{{ $duplicate['unique_value'] }}"
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <form method="POST" action="{{ route('projects.import', $project) }}" class="inline">
                    @csrf
                    <input type="hidden" name="temp_file_path" value="{{ session('temp_file_path') }}">
                    <input type="hidden" name="paper_type" value="{{ session('paper_type') }}">
                    <input type="hidden" name="confirm_overwrite" value="0">
                    <x-secondary-button type="submit">
                        {{ __('Batal') }}
                    </x-secondary-button>
                </form>

                <form method="POST" action="{{ route('projects.import', $project) }}" class="inline">
                    @csrf
                    <input type="hidden" name="temp_file_path" value="{{ session('temp_file_path') }}">
                    <input type="hidden" name="paper_type" value="{{ session('paper_type') }}">
                    <input type="hidden" name="confirm_overwrite" value="1">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Timpa Data Sedia Ada') }}
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
    @endif

    <!-- Export Modal -->
    <x-modal name="export-papers-modal" focusable>
        <form action="{{ route('projects.export_papers', $project) }}" method="GET" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">Eksport Kertas Siasatan dari: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sila pilih kategori kertas yang ingin dieksport ke fail CSV.</p>
            <div class="mt-6">
                <label for="paper_type_export" class="block text-sm font-medium text-gray-700 dark:text-gray-700">Kategori Kertas</label>
                <select name="paper_type" id="paper_type_export" required class="mt-1 block w-full form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>-- Sila Pilih Kategori --</option>
                    <option value="Jenayah">JSJ (Jenayah)</option>
                    <option value="Narkotik">JSJN (Narkotik)</option>
                    <option value="Komersil">JSJK (Komersil)</option>
                    <option value="TrafikSeksyen">JSPT (APJ 1987 - AKTA 333)</option>
                    <option value="TrafikRule">JSPT (KKLJ 1969 - LN 166/1959)</option>
                    <option value="OrangHilang">JP (Orang Hilang)</option>
                    <option value="LaporanMatiMengejut">JP (Mati Mengejut)</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-3">
                    Eksport ke CSV
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Destroy Papers Modal -->
    <x-modal name="destroy-papers-modal" :max-width="'2xl'" focusable>
        <div class="p-6" x-data="destroyPapersModal()" @open-modal.window="if ($event.detail === 'destroy-papers-modal') loadPapers()">
            <h2 class="text-lg font-medium text-gray-900">Padam Kertas Siasatan dari: {{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-600">Sila pilih jenis kertas siasatan yang ingin dipadam. Tindakan ini tidak boleh diundur.</p>
            
            <div class="mt-6" x-show="loading">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-xl text-gray-500"></i>
                    <p class="mt-2 text-sm text-gray-500">Memuat kertas siasatan...</p>
                </div>
            </div>

            <div class="mt-6" x-show="!loading && Object.keys(paperTypes).length === 0">
                <div class="text-center py-8">
                    <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Tiada kertas siasatan dalam projek ini.</p>
                </div>
            </div>

            <div class="mt-6" x-show="!loading && Object.keys(paperTypes).length > 0">
                <form @submit.prevent="submitDestroy">
                    <div class="mb-4 flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" @click="toggleSelectAll" x-bind:checked="allSelected" class="form-checkbox h-5 w-5 text-red-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua Jenis</span>
                        </label>
                        <span class="text-sm text-gray-500" x-text="`${selectedTypes.length} jenis dipilih`"></span>
                    </div>

                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                        <template x-for="(count, paperType) in paperTypes" :key="paperType">
                            <div class="p-4 hover:bg-gray-50">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           :value="paperType"
                                           @change="togglePaperType(paperType)"
                                           x-bind:checked="selectedTypes.includes(paperType)"
                                           class="form-checkbox h-5 w-5 text-red-600">
                                    <div class="ml-3 flex-1">
                                        <span class="text-sm font-medium text-gray-900" x-text="getDisplayName(paperType)"></span>
                                        <span class="ml-2 text-sm text-gray-500" x-text="`(${count} kertas)`"></span>
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                        <button type="submit" 
                                x-bind:disabled="selectedTypes.length === 0" 
                                x-bind:class="selectedTypes.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-3">
                            <i class="fas fa-trash-alt mr-2"></i>
                            <span x-text="selectedTypes.length === 0 ? 'Padam Kertas' : `Padam ${selectedTypes.length} Jenis`"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>
{{-- FILE: resources/views/projects/show.blade.php (Part 5 of 5) --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Alpine.js component for destroy papers modal
    function destroyPapersModal() {
        return {
            loading: true,
            paperTypes: {},
            selectedTypes: [],
            
            // Mapping for display names
            displayNames: {
                'Jenayah': 'JSJ (Jenayah)',
                'Narkotik': 'JSJN (Narkotik)', 
                'Komersil': 'JSJK (Komersil)',
                'TrafikSeksyen': 'JSPT (APJ 1987 - AKTA 333)',
                'TrafikRule': 'JSPT (KKLJ 1969 - LN 166/1959)',
                'OrangHilang': 'JP (Orang Hilang)',
                'LaporanMatiMengejut': 'JP (Mati Mengejut)'
            },
            
            get allSelected() {
                const totalTypes = Object.keys(this.paperTypes).length;
                return totalTypes > 0 && this.selectedTypes.length === totalTypes;
            },

            getDisplayName(paperType) {
                return this.displayNames[paperType] || paperType;
            },

            async loadPapers() {
                this.loading = true;
                this.selectedTypes = [];
                
                try {
                    const response = await fetch('{{ route("projects.get_papers_for_destroy", $project) }}');
                    const data = await response.json();
                    
                    this.paperTypes = data;
                } catch (error) {
                    console.error('Error loading papers:', error);
                    alert('Ralat memuat kertas siasatan. Sila cuba lagi.');
                } finally {
                    this.loading = false;
                }
            },

            toggleSelectAll() {
                if (this.allSelected) {
                    this.selectedTypes = [];
                } else {
                    this.selectedTypes = Object.keys(this.paperTypes);
                }
            },

            togglePaperType(paperType) {
                const index = this.selectedTypes.indexOf(paperType);
                
                if (index > -1) {
                    this.selectedTypes.splice(index, 1);
                } else {
                    this.selectedTypes.push(paperType);
                }
            },

            async submitDestroy() {
                if (this.selectedTypes.length === 0) {
                    return;
                }

                const totalPapers = this.selectedTypes.reduce((total, type) => {
                    return total + this.paperTypes[type];
                }, 0);

                const confirmed = confirm(`Anda pasti ingin memadam ${this.selectedTypes.length} jenis kertas siasatan (${totalPapers} kertas)? Tindakan ini tidak boleh diundur.`);
                
                if (!confirmed) {
                    return;
                }

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    this.selectedTypes.forEach((type, index) => {
                        formData.append(`selected_types[${index}]`, type);
                    });

                    const response = await fetch('{{ route("projects.destroy_selected_papers", $project) }}', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        // Close modal and reload page
                        this.$dispatch('close');
                        window.location.reload();
                    } else {
                        alert('Ralat memadam kertas siasatan. Sila cuba lagi.');
                    }
                } catch (error) {
                    console.error('Error deleting papers:', error);
                    alert('Ralat memadam kertas siasatan. Sila cuba lagi.');
                }
            }
        }
    }

    // Global variable to keep track of initialized DataTables
    const initializedTables = {};

    // Function to initialize a DataTable for a given tabName (e.g., 'Jenayah')
    function initDataTable(tabName) {
        // If the DataTable for this tab is already initialized, simply return.
        if (initializedTables[tabName]) {
            return;
        }

        const tableId = `#${tabName}-datatable`;

        if (!$(tableId).length) {
            console.warn(`DataTable element not found for tab: ${tabName} with ID: ${tableId}`);
            return;
        }

        const panel = $(tableId).closest('.overflow-auto');
        if (panel.length) {
            panel.addClass('datatable-container-loading dark:text-white');
        }

    @foreach($paperTypes as $key => $config)
        if (tabName === '{{ $key }}') {
            @if($key === 'Jenayah')
                @php
                    // This PHP block defines the columns for DataTables JavaScript
                    $dtColumns = [
                        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                    ];
                    
                    // Columns that need a special combined render function
                    $combinedRenderFields = [
                        'status_pergerakan_barang_kes',
                        'status_barang_kes_selesai_siasatan',
                        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan'
                    ];

                    // Fields that are already formatted by controller (no need for frontend processing)
                    // keputusan_akhir_mahkamah is already processed by formatArrayField in controller

                    foreach($jenayahColumns as $columnKey => $label) {
                        $columnConfig = [
                            'data' => $columnKey,
                            'name' => $columnKey,
                            'title' => $label,
                            'defaultContent' => '-',
                            'orderable' => true,
                            'searchable' => true
                        ];
                        
                        // Add a placeholder for columns that need the combined renderer
                        if (in_array($columnKey, $combinedRenderFields)) {
                            $columnConfig['render'] = '%%COMBINED_RENDER%%';
                        }
                        
                        $dtColumns[] = $columnConfig;
                    }
                @endphp

                let dtColumnsConfig = @json($dtColumns);

                // Define the combined render function in JavaScript
                const combinedRenderFunction = function(data, type, row, meta) {
                    if (!data) return '-';

                    let details = '';
                    const colName = meta.settings.aoColumns[meta.col].name;

                    if (colName === 'status_pergerakan_barang_kes') {
                        if (data === 'Ujian Makmal' && row.status_pergerakan_barang_kes_makmal) {
                            details = `: ${row.status_pergerakan_barang_kes_makmal}`;
                        } else if (data === 'Lain-Lain' && row.status_pergerakan_barang_kes_lain) {
                            details = `: ${row.status_pergerakan_barang_kes_lain}`;
                        }
                    } 
                    else if (colName === 'status_barang_kes_selesai_siasatan') {
                        if (data === 'Dilupuskan ke Perbendaharaan' && row.status_barang_kes_selesai_siasatan_RM) {
                            details = ` (RM ${row.status_barang_kes_selesai_siasatan_RM})`;
                        } else if (data === 'Lain-Lain' && row.status_barang_kes_selesai_siasatan_lain) {
                            details = `: ${row.status_barang_kes_selesai_siasatan_lain}`;
                        }
                    }
                    else if (colName === 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan') {
                        if (data === 'Lain-Lain' && row.kaedah_pelupusan_barang_kes_lain) {
                            details = `: ${row.kaedah_pelupusan_barang_kes_lain}`;
                        }
                    }
                    
                    return data + details;
                };

                // Loop through the config and replace the placeholders
                dtColumnsConfig.forEach(function(column) {
                    if (column.render === '%%COMBINED_RENDER%%') {
                        column.render = combinedRenderFunction;
                    }
                });

                $(tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route($config['route'], $project->id) }}",
                        type: "POST",
                        data: { _token: '{{ csrf_token() }}' }
                    },
                    columns: dtColumnsConfig, // Use the processed configuration
                    order: [[2, 'asc']], 
                    columnDefs: [{
                        targets: 0,
                        className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                    }],
                    fixedColumns: { left: 1 },
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tunjukkan _MENU_ entri",
                        info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                        infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                        emptyTable: "Tiada data tersedia dalam jadual"
                    },
                    "drawCallback": function( settings ) {
                        if (panel.length) {
                            panel.removeClass('datatable-container-loading');
                        }
                    },
                                    // Logic to read URL and apply search filter on initialization
                "initComplete": function(settings, json) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const statusFilter = urlParams.get('status');

                    if (statusFilter === 'terbengkalai') {
                        // The keyword to search for across all columns
                        this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
                    }
                }
                });
                initializedTables[tabName] = true;
        @elseif($key === 'Narkotik')
            {{-- Use custom columns for Narkotik --}}
            @php
                $dtColumns = [
                    ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                ];
                
                // List of lain_lain fields that need special rendering
                $lainLainFields = [
                    'lain_lain_rj_dikesan',
                    'lain_lain_permohonan_laporan'
                ];
                
                // Fields that need combined render function (for Ujian Makmal and Lain-Lain conditions)
                $combinedRenderFields = [
                    'status_pergerakan_barang_kes',
                    'status_barang_kes_selesai_siasatan',
                    'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan'
                ];
                
                // List of JSON fields that need special rendering
                $jsonFields = [
                    'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                    'resit_kew38e_pelupusan_wang_tunai',
                    'adakah_borang_serah_terima_pegawai_tangkapan',
                    'adakah_borang_serah_terima_pemilik_saksi',
                    'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan'
                ];
                
                foreach($narkotikColumns as $column => $label) {
                    $columnConfig = [
                        'data' => $column,
                        'name' => $column,
                        'title' => $label,
                        'defaultContent' => '-',
                        'orderable' => true,
                        'searchable' => true
                    ];
                    
                    // Add custom render function for lain_lain fields
                    if (in_array($column, $lainLainFields)) {
                        $columnConfig['render'] = '%%LAIN_LAIN_RENDER%%';
                    }
                    // Add custom render function for combined fields  
                    elseif (in_array($column, $combinedRenderFields)) {
                        $columnConfig['render'] = '%%COMBINED_RENDER%%';
                    }
                    // Add custom render function for JSON fields
                    elseif (in_array($column, $jsonFields)) {
                        $columnConfig['render'] = '%%JSON_RENDER%%';
                        $columnConfig['orderable'] = false;
                        $columnConfig['searchable'] = false;
                    }
                    
                    $dtColumns[] = $columnConfig;
                }
            @endphp

            // Step 1: Get the column configuration from PHP
            let dtColumnsConfig = @json($dtColumns);

            // Step 2: Define the render functions in JavaScript
            const lainLainRenderFunction = function(data, type, row) {
                if (data === null || data === undefined || data === '' || data === '-') {
                    return '-';
                }
                // For lain_lain fields, show "Lain-lain ; [actual text]"
                return 'Lain-lain ; ' + data;
            };

            const jsonRenderFunction = function(data, type, row) {
                if (data === null || data === undefined) return "-";
                let parsedData = data;
                // Check if data is a string that looks like a JSON array
                if (typeof data === "string" && data.startsWith('[') && data.endsWith(']')) {
                    try {
                        parsedData = JSON.parse(data);
                    } catch (e) {
                        return data; // Return original string if it's not valid JSON
                    }
                }
                // Check if we now have a valid array
                if (Array.isArray(parsedData)) {
                    return parsedData.length > 0 ? parsedData.join(", ") : "-";
                }
                // If not an array or parsable string, return it as is
                return parsedData;
            };

            // Combined render function for Narkotik
            const combinedRenderFunction = function(data, type, row, meta) {
                if (!data || data === '-') return '-';

                let details = '';
                const colName = meta.settings.aoColumns[meta.col].name;

                // Logic for 'status_pergerakan_barang_kes'
                if (colName === 'status_pergerakan_barang_kes') {
                    // CHECK FOR UJIAN MAKMAL (this field is available in DataTable)
                    if (data === 'Ujian Makmal' && row.status_pergerakan_barang_kes_makmal) {
                        details = ` : ${row.status_pergerakan_barang_kes_makmal}`;
                    } 
                    // Note: status_pergerakan_barang_kes_lain is not included in DataTable columns for Narkotik
                } 
                // Logic for 'status_barang_kes_selesai_siasatan'
                else if (colName === 'status_barang_kes_selesai_siasatan') {
                    // CHECK FOR DILUPUSKAN KE PERBENDAHARAAN with RM amount
                    if (data === 'Dilupuskan ke Perbendaharaan' && row.status_barang_kes_selesai_siasatan_RM) {
                        details = ` (RM ${row.status_barang_kes_selesai_siasatan_RM})`;
                    } 
                    // Note: status_barang_kes_selesai_siasatan_lain is not included in DataTable columns for Narkotik
                }
                // Logic for 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan'
                else if (colName === 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan') {
                    if (data === 'Lain-Lain' && row.kaedah_pelupusan_barang_kes_lain) {
                        details = ` : ${row.kaedah_pelupusan_barang_kes_lain}`;
                    }
                }
                
                return data + details;
            };

            // Step 3: Loop through the config and replace the placeholders
            dtColumnsConfig.forEach(function(column) {
                if (column.render === '%%LAIN_LAIN_RENDER%%') {
                    column.render = lainLainRenderFunction;
                } else if (column.render === '%%JSON_RENDER%%') {
                    column.render = jsonRenderFunction;
                } else if (column.render === '%%COMBINED_RENDER%%') {
                    column.render = combinedRenderFunction;
                }
            });

            // Step 4: Initialize the DataTable with the corrected configuration
            $(tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route($config['route'], $project->id) }}",
                    type: "POST",
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: dtColumnsConfig, // Use the processed JavaScript variable
                order: [[2, 'desc']],
                columnDefs: [{
                    targets: 0,
                    className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                }],
                fixedColumns: { left: 1 },
                language: {
                    search: "Cari:",
                    lengthMenu: "Tunjukkan _MENU_ entri",
                    info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                    infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                    emptyTable: "Tiada data tersedia dalam jadual"
                },
                "drawCallback": function( settings ) {
                    if (panel.length) {
                        panel.removeClass('datatable-container-loading');
                    }
                },
                                // Logic to read URL and apply search filter on initialization
                "initComplete": function(settings, json) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const statusFilter = urlParams.get('status');

                    if (statusFilter === 'terbengkalai') {
                        // The keyword to search for across all columns
                        this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
                    }
                }
            });
            initializedTables[tabName] = true;
        @elseif($key === 'OrangHilang')
            {{-- Use custom columns for OrangHilang --}}
            @php
                $dtColumns = [
                    ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                ];
                
                // List of lain_lain fields that need special rendering
                $lainLainFields = [
                    'alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah',
                    'alasan_orang_hilang_dijumpai_mati_mengejut_jenayah'
                ];
                
                foreach($orangHilangColumns as $column => $label) {
                    $columnConfig = [
                        'data' => $column,
                        'name' => $column,
                        'title' => $label,
                        'defaultContent' => '-',
                        'orderable' => true,
                        'searchable' => true
                    ];
                    
                    // Add custom render function for lain_lain fields
                    if (in_array($column, $lainLainFields)) {
                        $columnConfig['render'] = '%%LAIN_LAIN_RENDER%%';
                    }
                    
                    $dtColumns[] = $columnConfig;
                }
            @endphp

            // Step 1: Get the column configuration from PHP
            let dtColumnsConfig = @json($dtColumns);

            // Step 2: Define the lain_lain render function in JavaScript
            const lainLainRenderFunction = function(data, type, row) {
                if (data === null || data === undefined || data === '' || data === '-') {
                    return '-';
                }
                // For lain_lain fields, show "Lain-lain ; [actual text]"
                return 'Lain-lain ; ' + data;
            };

            // Step 3: Loop through the config and replace the placeholder
            dtColumnsConfig.forEach(function(column) {
                if (column.render === '%%LAIN_LAIN_RENDER%%') {
                    column.render = lainLainRenderFunction;
                }
            });

            // Step 4: Initialize the DataTable with the corrected configuration
            $(tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route($config['route'], $project->id) }}",
                    type: "POST",
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: dtColumnsConfig, // Use the processed JavaScript variable
                order: [[2, 'asc']],
                columnDefs: [{
                    targets: 0,
                    className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                }],
                fixedColumns: { left: 1 },
                language: {
                    search: "Cari:",
                    lengthMenu: "Tunjukkan _MENU_ entri",
                    info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                    infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                    emptyTable: "Tiada data tersedia dalam jadual"
                },
                "drawCallback": function( settings ) {
                    if (panel.length) {
                        panel.removeClass('datatable-container-loading');
                    }
                },
                                // Logic to read URL and apply search filter on initialization
                "initComplete": function(settings, json) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const statusFilter = urlParams.get('status');

                    if (statusFilter === 'terbengkalai') {
                        // The keyword to search for across all columns
                        this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
                    }
                }
            });
            initializedTables[tabName] = true;

{{-- FILE: resources/views/projects/show.blade.php (Part 5 of 5) --}}

@elseif($key === 'Komersil')
                @php
                    $dtColumns = [
                        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                    ];
                    
                    $combinedRenderFields = [
                        'status_pergerakan_barang_kes',
                        'status_barang_kes_selesai_siasatan',
                        'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan'
                    ];
                    $jsonFields = [
                        // Fields handled by formatArrayField in controller should not be processed by jsonRenderFunction
                        // Removed: 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', 'status_pem', 'keputusan_akhir_mahkamah'
                    ];
                    
                    foreach($komersilColumns as $column => $label) {
                        $columnConfig = [
                            'data' => $column, 'name' => $column, 'title' => $label,
                            'defaultContent' => '-', 'orderable' => true, 'searchable' => true
                        ];
                        
                        if (in_array($column, $combinedRenderFields)) {
                            $columnConfig['render'] = '%%COMBINED_RENDER%%';
                        } elseif (in_array($column, $jsonFields)) {
                            $columnConfig['render'] = '%%JSON_RENDER%%';
                            $columnConfig['orderable'] = false;
                            $columnConfig['searchable'] = false;
                        }
                        
                        $dtColumns[] = $columnConfig;
                    }
                @endphp

                let dtColumnsConfig = @json($dtColumns);

                const jsonRenderFunction = function(data, type, row) {
                    if (data === null || data === undefined) return "-";
                    let parsedData = data;
                    if (typeof data === "string" && data.startsWith('[') && data.endsWith(']')) {
                        try {
                            parsedData = JSON.parse(data);
                        } catch (e) { return data; }
                    }
                    if (Array.isArray(parsedData)) {
                        return parsedData.length > 0 ? parsedData.join(", ") : "-";
                    }
                    return parsedData;
                };

                // *** render function to use exact column names from migration ***
                const combinedRenderFunction = function(data, type, row, meta) {
                    if (!data || data === '-') return '-';

                    let details = '';
                    const colName = meta.settings.aoColumns[meta.col].name;

                    if (colName === 'status_pergerakan_barang_kes') {
                        if (data === 'Ujian Makmal' && row.status_pergerakan_barang_kes_makmal) {
                            details = `: ${row.status_pergerakan_barang_kes_makmal}`;
                        } else if (data === 'Lain-Lain' && row.status_pergerakan_barang_kes_lain) {
                            details = `: ${row.status_pergerakan_barang_kes_lain}`;
                        }
                    } 
                    else if (colName === 'status_barang_kes_selesai_siasatan') {
                        if (data === 'Dilupuskan ke Perbendaharaan' && row.status_barang_kes_selesai_siasatan_RM) {
                            details = ` (RM ${row.status_barang_kes_selesai_siasatan_RM})`;
                        } else if (data === 'Lain-Lain' && row.status_barang_kes_selesai_siasatan_lain) {
                            details = `: ${row.status_barang_kes_selesai_siasatan_lain}`;
                        }
                    }
                    else if (colName === 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan') {
                        if (data === 'Lain-Lain' && row.kaedah_pelupusan_lain) {
                            details = `: ${row.kaedah_pelupusan_lain}`;
                        }
                    }
                    
                    return data + details;
                };

                dtColumnsConfig.forEach(function(column) {
                    if (column.render === '%%JSON_RENDER%%') {
                        column.render = jsonRenderFunction;
                    } else if (column.render === '%%COMBINED_RENDER%%') {
                        column.render = combinedRenderFunction;
                    }
                });

    // Step 4: Initialize the DataTable with the corrected configuration
    $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route($config['route'], $project->id) }}",
            type: "POST",
            data: { _token: '{{ csrf_token() }}' }
        },
        columns: dtColumnsConfig,
        order: [[2, 'asc']],
        columnDefs: [{
            targets: 0,
            className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
        }],
        fixedColumns: { left: 1 },
                            language: {
                        search: "Cari:",
                        lengthMenu: "Tunjukkan _MENU_ entri",
                        info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                        infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                        emptyTable: "Tiada data tersedia dalam jadual"
                    },
        "drawCallback": function( settings ) {
            if (panel.length) { panel.removeClass('datatable-container-loading'); }
        },
        "initComplete": function(settings, json) {
            const urlParams = new URLSearchParams(window.location.search);
            const statusFilter = urlParams.get('status');

            if (statusFilter === 'terbengkalai') {
                this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
            }
        }
    });
    initializedTables[tabName] = true;

@elseif($key === 'LaporanMatiMengejut')
    {{-- Use custom columns for LaporanMatiMengejut --}}
    @php
        $dtColumns = [
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
        ];
        
        // Define ALL columns that need the special combined render function
        $combinedRenderFields = [
            'status_pergerakan_barang_kes',
            'status_barang_kes_selesai_siasatan',
            'kaedah_pelupusan_barang_kes'
        ];
        
        foreach($laporanMatiMengejutColumns as $columnKey => $label) {
            $columnConfig = [
                'data' => $columnKey,
                'name' => $columnKey,
                'title' => $label,
                'defaultContent' => '-',
                'orderable' => true,
                'searchable' => true
            ];
            
            // Assign the placeholder to any column in our list
            if (in_array($columnKey, $combinedRenderFields)) {
                $columnConfig['render'] = '%%COMBINED_RENDER%%';
            }
            
            $dtColumns[] = $columnConfig;
        }
    @endphp

    // Step 1: Get the column configuration from PHP
    let dtColumnsConfig = @json($dtColumns);

    // Step 2: Define the combined render function specifically for LMM
    const combinedRenderFunction = function(data, type, row, meta) {
        if (!data || data === '-') return '-';

        let details = '';
        const colName = meta.settings.aoColumns[meta.col].name;

        // Logic for 'status_pergerakan_barang_kes'
        if (colName === 'status_pergerakan_barang_kes') {
            if (row.status_pergerakan_barang_kes_lain) {
                details = ` : ${row.status_pergerakan_barang_kes_lain}`;
            }
        } 
        // Logic for 'status_barang_kes_selesai_siasatan'
        else if (colName === 'status_barang_kes_selesai_siasatan') {
            if (data === 'Dilupuskan ke Perbendaharaan' && row.dilupuskan_perbendaharaan_amount) {
                details = ` (RM ${parseFloat(row.dilupuskan_perbendaharaan_amount).toFixed(2)})`;
            } else if (row.status_barang_kes_selesai_siasatan_lain) {
                details = ` : ${row.status_barang_kes_selesai_siasatan_lain}`;
            }
        }
        // Logic for 'kaedah_pelupusan_barang_kes'
        else if (colName === 'kaedah_pelupusan_barang_kes') {
            if (row.kaedah_pelupusan_barang_kes_lain) {
                details = ` : ${row.kaedah_pelupusan_barang_kes_lain}`;
            }
        }
        
        return data + details;
    };

    // Step 3: Loop through the config and replace the placeholders
    dtColumnsConfig.forEach(function(column) {
        if (column.render === '%%COMBINED_RENDER%%') {
            column.render = combinedRenderFunction;
        }
    });

    // Step 4: Initialize the DataTable with the corrected configuration
    $(tableId).DataTable({
        processing: true,
        serverSide: true,
                ajax: {
                    url: "{{ route($config['route'], $project->id) }}",
                    type: "POST",
                    data: { _token: '{{ csrf_token() }}' }
                },
        columns: dtColumnsConfig,
        order: [[2, 'asc']],
        columnDefs: [{
            targets: 0,
            className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
        }],
        fixedColumns: { left: 1 },
        language: {
            search: "Cari:",
            lengthMenu: "Tunjukkan _MENU_ entri",
            info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
            infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
            emptyTable: "Tiada data tersedia dalam jadual"
        },
        "drawCallback": function( settings ) {
            if (panel.length) {
                panel.removeClass('datatable-container-loading');
            }
        },
        "initComplete": function(settings, json) {
            const urlParams = new URLSearchParams(window.location.search);
            const statusFilter = urlParams.get('status');

            if (statusFilter === 'terbengkalai') {
                this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
            }
        }
    });
    initializedTables[tabName] = true;

{{-- FILE: resources/views/projects/show.blade.php (Part 5) --}}

{{-- FILE: resources/views/projects/show.blade.php (Part 5 of 5) --}}

@elseif($key === 'TrafikSeksyen')
    {{-- Use custom columns for TrafikSeksyen --}}
    @php
        $dtColumns = [
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
        ];
        
        $combinedRenderFields = [
            'status_pergerakan_barang_kes',
            'status_barang_kes_selesai_siasatan',
            'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan'
        ];
        
        $lainLainFields = [ 'lain_lain_permohonan_laporan' ];

        $jsonFields = [ 'keputusan_akhir_mahkamah' ]; // TrafikSeksyen working correctly 
        
        foreach($trafikSeksyenColumns as $column => $label) {
            $columnConfig = [
                'data' => $column, 'name' => $column, 'title' => $label,
                'defaultContent' => '-', 'orderable' => true, 'searchable' => true
            ];
            
            if (in_array($column, $combinedRenderFields)) {
                $columnConfig['render'] = '%%COMBINED_RENDER%%';
            } elseif (in_array($column, $lainLainFields)) {
                $columnConfig['render'] = '%%LAIN_LAIN_RENDER%%';
            }
            
            $dtColumns[] = $columnConfig;
        }
    @endphp

    // Step 1: Get the column configuration from PHP
    let dtColumnsConfig = @json($dtColumns);

    // Step 2: Define the render functions in JavaScript
    const lainLainRenderFunction = function(data, type, row) {
        if (!data || data === '-') return '-';
        return 'Lain-lain ; ' + data;
    };

    const jsonRenderFunction = function(data, type, row) {
        if (data === null || data === undefined) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
        try {
            // Attempt to parse the data as JSON
            const parsedData = JSON.parse(data);
            if (Array.isArray(parsedData)) {
                // If it's an array, create badges for each element
                if (parsedData.length === 0) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
                }
                const badges = parsedData.map(item => 
                    `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1 mb-1">${item}</span>`
                );
                return `<div class="flex flex-wrap">${badges.join(' ')}</div>`;
            }
        } catch (e) { 
            // If it's not valid JSON, return as single badge
            return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">${data}</span>`;
        }
        return data || '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
    };

    // *** Use the complete rendering logic ***
    const combinedRenderFunction = function(data, type, row, meta) {
        if (!data || data === '-') return '-';

        let details = '';
        const colName = meta.settings.aoColumns[meta.col].name;

        // Logic for 'status_pergerakan_barang_kes'
        if (colName === 'status_pergerakan_barang_kes') {
            // CHECK FOR UJIAN MAKMAL
            if (data === 'Ujian Makmal' && row.status_pergerakan_barang_kes_makmal) {
                details = ` : ${row.status_pergerakan_barang_kes_makmal}`;
            } 
            // CHECK FOR LAIN-LAIN
            else if (data === 'Lain-Lain' && row.status_pergerakan_barang_kes_lain) {
                details = ` : ${row.status_pergerakan_barang_kes_lain}`;
            }
        } 
        // Logic for 'status_barang_kes_selesai_siasatan'
        else if (colName === 'status_barang_kes_selesai_siasatan') {
            // CHECK FOR DILUPUSKAN KE PERBENDAHARAAN
            if (data === 'Dilupuskan ke Perbendaharaan' && row.status_barang_kes_selesai_siasatan_RM) {
                details = ` (RM ${row.status_barang_kes_selesai_siasatan_RM})`;
            } 
            // CHECK FOR LAIN-LAIN
            else if (data === 'Lain-Lain' && row.status_barang_kes_selesai_siasatan_lain) {
                details = ` : ${row.status_barang_kes_selesai_siasatan_lain}`;
            }
        }
        // Logic for 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan'
        else if (colName === 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan') {
            if (data === 'Lain-Lain' && row.kaedah_pelupusan_barang_kes_lain) {
                details = ` : ${row.kaedah_pelupusan_barang_kes_lain}`;
            }
        }
        
        return data + details;
    };

    // Step 3: Loop through the config and replace the placeholders
    dtColumnsConfig.forEach(function(column) {
        if (column.render === '%%COMBINED_RENDER%%') {
            column.render = combinedRenderFunction;
        } else if (column.render === '%%LAIN_LAIN_RENDER%%') {
            column.render = lainLainRenderFunction;
        }
    });

    // Step 4: Initialize the DataTable with the corrected configuration
    $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route($config['route'], $project->id) }}",
            type: "POST",
            data: { _token: '{{ csrf_token() }}' }
        },
        columns: dtColumnsConfig, // Use the processed JavaScript variable
        order: [[2, 'asc']],
        columnDefs: [{
            targets: 0,
            className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
        }],
        fixedColumns: { left: 1 },
                        language: {
                        search: "Cari:",
                        lengthMenu: "Tunjukkan _MENU_ entri",
                        info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                        infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                        emptyTable: "Tiada data tersedia dalam jadual"
                    },
        "drawCallback": function( settings ) {
            if (panel.length) { panel.removeClass('datatable-container-loading'); }
        },
        "initComplete": function(settings, json) {
            const urlParams = new URLSearchParams(window.location.search);
            const statusFilter = urlParams.get('status');
            if (statusFilter === 'terbengkalai') {
                this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
            }
        }
    });
    initializedTables[tabName] = true;

@elseif($key === 'TrafikRule')
            @php
                $dtColumns = [
                    ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                ];
                
                foreach($trafikRuleColumns as $column => $label) {
                    $dtColumns[] = [
                        'data' => $column,
                        'name' => $column,
                        'title' => $label,
                        'defaultContent' => '-',
                        'orderable' => true,
                        'searchable' => true
                    ];
                }
            @endphp

            let table = $(tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route($config['route'], $project->id) }}",
                    type: "POST",
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: @json($dtColumns),
                order: [[2, 'asc']],
                columnDefs: [{
                    targets: 0,
                    className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                }],
                fixedColumns: { left: 1 },
                language: {
                    search: "Cari:",
                    lengthMenu: "Tunjukkan _MENU_ entri",
                    info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                    infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                    emptyTable: "Tiada data tersedia dalam jadual"
                },
                "drawCallback": function( settings ) {
                    if (panel.length) {
                        panel.removeClass('datatable-container-loading');
                    }
                },
                // Logic to read URL and apply search filter on initialization
                "initComplete": function(settings, json) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const statusFilter = urlParams.get('status');

                    if (statusFilter === 'terbengkalai') {
                        // The keyword to search for across all columns
                        this.api().search('TERBENGKALAI MELEBIHI 3 BULAN').draw();
                    }
                }
            });
            initializedTables[tabName] = true;

        @else
            {{-- Use automatic column generation for other models --}}
            @php
                // Get the model instance, raw DB columns, and appended accessors for the current model.
                $modelInstance = new $config['model'];
                $rawDbColumns = Schema::getColumnListing($modelInstance->getTable());
                $appendedAccessors = $modelInstance->getAppends();

                $booleanDbColumnsWithTextAccessors = [
                    'arahan_minit_oleh_sio_status', 'arahan_minit_ketua_bahagian_status', 'arahan_minit_ketua_jabatan_status',
                    'arahan_minit_oleh_ya_tpr_status', 'adakah_barang_kes_didaftarkan', 'adakah_sijil_surat_kebenaran_ipo',
                    'status_id_siasatan_dikemaskini', 'status_rajah_kasar_tempat_kejadian', 'status_gambar_tempat_kejadian',
                    'status_gambar_post_mortem_mayat_di_hospital', 'status_gambar_barang_kes_am', 'status_gambar_barang_kes_berharga', 'status_gambar_barang_kes_kenderaan',
                    'status_gambar_barang_kes_darah', 'status_gambar_barang_kes_kontraban', 'status_rj2', 'status_rj2b',
                    'status_rj9', 'status_rj99', 'status_rj10a', 'status_rj10b', 'status_saman_pdrm_s_257', 'status_saman_pdrm_s_167',
                    'status_semboyan_pertama_wanted_person', 'status_semboyan_kedua_wanted_person', 'status_semboyan_ketiga_wanted_person',
                    'status_penandaan_kelas_warna', 'status_permohonan_laporan_post_mortem_mayat', 'status_laporan_penuh_bedah_siasat',
                    'status_permohonan_laporan_jabatan_kimia', 'status_laporan_penuh_jabatan_kimia', 'status_permohonan_laporan_jabatan_patalogi',
                    'status_laporan_penuh_jabatan_patalogi', 'status_permohonan_laporan_puspakom', 'status_laporan_penuh_puspakom',
                    'status_permohonan_laporan_jkr', 'status_laporan_penuh_jkr', 'status_permohonan_laporan_jpj', 'status_laporan_penuh_jpj',
                    'status_permohonan_laporan_imigresen', 'status_laporan_penuh_imigresen', 'muka_surat_4_barang_kes_ditulis',
                    'muka_surat_4_dengan_arahan_tpr', 'muka_surat_4_keputusan_kes_dicatat', 'fail_lmm_ada_keputusan_koroner'
                ];

                $jsonArrayColumns = [
                    'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan', 'status_pem', 'status_pergerakan_barang_kes',
                    'status_barang_kes_selesai_siasatan', 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                    'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan', 'resit_kew_38e_bagi_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                    'adakah_borang_serah_terima_pegawai_tangkapan', 'adakah_borang_serah_terima_pemilik_saksi', 'keputusan_akhir_mahkamah'
                ];

                $dtColumns = [
                    ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'title' => 'Tindakan', 'width' => '100px'],
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => 'No.']
                ];

                foreach($rawDbColumns as $column) {
                    if (in_array($column, ['id', 'project_id'])) continue;

                    $columnConfig = ['name' => $column, 'defaultContent' => '-', 'orderable' => true, 'searchable' => true];

                    if (in_array($column, $booleanDbColumnsWithTextAccessors)) {
                        $accessorName = $column . '_text';
                        if (in_array($accessorName, $appendedAccessors)) {
                            $columnConfig['data'] = $accessorName;
                            $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title() . ' (Status)';
                        } else {
                            $columnConfig['data'] = $column;
                            $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title();
                        }
                    } else if (in_array($column, $jsonArrayColumns)) {
                        $columnConfig['data'] = $column;
                        $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title();
                        $columnConfig['orderable'] = false;
                        $columnConfig['searchable'] = false;
                        $columnConfig['render'] = '%%JSON_RENDER%%';
                    } else {
                        $columnConfig['data'] = $column;
                        $columnConfig['title'] = Str::of($column)->replace('_', ' ')->title();
                    }
                    $dtColumns[] = $columnConfig;
                }
            @endphp

            // Step 1: Get the column configuration from PHP
            let dtColumnsConfig = @json($dtColumns);

            // Step 2: Define the actual render function in JavaScript
            const jsonRenderFunction = function(data, type, row) {
                if (data === null || data === undefined) return "-";
                let parsedData = data;
                // Check if data is a string that looks like a JSON array
                if (typeof data === "string" && data.startsWith('[') && data.endsWith(']')) {
                    try {
                        parsedData = JSON.parse(data);
                    } catch (e) {
                        return data; // Return original string if it's not valid JSON
                    }
                }
                // Check if we now have a valid array
                if (Array.isArray(parsedData)) {
                    return parsedData.length > 0 ? parsedData.join(", ") : "-";
                }
                // If not an array or parsable string, return it as is
                return parsedData;
            };

            // Step 3: Loop through the config and replace the placeholder
            dtColumnsConfig.forEach(function(column) {
                if (column.render === '%%JSON_RENDER%%') {
                    column.render = jsonRenderFunction;
                }
            });

            // Step 4: Initialize the DataTable with the corrected configuration
            $(tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route($config['route'], $project->id) }}",
                    type: "POST",
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: dtColumnsConfig, // Use the processed JavaScript variable
                order: [[2, 'asc']],
                columnDefs: [{
                    targets: 0,
                    className: "sticky left-0 bg-gray-50 dark:text-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600"
                }],
                fixedColumns: { left: 1 },
                language: {
                    search: "Cari:",
                    lengthMenu: "Tunjukkan _MENU_ entri",
                    info: "Menunjukkan _START_ hingga _END_ daripada _TOTAL_ entri",
                    infoEmpty: "Menunjukkan 0 hingga 0 daripada 0 entri",
                    emptyTable: "Tiada data tersedia dalam jadual"
                },
                "drawCallback": function( settings ) {
                    if (panel.length) {
                        panel.removeClass('datatable-container-loading');
                    }
                }
            });
            initializedTables[tabName] = true;
        @endif
    }
@endforeach
    }

    // Initialize all Chart.js charts on initial page load.
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($dashboardData as $key => $data)
            @php
                $hasIssueData = ($data['lewatCount'] ?? 0) > 0 || ($data['terbengkalaiCount'] ?? 0) > 0 || ($data['kemaskiniCount'] ?? 0) > 0;
                $hasAuditData = ($data['jumlahKeseluruhan'] ?? 0) > 0;
                $jumlahDiperiksa = $data['jumlahDiperiksa'] ?? 0;
                $jumlahBelumDiperiksa = $data['jumlahBelumDiperiksa'] ?? 0;
                $jumlahKeseluruhan = $data['jumlahKeseluruhan'] ?? 0;
            @endphp

            @if($hasAuditData)
                const auditCtx_{{ $key }} = document.getElementById('auditPieChart-{{ $key }}')?.getContext('2d');
                if (auditCtx_{{ $key }}) {
                    new Chart(auditCtx_{{ $key }}, {
                        type: 'pie',
                        data: {
                            labels: ['Jumlah Diperiksa (KS)', 'Jumlah Belum Diperiksa (KS)'],
                            datasets: [{
                                data: [{{ $jumlahDiperiksa }}, {{ $jumlahBelumDiperiksa }}],
                                backgroundColor: ['#0ea5e9', '#cbd5e1'],
                                borderWidth: 0,
                                borderColor: '#FFFFFF'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                            generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                                                const labels = data.labels.map(function(label, i) {
                                                    const value = data.datasets[0].data[i];
                                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                    return {
                                                        text: `${label} (${percentage}%) (${value})`,
                                                        fillStyle: data.datasets[0].backgroundColor[i],
                                                        strokeStyle: data.datasets[0].borderColor[i],
                                                        lineWidth: data.datasets[0].borderWidth,
                                                        hidden: chart.getDatasetMeta(0).data[i].hidden,
                                                        index: i
                                                    };
                                                });
                                                
                                                // Add a third line for total count after the second legend
                                                labels.push({
                                                    text: `Jumlah Kertas Siasatan (${total})            `,
                                                    fillStyle: 'transparent',
                                                    strokeStyle: 'transparent',
                                                    lineWidth: 0,
                                                    hidden: false,
                                                    index: -1 // Special index to identify this as non-clickable
                                                });
                                                
                                                return labels;
                                            }
                                            return [];
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Peratusan Status Pemeriksaan (Keseluruhan)'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw;
                                            const total = context.chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                            return `${value} (${percentage})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

                @if($hasIssueData)
                const statusCtx_{{ $key }} = document.getElementById('statusPieChart-{{ $key }}')?.getContext('2d');
                if (statusCtx_{{ $key }}) {
                    @php
                        // Define which paper types use the 24-hour rule.
                        $typesWith24HourRule = ['Jenayah', 'TrafikRule', 'OrangHilang', 'LaporanMatiMengejut'];
                        
                        // Determine the correct label for the pie chart.
                        $lewatPieLabel = in_array($key, $typesWith24HourRule) 
                            ? 'KS Lewat Edar (> 24 Jam)' 
                            : 'KS Lewat Edar (> 48 Jam)';
                    @endphp
                    new Chart(statusCtx_{{ $key }}, {
                        type: 'pie',
                        data: {
                            // Use the PHP variable to dynamically set the JavaScript label
                            labels: ['{!! $lewatPieLabel !!}', 'KS Terbengkalai (> 3 Bulan)', 'KS Baru Dikemaskini'],
                            datasets: [{
                                data: [{{ $data['lewatCount'] }}, {{ $data['terbengkalaiCount'] }}, {{ $data['kemaskiniCount'] }}],
                                backgroundColor: ['#F87171', '#FBBF24', '#34D399'],
                                borderWidth: 0,
                                borderColor: '#FFFFFF'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                                                return data.labels.map(function(label, i) {
                                                    const value = data.datasets[0].data[i];
                                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                    return {
                                                        text: `${label} (${percentage}%) (${value})`,
                                                        fillStyle: data.datasets[0].backgroundColor[i],
                                                        strokeStyle: data.datasets[0].borderColor[i],
                                                        lineWidth: data.datasets[0].borderWidth,
                                                        hidden: chart.getDatasetMeta(0).data[i].hidden,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Ringkasan Status Isu Siasatan'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw;
                                            const total = context.chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                            return `${value} (${percentage})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif
        @endforeach
    });

    // Handle scroll position restoration
    (function() {
        if (sessionStorage.getItem('scrollPosition')) {
            document.body.classList.add('is-restoring-scroll');
        }
        document.addEventListener('DOMContentLoaded', function () {
            const paginationContainers = document.querySelectorAll('.pagination-links');
            function handlePaginationClick(event) {
                const link = event.target.closest('a');
                if (link && link.href) {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                }
            }
            paginationContainers.forEach(container => {
                container.addEventListener('click', handlePaginationClick);
            });
            const scrollPosition = sessionStorage.getItem('scrollPosition');
            if (scrollPosition) {
                document.body.classList.remove('is-restoring-scroll');
                window.scrollTo(0, parseInt(scrollPosition, 10));
                sessionStorage.removeItem('scrollPosition');
            }
        });
    })();
    </script>
    
    @endpush
</x-app-layout>