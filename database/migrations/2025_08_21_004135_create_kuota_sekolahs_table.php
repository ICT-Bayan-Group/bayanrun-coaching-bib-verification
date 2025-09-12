<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuota_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_bola_id')->constrained('sekolah_bolas')->onDelete('cascade');
            $table->integer('kuota_7_8')->default(0)->comment('Kuota untuk kategori umur 7-8 tahun');
            $table->integer('kuota_9_10')->default(0)->comment('Kuota untuk kategori umur 9-10 tahun');
            $table->integer('kuota_11_12')->default(0)->comment('Kuota untuk kategori umur 11-12 tahun');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable()->comment('Catatan tambahan dari admin');
            $table->timestamps();

            $table->index('sekolah_bola_id');
            $table->index('updated_by');
            $table->unique('sekolah_bola_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuota_sekolahs');
    }
};