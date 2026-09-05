<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slogans', function (Blueprint $table): void {
            $table->id();
            $table->string('title_tr', 255);
            $table->string('title_en', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slogans');
    }
};
