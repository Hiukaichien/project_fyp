<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\TrafikSeksyen;
use App\Models\TrafikRule;
use App\Models\OrangHilang;
use App\Models\LaporanMatiMengejut;
use App\Models\Komersil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a default user and get the user object
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
          'superadmin' => 'no', // Default user is not a superadmin
            ]
        );
        
         // Create a default superadmin user
        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'superadmin' => 'yes',
            ]
        );


        // 2. Create a default project and associate it with the new user
        $project = Project::updateOrCreate(
            ['name' => 'Projek Siasatan 1'],
            [
                'user_id' => $user->id, // <-- THE FIX: Add the user's ID
                'project_date' => '2024-07-01',
                'description' => 'Projek rintis untuk pengauditan dan kawal selia kertas siasatan.',
            ]
        );

            // 3. Seed Jenayah Papers (20 records) - Updated to follow TrafikSeksyen structure
                for ($i = 1; $i <= 10; $i++) {
            Jenayah::updateOrCreate(
                [
                    // This is the unique key used to find the record
                    'no_kertas_siasatan' => 'JNY/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'
                ],
                [
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/CRIME/' . str_pad(1000 + $i, 5, '0', STR_PAD_LEFT) . '/24',
                    'pegawai_penyiasat' => 'INSPEKTOR KAMAL',
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subWeeks($i),
                    'seksyen' => '420 Kanun Keseksaan', // A static example for Cheating
                    'pegawai_pemeriksa' => 'ASP ROHANI', // From Bahagian 2, but good to have a name
                ]
            );
        }


        // 4. Seed Narkotik Papers (20 records) - Basic fields from actual migration
        for ($i = 1; $i <= 20; $i++) {
            Narkotik::updateOrCreate(
                ['no_kertas_siasatan' => 'NAR/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/NARKOTIK/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT MAJID', 'INSP TAN', 'SGT AMIN', 'ASP JULIE', 'INSP AZMI'][array_rand(['SGT MAJID', 'INSP TAN', 'SGT AMIN', 'ASP JULIE', 'INSP AZMI'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['12(2) APJ 1987', '15(1)(a) APJ 1987', '39B APJ 1987', '6 APJ 1987'][array_rand(['12(2) APJ 1987', '15(1)(a) APJ 1987', '39B APJ 1987', '6 APJ 1987'])],
                    
                    // BAHAGIAN 2: Pemeriksaan & Status
                    'pegawai_pemeriksa' => ['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'][array_rand(['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'])],
                    'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 15)),
                    'tarikh_edaran_minit_ks_kedua' => Carbon::now()->subMonths(rand(1, 5))->subDays(rand(1, 10)),
                    'tarikh_edaran_minit_ks_sebelum_akhir' => Carbon::now()->subMonths(rand(1, 4))->subDays(rand(1, 8)),
                    'tarikh_edaran_minit_ks_akhir' => Carbon::now()->subMonths(rand(1, 3))->subDays(rand(1, 5)),
                ]
            );
        }

        // 5. Seed Komersil Papers (20 records) - Enhanced with comprehensive data
        for ($i = 1; $i <= 20; $i++) {
            $barangKesTypes = ['Wang Tunai', 'Emas', 'Kenderaan', 'Peralatan Elektronik', 'Dokumen'];
            $bankNames = ['Bank Islam', 'Maybank', 'CIMB Bank', 'Public Bank', 'RHB Bank'];
            $telcoNames = ['Celcom', 'Maxis', 'Digi', 'U Mobile', 'TM'];
            $keputusanMahkamah = ['Sabit', 'Tidak Sabit', 'Bebas', 'Bersalah', 'Tidak Bersalah'];
            
            Komersil::updateOrCreate(
                ['no_kertas_siasatan' => 'KML/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/REP/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                    'pegawai_penyiasat' => ['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'][array_rand(['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'][array_rand(['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'])],

                    // BAHAGIAN 2: Pemeriksaan JIPS
                    'pegawai_pemeriksa' => ['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'][array_rand(['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'])],
                    'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 15)),
                    'tarikh_edaran_minit_ks_kedua' => Carbon::now()->subMonths(rand(1, 5))->subDays(rand(1, 10)),
                    'tarikh_edaran_minit_ks_sebelum_akhir' => Carbon::now()->subMonths(rand(1, 4))->subDays(rand(1, 8)),
                    'tarikh_edaran_minit_ks_akhir' => Carbon::now()->subMonths(rand(1, 3))->subDays(rand(1, 5)),
                    'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => Carbon::now()->subMonths(rand(1, 2))->subDays(rand(1, 3)),

                    // BAHAGIAN 3: Arahan SIO & Ketua
                    'arahan_minit_oleh_sio_status' => rand(0, 1),
                    'arahan_minit_oleh_sio_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'arahan_minit_ketua_bahagian_status' => rand(0, 1),
                    'arahan_minit_ketua_bahagian_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 25)) : null,
                    'arahan_minit_ketua_jabatan_status' => rand(0, 1),
                    'arahan_minit_ketua_jabatan_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 20)) : null,
                    'arahan_minit_oleh_ya_tpr_status' => rand(0, 1),
                    'arahan_minit_oleh_ya_tpr_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 15)) : null,
                    'keputusan_siasatan_oleh_ya_tpr' => ['Tuduh', 'Tidak Tuduh', 'Siasatan Lanjut'][array_rand(['Tuduh', 'Tidak Tuduh', 'Siasatan Lanjut'])],
                    'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => 'Ya',
                    'ulasan_keputusan_siasatan_tpr' => 'Siasatan telah dilakukan dengan teliti dan mengikut prosedur yang betul.',
                    'ulasan_keseluruhan_pegawai_pemeriksa' => 'Kertas siasatan lengkap dan mengikut format yang ditetapkan.',

                    // BAHAGIAN 4: Barang Kes
                    'adakah_barang_kes_didaftarkan' => rand(0, 1),
                    'no_daftar_barang_kes_am' => 'BK/AM/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/24',
                    'no_daftar_barang_kes_berharga' => rand(0, 1) ? 'BK/BH/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/24' : null,
                    'no_daftar_barang_kes_kenderaan' => rand(0, 1) ? 'BK/KD/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/24' : null,
                    'jenis_barang_kes_am' => $barangKesTypes[array_rand($barangKesTypes)],
                    'jenis_barang_kes_berharga' => rand(0, 1) ? $barangKesTypes[array_rand($barangKesTypes)] : null,
                    'jenis_barang_kes_kenderaan' => rand(0, 1) ? 'Kereta/Motosikal/Lori' : null,
                    'status_pergerakan_barang_kes' => 'Simpanan Stor Ekshibit',
                    'status_barang_kes_selesai_siasatan' => 'Dikembalikan Kepada Pemilik',
                    'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'Dilelong',
                    'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'Ya',
                    'resit_kew_38e_bagi_pelupusan' => 'Ada Dilampirkan',
                    'adakah_borang_serah_terima_pegawai_tangkapan' => 'Ada Dilampirkan',
                    'adakah_borang_serah_terima_pemilik_saksi' => 'Ada Dilampirkan',
                    'adakah_sijil_surat_kebenaran_ipo' => rand(0, 1),
                    'adakah_gambar_pelupusan' => 'Ada Dilampirkan',
                    'status_saman_pdrm_s_257' => rand(0, 1),
                    'no_saman_pdrm_s_257' => rand(0, 1) ? 'S257/' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . '/24' : null,
                    'status_saman_pdrm_s_167' => rand(0, 1),
                    'no_saman_pdrm_s_167' => rand(0, 1) ? 'S167/' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . '/24' : null,

                    // BAHAGIAN 5: Bukti & Rajah
                    'status_id_siasatan_dikemaskini' => rand(0, 1),
                    'status_rajah_kasar_tempat_kejadian' => rand(0, 1),
                    'status_gambar_tempat_kejadian' => rand(0, 1),
                    'status_gambar_barang_kes_am' => rand(0, 1),
                    'status_gambar_barang_kes_berharga' => rand(0, 1),
                    'status_gambar_barang_kes_kenderaan' => rand(0, 1),
                    'status_gambar_barang_kes_darah' => rand(0, 1),
                    'status_gambar_barang_kes_kontraban' => rand(0, 1),

                    // BAHAGIAN 6: Laporan RJ & Semboyan
                    'status_pem' => json_encode(['PEM 1', 'PEM 2']),
                    'status_rj2' => rand(0, 1),
                    'tarikh_rj2' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 60)) : null,
                    'status_rj2b' => rand(0, 1),
                    'tarikh_rj2b' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 55)) : null,
                    'status_rj9' => rand(0, 1),
                    'tarikh_rj9' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 50)) : null,
                    'status_rj99' => rand(0, 1),
                    'tarikh_rj99' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 45)) : null,
                    'status_rj10a' => rand(0, 1),
                    'tarikh_rj10a' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 40)) : null,
                    'status_rj10b' => rand(0, 1),
                    'tarikh_rj10b' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 35)) : null,
                    'lain_lain_rj_dikesan' => 'RJ tambahan sekiranya diperlukan untuk kes ini.',
                    'status_semboyan_pertama_wanted_person' => rand(0, 1),
                    'tarikh_semboyan_pertama_wanted_person' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'status_semboyan_kedua_wanted_person' => rand(0, 1),
                    'tarikh_semboyan_kedua_wanted_person' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 25)) : null,
                    'status_semboyan_ketiga_wanted_person' => rand(0, 1),
                    'tarikh_semboyan_ketiga_wanted_person' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 20)) : null,
                    'status_penandaan_kelas_warna' => rand(0, 1),

                    // BAHAGIAN 7: Laporan E-FSA & Agensi Luar
                    'status_permohonan_laporan_post_mortem_mayat' => rand(0, 1),
                    'tarikh_permohonan_laporan_post_mortem_mayat' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 90)) : null,
                    
                    // E-FSA Bank Records (Now using string values)
                    'status_permohonan_E_FSA_1_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_bank_permohonan_E_FSA_1' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'status_laporan_penuh_E_FSA_1_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_bank_laporan_E_FSA_1_oleh_IO_AIO' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_1_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 60)) : null,

                    'status_permohonan_E_FSA_2_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_bank_permohonan_E_FSA_2_BANK' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'status_laporan_penuh_E_FSA_2_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_bank_laporan_E_FSA_2_oleh_IO_AIO' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_2_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 55)) : null,

                    'status_permohonan_E_FSA_3_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_bank_permohonan_E_FSA_3_BANK' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'status_laporan_penuh_E_FSA_3_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_bank_laporan_E_FSA_3_oleh_IO_AIO' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_3_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 52)) : null,

                    'status_permohonan_E_FSA_4_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_bank_permohonan_E_FSA_4_BANK' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'status_laporan_penuh_E_FSA_4_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_bank_laporan_E_FSA_4_oleh_IO_AIO' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_4_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 48)) : null,

                    'status_permohonan_E_FSA_5_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_bank_permohonan_E_FSA_5_BANK' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'status_laporan_penuh_E_FSA_5_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_bank_laporan_E_FSA_5_oleh_IO_AIO' => rand(0, 1) ? $bankNames[array_rand($bankNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_5_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 45)) : null,

                    // E-FSA Telco Records (Now using string values)
                    'status_permohonan_E_FSA_1_telco_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_telco_permohonan_E_FSA_1_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'status_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_telco_laporan_E_FSA_1_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_1_telco_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 50)) : null,

                    'status_permohonan_E_FSA_2_telco_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_telco_permohonan_E_FSA_2_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'status_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_telco_laporan_E_FSA_2_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_2_telco_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 47)) : null,

                    'status_permohonan_E_FSA_3_telco_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_telco_permohonan_E_FSA_3_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'status_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_telco_laporan_E_FSA_3_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_3_telco_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 44)) : null,

                    'status_permohonan_E_FSA_4_telco_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_telco_permohonan_E_FSA_4_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'status_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_telco_laporan_E_FSA_4_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_4_telco_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 41)) : null,

                    'status_permohonan_E_FSA_5_telco_oleh_IO_AIO' => ['Dibuat', 'Tidak', null][array_rand(['Dibuat', 'Tidak', null])],
                    'nama_telco_permohonan_E_FSA_5_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'status_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO' => ['Diterima', 'Tidak', null][array_rand(['Diterima', 'Tidak', null])],
                    'nama_telco_laporan_E_FSA_5_oleh_IO_AIO' => rand(0, 1) ? $telcoNames[array_rand($telcoNames)] : null,
                    'tarikh_laporan_penuh_E_FSA_5_telco_oleh_IO_AIO' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 38)) : null,

                    // Other Agency Reports
                    'status_permohonan_laporan_puspakom' => rand(0, 1),
                    'tarikh_permohonan_laporan_puspakom' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 45)) : null,
                    'status_laporan_penuh_puspakom' => rand(0, 1),
                    'tarikh_laporan_penuh_puspakom' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 40)) : null,

                    'status_permohonan_laporan_jkr' => rand(0, 1),
                    'tarikh_permohonan_laporan_jkr' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 35)) : null,
                    'status_laporan_penuh_jkr' => rand(0, 1),
                    'tarikh_laporan_penuh_jkr' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,

                    'status_permohonan_laporan_jpj' => rand(0, 1),
                    'tarikh_permohonan_laporan_jpj' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 25)) : null,
                    'status_laporan_penuh_jpj' => rand(0, 1),
                    'tarikh_laporan_penuh_jpj' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 20)) : null,

                    'status_permohonan_laporan_imigresen' => rand(0, 1),
                    'tarikh_permohonan_laporan_imigresen' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 15)) : null,
                    'status_laporan_penuh_imigresen' => rand(0, 1),
                    'tarikh_laporan_penuh_imigresen' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 10)) : null,

                    'status_permohonan_laporan_kastam' => rand(0, 1),
                    'tarikh_permohonan_laporan_kastam' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 12)) : null,
                    'status_laporan_penuh_kastam' => rand(0, 1),
                    'tarikh_laporan_penuh_kastam' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 8)) : null,

                    'status_permohonan_laporan_forensik_pdrm' => rand(0, 1),
                    'tarikh_permohonan_laporan_forensik_pdrm' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 18)) : null,
                    'status_laporan_penuh_forensik_pdrm' => rand(0, 1),
                    'tarikh_laporan_penuh_forensik_pdrm' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 14)) : null,
                    'jenis_barang_kes_forensik' => 'DNA, Cap Jari, Serbuk Mesiu',

                    // BAHAGIAN 8: Status Fail
                    'muka_surat_4_barang_kes_ditulis' => rand(0, 1),
                    'muka_surat_4_dengan_arahan_tpr' => rand(0, 1),
                    'muka_surat_4_keputusan_kes_dicatat' => rand(0, 1),
                    'fail_lmm_ada_keputusan_koroner' => rand(0, 1),
                    'status_kus_fail' => rand(0, 1),
                    'keputusan_akhir_mahkamah' => $keputusanMahkamah[array_rand($keputusanMahkamah)],
                    'ulasan_pegawai_pemeriksa_fail' => 'Fail kertas siasatan telah lengkap dan disimpan mengikut prosedur yang ditetapkan.',
                ]
            );
        }

        // 6. Seed Trafik Seksyen Papers (20 records) - Basic fields only
        for ($i = 1; $i <= 20; $i++) {
            TrafikSeksyen::updateOrCreate(
                ['no_kertas_siasatan' => 'TRFS/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT RAHMAN', 'INSP LILY', 'SGT AZMAN', 'ASP WONG', 'INSP SITI'][array_rand(['SGT RAHMAN', 'INSP LILY', 'SGT AZMAN', 'ASP WONG', 'INSP SITI'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987', '44(1) APJ 1987'][array_rand(['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987', '44(1) APJ 1987'])],
                    
                    // BAHAGIAN 2: Pemeriksaan & Status
                    'pegawai_pemeriksa' => ['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'][array_rand(['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'])],
                    'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 15)),
                    'tarikh_edaran_minit_ks_kedua' => Carbon::now()->subMonths(rand(1, 5))->subDays(rand(1, 10)),
                    'tarikh_edaran_minit_ks_sebelum_akhir' => Carbon::now()->subMonths(rand(1, 4))->subDays(rand(1, 8)),
                    'tarikh_edaran_minit_ks_akhir' => Carbon::now()->subMonths(rand(1, 3))->subDays(rand(1, 5)),
                ]
            );
        }

        // 7. Seed Trafik Rule Papers (20 records) - Basic fields only
        for ($i = 1; $i <= 20; $i++) {
            TrafikRule::updateOrCreate(
                ['no_kertas_siasatan' => 'TRFR/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    'no_fail_lmm_t' => 'LMM(T)/' . rand(100, 999) . '/' . date('y'),
                    'no_repot_polis' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT FARID', 'INSP MAYA', 'SGT ROSLI', 'ASP DAVID', 'INSP KHADIJAH'][array_rand(['SGT FARID', 'INSP MAYA', 'SGT ROSLI', 'ASP DAVID', 'INSP KHADIJAH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59', 'R.18 LN 166/59'][array_rand(['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59', 'R.18 LN 166/59'])],
                ]
            );
        }
        
        // 8. Seed Orang Hilang Papers (15 records) - Basic fields only
        for ($i = 1; $i <= 15; $i++) {
            OrangHilang::updateOrCreate(
                ['no_kertas_siasatan' => 'OH/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/CID/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT FATIMAH', 'INSP KAMAL', 'SGT ROZANA', 'ASP CHONG', 'INSP RAVI'][array_rand(['SGT FATIMAH', 'INSP KAMAL', 'SGT ROZANA', 'ASP CHONG', 'INSP RAVI'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => '365 KANUN KESEKSAAN',
                    'pegawai_pemeriksa' => ['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'][array_rand(['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'])],
                    'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 15)),
                ]
            );
        }

        // 9. Seed Laporan Mati Mengejut Papers (10 records) - Basic fields only
        for ($i = 1; $i <= 10; $i++) {
            LaporanMatiMengejut::updateOrCreate(
                ['no_kertas_siasatan' => 'LMM/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    'project_id' => $project->id,
                    'no_fail_lmm_sdr' => 'LMM/SDR/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24',
                    'no_repot_polis' => 'IPD/CID/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT AZLINA', 'INSP HAFIZ', 'SGT KAMARUL', 'ASP LINA', 'INSP SURESH'][array_rand(['SGT AZLINA', 'INSP HAFIZ', 'SGT KAMARUL', 'ASP LINA', 'INSP SURESH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => '174 KANUN ACARA JENAYAH',
                    'pegawai_pemeriksa' => ['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'][array_rand(['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'])],
                    'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 15)),
                    'tarikh_edaran_minit_ks_kedua' => Carbon::now()->subMonths(rand(1, 5))->subDays(rand(1, 10)),
                    'tarikh_edaran_minit_ks_sebelum_akhir' => Carbon::now()->subMonths(rand(1, 4))->subDays(rand(1, 8)),
                    'tarikh_edaran_minit_ks_akhir' => Carbon::now()->subMonths(rand(1, 3))->subDays(rand(1, 5)),
                ]
            );
        }
    }
}