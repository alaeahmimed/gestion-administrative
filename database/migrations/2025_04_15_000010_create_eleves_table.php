<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('code_apogee')->unique()->nullable();
           
             $table->string('cycle');
            $table->string('classe');
            $table->foreignId('parentt_id')->constrained()->onDelete('cascade');
            $table->foreignId('emploi_id')->nullable()->constrained('emplois')->onDelete('cascade'); 
             $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('eleves');
    }
};