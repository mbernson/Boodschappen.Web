<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\Product;
use Boodschappen\Domain\Barcode;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

use Storage;

class Hoogvliet extends BaseDataSource implements ProductDataSource
{
    /** @var Client */
    private $client;

    private $baseUrl = "https://www.hoogvliet.com";

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
        $crawler = $this->client->request('GET', $this->baseUrl."/INTERSHOP/web/WFS/org-webshop-Site/nl_NL/-/EUR/ViewParametricSearch-SimpleOfferSearch?SearchTerm=$query");

        Storage::put('hoogvliet.html', $crawler->html());

        $results = $crawler->filter('.ish-productList .ish-productList-item')->each(function(Crawler $node) {
            try {
                $title = trim($node->filter('.ws-product-title .hv-brand + div')->first()->text());
                $brand = $node->filter('.hv-brand')->first()->text();

                $price = $node->filter('.kor-product-sale-price')->first()->text();
                $price = floatval(filter_whitespace($price));

                $unit_size = trim($node->filter('.ratio-base-packing-unit')->first()->text());
                $barcode = $source_id = $sku = $node->filter('input[name="SKU"]')->first()->attr('value');
                $extended_attributes = [
                    'id' => $sku,
                    'image' => $this->baseUrl.$node->filter('img.ish-product-image')->attr('src'),
                ];

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
        return 5;
    }
}