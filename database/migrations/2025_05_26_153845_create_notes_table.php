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
        Schema::create('notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
    $table->string('matiere');
    $table->decimal('cc1', 4, 2)->nullable();
    $table->decimal('cc2', 4, 2)->nullable();
    $table->decimal('cc3', 4, 2)->nullable();
    $table->decimal('projet', 4, 2)->nullable();
    $table->timestamps();
    
    $table->unique(['eleve_id', 'matiere']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
