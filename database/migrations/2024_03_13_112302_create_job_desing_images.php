<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_design_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_design_id');
            $table->string('images');
            $table->foreign('job_design_id')->references('id')->on('job_designs')->onDelete('cascade');
            $table->timestamps();
        }, 'ENGINE=InnoDB');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_desing_images');
    }
};
