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
        Schema::dropIfExists('job_design_reviews');
        Schema::dropIfExists('design_reviews');
        Schema::create('job_design_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('job_design_id');
            $table->string('comment');
            $table->integer('ratings');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('job_design_id')->references('id')->on('job_designs')->onDelete('cascade');
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
        Schema::dropIfExists('design_reviews_migration');
    }
};
