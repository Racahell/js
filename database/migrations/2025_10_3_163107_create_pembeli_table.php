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
        Schema::create('pembeli', function (Blueprint $table) {
            $table->id('id_pembeli');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_pembeli');
            $table->string('alamat');
            $table->string('notlp');
            $table->string('email');
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('user')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembeli');
    }
};
