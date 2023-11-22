<?php

// Filename: 2023_03_01_120001_create_data_series_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('data_series_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extended_product_list_id')->constrained('extended_product_list_tables')->onDelete('cascade');
            $table->string('cod');
            $table->date('data');
            $table->decimal('ult', 8, 2)->nullable();
            $table->decimal('mini', 8, 2)->nullable();
            $table->decimal('maxi', 8, 2)->nullable();
            $table->decimal('abe', 8, 2)->nullable();
            $table->integer('volumes')->nullable();
            $table->integer('cab')->nullable();
            $table->decimal('med', 8, 2)->nullable();
            $table->integer('aju')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_series_tables');
    }
};
