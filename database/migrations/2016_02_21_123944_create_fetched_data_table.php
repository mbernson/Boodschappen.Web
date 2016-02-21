<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFetchedDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fetched_data', function (Blueprint $table) {
            $table->increments('id');

            $table->string('barcode');

            $table->string('source');

            $table->json('response')->nullable();

            $table->timestamp('created_at')->default(DB::raw('now()'));
        });
        DB::connection()->getPdo()->exec("alter table fetched_data add column barcode_type barcode_type not null;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fetched_data');
    }
}
