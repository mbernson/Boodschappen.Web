<?php namespace Boodschappen\Crawling;

use Boodschappen\Domain\Product;
use Boodschappen\Domain\Barcode;

interface ProductDataSource
{
    /**
     * Returns an array of products for the given search terms.
     *
     * @param $search_terms
     * @return Product[]
     */
    public function query($search_terms);

    /**
     * @param Barcode $barcode
     * @return Product|null
     */
//    public function queryBarcode(Barcode $barcode);

    /**
     * @param Product $product
     * @return Product
     */
//    public function update(Product $product);

    /**
     * @return int
     */
    public function getCompanyId();
}