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
    protected $modelClass;
    private $config;
    
    private $successCount = 0;
    private $skippedRows = [];

    private static $paperConfig = [
         'Jenayah' => [
            'model'       => \App\Models\Jenayah::class,
            'unique_by'   => 'no_kertas_siasatan',
            'column_map'  => [
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_fail_lmm_t' => 'no_fail_lmm_t',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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
                'status_rj10b' => 'status_rj10b',
                'tarikh_rj10b' => 'tarikh_rj10b',
                'lain_lain_rj_dikesan' => 'lain_lain_rj_dikesan',
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
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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

                // BAHAGIAN 7: Permohonan Laporan Agensi Luar
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',

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
                // BAHAGIAN 1: Maklumat Asas
                'no_kertas_siasatan' => 'no_kertas_siasatan',
                'no_fail_lmm_sdr' => 'no_fail_lmm_sdr',
                'no_repot_polis' => 'no_repot_polis',
                'pegawai_penyiasat' => 'pegawai_penyiasat',
                'tarikh_laporan_polis_dibuka' => 'tarikh_laporan_polis_dibuka',
                'seksyen' => 'seksyen',

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
                'fail_lmm_bahagian_pengurusan_pada_muka_surat_2' => 'fail_lmm_bahagian_pengurusan_pada_muka_surat_2',

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
                'status_permohonan_laporan_imigresen' => 'status_permohonan_laporan_imigresen',
                'tarikh_permohonan_laporan_imigresen' => 'tarikh_permohonan_laporan_imigresen',
                'status_laporan_penuh_imigresen' => 'status_laporan_penuh_imigresen',
                'tarikh_laporan_penuh_imigresen' => 'tarikh_laporan_penuh_imigresen',
                'lain_lain_permohonan_laporan' => 'lain_lain_permohonan_laporan',

                // BAHAGIAN 8: Status Fail
                'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar' => 'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar',
                'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr' => 'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar_dan_telah_ada_arahan_ya_tpr',
                'adakah_muka_surat_4_keputusan_kes_dicatat' => 'adakah_muka_surat_4_keputusan_kes_dicatat',
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => 'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan',
                'adakah_ks_kus_fail_selesai' => 'adakah_ks_kus_fail_selesai',
                'keputusan_akhir_mahkamah' => 'keputusan_akhir_mahkamah',
                'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'ulasan_keseluruhan_pegawai_pemeriksa_fail',
            ],
        ],
    ];

    public function __construct(int $projectId, int $userId, string $paperType)
    {
        if (!isset(self::$paperConfig[$paperType])) {
            throw new \InvalidArgumentException("Invalid paper type specified: {$paperType}");
        }
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->paperType = $paperType;
        $this->config = self::$paperConfig[$paperType];
        $this->modelClass = $this->config['model'];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $expectedHeaders = array_keys($this->config['column_map']);
                $actualHeaders = array_map(fn($h) => Str::snake(trim($h)), $event->getReader()->getActiveSheet()->toArray()[0]);
                $foundHeaders = array_intersect($expectedHeaders, $actualHeaders);
                if (empty($foundHeaders)) {
                    $message = 'Import gagal. Sila pastikan fail Excel mempunyai sekurang-kurangnya satu lajur yang diperlukan.';
                    throw ValidationException::withMessages(['excel_file' => $message]);
                }
            },
        ];
    }

    public function collection(Collection $rows)
    {
        $uniqueDbColumn = $this->config['unique_by'];
        $uniqueExcelHeaderSnake = array_search($uniqueDbColumn, $this->config['column_map']);
        
        if (!$uniqueExcelHeaderSnake) {
             throw new \Exception("Ralat konfigurasi untuk {$this->paperType}: Pengenal unik '{$uniqueDbColumn}' tidak dijumpai.");
        }

        $existingKeys = $this->modelClass::query()
            ->whereHas('project', fn($query) => $query->where('user_id', $this->userId))
            ->pluck($uniqueDbColumn)
            ->all();

        $dataToInsert = [];
        $rowNumber = 2;

        foreach ($rows as $row) {
            $uniqueValue = $row[$uniqueExcelHeaderSnake] ?? null;

            if (empty($uniqueValue)) {
                $this->skippedRows[] = "Baris {$rowNumber}: Dilangkau kerana '{$uniqueExcelHeaderSnake}' tiada.";
                $rowNumber++;
                continue;
            }

            if (in_array($uniqueValue, $existingKeys)) {
                $this->skippedRows[] = "Baris {$rowNumber}: Dilangkau kerana '{$uniqueValue}' sudah wujud.";
                $rowNumber++;
                continue;
            }

            $data = [
                'project_id' => $this->projectId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Get the cast types from the model instance
            $modelCasts = (new $this->modelClass)->getCasts();

            foreach ($this->config['column_map'] as $excelHeaderSnake => $dbColumn) {
                if (isset($row[$excelHeaderSnake])) {
                    $value = $row[$excelHeaderSnake];
                    $castType = $modelCasts[$dbColumn] ?? 'string'; // Default to string if no cast is defined

                    // Transform the value based on its expected cast type
                    switch ($castType) {
                        case 'date:Y-m-d':
                        case 'date':
                        case 'datetime':
                            $data[$dbColumn] = $this->transformDate($value);
                            break;
                        case 'boolean':
                            $data[$dbColumn] = $this->transformBoolean($value);
                            break;
                        case 'decimal:2': // Assuming you might use this format
                            $data[$dbColumn] = $this->transformDecimal($value);
                            break;
                        case 'array':
                        case 'json':
                            $data[$dbColumn] = $this->transformJsonArray($value);
                            break;
                        default:
                            $data[$dbColumn] = is_string($value) ? trim($value) : $value;
                            break;
                    }
                }
            }
            
            $dataToInsert[] = $data;
            $existingKeys[] = $uniqueValue;
            $this->successCount++;
            $rowNumber++;
        }

        if (!empty($dataToInsert)) {
            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                $this->modelClass::insert($chunk);
            }
        }
    }

    private function transformDate($value, $format = 'Y-m-d H:i:s')
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            try { return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format); } catch (\Exception $e) { return null; }
        }
        $formatsToTry = ['d/m/Y', 'd.m.Y', 'd-m-Y', 'm/d/Y', 'm.d.Y', 'm-d-Y', 'Y-m-d H:i:s', 'Y-m-d'];
        foreach ($formatsToTry as $inputFormat) {
            try { return Carbon::createFromFormat($inputFormat, $value)->format($format); } catch (\Exception $e) { continue; }
        }
        try { return Carbon::parse($value)->format($format); } catch (\Exception $e) { Log::warning("Could not parse date format for value: '{$value}'. Error: " . $e->getMessage()); return null; }
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
            $items = array_map('trim', explode(',', $value));
            return json_encode(array_filter($items)); // Encode to JSON string for the database
        }
        
        return null;
    }
    
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }
}