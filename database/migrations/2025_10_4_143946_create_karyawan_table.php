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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_karyawan');
            $table->string('alamat');
            $table->unsignedBigInteger('id_jabatan');
            $table->string('notlp');
            $table->string('email');
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('user')
                  ->onDelete('cascade');

            $table->foreign('id_jabatan')
                  ->references('id_jabatan')
                  ->on('jabatan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
