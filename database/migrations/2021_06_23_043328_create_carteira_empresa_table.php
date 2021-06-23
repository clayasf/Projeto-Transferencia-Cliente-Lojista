<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteiraEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carteira_empresa', function (Blueprint $table) {
            $table->id('carteira_empresa_id');
            $table->money_format('saldo');
            $table->unsignedBigInteger('id');
            $table->foreign('id')->references('carteira_empresa_id')->on('empresas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carteira_empresa');
    }
}
