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
        Schema::table('job_designs', function (Blueprint $table) {
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_designs', function (Blueprint $table) {
            //
        });
    }
};
