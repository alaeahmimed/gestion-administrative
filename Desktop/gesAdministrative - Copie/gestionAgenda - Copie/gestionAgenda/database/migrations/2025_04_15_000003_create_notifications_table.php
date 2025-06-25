<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->date('date');
            $table->unsignedBigInteger('sender_id')->default(0);; // L'expéditeur
            $table->unsignedBigInteger('receiver_id')->default(0);; // Le destinataire
            $table->string('type')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
    }
};