<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('transaksi', function (Blueprint $table) {
        $table->unsignedBigInteger('id_pembeli')->nullable()->after('id_karyawan');
        $table->foreign('id_pembeli')->references('id_pembeli')->on('pembeli')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('transaksi', function (Blueprint $table) {
        $table->dropForeign(['id_pembeli']);
        $table->dropColumn('id_pembeli');
    });
}
};
