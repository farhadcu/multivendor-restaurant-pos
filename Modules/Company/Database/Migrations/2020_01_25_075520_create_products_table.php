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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->string('name',150);
            $table->string('model')->unique();
            $table->string('sku')->nullable()->comment = "Stock Keeping Unit";
            $table->string('upc')->nullable()->comment = "Universal Product Code";
            $table->string('mpn')->nullable()->comment = "Manufacturer Part Number";
            $table->string('image')->nullable();
            $table->float('purchase_price',8,2)->nullable();
            $table->float('selling_price',8,2)->nullable();
            $table->float('qty',8,2)->nullable();
            $table->float('min_qty',8,2)->nullable();
            $table->float('max_qty',8,2)->nullable();
            $table->string('stock_unit')->nullable();
            $table->string('rack_no')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->date('mpg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->enum('subtract_stock',['1','2'])->default('2')->comment = "1 = Yes, 2 = No";
            $table->enum('returnable',['1','2'])->default('2')->comment = "1 = Yes, 2 = No";
            $table->enum('status',['1','2'])->default('1')->comment = "1 = active, 2 = deactive";
            $table->longText('history')->nullable();
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
