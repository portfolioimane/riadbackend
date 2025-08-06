<?php
// database/migrations/xxxx_xx_xx_create_employees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
                     $table->string('email')->unique()->nullable();
            $table->timestamps();
            $table->string('role')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
