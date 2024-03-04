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
        Schema::dropIfExists('job_type'); // Drop the table if it exists
        Schema::create('job_type', function (Blueprint $table) {
            $table->unsignedBigInteger('job_post_id'); // Correct the column name
            $table->unsignedBigInteger('job_type_id');
            $table->foreign('job_post_id')->references('id')->on('tbljob_posts')->onDelete('cascade');
            $table->foreign('job_type_id')->references('id')->on('tbljob_type')->onDelete('cascade');
            // Assuming you have primary key constraints for job_post_id and job_type_id
            // $table->primary(['job_post_id', 'job_type_id']);
        });
    }




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_type');
    }
};
