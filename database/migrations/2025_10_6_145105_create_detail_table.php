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
        Schema::create('detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_transaksi');
            $table->string('harga');
            $table->string('jumlah');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail');
    }
};
