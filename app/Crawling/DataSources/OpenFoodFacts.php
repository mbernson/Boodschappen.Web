<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Database\Product;
use Boodschappen\Domain\Barcode;
use GuzzleHttp\Client;

class OpenFoodFacts implements ProductDataSource
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri'  => 'http://world.openfoodfacts.org/api/v0/',
            'headers' => [
                'User-Agent' => 'boodschappen-app/1.0',
                'Content-type'     => 'application/json',
                'Accept'     => 'application/json',
            ],
        ]);
    }

    /**
     * @param $search_terms
     * @return array
     * @internal param string $barcode
     */
    public function query($search_terms)
    {
        $query = urlencode($search_terms);
        $path = "product/{$query}.json";

        $response = $this->client->get($path);
        $body = $response->getBody();
        $json = json_decode($body);
        var_dump($json);
        return $json;
    }

    /**
     * @param Barcode $barcode
     * @return Product
     */
    public function queryBarcode(Barcode $barcode)
    {
        $product = new Product();
        // TODO: Implement queryBarcode() method.
    }

    public function updatePrices(Product $product)
    {
        // TODO: Implement updatePrices() method.
    }

    public function getCompanyId()
    {
        return 4;
    }
}