<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Product;
use Boodschappen\Domain\Barcode;

use Boodschappen\Domain\Quantity;
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
     * @return Product[]
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

    /**
     * @param \stdClass $json
     * @return Product[]
     */
    private function parseResponse(\stdClass $json)
    {
        foreach($json->_embedded->lanes as $lane) {
            if($lane->type == 'SearchLane') {
                return $this->parseItems($lane->_embedded->items);
            }
        }
    }

    /**
     * @param array $items
     * @return Product[]
     */
    private function parseItems(array $items)
    {
        $results = [];
        foreach($items as $item) {
            if(!property_exists($item, '_embedded')) break;

            $product = new Product();
            $api_product = $item->_embedded->product;
            $product->title = str_replace('­', '', $api_product->description); // FIXME
            $product->url = "http://www.ah.nl/producten/product/{$api_product->id}/";
            $product->category = $api_product->categoryName;

            if(property_exists($api_product, 'brandName'))
                $product->brand = $api_product->brandName;
            else
                $product->brand = $this->guessBrand($product->title);

            $product->current_price = floatval($api_product->priceLabel->now);
            $product->quantity = Quantity::fromText($api_product->unitSize);
            $product->sku = $api_product->id;

            $product->extended_attributes = [
                'category' => $api_product->categoryName,
            ];
            if(property_exists($api_product, 'images')) {
                $product->extended_attributes['images'] = $api_product->images;
                $product->extended_attributes['image'] = end($api_product->images);
            }
            if(property_exists($api_product, 'discount')) {
                $product->extended_attributes['discount'] = $api_product->discount;
            }
            $results[] = $product;
        }
        return $results;
    }

    public function getCompanyId()
    {
        return 2;
    }
}
