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
        Schema::table('sekolah_bolas', function (Blueprint $table) {
            $table->string('user_token', 36)->unique()->nullable()->after('telepon');
            $table->index('user_token');
        });

        // Generate token untuk data yang sudah ada
        DB::statement("UPDATE sekolah_bolas SET user_token = UUID() WHERE user_token IS NULL");
        
        // Setelah semua terisi, buat field menjadi required
        Schema::table('sekolah_bolas', function (Blueprint $table) {
            $table->string('user_token', 36)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah_bolas', function (Blueprint $table) {
            $table->dropIndex(['user_token']);
            $table->dropColumn('user_token');
        });
    }
};