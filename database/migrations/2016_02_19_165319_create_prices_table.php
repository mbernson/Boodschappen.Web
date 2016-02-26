<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->bigInteger('product_id')->references('id')->on('products');

            $table->integer('company_id')->nullable()->references('id')->on('companies');
//            $table->integer('store_id')->nullable()->references('id')->on('stores');

            $table->decimal('price', 8, 2);

            $table->timestamp('created_at')->default(DB::raw('now()'));

            $table->primary(['product_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prices');
    }
}
