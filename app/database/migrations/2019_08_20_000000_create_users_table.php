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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default(Hash::make('password'));
			$table->text('contacts')->nullable()->default(null);
			$table->text('descr')->nullable()->default(null);
			$table->binary('icon')->nullable()->default(null);
            $table->float('balance')->default(0.0);		
            $table->float('deposit')->default(0.0);		
            $table->text('deposit_comment')->nullable()->default(null);		
			$table->text('comments')->nullable()->default(null);
            $table->boolean('is_suspended_for_supply')->default(false);		
            $table->bigInteger('delivery_point_id')->default(-1); 		// Delivery point id
            $table->integer('admin_privilegies')->default(0);
            $table->bigInteger('sorting_station_admin')->default(-1);	// Sorting station id	
            $table->bigInteger('delivery_point_admin')->default(-1);	// Delivery point id
            $table->bigInteger('delivery_unit_admin')->default(-1);		// Delivery unit id
            $table->bigInteger('farm_admin')->default(-1);		        // Farm id
            $table->rememberToken();
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
