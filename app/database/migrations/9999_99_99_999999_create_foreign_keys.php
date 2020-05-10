<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/*
        Schema::table('operations', function (Blueprint $table) {
			$table->foreign('cultivation_assignment_id')->references('id')->on('cultivation_assignments')->onDelete('cascade');
		});

        Schema::table('debetings', function (Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
			$table->foreign('delivery_point_id')->references('id')->on('delivery_points')->onDelete('cascade');
		});

        Schema::table('refills', function (Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

        Schema::table('delivery_units', function (Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

        Schema::table('cultivation_assignments', function (Blueprint $table) {
			$table->foreign('crop_id')->references('id')->on('crops')->onDelete('cascade');
		});

        Schema::table('harvesting_assignments', function (Blueprint $table) {
			$table->foreign('cultivation_assignment_id')->references('id')->on('cultivation_assignments')->onDelete('cascade');
			$table->foreign('crop_id')->references('id')->on('crops')->onDelete('cascade');
			$table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
			$table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
		});
		*/
		;
	}


    public function down()
    {
		;
    }
}
