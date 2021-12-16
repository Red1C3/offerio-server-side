<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('category', 64);
            $table->string('contact', 128);
            $table->string('imgName',128);
            $table->integer('amount');
            $table->date('timestamp-1');
            $table->date('timestamp-2');
            $table->date('timestamp-3');
            $table->date('timestamp-4');
            $table->decimal('price-1', 10, 4);
            $table->decimal('price-2', 10, 4);
            $table->decimal('price-3', 10, 4);
            $table->decimal('price-4', 10, 4);
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
        Schema::dropIfExists('products');
    }
}
