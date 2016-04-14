<?php

namespace Boodschappen\Jobs;

use Log;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\Category;
use Boodschappen\Database\Product;
use Boodschappen\Domain\Product as DomainProduct;
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
        Log::notice("Running QueryProductsJob with adapter {$this->adapter} for query {$this->query}");
        /** @var ProductDataSource $source */
        $source = app()->make($this->adapter);

        $company_id = $source->getCompanyId();

        $products = array_filter($source->query($this->query)); // Filter nulls

        if(empty($products)) {
            Log::notice("No products found for query $this->query");
        } else {
            /** @var DomainProduct $domain_product */
            foreach($products as $domain_product) {
                $product = $this->saveOrUpdateProduct($domain_product);
                $product->updatePrice($domain_product->current_price, $company_id);
            }
            $count = count($products);
            Log::notice("QueryProductsJob processed {$count} products successfully from adapter {$this->adapter}.");
        }
    }

    /**
     * @param DomainProduct $domain_product
     * @return Product
     */
    private function saveOrUpdateProduct(DomainProduct $domain_product)
    {
        $product = Product::firstOrNew([
            'sku' => $domain_product->sku,
        ]);

        $product->fill((array) $domain_product);
        $product->fill((array) $domain_product->quantity);

        if(empty($product->generic_product_id)) {
            if(empty($domain_product->category)) {
                $categoryInput = $domain_product->title;
                $category = $product->guessCategory($categoryInput, $this->categories);
            } else {
                $category = Category::firstOrCreate([
                    'title' => $domain_product->category,
                    'parent_id' => Category::FOOD,
                ]);

                // Reload the categories cache
                Product::cacheCategories();
            }
            $product->generic_product_id = $category->id;
        }

        $product->save();

        return $product;
    }
}
