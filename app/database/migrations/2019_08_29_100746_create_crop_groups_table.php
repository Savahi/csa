<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCropGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crop_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->binary('icon')->nullable()->default(null);
			$table->string('title');
			$table->string('title_en')->nullable()->default(null);
			$table->string('title_de')->nullable()->default(null);
			$table->string('descr')->nullable()->default(null);
			$table->string('code')->nullable()->default(null);
			$table->string('image')->nullable()->default(null);
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
        Schema::dropIfExists('crop_groups');
    }
}
