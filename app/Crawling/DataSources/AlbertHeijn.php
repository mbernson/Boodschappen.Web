<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Database\Product;
use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Barcode;
use GuzzleHttp\Client;

use Storage;

class AlbertHeijn extends BaseDataSource implements ProductDataSource
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $headers = [
            'X-Analytics' => '{"ns_referrer":"http://www.ah.nl/bonus"}',
            'Accept-Encoding' => 'gzip, deflate, sdch',
            'Accept-Language' => 'nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Referer' => 'http://www.ah.nl/producten',
            'X-Requested-With' => 'XMLHttpRequest',
            'Connection' => 'keep-alive',
        ];
        $this->client = new Client([
            'base_uri'  => 'http://www.ah.nl/service/rest',
            'headers' => $headers,
        ]);
    }

    /**
     * @param string $query
     * @return array
     */
    public function query($query)
    {
        $url = urlencode("/zoeken?").http_build_query([
            'rq' => $query,
            'searchType' => 'product',
            '_' => time(),
        ]);
        $path = "/service/rest/delegate?url=".$url;
        $response = $this->client->get($path);
        $body = $response->getBody();
        Storage::put('ah.json', $body);
        $json = json_decode($body);

        $products = $this->parseResponse($json);
        return $products;
    }

    /**
     * @param Barcode $barcode
     * @return array|null
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

    private function parseResponse(\stdClass $json)
    {
        foreach($json->_embedded->lanes as $lane) {
            if($lane->type == 'SearchLane') {
                return $this->parseItems($lane->_embedded->items);
            }
        }
    }

    private function parseItems(array $items)
    {
        $results = [];
        foreach($items as $item) {
            if(!property_exists($item, '_embedded')) break;

            $product = $item->_embedded->productCard->_embedded->product;
            $title = str_replace('­', '', $product->description); // FIXME

            if(property_exists($product, 'brandName'))
                $brand = $product->brandName;
            else
                $brand = $this->parseBrand($title);

            $price = floatval($product->priceLabel->now);
            $unit_size = $product->unitSize;
            $barcode = $source_id = $product->id;
            $extended_attributes = [
                'id' => $product->id,
                'category' => $product->categoryName,
                'images' => $product->images,
                'image' => end($product->images),
            ];
            $results[] = compact('title', 'price', 'brand', 'barcode', 'unit_size', 'extended_attributes');
        }
        return $results;
    }

    public function getCompanyId()
    {
        return 2;
    }
}