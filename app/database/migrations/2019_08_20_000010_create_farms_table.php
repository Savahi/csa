<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->binary('icon')->nullable()->default(null);
            $table->string('title'); 	
            $table->string('address'); 	
            $table->float('latitude')->nullable()->default(null);
            $table->float('longitude')->nullable()->default(null);
            $table->text('descr')->nullable()->default(null); 	
            $table->double('square');
            $table->double('prepared_square')->nullable()->default(null);
            $table->string('square_unit')->default('sq.m.');
			$table->string('image')->nullable()->default(null);
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
        Schema::dropIfExists('farms');
    }
}
