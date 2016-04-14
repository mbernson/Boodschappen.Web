<?php
use Boodschappen\Database\Category;

function cat(string $title, string $parent) {
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

function filter_whitespace($text) {
    $text = str_replace("\n", '', $text);
    $text = str_replace(' ', '', $text);
    return trim($text);
}

function priceChanges($str) {
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $parts = explode(',', $str);
    $parts = array_map('floatval', $parts);
    $parts = array_reverse($parts);
    return '&euro;'.join(' &rarr; &euro;', $parts);
}
