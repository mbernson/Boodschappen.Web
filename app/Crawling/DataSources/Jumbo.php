<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\Product;
use Boodschappen\Domain\Barcode;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

use Storage;

class Jumbo extends BaseDataSource implements ProductDataSource
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $query
     * @return array
     */
    public function query($query)
    {
        $crawler = $this->client->request('GET', "http://www.jumbo.com/zoeken?SearchTerm=$query");

        Storage::put('jumbo.html', $crawler->html());

        $results = $crawler->filter('.jum-item')->each(function(Crawler $node) {
            try {
                $title = $node->filter('h3')->first()->text();
                $brand = explode(" ", $title)[0];

                $price = $node->filter('.jum-price-format')->first()->text();
                $price = intval($price);
                if($price > 0) $price = $price / 100;

                $unit_size = $node->filter('.jum-pack-size')->first()->text();
                $extended_attributes = $node->attr('data-jum-product-impression');
                $barcode = $sku = $node->attr('data-jum-product-sku');

                if(!empty($extended_attributes)) {
                    $extended_attributes = json_decode($extended_attributes, true);
                    $source_id = $extended_attributes['id'];
                } else {
                    $extended_attributes = null;
                    $source_id = null;
                }

                return compact('title', 'brand', 'price', 'unit_size', 'source_id', 'barcode', 'extended_attributes');
            } catch(\Exception $e) {
                $this->logException($e);
                return null;
            }
        });

        return array_filter($results);
    }
    /**
     * @param Barcode $barcode
     * @return array|null
     */
    public function queryBarcode(Barcode $barcode)
    {
        // TODO: Implement queryBarcode() method.
    }

    public function updatePrices(Product $product)
    {
        // TODO: Implement updatePrices() method.
    }

    public function getCompanyId()
    {
        return 1;
    }
}