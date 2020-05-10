<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debetings', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned(); 	// A 'from' user 
			$table->bigInteger('to_user_id')->default(0); 		// A 'to' user 
			$table->bigInteger('supply_id')->unsigned(); 		// A supply 
			$table->bigInteger('delivery_point_id')->unsigned(); 			// A delivery point 
			$table->boolean('is_delivered')->default(false); 	// A supply 
            $table->string('title')->nullable()->default(null);
			$table->float('amount');				// 
            $table->string('descr')->nullable()->default(null);
            $table->datetime('made_at')->default(NOW());
			$table->boolean('is_problem')->default(false); 	// If a problem occurs... 
			$table->string('problem')->nullable()->default(null); 	// A description of the problem... 
			$table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
			$table->foreign('supply_id')->references('id')->on('supplies')->onDelete('restrict');
			$table->foreign('delivery_point_id')->references('id')->on('delivery_points')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debetings');
    }
}
