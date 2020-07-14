<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('table_no')->nullable();
            $table->integer('total_item');
            $table->float('total_qty',8,2)->default('0');
            $table->decimal('total_amount')->default('0');
            $table->decimal('discount_amount')->default('0');
            $table->string('vat_type')->nullable();
            $table->decimal('vat')->default('0');
            $table->string('adjustment_type')->nullable();
            $table->decimal('adjusted_amount')->default('0');
            $table->decimal('grand_total')->default('0');
            $table->decimal('recevied_amount')->default('0')->nullable();
            $table->decimal('changed_amount')->default('0')->nullable();
            $table->enum('status',['1','2','3'])->default('1')->comment = "1 = Complete, 2 = Pending, 3=Cancel";
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
        Schema::dropIfExists('orders');
    }
}
