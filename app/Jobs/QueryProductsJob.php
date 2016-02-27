<?php

namespace Boodschappen\Jobs;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\GenericProduct;
use Boodschappen\Database\Product;
use Boodschappen\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueryProductsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $adapter;
    private $query;

    /**
     * queryBarcodeInfoJob constructor.
     * @param string $adapter
     * @param $query
     */
    public function __construct($adapter, $query)
    {
        $this->adapter = $adapter;
        $this->query = $query;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var ProductDataSource $source */
        $source = app()->make($this->adapter);

        $company_id = $source->getCompanyId();

        $products = $source->query($this->query);

        if(empty($products)) {
            echo "No products found for query $this->query";
        } else {
            foreach($products as $data) {
                $price = $data['price'];
                unset($data['price']);
                $product = $this->saveOrUpdateProduct($data);
                $product->updatePrice($price, $company_id);
            }
        }
    }

    /**
     * @param array $data
     * @return Product
     */
    private function saveOrUpdateProduct(array $data)
    {
        if(array_key_exists('barcode', $data)) {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'barcode' => $data['barcode'],
            ]);
        } else {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'title' => $data['title'],
                'brand' => $data['brand'],
                'unit_size' => $data['unit_size'],
            ]);
        }
        $product->fill($data);
        if($product->exists) {
            echo "Updating product $product->title\n";
        } else {
            echo "Adding new product $product->title\n";
        }
        $category = $product->guessCategory($data['title']);
        $product->generic_product_id = $category->id;
        $product->save();
        return $product;
    }

}
