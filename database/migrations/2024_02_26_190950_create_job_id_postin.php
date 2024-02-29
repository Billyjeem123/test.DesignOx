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
        Schema::table('tbljob_posting_projecttype', function (Blueprint $table) {
            $table->foreignId('job_post_id')->constrained('tbljob_posts')->before('project_type'); // Define job_post_id as a foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbljob_posting_projecttype', function (Blueprint $table) {
//
        });
    }
};
