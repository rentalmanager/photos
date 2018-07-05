<?php echo '<?php' ?>

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RentalManagerPhotosSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Create table for the property identifications
        Schema::create('{{ $photos['tables']['photos'] }}', function(Blueprint $table) {
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

        // Pivot table
        Schema::create('{{ $photos['tables']['photo_nodes'] }}', function(Blueprint $table) {
            $table->unsignedInteger('{{ $photos['foreign_keys']['photo'] }}');
            $table->unsignedInteger('node_id')->index();
            $table->string('node_type');
            $table->unsignedInteger('ordering')->nullable(); // for storing ordering

            $table->foreign('{{ $photos['foreign_keys']['photo'] }}')->references('id')->on('{{ $photos['tables']['photos'] }}')->onDelete('cascade');
            $table->primary(['{{ $photos['foreign_keys']['photo'] }}', 'node_id', 'node_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $photos['tables']['photos'] }}');
        Schema::dropIfExists('{{ $photos['tables']['photo_nodes'] }}');
    }
}