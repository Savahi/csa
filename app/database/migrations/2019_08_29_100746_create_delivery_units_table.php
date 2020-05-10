<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned(); 
            $table->string('title')->default('Untitled'); 		
            $table->string('descr')->nullable()->default(null); 		
            $table->float('tonnage')->default(0.0); 	
            $table->float('volume')->default(0.0); 	
			$table->string('image')->default('');
            $table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_units');
    }
}
