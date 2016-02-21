<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        create type barcode_type as enum (
	        'org.gs1.EAN-8',
	        'org.gs1.EAN-13',
	        'org.gs1.UPC-E'
        );";
        DB::connection()->getPdo()->exec($sql);

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title')->nullable();
            $table->string('barcode')->unique();

            $table->integer('category_id')->nullable()->references('id')->on('categories');

            $table->timestamp('created_at')->default(DB::raw('now()'));
            $table->timestamp('updated_at')->default(DB::raw('now()'));

            $table->index('barcode');
        });

        DB::connection()->getPdo()->exec("alter table products add column barcode_type barcode_type not null;");
        DB::connection()->getPdo()->exec("create index idx_product_barcode_type on products(barcode_type);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
