<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RentalManagerPhotosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function(Blueprint $table) {
            $table->increments('id');
            $table->string('disk')->nullable(); // if you want to move next images on the next disk (Laravel default disk keys)
            $table->boolean('is_external')->default(false); // is file external
            $table->string('external_url')->nullable(); // external file root url
            $table->string('path')->nullable(); // for storing path
            $table->string('caption')->nullable(); // support for caption
            $table->string('file_type', 20)->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_extension')->nullable();
            $table->boolean('has_thumbnails')->default(false);
            $table->text('thumbnails')->nullable(); // For storing a json array of thumbnails
            $table->timestamps();
        });

        Schema::create('photo_node', function(Blueprint $table) {
            $table->unsignedInteger('photo_id')->index()->nullable();
            $table->unsignedInteger('node_id')->index()->nullable();
            $table->string('node_type');
            $table->unsignedInteger('ordering')->nullable(); // for storing ordering

            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->primary(['photo_id', 'node_id', 'node_type']);
        });


    }

    public function down()
    {
        Schema::dropIfExists('photos');
        Schema::dropIfExists('photo_node');
    }

}
