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
        Schema::create('data_kehadiran', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->date('tanggal');
            $table->foreignId('status_kehadiran_id')->nullable()->constrained('status_kehadiran');
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->time('tgl_upload_bukti')->nullable();
            $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kehadiran');
    }
};
