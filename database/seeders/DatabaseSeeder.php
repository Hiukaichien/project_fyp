<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\JenayahPaper;
use App\Models\NarkotikPaper;
use App\Models\TrafikSeksyenPaper;
// use App\Models\TrafikRulePaper; // Removed as requested
use App\Models\OrangHilangPaper;
use App\Models\LaporanMatiMengejutPaper;
use App\Models\KomersilPaper; // Added KomersilPaper
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
            ['name' => 'Projek Siasatan PDRM 2024'],
            [
                'project_date' => '2024-07-01',
                'description' => 'Projek rintis untuk pengauditan dan kawal selia kertas siasatan.',
            ]
        );

        // 3. Seed Jenayah Papers
        $jenayahData = [
            ['no_ks' => 'JNY/001/24', 'io_aio' => 'INSP ALI BIN ABU', 'seksyen' => '302 KK', 'tarikh_laporan_polis' => '01/05/2024'],
            ['no_ks' => 'JNY/002/24', 'io_aio' => 'SGT AHMAD', 'seksyen' => '39B ADB', 'tarikh_laporan_polis' => '10/06/2024'],
            ['no_ks' => 'JNY/003/24', 'io_aio' => 'INSP FATIMAH', 'seksyen' => '420 KK', 'tarikh_laporan_polis' => '20/07/2024'],
            ['no_ks' => 'JNY/004/24', 'io_aio' => 'SGT CHONG', 'seksyen' => '376 KK', 'tarikh_laporan_polis' => '01/11/2023'],
            ['no_ks' => 'JNY/005/24', 'io_aio' => 'INSP MUTHU', 'seksyen' => '324 KK', 'tarikh_laporan_polis' => '15/01/2024'],
        ];
        foreach ($jenayahData as $data) {
            JenayahPaper::updateOrCreate(
                ['no_ks' => $data['no_ks']],
                array_merge($data, ['project_id' => $project->id, 'tarikh_laporan_polis' => Carbon::createFromFormat('d/m/Y', $data['tarikh_laporan_polis'])->format('Y-m-d')])
            );
        }

        // 4. Seed Narkotik Papers
        $narkotikData = [
            ['no_ks' => 'NRK/001/24', 'io_aio' => 'INSP WONG', 'seksyen' => '12(2) ADB 1952', 'tarikh_laporan_polis' => '05/02/2024'],
            ['no_ks' => 'NRK/002/24', 'io_aio' => 'SGT RAZALI', 'seksyen' => '15(1)(a) ADB 1952', 'tarikh_laporan_polis' => '15/03/2024'],
            ['no_ks' => 'NRK/003/24', 'io_aio' => 'INSP CHUA', 'seksyen' => '39B ADB 1952', 'tarikh_laporan_polis' => '25/04/2024'],
            ['no_ks' => 'NRK/004/24', 'io_aio' => 'SGT LIM', 'seksyen' => '6 ADB 1952', 'tarikh_laporan_polis' => '10/05/2024'],
            ['no_ks' => 'NRK/005/24', 'io_aio' => 'INSP DAVID', 'seksyen' => '12(3) ADB 1952', 'tarikh_laporan_polis' => '20/06/2024'],
        ];
        foreach ($narkotikData as $data) {
            NarkotikPaper::updateOrCreate(
                ['no_ks' => $data['no_ks']],
                array_merge($data, ['project_id' => $project->id, 'tarikh_laporan_polis' => Carbon::createFromFormat('d/m/Y', $data['tarikh_laporan_polis'])->format('Y-m-d')])
            );
        }

        // 5. Seed Trafik Seksyen Papers
        $trafikSeksyenData = [
            ['no_kst' => 'TRF-S/001/24', 'io_aio' => 'SGT MAJID', 'seksyen' => '41(1) APJ 1987', 'tarikh_daftar' => '03/03/2024'],
            ['no_kst' => 'TRF-S/002/24', 'io_aio' => 'INSP TAN', 'seksyen' => '42(1) APJ 1987', 'tarikh_daftar' => '11/04/2024'],
            ['no_kst' => 'TRF-S/003/24', 'io_aio' => 'SGT AMIN', 'seksyen' => '43(1) APJ 1987', 'tarikh_daftar' => '22/05/2024'],
            ['no_kst' => 'TRF-S/004/24', 'io_aio' => 'INSP CHEN', 'seksyen' => 'Seksyen 44 APJ', 'tarikh_daftar' => '01/06/2024'],
            ['no_kst' => 'TRF-S/005/24', 'io_aio' => 'SGT BALA', 'seksyen' => 'Seksyen 45 APJ', 'tarikh_daftar' => '19/07/2024'],
        ];
        foreach ($trafikSeksyenData as $data) {
            TrafikSeksyenPaper::updateOrCreate(
                ['no_kst' => $data['no_kst']],
                array_merge($data, ['project_id' => $project->id, 'tarikh_daftar' => Carbon::createFromFormat('d/m/Y', $data['tarikh_daftar'])->format('Y-m-d')])
            );
        }
        
        // 6. Seed Orang Hilang Papers
        $orangHilangData = [
            ['no_ks_oh' => 'OH/001/24', 'io_aio' => 'SGT RINA', 'tarikh_laporan_polis' => '01/07/2024', 'tarikh_ks_oh_dibuka' => '01/07/2024'],
            ['no_ks_oh' => 'OH/002/24', 'io_aio' => 'INSP LIM', 'tarikh_laporan_polis' => '05/07/2024', 'tarikh_ks_oh_dibuka' => '06/07/2024'],
            ['no_ks_oh' => 'OH/003/24', 'io_aio' => 'SGT NORA', 'tarikh_laporan_polis' => '10/07/2024', 'tarikh_ks_oh_dibuka' => '10/07/2024'],
            ['no_ks_oh' => 'OH/004/24', 'io_aio' => 'INSP RAJ', 'tarikh_laporan_polis' => '15/07/2024', 'tarikh_ks_oh_dibuka' => '16/07/2024'],
            ['no_ks_oh' => 'OH/005/24', 'io_aio' => 'SGT FAIZ', 'tarikh_laporan_polis' => '20/07/2024', 'tarikh_ks_oh_dibuka' => '20/07/2024'],
        ];
        foreach ($orangHilangData as $data) {
            OrangHilangPaper::updateOrCreate(
                ['no_ks_oh' => $data['no_ks_oh']],
                array_merge($data, ['project_id' => $project->id, 'tarikh_laporan_polis' => Carbon::createFromFormat('d/m/Y', $data['tarikh_laporan_polis'])->format('Y-m-d'), 'tarikh_ks_oh_dibuka' => Carbon::createFromFormat('d/m/Y', $data['tarikh_ks_oh_dibuka'])->format('Y-m-d')])
            );
        }

        // 7. Seed Laporan Mati Mengejut Papers
        $lmmData = [
            ['no_lmm' => 'LMM/001/24', 'io_aio' => 'INSP AZMAN', 'no_repot_polis' => 'IPD/REP/00123/24', 'tarikh_laporan_polis' => '02/02/2024'],
            ['no_lmm' => 'LMM/002/24', 'io_aio' => 'SGT YUSOF', 'no_repot_polis' => 'IPD/REP/00124/24', 'tarikh_laporan_polis' => '13/03/2024'],
            ['no_lmm' => 'LMM/003/24', 'io_aio' => 'INSP SARAH', 'no_repot_polis' => 'IPD/REP/00125/24', 'tarikh_laporan_polis' => '24/04/2024'],
            ['no_lmm' => 'LMM/004/24', 'io_aio' => 'SGT HELMI', 'no_repot_polis' => 'IPD/REP/00126/24', 'tarikh_laporan_polis' => '05/05/2024'],
            ['no_lmm' => 'LMM/005/24', 'io_aio' => 'INSP MARIA', 'no_repot_polis' => 'IPD/REP/00127/24', 'tarikh_laporan_polis' => '16/06/2024'],
        ];
        foreach ($lmmData as $data) {
            LaporanMatiMengejutPaper::updateOrCreate(
                ['no_lmm' => $data['no_lmm']],
                array_merge($data, ['project_id' => $project->id, 'tarikh_laporan_polis' => Carbon::createFromFormat('d/m/Y', $data['tarikh_laporan_polis'])->format('Y-m-d')])
            );
        }

        // --- ADDED: Seed Komersil Papers ---
        $komersilData = [
            ['no_ks' => 'KML/001/24', 'io_aio' => 'ASP KUMAR', 'seksyen' => '420 KK', 'tarikh_ks_dibuka' => '01/01/2024'],
            ['no_ks' => 'KML/002/24', 'io_aio' => 'INSP ZAINAB', 'seksyen' => '4(1) AMLA', 'tarikh_ks_dibuka' => '12/02/2024'],
            ['no_ks' => 'KML/003/24', 'io_aio' => 'SGT LEE', 'seksyen' => 'Seksyen 424 KK', 'tarikh_ks_dibuka' => '23/03/2024'],
            ['no_ks' => 'KML/004/24', 'io_aio' => 'INSP GOH', 'seksyen' => 'Akta Syarikat 2016', 'tarikh_ks_dibuka' => '04/04/2024'],
            ['no_ks' => 'KML/005/24', 'io_aio' => 'ASP FATTAH', 'seksyen' => 'Akta Jualan Langsung', 'tarikh_ks_dibuka' => '15/05/2024'],
        ];
        foreach ($komersilData as $data) {
            KomersilPaper::updateOrCreate(
                ['no_ks' => $data['no_ks']],
                array_merge($data, ['project_id' => $project->id, 'tarikh_ks_dibuka' => Carbon::createFromFormat('d/m/Y', $data['tarikh_ks_dibuka'])->format('Y-m-d')])
            );
        }
    }
}