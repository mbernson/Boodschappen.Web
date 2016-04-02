<?php

namespace Boodschappen\Jobs;

use Log;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\Category;
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
    /** @var Category[]|null */
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
        /** @var Category $category */
        $category = Category::where('title', 'ilike', $query)->first();
        if($category) {
            $this->categories = $category->subcategories()->orderBy('depth', 'desc')->get();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
Log::notice("Running QueryProductsJob for query {$this->query}");
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
        if($domain_product->sku != null) {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'sku' => $domain_product->sku,
            ]);
        } elseif($domain_product->barcode != null) {
            /** @var Product $product */
            $product = Product::firstOrNew([
                'barcode_type' => $domain_product->barcode->type,
                'barcode' => $domain_product->barcode->value,
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

    if(empty($product->generic_product_id)) {
        if(empty($domain_product->category)) {
            $categoryInput = $domain_product->title;
            $category = $product->guessCategory($categoryInput, $this->categories);
        } else {
            $category = Category::firstOrCreate([
                'title' => $domain_product->category,
                'parent_id' => Category::FOOD,
            ]);
            echo "Created category $category->title\n";
            Product::cacheCategories();
        }
        $product->generic_product_id = $category->id;
    }

        $product->save();
        echo "\n";

        return $product;
    }

}
