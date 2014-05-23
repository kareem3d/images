<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('le_images', function(Blueprint $table)
		{
			$table->increments('id');

            // Name to identify image
            $table->string('name');

            // Relative path to the image
            $table->string('path');

            // Image position
            $table->string('order');

            // Polymorphic relationship
            $table->string('imageable_type');
            $table->integer('imageable_id');

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
		Schema::drop('le_images');
	}

}
