<?php

namespace Boodschappen\Jobs;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\GenericProduct;
use Boodschappen\Database\Product;
use Boodschappen\Domain\Product as DomainProduct;
use Boodschappen\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueryProductsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $adapter;
    private $query;
    /** @var GenericProduct[]|null */
    private $categories = null;

    /**
     * queryBarcodeInfoJob constructor.
     * @param string $adapter
     * @param $query
     */
    public function __construct($adapter, $query)
    {
        $this->adapter = $adapter;
        $this->query = $query;
        /** @var GenericProduct $category */
        $category = GenericProduct::where('title', 'ilike', "$query")->first();
        if($category) {
            $this->categories = $category->subcategories();
        }
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
            /** @var DomainProduct $domain_product */
            foreach($products as $domain_product) {
                $product = $this->saveOrUpdateProduct($domain_product);
                $product->updatePrice($domain_product->current_price, $company_id);
            }
        }
    }

    /**
     * @param DomainProduct $domain_product
     * @return Product
     */
    private function saveOrUpdateProduct(DomainProduct $domain_product)
    {
        if($domain_product->barcode != null) {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'barcode_type' => $domain_product->barcode->type,
                'barcode' => $domain_product->barcode->value,
            ]);
        } elseif($domain_product->sku != null) {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'sku' => $domain_product->sku,
            ]);
        } else {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'title' => $domain_product->title,
                'brand' => $domain_product->brand,
                'unit_size' => $domain_product->unit_size,
            ]);
        }
        $product->fill((array) $domain_product);
        if($product->exists) {
            echo "Updating product $product->title\n";
        } else {
            echo "Adding new product $product->title\n";
        }

        $category = $product->guessCategory($domain_product->title, $this->categories);

        $product->generic_product_id = $category->id;
        $product->save();

        return $product;
    }

}
