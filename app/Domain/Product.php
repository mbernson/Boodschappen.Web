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

    public function parseAmount(string $input): float
    {
        $numberFormatter = app('\NumberFormatter');
        $matches = [];
        if (preg_match_all('/[\d|\.|,]+/', $input, $matches) > 0) {
            $amount = last($matches[0]);
            return $numberFormatter->parse($amount);
        }
        return 0.0;
    }

    public function parseUnit(string $input): string
    {
        $matches = [];
        if(preg_match_all('/[A-Za-z]+/', $input, $matches) > 0) {
            return last($matches[0]);
        }
        return 'stuks';
    }

    private function parseBulk(string $input): int
    {
        $matches = [];
        if(preg_match('/[\d|\.|,]+\ ?x+/', $input, $matches) > 0) {
            return intval($matches[0]);
        }
        return 1;
    }
}
