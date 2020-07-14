<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->string('purchase_no')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('total_item');
            $table->integer('total_qty');
            $table->double('total_cost');
            $table->double('order_tax_amount')->nullable();
            $table->double('order_discount')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->double('grand_total');
            $table->double('paid_amount')->default('0.00');
            $table->double('due_amount')->default('0.00');
            $table->enum('status',['1','2'])->comment = "1=Received, 2=Partial";
            $table->enum('payment_status',['1','2'])->default('2')->comment = "1=Paid, 2=Due";
            $table->string('document')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
