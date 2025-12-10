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
  Schema::create('user_ads', function (Blueprint $table) {
   $table->id();
   $table->integer('user_id');
   $table->integer('company_id');
   $table->enum('status', ['pending', 'complete', 'reject'])->default('pending')->change();
   $table->decimal('amount', 10, 2)->default(0);
   $table->boolean('is_active');
   $table->timestamps();
  });
 }

 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::dropIfExists('user_ads');
 }
};
