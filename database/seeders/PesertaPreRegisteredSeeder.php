<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Papa\Parse\Parse;

class PesertaPreRegisteredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table first to avoid duplicates
        DB::table('peserta_pre_registereds')->truncate();

       $pesertaData = [
    [
        'nama' => 'dr. Legawa Nasyiah',
        'nomor_bib' => '1478',
        'email' => 'syahrini11@hotmail.com',
        'nomor_telepon' => '81016391472',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Suci Napitupulu',
        'nomor_bib' => '4756',
        'email' => 'najamhalimah@pd.mil',
        'nomor_telepon' => '81471790855',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Zelda Firmansyah',
        'nomor_bib' => '8958',
        'email' => 'cawisadipermadi@gmail.com',
        'nomor_telepon' => '84503969371',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Dt. Muhammad Winarsih',
        'nomor_bib' => '4395',
        'email' => 'hassanahpaiman@pt.ac.id',
        'nomor_telepon' => '83351651380',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Ozy Marpaung',
        'nomor_bib' => '4538',
        'email' => 'baktiadi68@pt.ponpes.id',
        'nomor_telepon' => '88367990818',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Ella Halimah',
        'nomor_bib' => '4351',
        'email' => 'qmarpaung@gmail.com',
        'nomor_telepon' => '88894681828',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Ir. Kenari Siregar, S.T.',
        'nomor_bib' => '1830',
        'email' => 'gunawancinta@pd.com',
        'nomor_telepon' => '86390627117',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Kamal Haryanto, M.Farm',
        'nomor_bib' => '3334',
        'email' => 'ajionowijaya@gmail.com',
        'nomor_telepon' => '86140472632',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Gangsar Pertiwi',
        'nomor_bib' => '1743',
        'email' => 'budimanusyi@ud.edu',
        'nomor_telepon' => '83984242833',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Tgk. Himawan Pratama',
        'nomor_bib' => '6353',
        'email' => 'zbudiman@cv.gov',
        'nomor_telepon' => '85724757066',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'R. Putri Gunawan, M.Pd',
        'nomor_bib' => '4467',
        'email' => 'haryantodagel@hotmail.com',
        'nomor_telepon' => '83531838754',
        'kategori_lari' => '2.5KIDS',
    ],
    // 👉 tambahkan peserta lain dengan format sama
];


        // Insert data with timestamps
        foreach ($pesertaData as $peserta) {
            DB::table('peserta_pre_registereds')->insert([
                'nama' => $peserta['nama'],
                'nomor_bib' => $peserta['nomor_bib'],
                'email' => $peserta['email'],
                'nomor_telepon' => $peserta['nomor_telepon'],
                'kategori_lari' => $peserta['kategori_lari'],
                'is_registered' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Pre-registered peserta data seeded successfully! Total: ' . count($pesertaData) . ' records');
    }
}