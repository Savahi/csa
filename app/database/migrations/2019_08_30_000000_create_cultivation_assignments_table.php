<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCultivationAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cultivation_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('farm_id')->unsigned();
			$table->bigInteger('crop_id')->unsigned();
            $table->string('title');
            $table->text('descr')->nullable()->default(null);
            $table->boolean('is_created_by_farm')->default(false);
            $table->boolean('is_accepted')->default(false);
			$table->boolean('is_finished')->default(false);
			$table->string('amount_unit')->default('kg');
			$table->float('amount_by_plan');
            $table->date('start_by_plan');
            $table->date('finish_by_plan');
			$table->float('amount_prognosed')->default(-1);
            $table->date('start_prognosed')->nullable()->default(null);
            $table->date('finish_prognosed')->nullable()->default(null);;
			$table->float('amount_actual')->default(0);
            $table->date('start_actual')->nullable()->default(null);
            $table->date('finish_actual')->nullable()->default(null);
			$table->double('square')->nullable()->default(null);
			$table->string('square_unit')->default('sq.m.');
            $table->float('work_time')->nullable()->default(null);
            $table->timestamps();
			$table->foreign('farm_id')->references('id')->on('farms')->onDelete('restrict');
			$table->foreign('crop_id')->references('id')->on('crops')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cultivation_assignments');
    }
}
