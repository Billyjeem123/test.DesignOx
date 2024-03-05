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
        Schema::create('job_type_job_post', function (Blueprint $table) {
            $table->unsignedBigInteger('job_type_id');
            $table->unsignedBigInteger('job_post_id');
            $table->foreign('job_type_id')->references('id')->on('job_type')->onDelete('cascade');
            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->primary(['job_type_id', 'job_post_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_type_job_post');
    }
};
