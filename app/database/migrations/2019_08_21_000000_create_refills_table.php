<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refills', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->bigInteger('made_by_id');
			$table->float('amount');	
            $table->string('title');
            $table->string('descr')->nullable()->default(null);
            $table->datetime('made_at')->nullable()->default(null);
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
        Schema::dropIfExists('refills');
    }
}
