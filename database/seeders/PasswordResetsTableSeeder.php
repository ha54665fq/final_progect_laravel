<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            DB::table('password_resets')->insert([
                'email' => 'user' . $i . '@example.com',
                'token' => Str::random(60),
                'created_at' => Carbon::now()->subMinutes(rand(0, 1000)),
            ]);
        }
    }
}
