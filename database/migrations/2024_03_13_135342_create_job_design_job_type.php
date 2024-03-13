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
        Schema::create('job_design_job_type', function (Blueprint $table) {
            $table->unsignedBigInteger('job_type_id');
            $table->unsignedBigInteger('job_design_id');
            $table->foreign('job_type_id')->references('id')->on('job_type')->onDelete('cascade');
            $table->foreign('job_design_id')->references('id')->on('job_designs')->onDelete('cascade');
            $table->primary(['job_type_id', 'job_design_id']);
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
        Schema::dropIfExists('job_design_job_type');
    }
};
