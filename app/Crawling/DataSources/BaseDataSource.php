<?php namespace Boodschappen\Crawling\DataSources;

use Log;

abstract class BaseDataSource
{
    protected static $common_words = [
        'voordeel',
        'vers'
    ];

    protected function guessBrand(string $name): string {
        $brand = explode(' ', $name)[0];

        if(in_array(strtolower($brand), static::$common_words)) {
            return config('boodschappen.companies')[$this->getCompanyId()];
        }

        return $brand;
    }

    protected function logException(\Exception $e) {
        Log::error($e->getMessage());
        Log::error($e);
    }

}
