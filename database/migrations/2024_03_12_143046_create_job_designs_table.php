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
    public function up(): void
    {
        Schema::dropIfExists('job_designs');
        Schema::create('job_designs', function (Blueprint $table) {
            $table->id();
            $table->string('project_title');
            $table->string('project_desc');
            $table->string('project_type');
            $table->decimal('project_price', 10, 2); // Assuming a decimal data type for budget
            $table->string('attachment');
            $table->string('downloadable_file');
            $table->unsignedBigInteger('talent_id');

            $table->foreign('talent_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('job_designs');
    }
};
