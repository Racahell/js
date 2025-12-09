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
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->unsignedBigInteger('id_detail');
            $table->date('tanggal_kirim');
            $table->date('tanggal_menerima')->nullable(); 
            $table->timestamps(); 

            
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
        Schema::dropIfExists('pengiriman');
    }
};
