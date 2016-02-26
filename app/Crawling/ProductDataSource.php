<?php namespace Boodschappen\Crawling;

use Boodschappen\Database\Product;
use Boodschappen\Domain\Barcode;

interface ProductDataSource
{
    /**
     * @param Barcode $barcode
     * @return array|null
     */
    public function queryBarcode(Barcode $barcode);

    /**
     * @param $search_terms
     * @return array|null
     */
    public function query($search_terms);

    public function updatePrices(Product $product);

    public function getCompanyId();
}