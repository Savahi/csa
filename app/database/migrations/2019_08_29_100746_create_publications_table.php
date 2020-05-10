<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class CreatePublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->text('title');
			$table->text('descr')->nullable()->default(null);
			$table->binary('icon')->nullable()->default(null);
			$table->datetime('created_at')->default( DB::raw('NOW()') );
			$table->integer('is_hidden')->default(true); 
			$table->text('text')->nullable()->default(null);
			$table->boolean('is_news')->default(false); 		//  'all', 'news', 'articles', 'supplies'
			$table->boolean('is_article')->default(false);
			$table->boolean('is_supply')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publications');
    }
}
