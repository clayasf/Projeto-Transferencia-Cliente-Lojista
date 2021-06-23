<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteiraUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carteira_usuario', function (Blueprint $table) {
            $table->id('carteira_usuario_id');
            $table->money_format('saldo');
            $table->unsignedBigInteger('id');
            $table->foreign('id')->references('carteira_usuario_id')->on('usuarios');
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
        Schema::dropIfExists('carteira_usuario');
    }
}
