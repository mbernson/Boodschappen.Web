<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generic_products', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')->unique();
            $table->integer('parent_id')->nullable()->references('id')->on('generic_products');
            $table->integer('depth')->default(0);

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
        Schema::drop('generic_products');
    }
}
