<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
 /**
  * Run the database seeds.
  */
 public function run(): void
 {
  DB::table('companies')->insert([
   [
    'name' => 'Company A',
    'logo' => null,
    'type' => 'ads',
    'status' => 'active',
    'description' => 'This is Company A description.',
    'amount' => '1000',
    'url' => 'https://companya.example.com',
    'created_at' => now(),
    'updated_at' => now(),
   ],
   [
    'name' => 'Company B',
    'logo' => null,
    'type' => 'tasks',
    'status' => 'inactive',
    'description' => 'This is Company B description.',
    'amount' => '500',
    'url' => 'https://companyb.example.com',
    'created_at' => now(),
    'updated_at' => now(),
   ],
   [
    'name' => 'Company C',
    'logo' => null,
    'type' => 'survey',
    'status' => 'active',
    'description' => 'This is Company C description.',
    'amount' => '750',
    'url' => 'https://companyc.example.com',
    'created_at' => now(),
    'updated_at' => now(),
   ],
  ]);
 }
}
