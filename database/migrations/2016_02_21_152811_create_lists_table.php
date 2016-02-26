<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->references('id')->on('users');
            $table->string('title');
            $table->integer('count')->default(0);

            $table->timestamps();
        });
        Schema::create('shopping_list_has_product', function (Blueprint $table) {
            $table->integer('list_id')->references('id')->on('shopping_lists');
            $table->integer('product_id')->references('id')->on('products');
            $table->timestamp('created_at')->default(DB::raw('now()'));

            $table->primary(['list_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shopping_list_has_product');
        Schema::drop('shopping_lists');
    }
}
