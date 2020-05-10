<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('descr')->nullable()->default(null); 	
            $table->string('type')->nullable()->default(null); 	
			$table->string('icon')->nullable()->default(null);  	
            $table->string('address')->nullable()->default(null);  	
            $table->double('latitude')->nullable()->default(null);  	
            $table->double('longitude')->nullable()->default(null);  	
            $table->double('square')->nullable()->default(null);  	
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
        Schema::dropIfExists('locations');
    }
}
