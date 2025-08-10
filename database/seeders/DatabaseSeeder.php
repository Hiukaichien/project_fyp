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
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'username' => 'user',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'superadmin' => 'no', // Default user is not a superadmin
                'can_be_deleted' => true,
                'visible_projects' => null, // Will be set after project creation
            ]
        );
        
         // Create a default superadmin user
        $superadmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'superadmin' => 'yes',
                'can_be_deleted' => false, // Prevent superadmin deletion
                'visible_projects' => null, // Superadmin can see all projects
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

        // Update the regular user's visible_projects to include the project they own
        $user->update([
            'visible_projects' => [$project->id]
        ]);

            // 3. Seed Jenayah Papers (20 records) - Updated to follow TrafikSeksyen structure
                for ($i = 1; $i <= 10; $i++) {
            Jenayah::updateOrCreate(
                [
                    // This is the unique key used to find the record
                    'no_kertas_siasatan' => 'JNY/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'
                ],
                [
                    'project_id' => $project->id,
                    // IPRS Standard Fields
                    'iprs_no_kertas_siasatan' => 'JNY/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24',
                    'iprs_tarikh_ks' => Carbon::now()->subWeeks($i),
                    'iprs_no_repot' => 'IPD/CRIME/' . str_pad(1000 + $i, 5, '0', STR_PAD_LEFT) . '/24',
                    'iprs_jenis_jabatan_ks' => 'Jenayah',
                    'iprs_pegawai_penyiasat' => 'INSPEKTOR KAMAL',
                    'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                    'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                    'iprs_seksyen' => '420 Kanun Keseksaan',
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
                    // IPRS Standard Fields
                    'iprs_no_kertas_siasatan' => 'NAR/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24',
                    'iprs_tarikh_ks' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'iprs_no_repot' => 'IPD/NARKOTIK/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'iprs_jenis_jabatan_ks' => 'Narkotik',
                    'iprs_pegawai_penyiasat' => ['SGT MAJID', 'INSP TAN', 'SGT AMIN', 'ASP JULIE', 'INSP AZMI'][array_rand(['SGT MAJID', 'INSP TAN', 'SGT AMIN', 'ASP JULIE', 'INSP AZMI'])],
                    'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                    'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                    'iprs_seksyen' => ['12(2) APJ 1987', '15(1)(a) APJ 1987', '39B APJ 1987', '6 APJ 1987'][array_rand(['12(2) APJ 1987', '15(1)(a) APJ 1987', '39B APJ 1987', '6 APJ 1987'])],
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

        // 5. Seed Komersil Papers (20 records) - CORRECTED FOR DATA TYPES
        for ($i = 1; $i <= 20; $i++) {
            $barangKesTypes = ['Wang Tunai', 'Emas', 'Kenderaan', 'Peralatan Elektronik', 'Dokumen'];
            $bankNames = ['Bank Islam', 'Maybank', 'CIMB Bank', 'Public Bank', 'RHB Bank'];
            $telcoNames = ['Celcom', 'Maxis', 'Digi', 'U Mobile', 'TM'];
            $keputusanMahkamah = ['Sabit', 'Tidak Sabit', 'Bebas', 'Bersalah', 'Tidak Bersalah'];
            
            Komersil::updateOrCreate(
                ['no_kertas_siasatan' => 'KML/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas (Data types are correct)
                    'project_id' => $project->id,
                    // IPRS Standard Fields
                    'iprs_no_kertas_siasatan' => 'KML/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24',
                    'iprs_tarikh_ks' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'iprs_no_repot' => 'IPD/REP/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                    'iprs_jenis_jabatan_ks' => 'Komersil',
                    'iprs_pegawai_penyiasat' => ['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'][array_rand(['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'])],
                    'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                    'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                    'iprs_seksyen' => ['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'][array_rand(['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'])],
                    'no_repot_polis' => 'IPD/REP/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                    'pegawai_penyiasat' => ['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'][array_rand(['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'][array_rand(['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'])],

                    // BAHAGIAN 2: Pemeriksaan JIPS (Data types are correct)
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
                    'adakah_arahan_tuduh_oleh_ya_tpr_diambil_tindakan' => json_encode(rand(0, 1) ? ['Ya'] : ['Tidak']),
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
                    // OK: This column is STRING in the migration
                    'status_pergerakan_barang_kes' => 'Simpanan Stor Ekshibit',
                    // CHANGED: These columns are now STRING in the migration
                    'status_barang_kes_selesai_siasatan' => 'Dikembalikan Kepada Pemilik',
                    'barang_kes_dilupusan_bagaimana_kaedah_pelupusan_dilaksanakan' => 'Dilelong',
                    'adakah_pelupusan_barang_kes_wang_tunai_ke_perbendaharaan' => 'Ya',
                    'resit_kew_38e_bagi_pelupusan' => 'Ada Dilampirkan',
                    'adakah_borang_serah_terima_pegawai_tangkapan' => 'Ada Dilampirkan',
                    // OK: These columns are STRING and BOOLEAN
                    'adakah_borang_serah_terima_pemilik_saksi' => 'Ada Dilampirkan',
                    'adakah_sijil_surat_kebenaran_ipd' => rand(0, 2),
                    'adakah_gambar_pelupusan' => 'Ada Dilampirkan',
                    'status_saman_pdrm_s_257' => rand(0, 1),
                    'no_saman_pdrm_s_257' => rand(0, 1) ? 'S257/' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . '/24' : null,
                    'status_saman_pdrm_s_167' => rand(0, 1),
                    'no_saman_pdrm_s_167' => rand(0, 1) ? 'S167/' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . '/24' : null,

                    // BAHAGIAN 5: Bukti & Rajah (Data types are correct)
                    'status_id_siasatan_dikemaskini' => rand(0, 1),
                    'status_rajah_kasar_tempat_kejadian' => rand(0, 1),
                    'status_gambar_tempat_kejadian' => rand(0, 1),
                    'status_gambar_barang_kes_am' => rand(0, 1),
                    'status_gambar_barang_kes_berharga' => rand(0, 1),
                    'status_gambar_barang_kes_kenderaan' => rand(0, 1),
                    'status_gambar_barang_kes_darah' => rand(0, 1),
                    'status_gambar_barang_kes_kontraban' => rand(0, 1),

                    // BAHAGIAN 6: Laporan RJ & Semboyan
                    // OK: This was already correct
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

                    // BAHAGIAN 7: Laporan E-FSA & Agensi Luar (Data types are correct)
                    'status_permohonan_laporan_post_mortem_mayat' => rand(0, 1),
                    'tarikh_permohonan_laporan_post_mortem_mayat' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 90)) : null,
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
                    'status_permohonan_laporan_puspakom' => rand(0, 1),
                    'tarikh_permohonan_laporan_puspakom' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 45)) : null,
                    'status_laporan_penuh_puspakom' => rand(0, 1),
                    'tarikh_laporan_penuh_puspakom' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 40)) : null,
                    #'status_permohonan_laporan_jkr' => rand(0, 1),
                    #'tarikh_permohonan_laporan_jkr' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 35)) : null,
                    #'status_laporan_penuh_jkr' => rand(0, 1),
                    #'tarikh_laporan_penuh_jkr' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                    #'status_permohonan_laporan_jpj' => rand(0, 1),
                    #'tarikh_permohonan_laporan_jpj' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 25)) : null,
                    #'status_laporan_penuh_jpj' => rand(0, 1),
                    #'tarikh_laporan_penuh_jpj' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 20)) : null,
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

                    // BAHAGIAN 8: Status Fail (Data types are correct)
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
                    // IPRS Standard Fields (8 columns for standardization)
                    'iprs_no_kertas_siasatan' => 'TRFS/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24',
                    'iprs_tarikh_ks' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'iprs_no_repot' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'iprs_jenis_jabatan_ks' => 'TrafikSeksyen',
                    'iprs_pegawai_penyiasat' => ['SGT RAHMAN', 'INSP LILY', 'SGT AZMAN', 'ASP WONG', 'INSP SITI'][array_rand(['SGT RAHMAN', 'INSP LILY', 'SGT AZMAN', 'ASP WONG', 'INSP SITI'])],
                    'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                    'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                    'iprs_seksyen' => ['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987', '44(1) APJ 1987'][array_rand(['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987', '44(1) APJ 1987'])],

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
                    
                    // Add some additional test data for other fields
                    'keputusan_akhir_mahkamah' => json_encode(['Jatuh Hukum', 'KUS / FAIL']),
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
                    // IPRS Standard Fields
                    'iprs_no_kertas_siasatan' => 'TRFR/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24',
                    'iprs_tarikh_ks' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'iprs_no_repot' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'iprs_jenis_jabatan_ks' => 'TrafikRule',
                    'iprs_pegawai_penyiasat' => ['SGT FARID', 'INSP MAYA', 'SGT ROSLI', 'ASP DAVID', 'INSP KHADIJAH'][array_rand(['SGT FARID', 'INSP MAYA', 'SGT ROSLI', 'ASP DAVID', 'INSP KHADIJAH'])],
                    'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                    'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                    'iprs_seksyen' => ['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59', 'R.18 LN 166/59'][array_rand(['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59', 'R.18 LN 166/59'])],
                    'no_fail_lmm_t' => 'LMM(T)/' . rand(100, 999) . '/' . date('y'),
                    'no_repot_polis' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT FARID', 'INSP MAYA', 'SGT ROSLI', 'ASP DAVID', 'INSP KHADIJAH'][array_rand(['SGT FARID', 'INSP MAYA', 'SGT ROSLI', 'ASP DAVID', 'INSP KHADIJAH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59', 'R.18 LN 166/59'][array_rand(['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59', 'R.18 LN 166/59'])],
                ]
            );
        }
        
        // 8. Seed Orang Hilang Papers (15 records) - Updated with new fields
        for ($i = 1; $i <= 15; $i++) {
            OrangHilang::updateOrCreate(
                ['no_kertas_siasatan' => 'OH/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    // IPRS Standard Fields
                    'iprs_no_kertas_siasatan' => 'OH/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24',
                    'iprs_tarikh_ks' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'iprs_no_repot' => 'IPD/CID/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'iprs_jenis_jabatan_ks' => 'OrangHilang',
                    'iprs_pegawai_penyiasat' => ['SGT FATIMAH', 'INSP KAMAL', 'SGT ROZANA', 'ASP CHONG', 'INSP RAVI'][array_rand(['SGT FATIMAH', 'INSP KAMAL', 'SGT ROZANA', 'ASP CHONG', 'INSP RAVI'])],
                    'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                    'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                    'iprs_seksyen' => '365 KANUN KESEKSAAN',
                    'no_repot_polis' => 'IPD/CID/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT FATIMAH', 'INSP KAMAL', 'SGT ROZANA', 'ASP CHONG', 'INSP RAVI'][array_rand(['SGT FATIMAH', 'INSP KAMAL', 'SGT ROZANA', 'ASP CHONG', 'INSP RAVI'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => '365 KANUN KESEKSAAN',
                    
                    // BAHAGIAN 2: Pemeriksaan & Status
                    'pegawai_pemeriksa' => ['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'][array_rand(['INSP NORAIDAH', 'ASP SALLEH', 'SGT FAIZAH'])],
                    'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 15)),
                    'tarikh_edaran_minit_ks_kedua' => Carbon::now()->subMonths(rand(1, 5))->subDays(rand(1, 10)),
                    'tarikh_edaran_minit_ks_sebelum_akhir' => Carbon::now()->subMonths(rand(1, 4))->subDays(rand(1, 8)),
                    'tarikh_edaran_minit_ks_akhir' => Carbon::now()->subMonths(rand(1, 3))->subDays(rand(1, 5)),
                    'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => Carbon::now()->subMonths(rand(1, 2))->subDays(rand(1, 3)),

                    // BAHAGIAN 3: Arahan & Keputusan
                    'arahan_minit_oleh_sio_status' => rand(0, 1),
                    'arahan_minit_oleh_sio_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'arahan_minit_ketua_bahagian_status' => rand(0, 1),
                    'arahan_minit_ketua_bahagian_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 25)) : null,
                    'arahan_minit_ketua_jabatan_status' => rand(0, 1),
                    'arahan_minit_ketua_jabatan_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 20)) : null,
                    'arahan_minit_oleh_ya_tpr_status' => rand(0, 1),
                    'arahan_minit_oleh_ya_tpr_tarikh' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 15)) : null,
                    'keputusan_siasatan_oleh_ya_tpr' => ['Tuduh', 'Tidak Tuduh', 'Siasatan Lanjut'][array_rand(['Tuduh', 'Tidak Tuduh', 'Siasatan Lanjut'])],
                    'ulasan_keputusan_siasatan_tpr' => 'Siasatan telah dilakukan dengan teliti dan mengikut prosedur yang betul.',
                    'ulasan_keseluruhan_pegawai_pemeriksa' => 'Kertas siasatan lengkap dan mengikut format yang ditetapkan.',

                    // BAHAGIAN 4: Barang Kes
                    'adakah_barang_kes_didaftarkan' => rand(0, 1),
                    'no_daftar_barang_kes_am' => rand(0, 1) ? 'BK/AM/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/24' : null,
                    'no_daftar_barang_kes_berharga' => rand(0, 1) ? 'BK/BH/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/24' : null,

                    // BAHAGIAN 5: Dokumen Siasatan
                    'status_id_siasatan_dikemaskini' => rand(0, 1),
                    'status_rajah_kasar_tempat_kejadian' => rand(0, 1),
                    'status_gambar_tempat_kejadian' => rand(0, 1),
                    'status_gambar_barang_kes_am' => rand(0, 1),
                    'status_gambar_barang_kes_berharga' => rand(0, 1),
                    'status_gambar_orang_hilang' => rand(0, 1),

                    // BAHAGIAN 6: Borang & Semakan
                    'status_pem' => json_encode(['PEM 1', 'PEM 2']),
                    'status_mps1' => rand(0, 1),
                    'tarikh_mps1' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 60)) : null,
                    'status_mps2' => rand(0, 1),
                    'tarikh_mps2' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 55)) : null,
                    'pemakluman_nur_alert_jsj_bawah_18_tahun' => ['Dibuat', 'Tidak Dibuat', 'Tidak Berkaitan'][array_rand(['Dibuat', 'Tidak Dibuat', 'Tidak Berkaitan'])],
                    'rakaman_percakapan_orang_hilang' => ['Ada', 'Tiada', 'Tidak Berkaitan'][array_rand(['Ada', 'Tiada', 'Tidak Berkaitan'])],
                    'laporan_polis_orang_hilang_dijumpai' => ['Dibuat', 'Tidak Dibuat', 'Tidak Berkaitan'][array_rand(['Dibuat', 'Tidak Dibuat', 'Tidak Berkaitan'])],
                    'hebahan_media_massa' => rand(0, 1),
                    'orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => rand(0, 1),
                    'alasan_orang_hilang_dijumpai_mati_mengejut_bukan_jenayah' => rand(0, 1) ? 'Sakit kronik yang tidak diketahui' : null,
                    'orang_hilang_dijumpai_mati_mengejut_jenayah' => rand(0, 1),
                    'alasan_orang_hilang_dijumpai_mati_mengejut_jenayah' => rand(0, 1) ? 'Kes pembunuhan' : null,
                    'semboyan_pemakluman_ke_kedutaan_bukan_warganegara' => rand(0, 1),
                    'ulasan_keseluruhan_pegawai_pemeriksa_borang' => 'Borang siasatan lengkap dan telah diisi dengan betul.',

                    // BAHAGIAN 7: Permohonan Laporan Agensi Luar (Updated with new fields)
                    'status_permohonan_laporan_imigresen' => rand(0, 1),
                    'tarikh_permohonan_laporan_imigresen' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                    // New fields added for BAHAGIAN 7
                    'permohonan_laporan_permit_kerja' => rand(0, 1),
                    'permohonan_laporan_agensi_pekerjaan' => rand(0, 1),
                    'permohonan_status_kewarganegaraan' => rand(0, 1),

                    // BAHAGIAN 8: Status Fail (Updated field types)
                    'adakah_muka_surat_4_keputusan_kes_dicatat' => rand(0, 1),
                    'adakah_ks_kus_fail_selesai' => ['KUS', 'FAIL', null][array_rand(['KUS', 'FAIL', null])], // String dropdown
                    'keputusan_akhir_mahkamah' => json_encode([ // JSON array for multiple checkboxes
                        ['Jatuh Hukum', 'NFA'],
                        ['DNA', 'DNAA'], 
                        ['KUS/SEMENTARA'],
                        ['MASIH DALAM SIASATAN / OYDS GAGAL DIKESAN'],
                        ['TERBENGKALAI/ TIADA TINDAKAN'],
                        null
                    ][array_rand([
                        ['Jatuh Hukum', 'NFA'],
                        ['DNA', 'DNAA'], 
                        ['KUS/SEMENTARA'],
                        ['MASIH DALAM SIASATAN / OYDS GAGAL DIKESAN'],
                        ['TERBENGKALAI/ TIADA TINDAKAN'],
                        null
                    ])]),
                    'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'Fail kertas siasatan telah lengkap dan disimpan mengikut prosedur yang ditetapkan.',
                ]
            );
        }

        // 9. Seed Laporan Mati Mengejut Papers (5 records) - Enhanced with complete field structure matching edit.blade.php
        // Clear existing LMM data first
        LaporanMatiMengejut::truncate();
        
        for ($i = 1; $i <= 5; $i++) {
            LaporanMatiMengejut::create([
                'project_id' => $project->id,
                // IPRS Standard Fields
                'iprs_no_kertas_siasatan' => 'LMM/' . date('Y') . '/' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'iprs_tarikh_ks' => Carbon::now()->subDays(rand(30, 90)),
                'iprs_no_repot' => 'RP' . date('Y') . str_pad($i, 6, '0', STR_PAD_LEFT),
                'iprs_jenis_jabatan_ks' => 'LaporanMatiMengejut',
                'iprs_pegawai_penyiasat' => 'Pegawai Penyiasat ' . $i,
                'iprs_status_ks' => ['Selesai Siasatan', 'Dalam Siasatan'][array_rand(['Selesai Siasatan', 'Dalam Siasatan'])],
                'iprs_status_kes' => ['Selesai', 'Dalam Proses'][array_rand(['Selesai', 'Dalam Proses'])],
                'iprs_seksyen' => 'Seksyen ' . rand(1, 10),
                'no_kertas_siasatan' => 'LMM/' . date('Y') . '/' . str_pad($i, 4, '0', STR_PAD_LEFT),
                // BAHAGIAN 1: Maklumat Asas (using actual migration field names)
                'no_fail_lmm_sdr' => 'LMM-SDR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'no_repot_polis' => 'RP' . date('Y') . str_pad($i, 6, '0', STR_PAD_LEFT),
                'pegawai_penyiasat' => 'Pegawai Penyiasat ' . $i,
                'tarikh_laporan_polis_dibuka' => Carbon::now()->subDays(rand(30, 90)),
                'seksyen' => 'Seksyen ' . rand(1, 10),

                // BAHAGIAN 2: Status Edaran & Pemeriksaan
                'pegawai_pemeriksa' => 'Pegawai Pemeriksa ' . $i,
                'tarikh_edaran_minit_ks_pertama' => Carbon::now()->subDays(rand(25, 85)),
                'tarikh_edaran_minit_ks_kedua' => Carbon::now()->subDays(rand(20, 80)),
                'tarikh_edaran_minit_ks_sebelum_akhir' => Carbon::now()->subDays(rand(15, 75)),
                'tarikh_edaran_minit_ks_akhir' => Carbon::now()->subDays(rand(10, 70)),
                'adakah_ms_2_lmm_telah_disahkan_oleh_kpd' => (bool)rand(0, 1),
                'adakah_lmm_telah_di_rujuk_kepada_ya_koroner' => (bool)rand(0, 1),
                'keputusan_ya_koroner' => 'Keputusan koroner untuk kes ' . $i,
                'tarikh_semboyan_pemeriksaan_jips_ke_daerah' => Carbon::now()->subDays(rand(5, 65)),
                'tarikh_edaran_minit_fail_lmm_t_pertama' => Carbon::now()->subDays(rand(20, 60)),
                'tarikh_edaran_minit_fail_lmm_t_kedua' => Carbon::now()->subDays(rand(15, 55)),
                'tarikh_edaran_minit_fail_lmm_t_sebelum_minit_akhir' => Carbon::now()->subDays(rand(10, 50)),
                'tarikh_edaran_minit_fail_lmm_t_akhir' => Carbon::now()->subDays(rand(5, 45)),

                // BAHAGIAN 3: Arahan Minit
                'arahan_minit_oleh_sio_status' => (bool)rand(0, 1),
                'arahan_minit_oleh_sio_tarikh' => Carbon::now()->subDays(rand(15, 40)),
                'arahan_minit_ketua_bahagian_status' => (bool)rand(0, 1),
                'arahan_minit_ketua_bahagian_tarikh' => Carbon::now()->subDays(rand(10, 35)),
                'arahan_minit_ketua_jabatan_status' => (bool)rand(0, 1),
                'arahan_minit_ketua_jabatan_tarikh' => Carbon::now()->subDays(rand(5, 30)),
                'arahan_minit_oleh_ya_tpr_status' => (bool)rand(0, 1),
                'arahan_minit_oleh_ya_tpr_tarikh' => Carbon::now()->subDays(rand(1, 25)),

                // BAHAGIAN 4: Borang RJ & Status
                'status_rj2' => rand(0, 2), // 0=Tiada, 1=Ada, 2=Tidak Berkaitan
                'tarikh_rj2' => rand(0, 1) ? Carbon::now()->subDays(rand(5, 20)) : null,
                'status_rj2b' => rand(0, 2),
                'tarikh_rj2b' => rand(0, 1) ? Carbon::now()->subDays(rand(5, 20)) : null,
                'status_rj9' => rand(0, 2),
                'tarikh_rj9' => rand(0, 1) ? Carbon::now()->subDays(rand(5, 20)) : null,
                'status_rj99' => rand(0, 2),
                'tarikh_rj99' => rand(0, 1) ? Carbon::now()->subDays(rand(5, 20)) : null,
                'status_rj10a' => rand(0, 2),
                'tarikh_rj10a' => rand(0, 1) ? Carbon::now()->subDays(rand(5, 20)) : null,
                'status_rj10b' => rand(0, 2),
                'tarikh_rj10b' => rand(0, 1) ? Carbon::now()->subDays(rand(5, 20)) : null,
                
                'status_semboyan_pemakluman_ke_kedutaan_bagi_kes_mati' => (bool)rand(0, 1),
                'adakah_barang_kes_didaftarkan' => (bool)rand(0, 1),
                'adakah_borang_serah_terima_pegawai_tangkapan_io' => (bool)rand(0, 1),
                'adakah_borang_serah_terima_penyiasat_pemilik_saksi' => (bool)rand(0, 1),
                'adakah_sijil_surat_kebenaran_ipd' => (bool)rand(0, 1),
                'adakah_gambar_pelupusan' => (bool)rand(0, 1),
                'status_id_siasatan_dikemaskini' => (bool)rand(0, 1),

                // BAHAGIAN 5: Status Gambar
                'status_rajah_kasar_tempat_kejadian' => (bool)rand(0, 1),
                'status_gambar_tempat_kejadian' => (bool)rand(0, 1),
                'status_gambar_post_mortem_mayat_di_hospital' => (bool)rand(0, 1),
                'status_gambar_barang_kes_am' => (bool)rand(0, 1),
                'status_gambar_barang_kes_berharga' => (bool)rand(0, 1),
                'status_gambar_barang_kes_darah' => (bool)rand(0, 1),

                // BAHAGIAN 6: Status PEM
                'status_pem' => json_encode(['Dilepaskan', 'Disimpan']), // Multiple selection

                // BAHAGIAN 7: Laporan-laporan
                // Post Mortem
                'status_permohonan_laporan_post_mortem_mayat' => (bool)rand(0, 1),
                'tarikh_permohonan_laporan_post_mortem_mayat' => Carbon::now()->subDays(rand(10, 30)),
                'status_laporan_penuh_bedah_siasat' => (bool)rand(0, 1),
                'tarikh_laporan_penuh_bedah_siasat' => Carbon::now()->subDays(rand(5, 25)),
                'keputusan_laporan_post_mortem' => 'Keputusan post mortem untuk kes ' . $i,

                // Jabatan Kimia
                'status_permohonan_laporan_jabatan_kimia' => (bool)rand(0, 1),
                'tarikh_permohonan_laporan_jabatan_kimia' => Carbon::now()->subDays(rand(10, 30)),
                'status_laporan_penuh_jabatan_kimia' => (bool)rand(0, 1),
                'tarikh_laporan_penuh_jabatan_kimia' => Carbon::now()->subDays(rand(5, 25)),
                'keputusan_laporan_jabatan_kimia' => 'Keputusan kimia untuk kes ' . $i,

                // Jabatan Patalogi
                'status_permohonan_laporan_jabatan_patalogi' => (bool)rand(0, 1),
                'tarikh_permohonan_laporan_jabatan_patalogi' => Carbon::now()->subDays(rand(10, 30)),
                'status_laporan_penuh_jabatan_patalogi' => (bool)rand(0, 1),
                'tarikh_laporan_penuh_jabatan_patalogi' => Carbon::now()->subDays(rand(5, 25)),
                'keputusan_laporan_jabatan_patalogi' => 'Keputusan patalogi untuk kes ' . $i,

                // Imigresen - using the CORRECTED field names from migration
                'tarikh_permohonan_laporan_imigresen' => Carbon::now()->subDays(rand(10, 30)),
                'status_laporan_penuh_imigresen' => (bool)rand(0, 1),
                'tarikh_laporan_penuh_imigresen' => Carbon::now()->subDays(rand(5, 25)),

                // Updated Imigresen fields - simplified boolean fields only
                'permohonan_laporan_pengesahan_masuk_keluar_malaysia' => (bool)rand(0, 1),
                'permohonan_laporan_permit_kerja_di_malaysia' => (bool)rand(0, 1),
                'permohonan_laporan_agensi_pekerjaan_di_malaysia' => (bool)rand(0, 1),
                'permohonan_status_kewarganegaraan' => (bool)rand(0, 1),

                'lain_lain_permohonan_laporan' => 'Lain-lain laporan untuk kes ' . $i,

                // BAHAGIAN 8: Status Fail
                'status_muka_surat_4_barang_kes_ditulis_bersama_no_daftar' => (bool)rand(0, 1),
                'status_barang_kes_arahan_tpr' => (bool)rand(0, 1),
                'adakah_muka_surat_4_keputusan_kes_dicatat' => (bool)rand(0, 1),
                'adakah_fail_lmm_t_atau_lmm_telah_ada_keputusan' => (bool)rand(0, 1),
                'adakah_ks_kus_fail_selesai' => ['KUS', 'FAIL'][rand(0, 1)],
                'keputusan_akhir_mahkamah' => json_encode(['Bunuh diri', 'Mati mengejut']),
                'ulasan_keseluruhan_pegawai_pemeriksa_fail' => 'Ulasan keseluruhan untuk kes ' . $i,

                // System generated fields
                'edar_lebih_24_jam_status' => rand(0, 1) ? 'Ya' : 'Tidak',
                'terbengkalai_3_bulan_status' => rand(0, 1) ? 'Ya' : 'Tidak',
                'baru_kemaskini_status' => rand(0, 1) ? 'Ya' : 'Tidak',

                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}