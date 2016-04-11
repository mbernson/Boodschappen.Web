<?php namespace Boodschappen\Crawling\DataSources;

use Boodschappen\Crawling\ProductDataSource;
use Boodschappen\Domain\Product;
use Boodschappen\Domain\Quantity;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Dekamarkt extends BaseDataSource implements ProductDataSource
{
    /** @var Client */
    private $client;

    private $baseUrl = 'https://boodschappen.dekamarkt.nl/';

    public function __construct()
    {
        $headers = [
            'Connection' => 'keep-alive',
            'Cache-Control' => 'max-age=0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36',
            'DNT' => '1',
            'Referer' => 'https://boodschappen.dekamarkt.nl/',
            'Accept-Encoding' => 'gzip, deflate, sdch',
            'Accept-Language' => 'nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4',
        ];
        $this->client = new Client([
            'headers' => $headers,
        ]);
    }

    /**
     * @param string $query
     * @return Product[]
     */
    public function query($query)
    {
        $crawler = $this->client->request('GET', "https://boodschappen.dekamarkt.nl/zoeken?search=$query");

        $results = $crawler->filter('#artikelenContainer article.artikel')->each(function(Crawler $node) {
            try {
                $product = new Product();
                $product->title = $node->filter('.title .name')->first()->text();
                $product->brand = $this->guessBrand($product->title);

                $product->url = $this->baseUrl.$node->filter('a.toDetail')->first()->attr('href');

                $price = $node->filter('.price')->first()->text();
                $product->current_price = floatval($price);

                try {
                    $product->quantity = Quantity::fromText($node->filter('.subname')->first()->text());
                } catch(\InvalidArgumentException $e) { }

                $product->sku = 'deka-'.$node->attr('data-artikel');

                $extended_attributes = [
                    'image' => $this->baseUrl.$node->filter('.image')->first()->attr('data-original')
                ];
                $product->extended_attributes = $extended_attributes;

                return $product;
            } catch(\Exception $e) {
                $this->logException($e);
                return null;
            }
        });

        return $results;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return 6;
    }
}
