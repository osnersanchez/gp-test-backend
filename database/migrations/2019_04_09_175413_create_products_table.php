<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');                        
            $table->string('name');
            $table->integer('price');
            $table->integer('quantity');
            $table->text('description');
            $table->string('photo');
            $table->timestamps();
     
            $table->integer('idUser')->unsigned();
            $table->integer('idCategory')->unsigned();

            $table->foreign('idUser')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('idCategory')->references('id')->on('categories')->onDelete('cascade');

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
