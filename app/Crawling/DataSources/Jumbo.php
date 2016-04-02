<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Product;
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
     * @return Product[]
     */
    public function query($query)
    {
        $crawler = $this->client->request('GET', "http://www.jumbo.com/zoeken?SearchTerm=$query");

        Storage::put('jumbo.html', $crawler->html());

        $results = $crawler->filter('.jum-item')->each(function(Crawler $node) {
            try {
                $product = new Product();
                $product->title = $node->filter('h3')->first()->text();
                $product->brand = $this->guessBrand($product->title);

                $product->url = $node->filter('h3 a')->first()->attr('href');

                $price = $node->filter('.jum-price-format:not(.jum-was-price)')->first()->text();
                $price = intval($price);
                if($price > 0) $price = $price / 100;
                $product->current_price = $price;

        try {
            $product->setGuessedUnitSizeAndAmount($node->filter('.jum-pack-size')->first()->text());
        } catch(\InvalidArgumentException $e) { }
                $product->sku = $node->attr('data-jum-product-sku');

                $extended_attributes = $node->attr('data-jum-product-impression');
                if(!empty($extended_attributes)) {
                    $extended_attributes = json_decode($extended_attributes, true);
                    $product->category = $extended_attributes['category'];
                } else {
                    $extended_attributes = [];
                }
                $extended_attributes['image'] = $node->filter('img')->first()->attr('data-jum-src');
                $product->extended_attributes = $extended_attributes;

                return $product;
            } catch(\Exception $e) {
                $this->logException($e);
                return null;
            }
        });

        return array_filter($results);
    }

    public function getCompanyId()
    {
        return 1;
    }
}
