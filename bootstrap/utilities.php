<?php
use Boodschappen\Database\Category;

/**
 * @param $title
 * @param $parent
 */
function cat($title, $parent) {
    $category = new Category();
    $category->title = $title;

    if(is_int($parent)) {
        $category->parent_id = $parent;
    } else if(is_string($parent)) {
        $category->parent_id = Category::where('title', 'ilike', "%$parent%")->first()->id;
    } else {
        $category->parent_id = 0;
        $category->depth = 0;
    }

    return $category->save();
}

function q($query) {
    $product_sources = [
        \Boodschappen\Crawling\DataSources\Hoogvliet::class,
        \Boodschappen\Crawling\DataSources\Jumbo::class,
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

function priceChanges($str) {
	$str = str_replace('{', '', $str);
	$str = str_replace('}', '', $str);
	$parts = explode(',', $str);
	$parts = array_map('floatval', $parts);
	$parts = array_reverse($parts);
        $formatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        $parts = array_map(function($price) use ($formatter) {
            return $formatter->formatCurrency($price, 'EUR');
        }, $parts);
	return join(' &rarr; ', $parts);
}
