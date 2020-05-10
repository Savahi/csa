<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title'); 	
            $table->text('descr')->nullable()->default(null); 	
            $table->string('address'); 	
            $table->double('latitude')->nullable()->default(null);  	
            $table->double('longitude')->nullable()->default(null);  	
			$table->binary('icon')->nullable()->default(null);
            $table->text('delivery_info')->nullable()->default(null); 	
            $table->text('pickup_info')->nullable()->default(null); 	
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
        Schema::dropIfExists('delivery_points');
    }
}
