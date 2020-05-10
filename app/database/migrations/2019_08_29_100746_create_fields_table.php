<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title'); 	
			$table->binary('icon')->nullable()->default(null);
            $table->text('descr')->nullable()->default(null); 	
            $table->bigInteger('farm_id'); 	
            $table->text('location')->nullable()->default(null); 	
            $table->float('square')->nullable()->default(null); 	
            $table->string('square_unit')->default('m'); 	
			$table->string('image')->default('');
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
        Schema::dropIfExists('fields');
    }
}
