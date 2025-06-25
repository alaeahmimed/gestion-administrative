<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
     Schema::create('emplois', function (Blueprint $table) {
    $table->id();
    $table->string('cycle')->nullable();
    $table->string('classe')->nullable(); // Retirer unique() car vous voulez pouvoir mettre à jour les emplois
    $table->string('file_path');
    $table->foreignId('administrateur_id')->nullable()->cconstrained()->onDelete('cascade');
    $table->nullableMorphs('emploisable');
    $table->timestamps();

     $table->unique(['cycle', 'classe']);
});
}

    public function down(): void {
        Schema::dropIfExists('emplois');
    }
};