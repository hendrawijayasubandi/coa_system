<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        DB::table('kategori')->insert([
            ['nama' => 'Salary', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Other Income', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Family Expense', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Transport Expense', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Meal Expense', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class CoaSeeder extends Seeder
{
    public function run()
    {
        DB::table('coa')->insert([
            ['kode' => '401', 'nama' => 'Gaji Karyawan', 'kategori_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '402', 'nama' => 'Gaji Ketua MPR', 'kategori_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '403', 'nama' => 'Profit Trading', 'kategori_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '601', 'nama' => 'Biaya Sekolah', 'kategori_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '602', 'nama' => 'Bensin', 'kategori_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '603', 'nama' => 'Parkir', 'kategori_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '604', 'nama' => 'Makan Siang', 'kategori_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '605', 'nama' => 'Makanan Pokok Bulanan', 'kategori_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        DB::table('transaksi')->insert([
            ['tanggal' => '2022-01-01', 'coa_kode' => '401', 'coa_nama' => 'Gaji Karyawan', 'desc' => 'Gaji Di Persuhaan A', 'debit' => 0.00, 'credit' => 5000000.00, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2022-01-02', 'coa_kode' => '402', 'coa_nama' => 'Gaji Ketua MPR', 'desc' => 'Gaji Ketum', 'debit' => 0.00, 'credit' => 7000000.00, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2022-01-10', 'coa_kode' => '602', 'coa_nama' => 'Bensin', 'desc' => 'Bensin Anak', 'debit' => 25000.00, 'credit' => 0.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            KategoriSeeder::class,
            CoaSeeder::class,
            TransaksiSeeder::class,
        ]);
    }
}
