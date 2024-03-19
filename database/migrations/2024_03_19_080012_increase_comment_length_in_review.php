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
        Schema::table('job_design_reviews', function (Blueprint $table) {
            // Drop the existing 'comment' column
            $table->dropColumn('comment');
        });

        Schema::table('job_design_reviews', function (Blueprint $table) {
            // Add the 'comment' column back as text type with nullable
            $table->text('reviews')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_design_reviews', function (Blueprint $table) {
            // Drop the 'comment' column
            $table->dropColumn('comment');
        });

        Schema::table('job_design_reviews', function (Blueprint $table) {
            // Add back the 'comment' column as string type with specified length
            $table->string('reviews', 255)->nullable();
        });
    }
};
