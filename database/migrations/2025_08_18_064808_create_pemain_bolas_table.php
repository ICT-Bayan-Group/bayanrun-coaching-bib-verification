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
    Schema::create('pemain_bolas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sekolah_bola_id')->constrained()->onDelete('cascade');
        $table->string('nama');
        $table->integer('umur');
        $table->enum('umur_kategori', ['7-8', '9-10', '11-12']);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemain_bolas');
    }
};
