<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryImagesTable extends Migration
{
    public function up()
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path'); // path or filename
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gallery_images');
    }
}
