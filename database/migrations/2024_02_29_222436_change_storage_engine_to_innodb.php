<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Retrieve the list of tables
        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND ENGINE = ?", [env('DB_DATABASE'), 'MyISAM']);

        // Iterate through each table and change the storage engine to InnoDB
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            DB::statement("ALTER TABLE `$tableName` ENGINE = InnoDB");
        }
    }
};
