<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('title');
			$table->binary('icon')->nullable()->default(null);
			$table->text('descr')->nullable()->default(null);
			$table->float('price_per_user')->nullable()->default(null);
			$table->text('delivery_info')->nullable()->default(null);
			$table->datetime('deliver_from')->nullable()->default(null);
			$table->datetime('deliver_to')->nullable()->default(null);
			$table->boolean('is_delivered')->default(false);
			$table->integer('type')->default(0); 		// ???? 
			$table->integer('status')->default(0); 		// ???? 
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
        Schema::dropIfExists('supplies');
    }
}
