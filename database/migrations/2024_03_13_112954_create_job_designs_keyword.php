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
        Schema::create('job_designs_keyword', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_design_id');
            $table->string('keywords');
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
        Schema::dropIfExists('job_designs_keyword');
    }
};
