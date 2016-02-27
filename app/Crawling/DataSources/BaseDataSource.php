<?php namespace Boodschappen\Crawling\DataSources;

use Log;

abstract class BaseDataSource
{
    protected function parseBrand($name) {
        $brand = explode(' ', $name)[0];

        return $brand;
    }

    protected function logException(\Exception $e) {
        Log::error($e->getMessage());
        Log::error($e);
    }

}