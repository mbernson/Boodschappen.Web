<?php namespace Boodschappen\Crawling\DataSources;

abstract class BaseDataSource
{
    protected function parseBrand($name) {
        $brand = explode(' ', $name)[0];

        return $brand;
    }

}