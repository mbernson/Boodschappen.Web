<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->references('id')->on('users');

            $table->string('barcode');

            // Location

            $table->timestamp('created_at')->default(DB::raw('now()'));
        });
        DB::connection()->getPdo()->exec("alter table scans add column barcode_type barcode_type not null;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('scans');
    }
}
