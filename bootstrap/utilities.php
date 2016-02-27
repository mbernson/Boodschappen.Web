<?php
use Boodschappen\Database\GenericProduct;

/**
 * @param $title
 * @param $parent
 */
function cat($title, $parent) {
    $category = new GenericProduct();
    $category->title = $title;

    if(is_int($parent)) {
        $category->parent_id = $parent;
    } else if(is_string($parent)) {
        $category->parent_id = GenericProduct::where('title', 'ilike', "%$parent%")->first()->id;
    } else {
        $category->parent_id = 0;
        $category->depth = 0;
    }

    return $category->save();
}

function q($query) {
    $product_sources = [
//        \Boodschappen\Crawling\DataSources\Hoogvliet::class,
//        \Boodschappen\Crawling\DataSources\Jumbo::class,
        \Boodschappen\Crawling\DataSources\AlbertHeijn::class,
    ];
    foreach($product_sources as $klass) {
        echo "Querying $klass for '$query'...\n\n";
        $job = new \Boodschappen\Jobs\QueryProductsJob($klass, $query);
        $job->handle();
    }
    return true;
}

function filter_whitespace($text) {
    $text = str_replace("\n", '', $text);
    $text = str_replace(' ', '', $text);
    return trim($text);
}

function companyName($id) {
    $companies = [
        1 => 'Jumbo',
        2 => 'Albert Heijn',
        3 => 'Bol.com',
        4 => 'OpenFoodFacts',
        5 => 'Hoogvliet',
    ];
    return $companies[$id];
}