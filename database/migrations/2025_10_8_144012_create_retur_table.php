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
        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->unsignedBigInteger('id_detail');
            $table->string('alasan')->nullable();
            $table->date('tanggal_retur')->nullable();
            $table->timestamps();

            // Definisikan foreign key
            $table->foreign('id_detail')
                  ->references('id_detail')
                  ->on('detail')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
