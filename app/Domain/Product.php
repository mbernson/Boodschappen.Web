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

    /** @var string */
    public $unit_size;
    /** @var float */
    public $unit_amount = 0.0;

    /** @var int */
    public $bulk = 1;

    /** @var float */
    public $current_price;

    /** @var int */
    public $generic_product_id;

    /** @var array|\stdClass */
    public $extended_attributes;

    /** @var Barcode */
    public $barcode;

    /**
     * @throws \Exception
     * @return bool
     */
    public function validate() {
        if($this->current_price < self::MINIMUM_PRICE)
            throw new \Exception("Invalid price");
        return true;
    }

    public function setGuessedUnitSizeAndAmount($combined)
    {
        $this->bulk = $this->parseBulk($combined);
        $this->unit_amount = $this->parseAmount($combined);
        $this->unit_size = $this->parseUnit($combined);
    }

    /**
     * @param string $text
     * @return float
     */
    public function parseAmount($text)
    {
        $numberFormatter = app('\NumberFormatter');
        $matches = [];
        if (preg_match_all('/[\d|\.|,]+/', $text, $matches) > 0) {
            $amount = last($matches[0]);
            return $numberFormatter->parse($amount);
        }
        return 0.0;
    }

    /**
     * @param $text
     * @return string|null
     */
    public function parseUnit($text)
    {
        $matches = [];
        if(preg_match_all('/[A-Za-z]+/', $text, $matches) > 0) {
            return last($matches[0]);
        }
        return null;
    }

    private function parseBulk($text)
    {
        $matches = [];
        if(preg_match('/[\d|\.|,]+\ ?x+/', $text, $matches) > 0) {
            return intval($matches[0]);
        }
        return 1;
    }
}