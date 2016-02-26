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

            $table->string('source');

            $table->json('query');

            $table->json('response')->nullable();

            $table->timestamp('created_at')->default(DB::raw('now()'));
        });
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
