<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersonalAccessTokensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            DB::table('personal_access_tokens')->insert([
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => rand(1, 10),
                'name' => 'Token ' . $i,
                'token' => Str::random(64),
                'abilities' => json_encode(['*']),
                'last_used_at' => Carbon::now()->subDays(rand(0, 30)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
