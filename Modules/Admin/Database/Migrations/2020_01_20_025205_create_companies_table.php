<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name')->unique();
            $table->string('company_slug')->unique();
            $table->string('owner_name');
            $table->string('email');
            $table->string('mobile');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('invoice_logo')->nullable();
            $table->string('invoice_prefix')->nullable();
            $table->string('vat_no')->nullable();
            $table->decimal('vat')->nullable()->comment = '%';
            $table->integer('type')->nullable();
            $table->enum('status',['1','2'])->default('1')->comment = "1 = active, 2 = deactive";
            $table->longText('history');
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
        Schema::dropIfExists('companies');
    }
}
