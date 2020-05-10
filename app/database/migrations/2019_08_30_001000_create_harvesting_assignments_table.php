<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHarvestingAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('harvesting_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cultivation_assignment_id')->unsigned();  	// Refers to CULTIVATION ASSIGNMENT record
            $table->bigInteger('crop_id')->unsigned();  					// Refers to CROP table record
            $table->bigInteger('farm_id')->unsigned();  					// Refers to FARM table record
            $table->bigInteger('supply_id')->unsigned()->default(0);  		// Refers to FARM table record
            $table->string('title'); 	
            $table->text('descr')->nullable()->default(null); 	
            $table->boolean('is_accepted')->default(false);
			$table->boolean('is_finished')->default(false);
			$table->float('amount_by_plan');
            $table->datetime('start_by_plan');
            $table->datetime('finish_by_plan');
			$table->float('amount_prognosed')->default(-1);
            $table->datetime('start_prognosed')->nullable()->default(null);
            $table->datetime('finish_prognosed')->nullable()->default(null);;
			$table->float('amount_actual')->nullable()->default(null);
            $table->datetime('start_actual')->nullable()->default(null);
            $table->datetime('finish_actual')->nullable()->default(null);
			$table->string('amount_unit_code')->default('kg');
            $table->float('work_time')->nullable()->default(null);
            $table->timestamps();
			$table->foreign('cultivation_assignment_id')->references('id')->on('cultivation_assignments')->onDelete('restrict');
			$table->foreign('crop_id')->references('id')->on('crops')->onDelete('restrict');
			$table->foreign('farm_id')->references('id')->on('farms')->onDelete('restrict');
			$table->foreign('supply_id')->references('id')->on('supplies')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('harvesting_assignments');
    }
}
