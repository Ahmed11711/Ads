<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 /**
  * Run the migrations.
  */
 public function up(): void
 {
  Schema::table('users', function (Blueprint $table) {
   // $table->timestamp('last_login_at')->nullable();

   // $table->string('bank_name')->nullable();
   // $table->string('iban')->nullable();
   // $table->string('country')->nullable();
   // $table->string('wallet')->nullable();
  });
 }

 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::table('users', function (Blueprint $table) {
   //
  });
 }
};
