<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Database\Product;
use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Barcode;
use GuzzleHttp\Client;

class BolDotCom implements ProductDataSource
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri'  => 'https://api.bol.com',
            'protocols' => ['https'],
            'headers' => [
                'User-Agent' => 'boodschappen-app/1.0',
                'Content-type'     => 'application/json',
                'Accept'     => 'application/json',
                'synchronous' => true,
            ],
            'query' => [
                'format' => 'json',
                'apikey' => env('BOL_API_KEY'),
            ],
            'debug' => false,
        ]);
    }

    /**
     * @param $search_terms
     * @return array
     * @internal param string $barcode
     */
    public function query($search_terms)
    {
        $path = "/catalog/v4/search";
        $options = [
            'query' => [
                'format' => 'json',
                'apikey' => env('BOL_API_KEY'),
                'q' => $search_terms,
                'pids' => 1,
                'includeattributes'=>'true',
                'offers'=>'all',
            ]
        ];
        $response = $this->client->get($path, $options);
        $body = $response->getBody();
        var_dump($body);
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
        return 3;
    }
}