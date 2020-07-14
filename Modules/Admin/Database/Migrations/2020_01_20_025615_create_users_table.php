<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('company_roles');
            $table->string('name',150);
            $table->string('email',100)->unique();
            $table->string('mobile')->unique();
            $table->enum('gender',['1','2'])->comment = "1 = Male, 2 = Female";
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
            $table->string('password',100);
            $table->enum('status',['1','2'])->default('1')->comment = "1 = active, 2 = deactive";
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
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
        Schema::dropIfExists('users');
    }
}
