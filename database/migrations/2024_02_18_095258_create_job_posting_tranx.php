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
        Schema::create('job_posting_payment_tranx', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id');
            $table->string('reference');
            $table->string('email');
            $table->string('amount');
            $table->foreign('job_post_id')->references('id')->on('job_posts');

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
        Schema::dropIfExists('job_posting_tranx');
    }
};
