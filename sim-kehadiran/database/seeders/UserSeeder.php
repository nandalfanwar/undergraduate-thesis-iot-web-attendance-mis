<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
            'uid_rfid'=>'E4594905',    
            'nama'=>'Guru/Tendik',
            'email'=>'gurutendik@example.com',
            'username'=>'gurutendik',
            'password'=>Hash::make('password'),
            'role_id'=>1
            ],
            [
            'uid_rfid'=>'1B944B05',
            'nama'=>'Staf Kedisiplinan',
            'email'=>'stafk@example.com',
            'username'=>'stafk',
            'password'=>Hash::make('password'),
            'role_id'=>2
            ],
            [
            'uid_rfid'=>'FBD13805',
            'nama'=>'Kepala Sekolah',
            'email'=>'kepsek@example.com',
            'username'=>'kepsek',
            'password'=>Hash::make('password'),
            'role_id'=>3
            ],
        ]);
    }
}
