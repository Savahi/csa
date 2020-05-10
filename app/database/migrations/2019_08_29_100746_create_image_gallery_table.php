<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class CreateImageGalleryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_gallery', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('title');
			$table->text('descr')->nullable()->default(null);
			$table->string('icon_name')->nullable()->default(null);
			$table->string('file_name')->nullable()->default(null);
			$table->binary('icon')->nullable()->default(null);
			$table->bigInteger('image_gallery_album_id')->default(1);
			$table->timestamp('datetime')->default( DB::raw('CURRENT_TIMESTAMP()') );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_gallery');
    }
}
