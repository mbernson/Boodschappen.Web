<?php

return [
    'companies' => [
        1 => 'Jumbo',
        2 => 'Albert Heijn',
        3 => 'Bol.com',
        4 => 'OpenFoodFacts',
        5 => 'Hoogvliet',
        6 => 'Dekamarkt',
    ],
    'product_sources' => [
        \Boodschappen\Crawling\DataSources\AlbertHeijn::class,
        \Boodschappen\Crawling\DataSources\Hoogvliet::class,
        \Boodschappen\Crawling\DataSources\Dekamarkt::class,
        \Boodschappen\Crawling\DataSources\Jumbo::class,
    ],
];
