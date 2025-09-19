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
        'email' => 'ayulestari@pd.ac.id',
        'nomor_telepon' => '83012345678',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Prof. Bambang Hermanto',
        'nomor_bib' => '4682',
        'email' => 'bambanghermanto@univ.edu',
        'nomor_telepon' => '86123456789',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Rina Andriani',
        'nomor_bib' => '6905',
        'email' => 'rinaandriani@hotmail.com',
        'nomor_telepon' => '84234567890',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Doni Setiadi',
        'nomor_bib' => '9238',
        'email' => 'donisetiadi@company.com',
        'nomor_telepon' => '88345678901',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Sinta Dewi, M.Pd',
        'nomor_bib' => '1571',
        'email' => 'sintadewi@school.edu',
        'nomor_telepon' => '82456789012',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Rahmat Hidayat',
        'nomor_bib' => '3894',
        'email' => 'rahmathidayat@gmail.com',
        'nomor_telepon' => '85567890123',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'dr. Lilis Suryani',
        'nomor_bib' => '5128',
        'email' => 'lilissuryani@clinic.id',
        'nomor_telepon' => '81678901234',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Hadi Wijaksono',
        'nomor_bib' => '7460',
        'email' => 'hadiwijaksono@yahoo.com',
        'nomor_telepon' => '87789012345',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Fitri Handayani, S.Kom',
        'nomor_bib' => '2793',
        'email' => 'fitrihandayani@tech.co.id',
        'nomor_telepon' => '83890123456',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Eko Prasetyo',
        'nomor_bib' => '4018',
        'email' => 'ekoprasetyo@pd.mil',
        'nomor_telepon' => '86901234567',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Winda Sari',
        'nomor_bib' => '6351',
        'email' => 'windasari@hotmail.com',
        'nomor_telepon' => '84012345678',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Drs. Sugeng Riyanto',
        'nomor_bib' => '8684',
        'email' => 'sugengriyanto@school.sch.id',
        'nomor_telepon' => '88123456789',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Nina Marliana',
        'nomor_bib' => '1905',
        'email' => 'ninamarliana@gmail.com',
        'nomor_telepon' => '82234567890',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Irfan Hakim, M.T',
        'nomor_bib' => '3239',
        'email' => 'irfanhakim@engineering.edu',
        'nomor_telepon' => '85345678901',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Tari Wulandari',
        'nomor_bib' => '5572',
        'email' => 'tariwulandari@yahoo.com',
        'nomor_telepon' => '81456789012',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Bayu Aji',
        'nomor_bib' => '7895',
        'email' => 'bayuaji@company.id',
        'nomor_telepon' => '87567890123',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'dr. Sari Indrawati, Sp.PD',
        'nomor_bib' => '2128',
        'email' => 'sariindrawati@hospital.com',
        'nomor_telepon' => '83678901234',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Wawan Setiawan',
        'nomor_bib' => '4461',
        'email' => 'wawansetiawan@pd.ac.id',
        'nomor_telepon' => '86789012345',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Lia Permata, S.Pd',
        'nomor_bib' => '6795',
        'email' => 'liapermata@education.edu',
        'nomor_telepon' => '84890123456',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Andi Susanto',
        'nomor_bib' => '9018',
        'email' => 'andisusanto@hotmail.com',
        'nomor_telepon' => '88901234567',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Reni Fitriani',
        'nomor_bib' => '1350',
        'email' => 'renifitriani@gmail.com',
        'nomor_telepon' => '82012345678',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Ir. Dedy Kurniawan, M.M',
        'nomor_bib' => '3683',
        'email' => 'dedykurniawan@business.co.id',
        'nomor_telepon' => '85123456789',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Yati Sumarni',
        'nomor_bib' => '5906',
        'email' => 'yatisumarni@yahoo.com',
        'nomor_telepon' => '81234567890',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Ridwan Kamil',
        'nomor_bib' => '7239',
        'email' => 'ridwankamil@gov.id',
        'nomor_telepon' => '87345678901',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Siska Amelinda',
        'nomor_bib' => '2571',
        'email' => 'siskaamelinda@pd.mil',
        'nomor_telepon' => '83456789012',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Ferry Gunawan',
        'nomor_bib' => '4894',
        'email' => 'ferrygunawan@company.com',
        'nomor_telepon' => '86567890123',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'dr. Retno Wulan, M.Kes',
        'nomor_bib' => '6129',
        'email' => 'retnowulan@clinic.id',
        'nomor_telepon' => '84678901234',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Arif Budiman',
        'nomor_bib' => '8462',
        'email' => 'arifbudiman@hotmail.com',
        'nomor_telepon' => '88789012345',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Dian Sastika, S.E',
        'nomor_bib' => '1794',
        'email' => 'diansastika@finance.gov',
        'nomor_telepon' => '82890123456',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Gilang Ramadhan',
        'nomor_bib' => '3019',
        'email' => 'gilangramadhan@gmail.com',
        'nomor_telepon' => '85901234567',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Nita Anggraini',
        'nomor_bib' => '5351',
        'email' => 'nitaanggraini@yahoo.com',
        'nomor_telepon' => '81012345678',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Prof. Dr. Sutomo Wignjosoebroto',
        'nomor_bib' => '7684',
        'email' => 'sutomowignjosoebroto@univ.ac.id',
        'nomor_telepon' => '87123456789',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Erika Putri',
        'nomor_bib' => '2907',
        'email' => 'erikaputri@pd.ac.id',
        'nomor_telepon' => '83234567890',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Drs. Wahyu Hidayat, M.Pd',
        'nomor_bib' => '4240',
        'email' => 'wahyuhidayat@school.edu',
        'nomor_telepon' => '86345678901',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Vina Septiani',
        'nomor_bib' => '6573',
        'email' => 'vinaseptiani@hotmail.com',
        'nomor_telepon' => '84456789012',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Hendri Saputra',
        'nomor_bib' => '8896',
        'email' => 'hendrisaputra@company.id',
        'nomor_telepon' => '88567890123',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'dr. Maya Sari, Sp.OG',
        'nomor_bib' => '1129',
        'email' => 'mayasariobgyn@hospital.com',
        'nomor_telepon' => '82678901234',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Reza Fauzi',
        'nomor_bib' => '3462',
        'email' => 'rezafauzi@gmail.com',
        'nomor_telepon' => '85789012345',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Lestari Handayani, M.Si',
        'nomor_bib' => '5795',
        'email' => 'lestarihandayani@research.ac.id',
        'nomor_telepon' => '81890123456',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Yoga Pratama',
        'nomor_bib' => '7018',
        'email' => 'yogapratama@yahoo.com',
        'nomor_telepon' => '87901234567',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Citra Dewi',
        'nomor_bib' => '2351',
        'email' => 'citradewi@pd.mil',
        'nomor_telepon' => '83012345678',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Ir. Gunawan Santoso, S.T',
        'nomor_bib' => '4684',
        'email' => 'gunawansantoso@engineering.edu',
        'nomor_telepon' => '86123456789',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Sari Rahayu',
        'nomor_bib' => '6907',
        'email' => 'sarirahayu@hotmail.com',
        'nomor_telepon' => '84234567890',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Rian Firmansyah',
        'nomor_bib' => '9240',
        'email' => 'rianfirmansyah@company.com',
        'nomor_telepon' => '88345678901',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Nia Kurniasih, S.Pd',
        'nomor_bib' => '1573',
        'email' => 'niakurniasih@school.edu',
        'nomor_telepon' => '82456789012',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Dimas Prasetyo',
        'nomor_bib' => '3896',
        'email' => 'dimasprasetyo@gmail.com',
        'nomor_telepon' => '85567890123',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'dr. Indah Permatasari',
        'nomor_bib' => '5130',
        'email' => 'indahpermatasari@clinic.id',
        'nomor_telepon' => '81678901234',
        'kategori_lari' => '2.5KIDS',
    ],
    [
        'nama' => 'Bimo Satrianto',
        'nomor_bib' => '7462',
        'email' => 'bimosatrianto@yahoo.com',
        'nomor_telepon' => '87789012345',
        'kategori_lari' => '10K',
    ],
    [
        'nama' => 'Dewi Lestari, M.M',
        'nomor_bib' => '2795',
        'email' => 'dewilestari@business.co.id',
        'nomor_telepon' => '83890123456',
        'kategori_lari' => 'Half Marathon',
    ],
    [
        'nama' => 'Fajar Nugroho',
        'nomor_bib' => '4020',
        'email' => 'fajarnugroho@pd.ac.id',
        'nomor_telepon' => '86901234567',
        'kategori_lari' => '5K TEENS',
    ],
    [
        'nama' => 'Ratih Wulandari',
        'nomor_bib' => '6353',
        'email' => 'ratihwulandari@hotmail.com',
        'nomor_telepon' => '84012345678',
        'kategori_lari' => '5K OPEN',
    ],
    [
        'nama' => 'Drs. Bambang Sutrisno, M.Pd',
        'nomor_bib' => '8686',
        'email' => 'bambangsutrisno@education.sch.id',
        'nomor_telepon' => '88123456789',
        'kategori_lari' => '2.5KIDS',
    ],
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