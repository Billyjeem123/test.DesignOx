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
    Schema::create('tbljob_posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('tblusers');
        $table->string('project_desc');
        $table->decimal('budget', 10, 2); // Assuming a decimal data type for budget
        $table->string('duration'); // You can store the duration as a string
        $table->string('experience_level');
        $table->integer('numbers_of_proposals');
        $table->integer('on_going')->default(0);
        $table->string('project_link_attachment')->nullable();
        $table->tinyInteger('has_paid')->default(0); // Assuming a boolean-like field for payment status
        $table->timestamp('work_start_time')->nullable(); // Timestamp for project start time
        $table->timestamp('work_end_time')->nullable(); // Timestamp for project end time
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
        Schema::dropIfExists('tbljob_posts');
    }
};
