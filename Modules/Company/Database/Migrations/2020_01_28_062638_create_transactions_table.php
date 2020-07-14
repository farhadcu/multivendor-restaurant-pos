<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('transaction_no')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->integer('transaction_type_id')->comment = "1=Deposit, 2=Expense, 3=AP, 4=AR, 5= Account Transfer ";
            $table->string('transaction_type');
            $table->integer('account_id')->nullable();
            $table->unsignedBigInteger('transaction_category_id')->nullable();
            $table->foreign('transaction_category_id')->references('id')->on('transaction_categories');
            $table->decimal('amount');
            $table->text('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('balance')->nullable()->default('0.00');
            $table->unsignedBigInteger('transfer_reference')->default('0');
            $table->string('document')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
