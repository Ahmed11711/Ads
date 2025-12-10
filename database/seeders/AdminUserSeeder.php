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
  User::create([
   'name' => 'user',
   'email' => 'user@gmail.com',
   'password' => Hash::make('123123'), // تشفير كلمة المرور
   'affiliate_code' => strtoupper(uniqid()), // كود افتراضي
   'is_verified' => true,
   'role' => 'user', // تأكد ان الدور صحيح
  ]);
 }
}
