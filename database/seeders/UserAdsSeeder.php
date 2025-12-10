<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAdsSeeder extends Seeder
{
 /**
  * Run the database seeds.
  */
 public function run(): void
 {
  DB::table('user_ads')->insert([
   [
    'user_id' => 1,
    'company_id' => 1,
    'status' => 'pending',
    'amount' => 100.50,
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
   ],
   [
    'user_id' => 2,
    'company_id' => 2,
    'status' => 'complete',
    'amount' => 250.00,
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
   ],
   [
    'user_id' => 3,
    'company_id' => 1,
    'status' => 'reject',
    'amount' => 75.00,
    'is_active' => false,
    'created_at' => now(),
    'updated_at' => now(),
   ],
  ]);
 }
}
