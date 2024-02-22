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
        Schema::create('tbljob_posting_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('tbljob_posts'); // Define job_post_id as a foreign key
            $table->string('tools');
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
        Schema::dropIfExists('tbljob_posting_tools');
    }
};
