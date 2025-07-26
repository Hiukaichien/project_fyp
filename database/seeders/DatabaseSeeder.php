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

        // 3. Seed Jenayah Papers (20 records)
        // Note: Using 'pegawai_penyiasat' as per your migration file.
        for ($i = 1; $i <= 20; $i++) {
            Jenayah::updateOrCreate(
                ['no_ks' => 'JNY/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'pegawai_penyiasat' => ['INSP ALI', 'SGT AHMAD', 'INSP FATIMAH'][array_rand(['INSP ALI', 'SGT AHMAD', 'INSP FATIMAH'])],
                    'seksyen' => ['302 KK', '39B ADB', '420 KK'][array_rand(['302 KK', '39B ADB', '420 KK'])],
                    'tarikh_laporan_polis' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }

        // 4. Seed Narkotik Papers (20 records)
        // Note: Using 'pegawai_penyiasat' as per your migration file.
        for ($i = 1; $i <= 20; $i++) {
            Narkotik::updateOrCreate(
                ['no_ks' => 'NRK/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'pegawai_penyiasat' => ['INSP WONG', 'SGT RAZALI', 'INSP CHUA'][array_rand(['INSP WONG', 'SGT RAZALI', 'INSP CHUA'])],
                    'seksyen' => ['12(2) ADB 1952', '15(1)(a) ADB 1952', '39B ADB 1952'][array_rand(['12(2) ADB 1952', '15(1)(a) ADB 1952', '39B ADB 1952'])],
                    'tarikh_laporan_polis' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }

        // 5. Seed Komersil Papers (20 records)
        // BAHAGIAN 1: Maklumat Asas fields only
        for ($i = 1; $i <= 20; $i++) {
            Komersil::updateOrCreate(
                ['no_kertas_siasatan' => 'KML/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/REP/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                    'pegawai_penyiasat' => ['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'][array_rand(['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP RAHMAN', 'SGT AMINAH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'seksyen' => ['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'][array_rand(['420 KK', '4(1) AMLA', 'Seksyen 424 KK', '409 KK', '417 KK'])],
                ]
            );
        }

        // 6. Seed Trafik Seksyen Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            TrafikSeksyen::updateOrCreate(
                ['no_kertas_siasatan' => 'TRFS/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'],
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    'no_repot_polis' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT MAJID', 'INSP TAN', 'SGT AMIN'][array_rand(['SGT MAJID', 'INSP TAN', 'SGT AMIN'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'seksyen' => ['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987'][array_rand(['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987'])],
                ]
            );
        }

        // 6. Seed Trafik Rule Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            TrafikRule::updateOrCreate(
                ['no_kertas_siasatan' => 'TRFR/KS/' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/24'], // Note: Changed prefix to TRFR for "Trafik Rule"
                [
                    // BAHAGIAN 1: Maklumat Asas
                    'project_id' => $project->id,
                    'no_fail_lmm_t' => 'LMM(T)/' . rand(100, 999) . '/' . date('y'),
                    'no_repot_polis' => 'IPD/TRAFFIC/' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT) . '/' . date('y'),
                    'pegawai_penyiasat' => ['SGT MAJID', 'INSP TAN', 'SGT AMIN'][array_rand(['SGT MAJID', 'INSP TAN', 'SGT AMIN'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'seksyen' => ['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59'][array_rand(['R.166A LN 166/59', 'R.17 LN 166/59', 'R.10 LN 166/59'])], // Using more appropriate sections for Trafik Rule
                ]
            );
        }
        
        // 7. Seed Orang Hilang Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            OrangHilang::create([
                'project_id' => $project->id,
                
                // BAHAGIAN 1: Maklumat Asas
                'no_repot_polis' => 'IPD/OH/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                'pegawai_penyiasat' => ['SGT RINA', 'INSP LIM', 'SGT NORA', 'INSP HAFIZ', 'SGT AZURA'][array_rand(['SGT RINA', 'INSP LIM', 'SGT NORA', 'INSP HAFIZ', 'SGT AZURA'])],
                'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                'seksyen' => ['365 KK', '376 KK', '302 KK', '41 KK'][array_rand(['365 KK', '376 KK', '302 KK', '41 KK'])],
            ]);
        }

        // 8. Seed Laporan Mati Mengejut Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            LaporanMatiMengejut::updateOrCreate(
                ['no_kertas_siasatan' => 'LMM/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'project_id' => $project->id,
                    // BAHAGIAN 1: Maklumat Asas
                    'no_fail_lmm_sdr' => 'LMM/SDR/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24',
                    'no_repot_polis' => 'IPD/REP/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                    'pegawai_penyiasat' => ['INSP AZMAN', 'SGT YUSOF', 'INSP SARAH'][array_rand(['INSP AZMAN', 'SGT YUSOF', 'INSP SARAH'])],
                    'tarikh_laporan_polis_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'seksyen' => ['41 KK', '302 KK', '304 KK', '324 KK'][array_rand(['41 KK', '302 KK', '304 KK', '324 KK'])],
                    
                ]
            );
        }
    }
}