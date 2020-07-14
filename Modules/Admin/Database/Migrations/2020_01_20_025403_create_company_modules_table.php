<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module_name',100);
            $table->string('module_link',100)->default('javascript:void(0);');
            $table->string('module_icon',50);
            $table->integer('module_sequence');
            $table->unsignedInteger('parent_id')->default('0')->nullable();
            $table->enum('status',['1','2'])->default('1')->comment = "1 = active, 2 = deactive";
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
        Schema::dropIfExists('company_modules');
    }
}
