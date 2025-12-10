<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
 /**
  * Run the database seeds.
  */
 public function run(): void
 {
  User::create([
   'name' => 'Admin User',
   'email' => 'admin@example.com',
   'password' => Hash::make('password123'), // Change to a strong password
   'affiliate_code' => Str::upper(Str::random(10)),
   'is_verified' => true,
   'role' => 'admin',
  ]);
 }
}
