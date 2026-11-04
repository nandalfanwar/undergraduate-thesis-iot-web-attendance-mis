<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusKehadiranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status_kehadiran')->insert([
            ['status_kehadiran_label'=>'Hadir'],
            ['status_kehadiran_label'=>'Alpa'],
            ['status_kehadiran_label'=>'Sakit'],
            ['status_kehadiran_label'=>'Izin'],
            ['status_kehadiran_label'=>'Cuti'],
            ['status_kehadiran_label'=>'Tugas'],
            ['status_kehadiran_label'=>'Terlambat'],
        ]);
    }
}
