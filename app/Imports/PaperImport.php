<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class PaperImport implements ToCollection, WithHeadingRow, WithEvents
{
    protected $projectId;
    protected $userId;
    protected $paperType;
    protected $duplicateHandling;
    protected $confirmOverwrite;
    protected $modelClass;
    private $config;

    private $createdCount = 0;
    private $updatedCount = 0;
    private $skippedCount = 0;
    private $successCount = 0;
    private $skippedRows = [];
    private $updatedRecords = []; // Track which records were updated and what changed
    private $duplicateRecords = []; // Track duplicate records found
    private $newRecordsCount = 0; // Track how many new records would be created

    private static $paperConfig = [
         'Jenayah' => [
            'model'       => \App\Models\Jenayah::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [

                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                'iprs_no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'iprs_tarikh_ks'             => 'iprs_tarikh_ks',
                'iprs_no_repot'              => 'iprs_no_repot',
                'iprs_jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'iprs_pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'iprs_status_ks'             => 'iprs_status_ks',
                'iprs_status_kes'            => 'iprs_status_kes',
                'iprs_seksyen'               => 'iprs_seksyen',


                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_repot_polis_b1' => 'no_repot_polis',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',

                // BAHAGIAN 2: Pemeriksaan & Status
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',

                // BAHAGIAN 3: Arahan & Keputusan
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'ulasan_keseluruhan_pegawai_pemeriksa' => 'ulasan_keseluruhan_pegawai_pemeriksa',

                // BAHAGIAN 4: Barang Kes
                'adakah_barang_kes_didaftarkan' => 'adakah_barang_kes_didaftarkan',
                'no_daftar_barang_kes_am' => 'no_daftar_barang_kes_am',
                'no_daftar_barang_kes_berharga' => 'no_daftar_barang_kes_berharga',
                'no_daftar_barang_kes_kenderaan' => 'no_daftar_barang_kes_kenderaan',
                'no_daftar_botol_spesimen_urin' => 'no_daftar_botol_spesimen_urin',
                'no_daftar_spesimen_darah' => 'no_daftar_spesimen_darah',
                'no_daftar_kontraban' => 'no_daftar_kontraban',
                'jenis_barang_kes_am' => 'jenis_barang_kes_am',
                'jenis_barang_kes_berharga' => 'jenis_barang_kes_berharga',
                'jenis_barang_kes_kenderaan' => 'jenis_barang_kes_kenderaan',
                'jenis_barang_kes_kontraban' => 'jenis_barang_kes_kontraban',
                'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes',
                'status_pergerakan_barang_kes_makmal' => 'status_pergerakan_barang_kes_makmal',
                'status_pergerakan_barang_kes_lain' => 'status_pergerakan_barang_kes_lain',
                'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan',
                'status_barang_kes_selesai_siasatan_rm' => 'status_barang_kes_selesai_siasatan_RM', // Note the case difference
                'status_barang_kes_selesai_siasatan_lain' => 'status_barang_kes_selesai_siasatan_lain',
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                'kaedah_pelupusan_barang_kes_lain' => 'kaedah_pelupusan_barang_kes_lain',
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                'resit_kew38e_pelupusan_wang_tunai' => 'resit_kew38e_pelupusan_wang_tunai',
                'adakah_borang_serah_terima_pegawai_tangkapan' => 'adakah_borang_serah_terima_pegawai_tangkapan',
                'adakah_borang_serah_terima_pemilik_saksi' => 'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipo' => 'adakah_sijil_surat_kebenaran_ipo',
                'adakah_gambar_pelupusan' => 'adakah_gambar_pelupusan',
                'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes',

                // BAHAGIAN 5: Dokumen Siasatan
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',
                'status_gambar_post_mortem_mayat_di_hospital' => 'status_gambar_post_mortem_mayat_di_hospital',
                'status_gambar_barang_kes_am' => 'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga' => 'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_kenderaan' => 'status_gambar_barang_kes_kenderaan',
                'status_gambar_barang_kes_darah' => 'status_gambar_barang_kes_darah',
                'status_gambar_barang_kes_kontraban' => 'status_gambar_barang_kes_kontraban',

                // BAHAGIAN 6: Borang & Semakan
                'status_pem' => 'status_pem',
                'status_rj2' => 'status_rj2',
                'tarikh_rj2' => 'tarikh_rj2',
                'status_rj2b' => 'status_rj2b',
                'tarikh_rj2b' => 'tarikh_rj2b',
                'status_rj9' => 'status_rj9',
                'tarikh_rj9' => 'tarikh_rj9',
                'status_rj99' => 'status_rj99',
                'tarikh_rj99' => 'tarikh_rj99',
                'status_rj10a' => 'status_rj10a',
                'tarikh_rj10a' => 'tarikh_rj10a',
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                'lain_lain_rj_dikesan' => 'lain_lain_rj_dikesan',
                'status_semboyan_pertama_wanted_person' => 'status_semboyan_pertama_wanted_person',
                'tarikh_semboyan_pertama_wanted_person' => 'tarikh_semboyan_pertama_wanted_person',
                'status_semboyan_kedua_wanted_person' => 'status_semboyan_kedua_wanted_person',
                'tarikh_semboyan_kedua_wanted_person' => 'tarikh_semboyan_kedua_wanted_person',
                'status_semboyan_ketiga_wanted_person' => 'status_semboyan_ketiga_wanted_person',
                'tarikh_semboyan_ketiga_wanted_person' => 'tarikh_semboyan_ketiga_wanted_person',
                'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'ulasan_keseluruhan_pegawai_pemeriksa_borang',
                'status_penandaan_kelas_warna' => 'status_penandaan_kelas_warna',

                // BAHAGIAN 7: Permohonan Laporan Agensi Luar
                'status_permohonan_laporan_pakar_judi' => 'status_permohonan_laporan_pakar_judi',
                'tarikh_permohonan_laporan_pakar_judi' => 'tarikh_permohonan_laporan_pakar_judi',
                'status_laporan_penuh_pakar_judi' => 'status_laporan_penuh_pakar_judi',
                'tarikh_laporan_penuh_pakar_judi' => 'tarikh_laporan_penuh_pakar_judi',
                'keputusan_laporan_pakar_judi' => 'keputusan_laporan_pakar_judi',
                'status_permohonan_laporan_post_mortem_mayat' => 'status_permohonan_laporan_post_mortem_mayat',
                'tarikh_permohonan_laporan_post_mortem_mayat' => 'tarikh_permohonan_laporan_post_mortem_mayat',
                'status_laporan_penuh_bedah_siasat' => 'status_laporan_penuh_bedah_siasat',
                'tarikh_laporan_penuh_bedah_siasat' => 'tarikh_laporan_penuh_bedah_siasat',
                'status_permohonan_laporan_jabatan_kimia' => 'status_permohonan_laporan_jabatan_kimia',
                'tarikh_permohonan_laporan_jabatan_kimia' => 'tarikh_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia' => 'status_laporan_penuh_jabatan_kimia',
                'tarikh_laporan_penuh_jabatan_kimia' => 'tarikh_laporan_penuh_jabatan_kimia',
                'keputusan_laporan_jabatan_kimia' => 'keputusan_laporan_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi' => 'status_permohonan_laporan_jabatan_patalogi',
                'tarikh_permohonan_laporan_jabatan_patalogi' => 'tarikh_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi' => 'status_laporan_penuh_jabatan_patalogi',
                'tarikh_laporan_penuh_jabatan_patalogi' => 'tarikh_laporan_penuh_jabatan_patalogi',
                'keputusan_laporan_jabatan_patalogi' => 'keputusan_laporan_jabatan_patalogi',
                'status_permohonan_laporan_puspakom' => 'status_permohonan_laporan_puspakom',
                'tarikh_permohonan_laporan_puspakom' => 'tarikh_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom' => 'status_laporan_penuh_puspakom',
                'tarikh_laporan_penuh_puspakom' => 'tarikh_laporan_penuh_puspakom',
                'status_permohonan_laporan_jpj' => 'status_permohonan_laporan_jpj',
                'tarikh_permohonan_laporan_jpj' => 'tarikh_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj' => 'status_laporan_penuh_jpj',
                'tarikh_laporan_penuh_jpj' => 'tarikh_laporan_penuh_jpj',
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',
                'status_permohonan_laporan_kastam' => 'status_permohonan_laporan_kastam',
                'tarikh_permohonan_laporan_kastam' => 'tarikh_permohonan_laporan_kastam',
                'status_laporan_penuh_kastam' => 'status_laporan_penuh_kastam',
                'tarikh_laporan_penuh_kastam' => 'tarikh_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm' => 'status_permohonan_laporan_forensik_pdrm',
                'tarikh_permohonan_laporan_forensik_pdrm' => 'tarikh_permohonan_laporan_forensik_pdrm',
                'status_laporan_penuh_forensik_pdrm' => 'status_laporan_penuh_forensik_pdrm',
                'tarikh_laporan_penuh_forensik_pdrm' => 'tarikh_laporan_penuh_forensik_pdrm',
                'keputusan_laporan_forensik_pdrm' => 'keputusan_laporan_forensik_pdrm',
                'jenis_ujian_analisis_forensik' => 'jenis_ujian_analisis_forensik',
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'muka_surat_4_barang_kes_ditulis' => 'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr' => 'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat' => 'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner' => 'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail' => 'status_kus_fail',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_pegawai_pemeriksa_fail' => 'ulasan_pegawai_pemeriksa_fail',
            ],
        ],
        'Narkotik' => [
            'model'       => \App\Models\Narkotik::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [

                                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                'iprs_no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'iprs_tarikh_ks'             => 'iprs_tarikh_ks',
                'iprs_no_repot'              => 'iprs_no_repot',
                'iprs_jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'iprs_pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'iprs_status_ks'             => 'iprs_status_ks',
                'iprs_status_kes'            => 'iprs_status_kes',
                'iprs_seksyen'               => 'iprs_seksyen',

                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_repot_polis_b1' => 'no_repot_polis',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',

                // BAHAGIAN 2: Pemeriksaan & Status
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',

                // BAHAGIAN 3: Arahan & Keputusan
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'ulasan_keseluruhan_pegawai_pemeriksa_b3' => 'ulasan_keseluruhan_pegawai_pemeriksa_b3',

                // BAHAGIAN 4: Barang Kes
                'adakah_barang_kes_didaftarkan' => 'adakah_barang_kes_didaftarkan',
                'no_daftar_barang_kes_am' => 'no_daftar_barang_kes_am',
                'no_daftar_barang_kes_berharga' => 'no_daftar_barang_kes_berharga',
                'no_daftar_barang_kes_kenderaan' => 'no_daftar_barang_kes_kenderaan',
                'no_daftar_botol_spesimen_urin' => 'no_daftar_botol_spesimen_urin',
                'no_daftar_barang_kes_dadah' => 'no_daftar_barang_kes_dadah',
                'no_daftar_spesimen_darah' => 'no_daftar_spesimen_darah',
                'jenis_barang_kes_am' => 'jenis_barang_kes_am',
                'jenis_barang_kes_berharga' => 'jenis_barang_kes_berharga',
                'jenis_barang_kes_kenderaan' => 'jenis_barang_kes_kenderaan',
                'jenis_barang_kes_dadah' => 'jenis_barang_kes_dadah',
                'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes',
                'status_pergerakan_barang_kes_makmal' => 'status_pergerakan_barang_kes_makmal',
                'status_pergerakan_barang_kes_lain' => 'status_pergerakan_barang_kes_lain',
                'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan',
                'status_barang_kes_selesai_siasatan_rm' => 'status_barang_kes_selesai_siasatan_RM', // Note the case
                'status_barang_kes_selesai_siasatan_lain' => 'status_barang_kes_selesai_siasatan_lain',
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                'kaedah_pelupusan_barang_kes_lain' => 'kaedah_pelupusan_barang_kes_lain',
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                'resit_kew38e_pelupusan_wang_tunai' => 'resit_kew38e_pelupusan_wang_tunai',
                'adakah_borang_serah_terima_pegawai_tangkapan' => 'adakah_borang_serah_terima_pegawai_tangkapan',
                'adakah_borang_serah_terima_pemilik_saksi' => 'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipo' => 'adakah_sijil_surat_kebenaran_ipo',
                'adakah_gambar_pelupusan' => 'adakah_gambar_pelupusan',
                'ulasan_keseluruhan_pegawai_pemeriksa_b4' => 'ulasan_keseluruhan_pegawai_pemeriksa_b4',

                // BAHAGIAN 5: Dokumen Siasatan
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',
                'gambar_botol_urin_3d_berseal' => 'gambar_botol_urin_3d_berseal',
                'gambar_pembalut_urin_dan_test_strip' => 'gambar_pembalut_urin_dan_test_strip',
                'status_gambar_barang_kes_am' => 'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga' => 'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_kenderaan' => 'status_gambar_barang_kes_kenderaan',
                'status_gambar_barang_kes_dadah' => 'status_gambar_barang_kes_dadah',
                'status_gambar_barang_kes_ketum' => 'status_gambar_barang_kes_ketum',
                'status_gambar_barang_kes_darah' => 'status_gambar_barang_kes_darah',
                'status_gambar_barang_kes_kontraban' => 'status_gambar_barang_kes_kontraban',

                // BAHAGIAN 6: Borang & Semakan
                'status_pem' => 'status_pem',
                'status_rj2' => 'status_rj2',
                'tarikh_rj2' => 'tarikh_rj2',
                'status_rj2b' => 'status_rj2b',
                'tarikh_rj2b' => 'tarikh_rj2b',
                'status_rj9' => 'status_rj9',
                'tarikh_rj9' => 'tarikh_rj9',
                'status_rj99' => 'status_rj99',
                'tarikh_rj99' => 'tarikh_rj99',
                'status_rj10a' => 'status_rj10a',
                'tarikh_rj10a' => 'tarikh_rj10a',
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                'lain_lain_rj_dikesan' => 'lain_lain_rj_dikesan',
                'status_semboyan_pertama_wanted_person' => 'status_semboyan_pertama_wanted_person',
                'tarikh_semboyan_pertama_wanted_person' => 'tarikh_semboyan_pertama_wanted_person',
                'status_semboyan_kedua_wanted_person' => 'status_semboyan_kedua_wanted_person',
                'tarikh_semboyan_kedua_wanted_person' => 'tarikh_semboyan_kedua_wanted_person',
                'status_semboyan_ketiga_wanted_person' => 'status_semboyan_ketiga_wanted_person',
                'tarikh_semboyan_ketiga_wanted_person' => 'tarikh_semboyan_ketiga_wanted_person',
                'ulasan_keseluruhan_pegawai_pemeriksa_b6' => 'ulasan_keseluruhan_pegawai_pemeriksa_b6',
                'status_penandaan_kelas_warna' => 'status_penandaan_kelas_warna',

                // BAHAGIAN 7: Permohonan Laporan Agensi Luar
                'status_permohonan_laporan_jabatan_kimia' => 'status_permohonan_laporan_jabatan_kimia',
                'tarikh_permohonan_laporan_jabatan_kimia' => 'tarikh_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia' => 'status_laporan_penuh_jabatan_kimia',
                'tarikh_laporan_penuh_jabatan_kimia' => 'tarikh_laporan_penuh_jabatan_kimia',
                'keputusan_laporan_jabatan_kimia' => 'keputusan_laporan_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi' => 'status_permohonan_laporan_jabatan_patalogi',
                'tarikh_permohonan_laporan_jabatan_patalogi' => 'tarikh_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi' => 'status_laporan_penuh_jabatan_patalogi',
                'tarikh_laporan_penuh_jabatan_patalogi' => 'tarikh_laporan_penuh_jabatan_patalogi',
                'keputusan_laporan_jabatan_patalogi' => 'keputusan_laporan_jabatan_patalogi',
                'status_permohonan_laporan_puspakom' => 'status_permohonan_laporan_puspakom',
                'tarikh_permohonan_laporan_puspakom' => 'tarikh_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom' => 'status_laporan_penuh_puspakom',
                'tarikh_laporan_penuh_puspakom' => 'tarikh_laporan_penuh_puspakom',
                'status_permohonan_laporan_jpj' => 'status_permohonan_laporan_jpj',
                'tarikh_permohonan_laporan_jpj' => 'tarikh_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj' => 'status_laporan_penuh_jpj',
                'tarikh_laporan_penuh_jpj' => 'tarikh_laporan_penuh_jpj',
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',
                'status_permohonan_laporan_kastam' => 'status_permohonan_laporan_kastam',
                'tarikh_permohonan_laporan_kastam' => 'tarikh_permohonan_laporan_kastam',
                'status_laporan_penuh_kastam' => 'status_laporan_penuh_kastam',
                'tarikh_laporan_penuh_kastam' => 'tarikh_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm' => 'status_permohonan_laporan_forensik_pdrm',
                'tarikh_permohonan_laporan_forensik_pdrm' => 'tarikh_permohonan_laporan_forensik_pdrm',
                'jenis_barang_kes_di_hantar' => 'jenis_barang_kes_di_hantar',
                'status_laporan_penuh_forensik_pdrm' => 'status_laporan_penuh_forensik_pdrm',
                'tarikh_laporan_penuh_forensik_pdrm' => 'tarikh_laporan_penuh_forensik_pdrm',
                'keputusan_laporan_forensik_pdrm' => 'keputusan_laporan_forensik_pdrm',
                'jenis_ujian_analisis_forensik' => 'jenis_ujian_analisis_forensik',
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'muka_surat_4_barang_kes_ditulis' => 'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr' => 'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat' => 'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner' => 'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail' => 'status_kus_fail',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'ulasan_keseluruhan_pegawai_pemeriksa_fail',
            ],
        ],
        'Komersil' => [
            'model'       => \App\Models\Komersil::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [

                                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                'iprs_no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'iprs_tarikh_ks'             => 'iprs_tarikh_ks',
                'iprs_no_repot'              => 'iprs_no_repot',
                'iprs_jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'iprs_pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'iprs_status_ks'             => 'iprs_status_ks',
                'iprs_status_kes'            => 'iprs_status_kes',
                'iprs_seksyen'               => 'iprs_seksyen',

                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_repot_polis_b1' => 'no_repot_polis',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',

                // BAHAGIAN 2: Pemeriksaan JIPS
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',

                // BAHAGIAN 3: Arahan SIO & Ketua
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'ulasan_keseluruhan_pegawai_pemeriksa' => 'ulasan_keseluruhan_pegawai_pemeriksa',

                // BAHAGIAN 4: Barang Kes
                'adakah_barang_kes_didaftarkan' => 'adakah_barang_kes_didaftarkan',
                'no_daftar_barang_kes_am' => 'no_daftar_barang_kes_am',
                'no_daftar_barang_kes_berharga' => 'no_daftar_barang_kes_berharga',
                'no_daftar_barang_kes_kenderaan' => 'no_daftar_barang_kes_kenderaan',
                'no_daftar_botol_spesimen_urin' => 'no_daftar_botol_spesimen_urin',
                'jenis_barang_kes_am' => 'jenis_barang_kes_am',
                'jenis_barang_kes_berharga' => 'jenis_barang_kes_berharga',
                'jenis_barang_kes_kenderaan' => 'jenis_barang_kes_kenderaan',
                'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes',
                'status_pergerakan_barang_kes_ujian_makmal' => 'status_pergerakan_barang_kes_ujian_makmal',
                'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan',
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                'resit_kew_38e_bagi_pelupusan' => 'resit_kew_38e_bagi_pelupusan',
                'adakah_borang_serah_terima_pegawai_tangkapan' => 'adakah_borang_serah_terima_pegawai_tangkapan',
                'adakah_borang_serah_terima_pemilik_saksi' => 'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipo' => 'adakah_sijil_surat_kebenaran_ipo',
                'adakah_gambar_pelupusan' => 'adakah_gambar_pelupusan',
                'status_pergerakan_barang_kes_lain' => 'status_pergerakan_barang_kes_lain',
                'status_barang_kes_selesai_siasatan_lain' => 'status_barang_kes_selesai_siasatan_lain',
                'kaedah_pelupusan_lain' => 'kaedah_pelupusan_lain',
                'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes',

                // BAHAGIAN 5: Bukti & Rajah
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',
                'status_gambar_barang_kes_am' => 'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga' => 'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_kenderaan' => 'status_gambar_barang_kes_kenderaan',
                'status_gambar_barang_kes_darah' => 'status_gambar_barang_kes_darah',
                'status_gambar_barang_kes_kontraban' => 'status_gambar_barang_kes_kontraban',

                // BAHAGIAN 6: Laporan RJ & Semboyan
                'status_pem' => 'status_pem',
                'status_rj2' => 'status_rj2',
                'tarikh_rj2' => 'tarikh_rj2',
                'status_rj2b' => 'status_rj2b',
                'tarikh_rj2b' => 'tarikh_rj2b',
                'status_rj9' => 'status_rj9',
                'tarikh_rj9' => 'tarikh_rj9',
                'status_rj99' => 'status_rj99',
                'tarikh_rj99' => 'tarikh_rj99',
                'status_rj10a' => 'status_rj10a',
                'tarikh_rj10a' => 'tarikh_rj10a',
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                'lain_lain_rj_dikesan' => 'lain_lain_rj_dikesan',
                'status_semboyan_pertama_wanted_person' => 'status_semboyan_pertama_wanted_person',
                'tarikh_semboyan_pertama_wanted_person' => 'tarikh_semboyan_pertama_wanted_person',
                'status_semboyan_kedua_wanted_person' => 'status_semboyan_kedua_wanted_person',
                'tarikh_semboyan_kedua_wanted_person' => 'tarikh_semboyan_kedua_wanted_person',
                'status_semboyan_ketiga_wanted_person' => 'status_semboyan_ketiga_wanted_person',
                'tarikh_semboyan_ketiga_wanted_person' => 'tarikh_semboyan_ketiga_wanted_person',
                'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'ulasan_keseluruhan_pegawai_pemeriksa_borang',
                'status_penandaan_kelas_warna' => 'status_penandaan_kelas_warna',
                'status_saman_pdrm_s_257' => 'status_saman_pdrm_s_257',
                'no_saman_pdrm_s_257' => 'no_saman_pdrm_s_257',
                'status_saman_pdrm_s_167' => 'status_saman_pdrm_s_167',
                'no_saman_pdrm_s_167' => 'no_saman_pdrm_s_167',

                // BAHAGIAN 7: Laporan E-FSA, Puspakom, dll
                'status_permohonan_laporan_post_mortem_mayat' => 'status_permohonan_laporan_post_mortem_mayat',
                'tarikh_permohonan_laporan_post_mortem_mayat' => 'tarikh_permohonan_laporan_post_mortem_mayat',
                'status_permohonan_e_fsa_1_oleh_io_aio' => 'status_permohonan_E_FSA_1_oleh_IO_AIO',
                'nama_bank_permohonan_e_fsa_1' => 'nama_bank_permohonan_E_FSA_1',
                'status_laporan_penuh_e_fsa_1_oleh_io_aio' => 'status_laporan_penuh_E_FSA_1_oleh_IO_AIO',
                'nama_bank_laporan_e_fsa_1_oleh_io_aio' => 'nama_bank_laporan_E_FSA_1_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_1_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO',
                'status_permohonan_e_fsa_2_oleh_io_aio' => 'status_permohonan_E_FSA_2_oleh_IO_AIO',
                'nama_bank_permohonan_e_fsa_2_bank' => 'nama_bank_permohonan_E_FSA_2_BANK',
                'status_laporan_penuh_e_fsa_2_oleh_io_aio' => 'status_laporan_penuh_E_FSA_2_oleh_IO_AIO',
                'nama_bank_laporan_e_fsa_2_oleh_io_aio' => 'nama_bank_laporan_E_FSA_2_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_2_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO',
                'status_permohonan_e_fsa_3_oleh_io_aio' => 'status_permohonan_E_FSA_3_oleh_IO_AIO',
                'nama_bank_permohonan_e_fsa_3_bank' => 'nama_bank_permohonan_E_FSA_3_BANK',
                'status_laporan_penuh_e_fsa_3_oleh_io_aio' => 'status_laporan_penuh_E_FSA_3_oleh_IO_AIO',
                'nama_bank_laporan_e_fsa_3_oleh_io_aio' => 'nama_bank_laporan_E_FSA_3_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_3_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO',
                'status_permohonan_e_fsa_4_oleh_io_aio' => 'status_permohonan_E_FSA_4_oleh_IO_AIO',
                'nama_bank_permohonan_e_fsa_4_bank' => 'nama_bank_permohonan_E_FSA_4_BANK',
                'status_laporan_penuh_e_fsa_4_oleh_io_aio' => 'status_laporan_penuh_E_FSA_4_oleh_IO_AIO',
                'nama_bank_laporan_e_fsa_4_oleh_io_aio' => 'nama_bank_laporan_E_FSA_4_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_4_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO',
                'status_permohonan_e_fsa_5_oleh_io_aio' => 'status_permohonan_E_FSA_5_oleh_IO_AIO',
                'nama_bank_permohonan_e_fsa_5_bank' => 'nama_bank_permohonan_E_FSA_5_BANK',
                'status_laporan_penuh_e_fsa_5_oleh_io_aio' => 'status_laporan_penuh_E_FSA_5_oleh_IO_AIO',
                'nama_bank_laporan_e_fsa_5_oleh_io_aio' => 'nama_bank_laporan_E_FSA_5_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_5_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO',
                'status_permohonan_e_fsa_1_telco_oleh_io_aio' => 'status_permohonan_E_FSA_1_telco_oleh_IO_AIO',
                'nama_telco_permohonan_e_fsa_1_oleh_io_aio' => 'nama_telco_permohonan_E_FSA_1_oleh_IO_AIO',
                'status_laporan_penuh_e_fsa_1_telco_oleh_io_aio' => 'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO',
                'nama_telco_laporan_e_fsa_1_oleh_io_aio' => 'nama_telco_laporan_E_FSA_1_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_1_telco_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO',
                'status_permohonan_e_fsa_2_telco_oleh_io_aio' => 'status_permohonan_E_FSA_2_telco_oleh_IO_AIO',
                'nama_telco_permohonan_e_fsa_2_oleh_io_aio' => 'nama_telco_permohonan_E_FSA_2_oleh_IO_AIO',
                'status_laporan_penuh_e_fsa_2_telco_oleh_io_aio' => 'status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO',
                'nama_telco_laporan_e_fsa_2_oleh_io_aio' => 'nama_telco_laporan_E_FSA_2_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_2_telco_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO',
                'status_permohonan_e_fsa_3_telco_oleh_io_aio' => 'status_permohonan_E_FSA_3_telco_oleh_IO_AIO',
                'nama_telco_permohonan_e_fsa_3_oleh_io_aio' => 'nama_telco_permohonan_E_FSA_3_oleh_IO_AIO',
                'status_laporan_penuh_e_fsa_3_telco_oleh_io_aio' => 'status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO',
                'nama_telco_laporan_e_fsa_3_oleh_io_aio' => 'nama_telco_laporan_E_FSA_3_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_3_telco_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO',
                'status_permohonan_e_fsa_4_telco_oleh_io_aio' => 'status_permohonan_E_FSA_4_oleh_IO_AIO',
                'nama_telco_permohonan_e_fsa_4_oleh_io_aio' => 'nama_telco_permohonan_E_FSA_4_oleh_IO_AIO',
                'status_laporan_penuh_e_fsa_4_telco_oleh_io_aio' => 'status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO',
                'nama_telco_laporan_e_fsa_4_oleh_io_aio' => 'nama_telco_laporan_E_FSA_4_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_4_telco_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO',
                'status_permohonan_e_fsa_5_telco_oleh_io_aio' => 'status_permohonan_E_FSA_5_telco_oleh_IO_AIO',
                'nama_telco_permohonan_e_fsa_5_oleh_io_aio' => 'nama_telco_permohonan_E_FSA_5_oleh_IO_AIO',
                'status_laporan_penuh_e_fsa_5_telco_oleh_io_aio' => 'status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO',
                'nama_telco_laporan_e_fsa_5_oleh_io_aio' => 'nama_telco_laporan_E_FSA_5_oleh_IO_AIO',
                'tarikh_laporan_penuh_e_fsa_5_telco_oleh_io_aio' => 'tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO',
                'status_permohonan_laporan_puspakom' => 'status_permohonan_laporan_puspakom',
                'tarikh_permohonan_laporan_puspakom' => 'tarikh_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom' => 'status_laporan_penuh_puspakom',
                'tarikh_laporan_penuh_puspakom' => 'tarikh_laporan_penuh_puspakom',
                'status_permohonan_laporan_jkr' => 'status_permohonan_laporan_jkr',
                'tarikh_permohonan_laporan_jkr' => 'tarikh_permohonan_laporan_jkr',
                'status_laporan_penuh_jkr' => 'status_laporan_penuh_jkr',
                'tarikh_laporan_penuh_jkr' => 'tarikh_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj' => 'status_permohonan_laporan_jpj',
                'tarikh_permohonan_laporan_jpj' => 'tarikh_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj' => 'status_laporan_penuh_jpj',
                'tarikh_laporan_penuh_jpj' => 'tarikh_laporan_penuh_jpj',
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',
                'status_permohonan_laporan_kastam' => 'status_permohonan_laporan_kastam',
                'tarikh_permohonan_laporan_kastam' => 'tarikh_permohonan_laporan_kastam',
                'status_laporan_penuh_kastam' => 'status_laporan_penuh_kastam',
                'tarikh_laporan_penuh_kastam' => 'tarikh_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm' => 'status_permohonan_laporan_forensik_pdrm',
                'tarikh_permohonan_laporan_forensik_pdrm' => 'tarikh_permohonan_laporan_forensik_pdrm',
                'status_laporan_penuh_forensik_pdrm' => 'status_laporan_penuh_forensik_pdrm',
                'tarikh_laporan_penuh_forensik_pdrm' => 'tarikh_laporan_penuh_forensik_pdrm',
                'keputusan_laporan_forensik_pdrm' => 'keputusan_laporan_forensik_pdrm',
                'jenis_ujian_analisis_forensik' => 'jenis_ujian_analisis_forensik',
                'jenis_barang_kes_forensik' => 'jenis_barang_kes_forensik',
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'muka_surat_4_barang_kes_ditulis' => 'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr' => 'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat' => 'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner' => 'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail' => 'status_kus_fail',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_pegawai_pemeriksa_fail' => 'ulasan_pegawai_pemeriksa_fail',
            ],
        ],
        'TrafikSeksyen' => [ 
            'model'       => \App\Models\TrafikSeksyen::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [

                                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                'iprs_no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'iprs_tarikh_ks'             => 'iprs_tarikh_ks',
                'iprs_no_repot'              => 'iprs_no_repot',
                'iprs_jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'iprs_pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'iprs_status_ks'             => 'iprs_status_ks',
                'iprs_status_kes'            => 'iprs_status_kes',
                'iprs_seksyen'               => 'iprs_seksyen',


                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_repot_polis_b1' => 'no_repot_polis',
                'no_lmm_t_b1' => 'no_lmm_t',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',

                // BAHAGIAN 2: Pemeriksaan & Status
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',

                // BAHAGIAN 3: Arahan & Keputusan
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'keputusan_siasatan_oleh_ya_koroner' => 'keputusan_siasatan_oleh_ya_koroner',
                'ulasan_keputusan_oleh_ya_koroner' => 'ulasan_keputusan_oleh_ya_koroner',
                'ulasan_keseluruhan_pegawai_pemeriksa' => 'ulasan_keseluruhan_pegawai_pemeriksa',
                
                // Fail L.M.M (T)
                'adakah_ms2_lmm_t_disahkan_oleh_kpd' => 'adakah_ms2_lmm_t_disahkan_oleh_kpd',
                'adakah_lmm_t_dirujuk_kepada_ya_koroner' => 'adakah_lmm_t_dirujuk_kepada_ya_koroner',
                'keputusan_ya_koroner_lmm_t' => 'keputusan_ya_koroner_lmm_t',

                // BAHAGIAN 4: Barang Kes
                'adakah_barang_kes_didaftarkan' => 'adakah_barang_kes_didaftarkan',
                'no_daftar_barang_kes_am' => 'no_daftar_barang_kes_am',
                'no_daftar_barang_kes_berharga' => 'no_daftar_barang_kes_berharga',
                'no_daftar_barang_kes_kenderaan' => 'no_daftar_barang_kes_kenderaan',
                'no_daftar_botol_spesimen_urin' => 'no_daftar_botol_spesimen_urin',
                'no_daftar_spesimen_darah' => 'no_daftar_spesimen_darah',
                'jenis_barang_kes_am' => 'jenis_barang_kes_am',
                'jenis_barang_kes_berharga' => 'jenis_barang_kes_berharga',
                'jenis_barang_kes_kenderaan' => 'jenis_barang_kes_kenderaan',
                'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes',
                'status_pergerakan_barang_kes_makmal' => 'status_pergerakan_barang_kes_makmal',
                'status_pergerakan_barang_kes_lain' => 'status_pergerakan_barang_kes_lain',
                'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan',
                'status_barang_kes_selesai_siasatan_rm' => 'status_barang_kes_selesai_siasatan_RM', // Note case
                'status_barang_kes_selesai_siasatan_lain' => 'status_barang_kes_selesai_siasatan_lain',
                'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan',
                'kaedah_pelupusan_barang_kes_lain' => 'kaedah_pelupusan_barang_kes_lain',
                'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan',
                'resit_kew38e_pelupusan_wang_tunai' => 'resit_kew38e_pelupusan_wang_tunai',
                'adakah_borang_serah_terima_pegawai_tangkapan' => 'adakah_borang_serah_terima_pegawai_tangkapan',
                'adakah_borang_serah_terima_pemilik_saksi' => 'adakah_borang_serah_terima_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipo' => 'adakah_sijil_surat_kebenaran_ipo',
                'adakah_gambar_pelupusan' => 'adakah_gambar_pelupusan',
                'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes',

                // BAHAGIAN 5: Dokumen Siasatan
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',
                'status_gambar_post_mortem_mayat_di_hospital' => 'status_gambar_post_mortem_mayat_di_hospital',
                'status_gambar_barang_kes_am' => 'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga' => 'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_kenderaan' => 'status_gambar_barang_kes_kenderaan',
                'status_gambar_barang_kes_darah' => 'status_gambar_barang_kes_darah',
                'status_gambar_barang_kes_kontraban' => 'status_gambar_barang_kes_kontraban',

                // BAHAGIAN 6: Borang & Semakan
                'status_pem' => 'status_pem',
                'status_rj2' => 'status_rj2',
                'tarikh_rj2' => 'tarikh_rj2',
                'status_rj2b' => 'status_rj2b',
                'tarikh_rj2b' => 'tarikh_rj2b',
                'status_rj9' => 'status_rj9',
                'tarikh_rj9' => 'tarikh_rj9',
                'status_rj99' => 'status_rj99',
                'tarikh_rj99' => 'tarikh_rj99',
                'status_rj10a' => 'status_rj10a',
                'tarikh_rj10a' => 'tarikh_rj10a',
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                'lain_lain_rj_dikesan' => 'lain_lain_rj_dikesan',
                'status_saman_pdrm_s_257' => 'status_saman_pdrm_s_257',
                'no_saman_pdrm_s_257' => 'no_saman_pdrm_s_257',
                'status_saman_pdrm_s_167' => 'status_saman_pdrm_s_167',
                'no_saman_pdrm_s_167' => 'no_saman_pdrm_s_167',
                'status_semboyan_pertama_wanted_person' => 'status_semboyan_pertama_wanted_person',
                'tarikh_semboyan_pertama_wanted_person' => 'tarikh_semboyan_pertama_wanted_person',
                'status_semboyan_kedua_wanted_person' => 'status_semboyan_kedua_wanted_person',
                'tarikh_semboyan_kedua_wanted_person' => 'tarikh_semboyan_kedua_wanted_person',
                'status_semboyan_ketiga_wanted_person' => 'status_semboyan_ketiga_wanted_person',
                'tarikh_semboyan_ketiga_wanted_person' => 'tarikh_semboyan_ketiga_wanted_person',
                'status_penandaan_kelas_warna' => 'status_penandaan_kelas_warna',
                'ulasan_keseluruhan_pegawai_pemeriksa_bahagian_6' => 'ulasan_keseluruhan_pegawai_pemeriksa_bahagian_6',

                // BAHAGIAN 7: Permohonan Laporan Agensi Luar
                'status_permohonan_laporan_post_mortem_mayat' => 'status_permohonan_laporan_post_mortem_mayat',
                'tarikh_permohonan_laporan_post_mortem_mayat' => 'tarikh_permohonan_laporan_post_mortem_mayat',
                'status_laporan_penuh_bedah_siasat' => 'status_laporan_penuh_bedah_siasat',
                'tarikh_laporan_penuh_bedah_siasat' => 'tarikh_laporan_penuh_bedah_siasat',
                'status_permohonan_laporan_jabatan_kimia' => 'status_permohonan_laporan_jabatan_kimia',
                'tarikh_permohonan_laporan_jabatan_kimia' => 'tarikh_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia' => 'status_laporan_penuh_jabatan_kimia',
                'tarikh_laporan_penuh_jabatan_kimia' => 'tarikh_laporan_penuh_jabatan_kimia',
                'keputusan_laporan_jabatan_kimia' => 'keputusan_laporan_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi' => 'status_permohonan_laporan_jabatan_patalogi',
                'tarikh_permohonan_laporan_jabatan_patalogi' => 'tarikh_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi' => 'status_laporan_penuh_jabatan_patalogi',
                'tarikh_laporan_penuh_jabatan_patalogi' => 'tarikh_laporan_penuh_jabatan_patalogi',
                'keputusan_laporan_jabatan_patalogi' => 'keputusan_laporan_jabatan_patalogi',
                'status_permohonan_laporan_puspakom' => 'status_permohonan_laporan_puspakom',
                'tarikh_permohonan_laporan_puspakom' => 'tarikh_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom' => 'status_laporan_penuh_puspakom',
                'tarikh_laporan_penuh_puspakom' => 'tarikh_laporan_penuh_puspakom',
                'status_permohonan_laporan_jkr' => 'status_permohonan_laporan_jkr',
                'tarikh_permohonan_laporan_jkr' => 'tarikh_permohonan_laporan_jkr',
                'status_laporan_penuh_jkr' => 'status_laporan_penuh_jkr',
                'tarikh_laporan_penuh_jkr' => 'tarikh_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj' => 'status_permohonan_laporan_jpj',
                'tarikh_permohonan_laporan_jpj' => 'tarikh_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj' => 'status_laporan_penuh_jpj',
                'tarikh_laporan_penuh_jpj' => 'tarikh_laporan_penuh_jpj',
                'status_permohonan_laporan_jkjr' => 'status_permohonan_laporan_jkjr',
                'tarikh_permohonan_laporan_jkjr' => 'tarikh_permohonan_laporan_jkjr',
                'status_laporan_penuh_jkjr' => 'status_laporan_penuh_jkjr',
                'tarikh_laporan_penuh_jkjr' => 'tarikh_laporan_penuh_jkjr',
                'status_permohonan_laporan_kastam' => 'status_permohonan_laporan_kastam',
                'tarikh_permohonan_laporan_kastam' => 'tarikh_permohonan_laporan_kastam',
                'status_laporan_penuh_kastam' => 'status_laporan_penuh_kastam',
                'tarikh_laporan_penuh_kastam' => 'tarikh_laporan_penuh_kastam',
                'status_permohonan_laporan_forensik_pdrm' => 'status_permohonan_laporan_forensik_pdrm',
                'tarikh_permohonan_laporan_forensik_pdrm' => 'tarikh_permohonan_laporan_forensik_pdrm',
                'status_laporan_penuh_forensik_pdrm' => 'status_laporan_penuh_forensik_pdrm',
                'tarikh_laporan_penuh_forensik_pdrm' => 'tarikh_laporan_penuh_forensik_pdrm',
                'jenis_barang_kes_forensik' => 'jenis_barang_kes_forensik',
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'muka_surat_4_barang_kes_ditulis' => 'muka_surat_4_barang_kes_ditulis',
                'muka_surat_4_dengan_arahan_tpr' => 'muka_surat_4_dengan_arahan_tpr',
                'muka_surat_4_keputusan_kes_dicatat' => 'muka_surat_4_keputusan_kes_dicatat',
                'fail_lmm_ada_keputusan_koroner' => 'fail_lmm_ada_keputusan_koroner',
                'status_kus_fail' => 'status_kus_fail',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_pegawai_pemeriksa_fail' => 'ulasan_pegawai_pemeriksa_fail',
            ],
        ],
        'TrafikRule' => [ 
            'model'       => \App\Models\TrafikRule::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [

                                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                'iprs_no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'iprs_tarikh_ks'             => 'iprs_tarikh_ks',
                'iprs_no_repot'              => 'iprs_no_repot',
                'iprs_jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'iprs_pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'iprs_status_ks'             => 'iprs_status_ks',
                'iprs_status_kes'            => 'iprs_status_kes',
                'iprs_seksyen'               => 'iprs_seksyen',


                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_fail_lmm_t_b1' => 'no_fail_lmm_t',
                'no_repot_polis_b1' => 'no_repot_polis',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',

                // BAHAGIAN 2: Pemeriksaan & Status
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',
                'tarikh_edaran_minit_fail_lmm_t_pertama' => 'tarikh_edaran_minit_fail_lmm_t_pertama',
                'tarikh_edaran_minit_fail_lmm_t_kedua' => 'tarikh_edaran_minit_fail_lmm_t_kedua',
                'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => 'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir',
                'tarikh_edaran_minit_fail_lmm_t_akhir' => 'tarikh_edaran_minit_fail_lmm_t_akhir',
                'fail_lmm_t_muka_surat_2_disahkan_kpd' => 'fail_lmm_t_muka_surat_2_disahkan_kpd',

                // BAHAGIAN 3: Arahan & Keputusan
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'ulasan_keseluruhan_pegawai_pemeriksa' => 'ulasan_keseluruhan_pegawai_pemeriksa',

                // BAHAGIAN 5: Dokumen Siasatan
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',

                // BAHAGIAN 6: Borang & Semakan
                'status_pem' => 'status_pem',
                
                // RJ Fields - Added as per client requirements
                'status_rj2' => 'status_rj2',
                'tarikh_rj2' => 'tarikh_rj2',
                'status_rj2b' => 'status_rj2b',
                'tarikh_rj2b' => 'tarikh_rj2b',
                'status_rj9' => 'status_rj9',
                'tarikh_rj9' => 'tarikh_rj9',
                'status_rj99' => 'status_rj99',
                'tarikh_rj99' => 'tarikh_rj99',
                'status_rj10a' => 'status_rj10a',
                'tarikh_rj10a' => 'tarikh_rj10a',
                
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                // Note: "lain_lain_rj_dikesan" removed as per client requirements
                'status_saman_pdrm_s_257' => 'status_saman_pdrm_s_257',
                'no_saman_pdrm_s_257' => 'no_saman_pdrm_s_257',
                'status_saman_pdrm_s_167' => 'status_saman_pdrm_s_167',
                'no_saman_pdrm_s_167' => 'no_saman_pdrm_s_167',
                'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'ulasan_keseluruhan_pegawai_pemeriksa_borang',

                // BAHAGIAN 7: Permohonan Laporan Agensi Luar
                'status_permohonan_laporan_jkr' => 'status_permohonan_laporan_jkr',
                'tarikh_permohonan_laporan_jkr' => 'tarikh_permohonan_laporan_jkr',
                'status_laporan_penuh_jkr' => 'status_laporan_penuh_jkr',
                'tarikh_laporan_penuh_jkr' => 'tarikh_laporan_penuh_jkr',
                'status_permohonan_laporan_jpj' => 'status_permohonan_laporan_jpj',
                'tarikh_permohonan_laporan_jpj' => 'tarikh_permohonan_laporan_jpj',
                'status_laporan_penuh_jpj' => 'status_laporan_penuh_jpj',
                'tarikh_laporan_penuh_jpj' => 'tarikh_laporan_penuh_jpj',
                'status_permohonan_laporan_jkjr' => 'status_permohonan_laporan_jkjr',
                'tarikh_permohonan_laporan_jkjr' => 'tarikh_permohonan_laporan_jkjr',
                'status_laporan_penuh_jkjr' => 'status_laporan_penuh_jkjr',
                'tarikh_laporan_penuh_jkjr' => 'tarikh_laporan_penuh_jkjr',
                
                // PUSPAKOM - Added as per client requirements
                'status_permohonan_laporan_puspakom' => 'status_permohonan_laporan_puspakom',
                'tarikh_permohonan_laporan_puspakom' => 'tarikh_permohonan_laporan_puspakom',
                'status_laporan_penuh_puspakom' => 'status_laporan_penuh_puspakom',
                'tarikh_laporan_penuh_puspakom' => 'tarikh_laporan_penuh_puspakom',
                
                // HOSPITAL - Added as per client requirements
                'status_permohonan_laporan_hospital' => 'status_permohonan_laporan_hospital',
                'tarikh_permohonan_laporan_hospital' => 'tarikh_permohonan_laporan_hospital',
                'status_laporan_penuh_hospital' => 'status_laporan_penuh_hospital',
                'tarikh_laporan_penuh_hospital' => 'tarikh_laporan_penuh_hospital',
                
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'adakah_muka_surat_4_keputusan_kes_dicatat' => 'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_ks_kus_fail_selesai' => 'adakah_ks_kus_fail_selesai',
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'ulasan_keseluruhan_pegawai_pemeriksa_fail',
            ],
        ],
        'OrangHilang' => [
            'model'       => \App\Models\OrangHilang::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [

                                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_repot_polis_b1' => 'no_repot_polis',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',

                // BAHAGIAN 2: Pemeriksaan & Status
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',

                // BAHAGIAN 3: Arahan & Keputusan
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'arahan_tuduh_oleh_ya_tpr' => 'arahan_tuduh_oleh_ya_tpr',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'ulasan_keseluruhan_pegawai_pemeriksa' => 'ulasan_keseluruhan_pegawai_pemeriksa',

                // BAHAGIAN 4: Barang Kes
                'adakah_barang_kes_didaftarkan' => 'adakah_barang_kes_didaftarkan',
                'no_daftar_barang_kes_am' => 'no_daftar_barang_kes_am',
                'no_daftar_barang_kes_berharga' => 'no_daftar_barang_kes_berharga',

                // BAHAGIAN 5: Dokumen Siasatan
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',
                'status_gambar_barang_kes_am' => 'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga' => 'status_gambar_barang_kes_berharga',
                'status_gambar_orang_hilang' => 'status_gambar_orang_hilang',

                // BAHAGIAN 6: Borang & Semakan
                'status_pem' => 'status_pem',
                'status_mps1' => 'status_mps1',
                'tarikh_mps1' => 'tarikh_mps1',
                'status_mps2' => 'status_mps2',
                'tarikh_mps2' => 'tarikh_mps2',
                'pemakluman_nur_alert_jsj_bawah_18_tahun' => 'pemakluman_nur_alert_jsj_bawah_18_tahun',
                'rakaman_percakapan_orang_hilang' => 'rakaman_percakapan_orang_hilang',
                'laporan_polis_orang_hilang_dijumpai' => 'laporan_polis_orang_hilang_dijumpai',
                'hebahan_media_massa' => 'hebahan_media_massa',
                'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => 'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah',
                'alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => 'alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah',
                'orang_hilang_dijumpai_mati_mengejut_jenayah' => 'orang_hilang_dijumpai_mati_mengejut_jenayah',
                'alasan_orang_hilang_dijumpai_mati_mengejut_jenayah' => 'alasan_orang_hilang_dijumpai_mati_mengejut_jenayah',
                'semboyan_pemakluman_ke_kedutaan_bukan_warganegara' => 'semboyan_pemakluman_ke_kedutaan_bukan_warganegara',
                'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'ulasan_keseluruhan_pegawai_pemeriksa_borang',

                // BAHAGIAN 7: Imigresen
                'permohonan_laporan_permit_kerja' => 'permohonan_laporan_permit_kerja',
                'permohonan_laporan_agensi_pekerjaan' => 'permohonan_laporan_agensi_pekerjaan',
                'permohonan_status_kewarganegaraan' => 'permohonan_status_kewarganegaraan',
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',

                // BAHAGIAN 8: Status Fail
                'adakah_muka_surat_4_keputusan_kes_dicatat' => 'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_ks_kus_fail_selesai' => 'adakah_ks_kus_fail_selesai',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'ulasan_keseluruhan_pegawai_pemeriksa_fail',
            ],
        ],
        'LaporanMatiMengejut' => [
            'model'       => \App\Models\LaporanMatiMengejut::class,
            'unique_by'   => 'no_kertas_siasatan', // Changed from no_fail_lmm_sdr to be consistent
            'column_map'  => [

                                // IPRS maklumat
                'no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'tarikh_ks'             => 'iprs_tarikh_ks',
                'no_repot'              => 'iprs_no_repot',
                'jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'status_ks'             => 'iprs_status_ks',
                'status_kes'            => 'iprs_status_kes',
                'seksyen'               => 'iprs_seksyen',

                'iprs_no_kertas_siasatan'    => 'iprs_no_kertas_siasatan',
                'iprs_tarikh_ks'             => 'iprs_tarikh_ks',
                'iprs_no_repot'              => 'iprs_no_repot',
                'iprs_jenis_jabatan_ks'      => 'iprs_jenis_jabatan_ks',
                'iprs_pegawai_penyiasat'     => 'iprs_pegawai_penyiasat',
                'iprs_status_ks'             => 'iprs_status_ks',
                'iprs_status_kes'            => 'iprs_status_kes',
                'iprs_seksyen'               => 'iprs_seksyen',


                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan_b1' => 'no_kertas_siasatan',
                'no_fail_lmm_sdr_b1' => 'no_fail_lmm_sdr',
                'no_repot_polis_b1' => 'no_repot_polis',
                'pegawai_penyiasat_b1' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka_b1' => 'tarikh_laporan_polis_dibuka',
                'seksyen_b1' => 'seksyen',
                // New LMM fields
                'adakah_ms_2_lmm_telah_disahkan_oleh_kpd' => 'adakah_ms_2_lmm_telah_disahkan_oleh_kpd',
                'adakah_lmm_telah_di_rujuk_kepada_ya_koroner' => 'adakah_lmm_telah_di_rujuk_kepada_ya_koroner',
                'keputusan_ya_koroner' => 'keputusan_ya_koroner',

                // BAHAGIAN 2: Pemeriksaan & Status
                'pegawai_pemeriksa' => 'pegawai_pemeriksa',
                'tarikh_edaran_minit_ks_pertama' => 'tarikh_edaran_minit_ks_pertama',
                'tarikh_edaran_minit_ks_kedua' => 'tarikh_edaran_minit_ks_kedua',
                'tarikh_edaran_minit_ks_sebelum_akhir' => 'tarikh_edaran_minit_ks_sebelum_akhir',
                'tarikh_edaran_minit_ks_akhir' => 'tarikh_edaran_minit_ks_akhir',
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => 'tarikh_semboyan_pemeriksaan_jips_ke_daerah',
                'tarikh_edaran_minit_fail_lmm_t_pertama' => 'tarikh_edaran_minit_fail_lmm_t_pertama',
                'tarikh_edaran_minit_fail_lmm_t_kedua' => 'tarikh_edaran_minit_fail_lmm_t_kedua',
                'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => 'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir',
                'tarikh_edaran_minit_fail_lmm_t_akhir' => 'tarikh_edaran_minit_fail_lmm_t_akhir',

                // BAHAGIAN 3: Arahan & Keputusan
                'arahan_minit_oleh_sio_status' => 'arahan_minit_oleh_sio_status',
                'arahan_minit_oleh_sio_tarikh' => 'arahan_minit_oleh_sio_tarikh',
                'arahan_minit_ketua_bahagian_status' => 'arahan_minit_ketua_bahagian_status',
                'arahan_minit_ketua_bahagian_tarikh' => 'arahan_minit_ketua_bahagian_tarikh',
                'arahan_minit_ketua_jabatan_status' => 'arahan_minit_ketua_jabatan_status',
                'arahan_minit_ketua_jabatan_tarikh' => 'arahan_minit_ketua_jabatan_tarikh',
                'arahan_minit_oleh_ya_tpr_status' => 'arahan_minit_oleh_ya_tpr_status',
                'arahan_minit_oleh_ya_tpr_tarikh' => 'arahan_minit_oleh_ya_tpr_tarikh',
                'keputusan_siasatan_oleh_ya_tpr' => 'keputusan_siasatan_oleh_ya_tpr',
                'arahan_tuduh_oleh_ya_tpr' => 'arahan_tuduh_oleh_ya_tpr',
                'ulasan_keputusan_siasatan_tpr' => 'ulasan_keputusan_siasatan_tpr',
                'keputusan_siasatan_oleh_ya_koroner' => 'keputusan_siasatan_oleh_ya_koroner',
                'ulasan_keputusan_oleh_ya_koroner' => 'ulasan_keputusan_oleh_ya_koroner',
                'ulasan_keseluruhan_pegawai_pemeriksa' => 'ulasan_keseluruhan_pegawai_pemeriksa',

                // BAHAGIAN 4: Barang Kes
                'adakah_barang_kes_didaftarkan' => 'adakah_barang_kes_didaftarkan',
                'no_daftar_barang_kes_am' => 'no_daftar_barang_kes_am',
                'no_daftar_barang_kes_berharga' => 'no_daftar_barang_kes_berharga',
                'jenis_barang_kes_am' => 'jenis_barang_kes_am',
                'jenis_barang_kes_berharga' => 'jenis_barang_kes_berharga',
                'status_pergerakan_barang_kes' => 'status_pergerakan_barang_kes',
                'status_pergerakan_barang_kes_lain' => 'status_pergerakan_barang_kes_lain',
                'ujian_makmal_details' => 'ujian_makmal_details',
                'status_barang_kes_selesai_siasatan' => 'status_barang_kes_selesai_siasatan',
                'status_barang_kes_selesai_siasatan_lain' => 'status_barang_kes_selesai_siasatan_lain',
                'dilupuskan_perbendaharaan_amount' => 'dilupuskan_perbendaharaan_amount',
                'kaedah_pelupusan_barang_kes' => 'kaedah_pelupusan_barang_kes',
                'kaedah_pelupusan_barang_kes_lain' => 'kaedah_pelupusan_barang_kes_lain',
                'arahan_pelupusan_barang_kes' => 'arahan_pelupusan_barang_kes',
                'adakah_borang_serah_terima_pegawai_tangkapan_io' => 'adakah_borang_serah_terima_pegawai_tangkapan_io',
                'adakah_borang_serah_terima_penyiasat_pemilik_saksi' => 'adakah_borang_serah_terima_penyiasat_pemilik_saksi',
                'adakah_sijil_surat_kebenaran_ipd' => 'adakah_sijil_surat_kebenaran_ipd',
                'adakah_gambar_pelupusan' => 'adakah_gambar_pelupusan',
                'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes' => 'ulasan_keseluruhan_pegawai_pemeriksa_barang_kes',

                // BAHAGIAN 5: Dokumen Siasatan
                'status_id_siasatan_dikemaskini' => 'status_id_siasatan_dikemaskini',
                'status_rajah_kasar_tempat_kejadian' => 'status_rajah_kasar_tempat_kejadian',
                'status_gambar_tempat_kejadian' => 'status_gambar_tempat_kejadian',
                'status_gambar_post_mortem_mayat_di_hospital' => 'status_gambar_post_mortem_mayat_di_hospital',
                'status_gambar_barang_kes_am' => 'status_gambar_barang_kes_am',
                'status_gambar_barang_kes_berharga' => 'status_gambar_barang_kes_berharga',
                'status_gambar_barang_kes_darah' => 'status_gambar_barang_kes_darah',

                // BAHAGIAN 6: Borang & Semakan
                'status_pem' => 'status_pem',
                'status_rj2' => 'status_rj2',
                'tarikh_rj2' => 'tarikh_rj2',
                'status_rj2b' => 'status_rj2b',
                'tarikh_rj2b' => 'tarikh_rj2b',
                'status_rj9' => 'status_rj9',
                'tarikh_rj9' => 'tarikh_rj9',
                'status_rj99' => 'status_rj99',
                'tarikh_rj99' => 'tarikh_rj99',
                'status_rj10a' => 'status_rj10a',
                'tarikh_rj10a' => 'tarikh_rj10a',
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                'lain_lain_rj_dikesan' => 'lain_lain_rj_dikesan',
                'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati' => 'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati',
                'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'ulasan_keseluruhan_pegawai_pemeriksa_borang',

                // BAHAGIAN 7: Permohonan Laporan Agensi Luar
                'status_permohonan_laporan_post_mortem_mayat' => 'status_permohonan_laporan_post_mortem_mayat',
                'tarikh_permohonan_laporan_post_mortem_mayat' => 'tarikh_permohonan_laporan_post_mortem_mayat',
                'status_laporan_penuh_bedah_siasat' => 'status_laporan_penuh_bedah_siasat',
                'tarikh_laporan_penuh_bedah_siasat' => 'tarikh_laporan_penuh_bedah_siasat',
                'keputusan_laporan_post_mortem' => 'keputusan_laporan_post_mortem',
                'status_permohonan_laporan_jabatan_kimia' => 'status_permohonan_laporan_jabatan_kimia',
                'tarikh_permohonan_laporan_jabatan_kimia' => 'tarikh_permohonan_laporan_jabatan_kimia',
                'status_laporan_penuh_jabatan_kimia' => 'status_laporan_penuh_jabatan_kimia',
                'tarikh_laporan_penuh_jabatan_kimia' => 'tarikh_laporan_penuh_jabatan_kimia',
                'keputusan_laporan_jabatan_kimia' => 'keputusan_laporan_jabatan_kimia',
                'status_permohonan_laporan_jabatan_patalogi' => 'status_permohonan_laporan_jabatan_patalogi',
                'tarikh_permohonan_laporan_jabatan_patalogi' => 'tarikh_permohonan_laporan_jabatan_patalogi',
                'status_laporan_penuh_jabatan_patalogi' => 'status_laporan_penuh_jabatan_patalogi',
                'tarikh_laporan_penuh_jabatan_patalogi' => 'tarikh_laporan_penuh_jabatan_patalogi',
                'keputusan_laporan_jabatan_patalogi' => 'keputusan_laporan_jabatan_patalogi',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',
                
                // New simplified Imigresen fields
                'permohonan_laporan_pengesahan_masuk_keluar_malaysia' => 'permohonan_laporan_pengesahan_masuk_keluar_malaysia',
                'permohonan_laporan_permit_kerja_di_malaysia' => 'permohonan_laporan_permit_kerja_di_malaysia',
                'permohonan_laporan_agensi_pekerjaan_di_malaysia' => 'permohonan_laporan_agensi_pekerjaan_di_malaysia',
                'permohonan_status_kewarganegaraan' => 'permohonan_status_kewarganegaraan',
                
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar' => 'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar',
                'status_barang_kes_arahan_tpr' => 'status_barang_kes_arahan_tpr',
                'adakah_muka_surat_4_keputusan_kes_dicatat' => 'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan',
                'adakah_ks_kus_fail_selesai' => 'adakah_ks_kus_fail_selesai',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'ulasan_keseluruhan_pegawai_pemeriksa_fail',
            ],
        ],
    ];

       public function __construct(int $projectId, int $userId, string $paperType, string $duplicateHandling = 'update', bool $confirmOverwrite = false)
    {
        if (!isset(self::$paperConfig[$paperType])) {
            throw new \InvalidArgumentException("Invalid paper type specified: {$paperType}");
        }
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->paperType = $paperType;
        $this->duplicateHandling = $duplicateHandling;
        $this->confirmOverwrite = $confirmOverwrite;
        $this->config = self::$paperConfig[$paperType];
        $this->modelClass = $this->config['model'];
    }

    // *** NORMALIZATION FUNCTION ***
    /**
     * Normalizes a header by converting it to snake_case.
     * e.g., "NO. KERTAS SIASATAN" becomes "no_kertas_siasatan".
     * e.g., "NO. FAIL L.M.M.(T)" becomes "no_fail_lmm_t".
     */
// AFTER (This now handles empty columns without crashing)
private function normalizeHeader(?string $header): string
{
    // If the input is null or an empty string, return an empty string immediately.
    if (empty($header)) {
        return '';
    }

    // 1. Convert the entire header to lowercase FIRST. This is the crucial step.
    $header = strtolower($header);
    
    // 2. Replace dots, parentheses, and multiple spaces with a single space.
    $header = preg_replace('/[\.\(\)]+|\s+/', ' ', $header);
    
    // 3. Trim and convert to snake_case. This is now safe for all cases.
    return Str::snake(trim($header));
}


public function registerEvents(): array
{
    return [
        BeforeImport::class => function (BeforeImport $event) {

            $validUniqueHeaderKeys = ['no_kertas_siasatan', 'iprs_no_kertas_siasatan'];

            // Get the raw headers from the first row of the file
            $rawHeaders = $event->getReader()->getActiveSheet()->toArray()[0] ?? [];

            // Check if the file has any headers at all
            if (empty($rawHeaders)) {
                throw ValidationException::withMessages(['excel_file' => 'Import gagal. Fail Excel tidak mempunyai sebarang lajur pengepala (header).']);
            }
            
            // Filter out any null/empty headers to prevent errors
            $filteredRawHeaders = array_filter($rawHeaders);
            
            // Normalize the valid headers from the file
            $normalizedActualHeaders = array_map([$this, 'normalizeHeader'], $filteredRawHeaders);


            $intersection = array_intersect($validUniqueHeaderKeys, $normalizedActualHeaders);

            if (empty($intersection)) {
                // If the intersection is empty, it means NONE of our required headers were found.

                // Create a user-friendly list of the headers we were looking for.
                $friendlyHeaderList = "'No. Kertas Siasatan' atau 'IPRS: No. Kertas Siasatan'";
                
                // Show the user the exact raw headers that the system found in their file.
                $foundHeadersString = implode(', ', $filteredRawHeaders);
                
                $message = "Import gagal. Lajur pengenalan unik ({$friendlyHeaderList}) tidak dijumpai. " .
                           "Lajur yang dijumpai dalam fail anda ialah: [{$foundHeadersString}]. " .
                           "Sila pastikan salah satu lajur mandatori tersebut wujud dalam fail anda.";
                
                // Log for debugging
                Log::warning('Import validation failed. None of the required unique headers were found. Valid options: ' . implode(', ', $validUniqueHeaderKeys) . '. Found normalized headers: ' . implode(', ', $normalizedActualHeaders));
                
                throw ValidationException::withMessages(['excel_file' => $message]);
            }
        },
    ];
}

public function collection(Collection $rows)
{
    // These initial lines remain the same
    $uniqueDbColumn = $this->config['unique_by'];
    $columnMap = $this->config['column_map'];
    $modelCasts = (new $this->modelClass)->getCasts();
    $rowNumber = 2;

    foreach ($rows as $row) {
        $normalizedRow = new Collection();
        foreach ($row as $originalHeader => $value) {
            if ($originalHeader !== null) {
                $normalizedKey = $this->normalizeHeader((string)$originalHeader);
                $normalizedRow->put($normalizedKey, $value);
            }
        }

        $dataForDb = ['project_id' => $this->projectId];
        $uniqueValue = null;
        $uniqueExcelHeaderSnake = null;
        $possibleUniqueKeys = ['no_kertas_siasatan_b1', 'no_kertas_siasatan', 'iprs_no_kertas_siasatan'];

        foreach ($possibleUniqueKeys as $key) {
            $value = $normalizedRow->get($key);
            if (!empty($value)) {
                $uniqueValue = $value;
                $uniqueExcelHeaderSnake = $key;
                break;
            }
        }
        
        if (empty($uniqueValue)) {
            $this->skippedRows[] = "Row {$rowNumber}: Skipped because the required identifier 'No. Kertas Siasatan' is missing or empty.";
            $this->skippedCount++;
            $rowNumber++;
            continue;
        }

        foreach ($columnMap as $excelHeaderKey => $dbColumn) {
            if ($normalizedRow->has($excelHeaderKey) && !isset($dataForDb[$dbColumn])) {
                $value = $normalizedRow->get($excelHeaderKey);
                $castType = $modelCasts[$dbColumn] ?? 'string';
                if (in_array($dbColumn, ['status_rj2', 'status_rj2b', 'status_rj9', 'status_rj99', 'status_rj10a', 'status_rj10b'])) {
                    $dataForDb[$dbColumn] = $this->transformThreeStateField($value);
                } elseif (in_array($dbColumn, ['status_semboyan_pertama_wanted_person', 'status_semboyan_kedua_wanted_person', 'status_semboyan_ketiga_wanted_person'])) {
                    $dataForDb[$dbColumn] = $this->transformThreeStateStringField($value);
                } elseif ($dbColumn === 'status_laporan_penuh_pakar_judi') {
                    $dataForDb[$dbColumn] = $this->transformPakarJudiLaporanField($value);
                } else {
                    switch ($castType) {
                        case 'date:Y-m-d': case 'date': case 'datetime': $dataForDb[$dbColumn] = $this->transformDate($value); break;
                        case 'boolean': $dataForDb[$dbColumn] = $this->transformBoolean($value); break;
                        case 'decimal:2': $dataForDb[$dbColumn] = $this->transformDecimal($value); break;
                        case 'array': case 'json': $dataForDb[$dbColumn] = $this->transformJsonArray($value); break;
                        default: $dataForDb[$dbColumn] = is_string($value) ? trim($value) : $value; break;
                    }
                }
            }
        }

        // --- CHANGE #1: The duplicate check now uses an OR query ---
        // It checks if the unique value matches in EITHER of the key columns.
        $existingRecord = $this->modelClass::where('project_id', $this->projectId)
            ->where(function ($query) use ($uniqueValue) {
                $query->where('no_kertas_siasatan', $uniqueValue)
                      ->orWhere('iprs_no_kertas_siasatan', $uniqueValue);
            })
            ->first();
        
        // This duplicate handling logic remains the same and now works correctly
        if ($existingRecord) {
            switch ($this->duplicateHandling) {
                case 'detect':
                    $uniqueColumnDisplay = $this->getDisplayColumnName($uniqueExcelHeaderSnake);
                    $this->duplicateRecords[] = [ 'row_number' => $rowNumber, 'unique_column' => $uniqueColumnDisplay, 'unique_value' => $uniqueValue, 'data' => $dataForDb ];
                    $rowNumber++;
                    continue 2;
                case 'skip':
                    $uniqueColumnDisplay = $this->getDisplayColumnName($uniqueExcelHeaderSnake);
                    $this->skippedRows[] = "Row {$rowNumber}: Record with {$uniqueColumnDisplay} '{$uniqueValue}' already exists in the system.";
                    $this->skippedCount++;
                    $rowNumber++;
                    continue 2;
                case 'fill_empty':
                    // This logic remains the same
                    $rowNumber++;
                    continue 2;
                case 'update':
                default:
                    break;
            }
        } else {
            if ($this->duplicateHandling === 'detect') {
                $this->newRecordsCount++;
                $rowNumber++;
                continue;
            }
        }
        
        // --- CHANGE #2: Replace `updateOrCreate` with manual if/else logic ---
        if ($existingRecord) {
            // --- UPDATE PATH ---
            $originalData = $existingRecord->getOriginal();
            $existingRecord->update($dataForDb);
            $this->updatedCount++;

            // Track which fields were actually changed
            $changedFields = [];
            foreach ($dataForDb as $field => $newValue) {
                if ($this->valuesAreDifferent($originalData[$field] ?? null, $newValue)) {
                    $changedFields[] = $this->getDisplayColumnName($field);
                }
            }
            if (!empty($changedFields)) {
                $this->updatedRecords[] = [ 'unique_value' => $uniqueValue, 'row_number' => $rowNumber, 'changed_fields' => $changedFields ];
            }

        } else {
            // --- CREATE PATH ---
            // Manually add the unique identifier to the data before creating.
            // This ensures that even if the Excel header was 'iprs_no_kertas_siasatan',
            // the value gets saved to the primary unique column.
            $dataForDb[$uniqueDbColumn] = $uniqueValue;
            $this->modelClass::create($dataForDb);
            $this->createdCount++;
        }
        
        $rowNumber++;
    }
    
    $this->successCount = $this->createdCount + $this->updatedCount;
}


// FILE: app/Imports/PaperImport.php

private function transformDate($value)
{
    // Return null immediately if the value is empty, null, or just whitespace.
    if (empty(trim((string)$value))) {
        return null;
    }

    // 1. First, try to handle Excel's numeric date format (e.g., 45641)
    if (is_numeric($value)) {
        try {
            // Attempt to convert from Excel's integer format to a DateTime object
            $dateTimeObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $dateTimeObject->format('Y-m-d');
        } catch (\Exception $e) {
            // It's a number, but not a valid Excel date.
            // We'll let it fall through to be treated as a string.
        }
    }

    // 2. If it's a string, trim whitespace.
    $dateString = trim((string) $value);

    // 3. Explicitly try to parse the most common user format first: d/m/Y
    try {
        // The '|' character resets the time to 00:00:00, which is good practice for date-only fields.
        $date = Carbon::createFromFormat('d/m/Y|', $dateString);
        if ($date !== false) {
            return $date->format('Y-m-d'); // Return in the correct database format
        }
    } catch (\Exception $e) {
        // It wasn't in d/m/Y format, so we'll try other formats.
    }
    
    // 4. As a fallback, try Laravel's more general parser for other formats (like Y-m-d)
    try {
        return Carbon::parse($dateString)->format('Y-m-d');
    } catch (\Exception $e) {
        // If all attempts fail, log the problematic value and return null.
        Log::warning("Could not parse date format during import for value: '{$dateString}'. Error: " . $e->getMessage());
        return null; 
    }
}

    private function transformBoolean($value)
    {
        if (is_null($value) || $value === '') return null;
        
        $value = is_string($value) ? trim(strtolower($value)) : $value;
        
        if (in_array($value, ['ya', 'yes', 'true', '1', 1, 'ada', 'cipta', 'dibuat', 'diterima', 'dikemaskini'])) {
            return true;
        } elseif (in_array($value, ['tidak', 'no', 'false', '0', 0, 'tiada', 'tidak cipta', 'tidak dibuat', 'tidak diterima', 'tidak dikemaskini'])) {
            return false;
        }
        
        return null;
    }

    private function transformDecimal($value)
    {
        if (is_null($value) || $value === '') return null;
        
        $value = preg_replace('/[RM\s,]/', '', $value);
        
        return is_numeric($value) ? (float) $value : null;
    }

    private function transformJsonArray($value)
    {
        if (is_null($value) || $value === '') return null;
        
        if (is_string($value)) {
            // Trim the value first
            $value = trim($value);
            
            // If it's already a JSON string, try to decode and re-encode to ensure proper format
            if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return json_encode($decoded); // Return the properly formatted JSON
                }
            }
            
            // Check if it contains commas (multiple values)
            if (str_contains($value, ',')) {
                $items = array_map('trim', explode(',', $value));
                return json_encode(array_filter($items, function($item) {
                    return $item !== '';
                }));
            }
            
            // Single value - convert to JSON array
            return json_encode([$value]);
        }
        
        if (is_array($value)) {
            return json_encode($value);
        }
        
        return null;
    }

    
    /**
     * Transform three-state field values for RJ fields
     * Handles: 0 = Tiada/Tidak Cipta, 1 = Ada/Cipta, 2 = Tidak Berkaitan
     */
    private function transformThreeStateField($value)
    {
        if (is_null($value) || $value === '') return 0; // Default to Tiada
        
        $value = is_string($value) ? trim(strtolower($value)) : $value;
        
        // Handle "Ada/Cipta" variants (value = 1)
        if (in_array($value, ['ada', 'cipta', 'ya', 'yes', '1', 1, 'ada/cipta', 'dicipta'])) {
            return 1;
        }
        
        // Handle "Tidak Berkaitan" variants (value = 2)
        if (in_array($value, ['tidak berkaitan', 'tidak_berkaitan', 'tidak-berkaitan', '2', 2, 'n/a', 'na'])) {
            return 2;
        }
        
        // Default to "Tiada/Tidak Cipta" (value = 0)
        return 0;
    }

    private function transformThreeStateStringField($value)
    {
        if (is_null($value) || $value === '') return 'Tiada / Tidak Cipta'; // Default
        
        $value = is_string($value) ? trim(strtolower($value)) : $value;
        
        // Handle "Ada / Cipta" variants
        if (in_array($value, ['ada', 'cipta', 'ya', 'yes', '1', 1, 'ada/cipta', 'ada / cipta', 'dicipta'])) {
            return 'Ada / Cipta';
        }
        
        // Handle "Tidak Berkaitan" variants
        if (in_array($value, ['tidak berkaitan', 'tidak_berkaitan', 'tidak-berkaitan', '2', 2, 'n/a', 'na'])) {
            return 'Tidak Berkaitan';
        }
        
        // Default to "Tiada / Tidak Cipta"
        return 'Tiada / Tidak Cipta';
    }

    private function transformPakarJudiLaporanField($value)
    {
        if (is_null($value) || $value === '') return 'Tidak Diterima'; // Default
        
        $value = is_string($value) ? trim(strtolower($value)) : $value;
        
        // Handle "Diterima" variants
        if (in_array($value, ['diterima', 'ada', 'yes', 'ya', '1', 1, 'received'])) {
            return 'Diterima';
        }
        
        // Handle "Masih Menunggu" variants
        if (in_array($value, ['masih menunggu laporan pakar judi', 'menunggu', 'pending', 'waiting', '2', 2, 'masih menunggu'])) {
            return 'Masih Menunggu Laporan Pakar Judi';
        }
        
        // Default to "Tidak Diterima"
        return 'Tidak Diterima';
    }

    private function valuesAreDifferent($oldValue, $newValue)
    {
        // Handle null comparisons
        if (is_null($oldValue) && is_null($newValue)) {
            return false;
        }
        
        if (is_null($oldValue) || is_null($newValue)) {
            return true;
        }
        
        // Handle date comparisons - convert both to same format
        if ($oldValue instanceof \Carbon\Carbon && !is_null($newValue)) {
            $oldValue = $oldValue->format('Y-m-d');
        }
        
        // Convert both to strings for comparison
        return trim((string) $oldValue) !== trim((string) $newValue);
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getUpdatedRecords(): array
    {
        return $this->updatedRecords;
    }

    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }
    
    public function getDuplicateRecords(): array
    {
        return $this->duplicateRecords;
    }
    
    public function getNewRecordsCount(): int
    {
        return $this->newRecordsCount;
    }
    
    /**
     * Get a user-friendly display name for column headers
     */
    private function getDisplayColumnName(string $columnName): string
    {
        $displayNames = [
            'no_kertas_siasatan' => 'No. Kertas Siasatan',
            'no_fail_lmm_t' => 'No. Fail L.M.M.(T)',
            'no_fail_lmm_sdr' => 'No. Fail L.M.M.(SDR)',
            'no_repot_polis' => 'No. Repot Polis',
            'pegawai_penyiasat' => 'Pegawai Penyiasat',
            'tarikh_laporan_polis_dibuka' => 'Tarikh Laporan Polis Dibuka',
            'seksyen' => 'Seksyen'
        ];
        
        return $displayNames[$columnName] ?? ucwords(str_replace('_', ' ', $columnName));
    }
}