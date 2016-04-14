<?php


use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Product;

class DataSourcesTest extends TestCase
{
    public function dataSourcesProvider() {
        return [
            [\Boodschappen\Crawling\DataSources\AlbertHeijn::class],
            [\Boodschappen\Crawling\DataSources\Hoogvliet::class],
            [\Boodschappen\Crawling\DataSources\Dekamarkt::class],
            [\Boodschappen\Crawling\DataSources\Jumbo::class],
        ];
    }

    /**
     * @dataProvider dataSourcesProvider
     */
    public function testQueryingProducts($class) {
        /** @var ProductDataSource $source */
        $source = app($class);

        $products = $source->query('pindakaas');
        $this->assertNotEmpty($products, "DataSource $class should return results for a common query");
        
        /** @var Product $product */
        foreach($products as $product) {
            $this->assertNotNull($product->sku);
        }
    }
}
