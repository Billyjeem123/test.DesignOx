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
    Schema::create('job_posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('users');
        $table->string('project_desc');
        $table->string('project_type');
        $table->string('tools_used');
        $table->decimal('budget', 10, 2); // Assuming a decimal data type for budget
        $table->string('duration'); // You can store the duration as a string
        $table->string('experience_level');
        $table->integer('numbers_of_proposals');
        $table->string('project_link_attachment')->nullable();
        $table->string('payment_channel')->nullable();
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
        Schema::dropIfExists('job_posts');
    }
};
