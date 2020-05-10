<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.   
	 *	Operation Template [ 
	 *  { operation_code:1, operations:[ op.0, op.1 ], start_at_start_id:-1, start_at_finish_id:-1, start_by_plan:-1, finish_by_plan:-1, start_prognosed, finish_prognosed, start_actual, finish_actual
	 *	{ operation_code:op.0, start_at_start_id:-1, start_at_finish_id:-1, start_by_plan:xx, finish_by_plan:xx, start_prognosed, finish_prognosed, start_actual, finish_actual },  
	 * 	{ operation_code:op.1, start_at_start_id:-1, start_at_finish_id:1, start_by_plan:xx, finish_by_plan:xx, start_prognosed, finish_prognosed, start_actual, finish_actual } 
	 *  ]
     *				
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('descr')->nullable()->default(null);
			$table->bigInteger('cultivation_assignment_id')->unsigned();
            $table->boolean('is_accepted')->default(false);
			$table->boolean('is_finished')->default(false);
            $table->date('start_by_plan');
            $table->date('finish_by_plan');
            $table->date('start_prognosed')->nullable()->default(null);
            $table->date('finish_prognosed')->nullable()->default(null);
            $table->date('start_actual')->nullable()->default(null);
            $table->date('finish_actual')->nullable()->default(null);
			$table->foreign('cultivation_assignment_id')->references('id')->on('cultivation_assignments')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
}
