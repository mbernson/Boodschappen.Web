<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Product;
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
     * @return Product[]
     */
    public function query($query)
    {
        $crawler = $this->client->request('GET', $this->baseUrl."/INTERSHOP/web/WFS/org-webshop-Site/nl_NL/-/EUR/ViewParametricSearch-SimpleOfferSearch?SearchTerm=$query");

        Storage::put('hoogvliet.html', $crawler->html());

        $results = $crawler->filter('.ish-productList .ish-productList-item')->each(function(Crawler $node) {
            try {
                $product = new Product();
                $product->title = trim($node->filter('.ws-product-title .hv-brand + div')->first()->text());
                $product->brand = $node->filter('.hv-brand')->first()->text();
                $product->url = $node->filter('.kor-product-link')->first()->attr('href');

                $price = $node->filter('.kor-product-sale-price')->first()->text();
                $product->current_price = floatval(filter_whitespace($price));

                $product->setGuessedUnitSizeAndAmount(trim($node->filter('.ratio-base-packing-unit')->first()->text()));
                $product->sku = $node->filter('input[name="SKU"]')->first()->attr('value');
                $product->extended_attributes = [
                    'image' => $this->baseUrl.$node->filter('img.ish-product-image')->attr('src'),
                ];

                return $product;
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

    public function getCompanyId()
    {
        return 5;
    }
}