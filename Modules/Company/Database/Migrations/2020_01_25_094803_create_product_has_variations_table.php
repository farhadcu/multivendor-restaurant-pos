<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductHasVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_has_variations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('variation_name');
            $table->string('variation_model')->unique();
            $table->float('variation_qty',8,2)->nullable();
            $table->string('price_prefix')->nullable();
            $table->decimal('variation_price')->nullable();
            $table->string('variation_weight')->nullable();
            $table->enum('is_primary',['1','2'])->default('2')->comment =" 1=yes, 2=no";
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
        Schema::dropIfExists('product_has_variations');
    }
}
