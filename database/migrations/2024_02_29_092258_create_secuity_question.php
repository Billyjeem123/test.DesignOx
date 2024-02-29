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
        Schema::create('tblsecuity_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tbljob_posts'); // Define user_id as a foreign key
            $table->string('question', 255);
            $table->string('answer', 255);
            $table->integer('is_activated', 0);
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
        Schema::dropIfExists('tblsecuity_question');
    }
};
