<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\JenayahPaper;
use App\Models\NarkotikPaper;
use App\Models\TrafikSeksyenPaper;
use App\Models\OrangHilangPaper;
use App\Models\LaporanMatiMengejutPaper;
use App\Models\KomersilPaper;
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
        // 1. Create a default user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Create a default project to associate papers with
        $project = Project::updateOrCreate(
            ['name' => 'Projek Siasatan 1'],
            [
                'project_date' => '2024-07-01',
                'description' => 'Projek rintis untuk pengauditan dan kawal selia kertas siasatan.',
            ]
        );

        // 3. Seed Jenayah Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            JenayahPaper::updateOrCreate(
                ['no_ks' => 'JNY/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'io_aio' => ['INSP ALI', 'SGT AHMAD', 'INSP FATIMAH', 'SGT CHONG', 'INSP MUTHU'][array_rand(['INSP ALI', 'SGT AHMAD', 'INSP FATIMAH', 'SGT CHONG', 'INSP MUTHU'])],
                    'seksyen' => ['302 KK', '39B ADB', '420 KK', '376 KK', '324 KK'][array_rand(['302 KK', '39B ADB', '420 KK', '376 KK', '324 KK'])],
                    'tarikh_laporan_polis' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }

        // 4. Seed Narkotik Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            NarkotikPaper::updateOrCreate(
                ['no_ks' => 'NRK/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'io_aio' => ['INSP WONG', 'SGT RAZALI', 'INSP CHUA', 'SGT LIM', 'INSP DAVID'][array_rand(['INSP WONG', 'SGT RAZALI', 'INSP CHUA', 'SGT LIM', 'INSP DAVID'])],
                    'seksyen' => ['12(2) ADB 1952', '15(1)(a) ADB 1952', '39B ADB 1952', '6 ADB 1952'][array_rand(['12(2) ADB 1952', '15(1)(a) ADB 1952', '39B ADB 1952', '6 ADB 1952'])],
                    'tarikh_laporan_polis' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }

        // 5. Seed Trafik Seksyen Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            TrafikSeksyenPaper::updateOrCreate(
                ['no_kst' => 'TRF-S/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'io_aio' => ['SGT MAJID', 'INSP TAN', 'SGT AMIN', 'INSP CHEN', 'SGT BALA'][array_rand(['SGT MAJID', 'INSP TAN', 'SGT AMIN', 'INSP CHEN', 'SGT BALA'])],
                    'seksyen' => ['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987', 'Seksyen 44 APJ'][array_rand(['41(1) APJ 1987', '42(1) APJ 1987', '43(1) APJ 1987', 'Seksyen 44 APJ'])],
                    'tarikh_daftar' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }
        
        // 6. Seed Orang Hilang Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            $reportDate = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30));
            OrangHilangPaper::updateOrCreate(
                ['no_ks_oh' => 'OH/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'io_aio' => ['SGT RINA', 'INSP LIM', 'SGT NORA', 'INSP RAJ', 'SGT FAIZ'][array_rand(['SGT RINA', 'INSP LIM', 'SGT NORA', 'INSP RAJ', 'SGT FAIZ'])],
                    'tarikh_laporan_polis' => $reportDate->format('Y-m-d'),
                    'tarikh_ks_oh_dibuka' => $reportDate->addDays(rand(0, 2))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }

        // 7. Seed Laporan Mati Mengejut Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            LaporanMatiMengejutPaper::updateOrCreate(
                ['no_lmm' => 'LMM/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'io_aio' => ['INSP AZMAN', 'SGT YUSOF', 'INSP SARAH', 'SGT HELMI', 'INSP MARIA'][array_rand(['INSP AZMAN', 'SGT YUSOF', 'INSP SARAH', 'SGT HELMI', 'INSP MARIA'])],
                    'no_repot_polis' => 'IPD/REP/' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT) . '/24',
                    'tarikh_laporan_polis' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }

        // 8. Seed Komersil Papers (20 records)
        for ($i = 1; $i <= 20; $i++) {
            KomersilPaper::updateOrCreate(
                ['no_ks' => 'KML/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/24'],
                [
                    'io_aio' => ['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP GOH', 'ASP FATTAH'][array_rand(['ASP KUMAR', 'INSP ZAINAB', 'SGT LEE', 'INSP GOH', 'ASP FATTAH'])],
                    'seksyen' => ['420 KK', '4(1) AMLA', 'Seksyen 424 KK', 'Akta Syarikat 2016'][array_rand(['420 KK', '4(1) AMLA', 'Seksyen 424 KK', 'Akta Syarikat 2016'])],
                    'tarikh_ks_dibuka' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 30))->format('Y-m-d'),
                    'project_id' => $project->id,
                ]
            );
        }
    }
}