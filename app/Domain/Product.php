<?php namespace Boodschappen\Domain;

use NumberFormatter;

class Product
{
    const MINIMUM_PRICE = 0.1;

    /** @var string */
    public $title;

    /** @var string */
    public $sku;

    /** @var string */
    public $brand;

    /** @var Quantity */
    public $quantity;

    /** @var float */
    public $current_price;

    /** @var string */
    public $url;

    /** @var string */
    public $category;

    /** @var array|\stdClass */
    public $extended_attributes;

    /** @var Barcode */
    public $barcode;

    /**
     * @throws \Exception
     */
    public function validate(): bool {
        if(is_null($this->sku))
            throw new \Exception("SKU may not be null");
        if($this->current_price < self::MINIMUM_PRICE)
            throw new \Exception("Invalid price");
        if(!$this->quantity->validate())
            throw new \Exception("Quantity did not validate");
            
        return true;
    }
}
