<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('extended_product_list_tables', function (Blueprint $table) {
            $table->id();
            $table->string('Código_Produto')->unique();
            $table->string('Classificação')->nullable();
            $table->string('Subproduto')->nullable();
            $table->string('Local')->nullable();
            $table->string('Fetch_Status')->nullable();
            $table->integer('bolsa')->nullable();
            $table->integer('roda')->nullable();
            $table->integer('fonte')->nullable();
            $table->string('tav')->nullable();
            $table->string('subtav')->nullable();
            $table->integer('decimais')->nullable();
            $table->integer('correlatos')->nullable();
            $table->string('empresa')->nullable();
            $table->string('contrato')->nullable();
            $table->integer('subproduto_id')->nullable();
            $table->string('entcode')->nullable();
            $table->string('nome')->nullable();
            $table->string('longo')->nullable();
            $table->text('descr')->nullable();
            $table->string('codf')->nullable();
            $table->string('bd')->nullable();
            $table->text('palavras')->nullable();
            $table->integer('habilitado')->nullable();
            $table->integer('lote')->nullable();
            $table->integer('rep')->nullable();
            $table->integer('vln')->nullable();
            $table->integer('dia')->nullable();
            $table->string('freq')->nullable();
            $table->string('dex')->nullable();
            $table->dateTime('inserido')->nullable();
            $table->dateTime('alterado')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extended_product_list_tables');
    }
};

