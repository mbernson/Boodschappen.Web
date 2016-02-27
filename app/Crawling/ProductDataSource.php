<?php namespace Boodschappen\Crawling;

use Boodschappen\Database\Product;
use Boodschappen\Domain\Barcode;

interface ProductDataSource
{
    /**
     * Returns an array of products for the given search terms.
     *
     * @param $search_terms
     * @return array|null
     */
    public function query($search_terms);

}