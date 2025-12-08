<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpx_serveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id')->unique();
            $table->unsignedSmallInteger('loi');
            $table->decimal('payout', 8, 2);
            $table->decimal('conversion_rate', 8, 2);
            $table->unsignedTinyInteger('quality_score');
            $table->unsignedInteger('statistics_rating_count')->default(0);
            $table->decimal('statistics_rating_avg', 3, 2)->default(0.00);
            $table->string('type', 5)->nullable();
            $table->boolean('top')->default(false);
            $table->boolean('details')->default(false);
            $table->decimal('payout_publisher_usd', 8, 2)->default(0.00);
            $table->string('href_new')->nullable();
            $table->boolean('webcam')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpx_serveys');
    }
};
