<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('staff')->insert([
            [
                'username' => 'vt',
                'password' => bcrypt('12345'),
                'role' => 1,
                'chain_store_id' => 1
            ], [
                'username' => 'vt2',
                'password' => bcrypt('12345'),
                'role' => 0,
                'chain_store_id' => 1
            ],
        ]);
    }
}
