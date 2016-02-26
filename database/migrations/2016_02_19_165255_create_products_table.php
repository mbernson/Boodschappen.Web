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

            $table->string('title');
            $table->string('brand')->nullable();
            $table->string('unit_size')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->string('source_url')->nullable();
            $table->string('source_id')->unique();

            $table->integer('generic_product_id')->references('id')->on('generic_products');

            $table->json('extended_attributes')->nullable();

            $table->timestamp('created_at')->default(DB::raw('now()'));
            $table->timestamp('updated_at')->default(DB::raw('now()'));

            $table->index('barcode');
        });

        DB::connection()->getPdo()->exec("alter table products add column barcode_type barcode_type;");
        DB::connection()->getPdo()->exec("create index idx_product_barcode_type on products(barcode_type);");
        DB::connection()->getPdo()->exec('CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
   NEW.created_at = now();
   RETURN NEW;
END;
$$ language \'plpgsql\';');
        DB::connection()->getPdo()->exec("CREATE TRIGGER update_products_on_update BEFORE UPDATE
    ON products FOR EACH ROW EXECUTE PROCEDURE
    update_updated_at_column();");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
        DB::connection()->getPdo()->exec("drop type barcode_type;");
    }
}
