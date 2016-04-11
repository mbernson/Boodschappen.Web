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
     * @return int
     */
    public function getCompanyId();
}
