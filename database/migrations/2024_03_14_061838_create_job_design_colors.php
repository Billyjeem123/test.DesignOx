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
        Schema::dropIfExists('job_design_job_colors');
        Schema::create('job_design_job_colors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_design_id');
            $table->unsignedBigInteger('color_id');
            $table->foreign('job_design_id')->references('id')->on('job_designs')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
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
        Schema::dropIfExists('job_design_job_colors');
    }
};
