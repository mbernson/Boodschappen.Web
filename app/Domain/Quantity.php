<?php namespace Boodschappen\Domain;

use NumberFormatter;

class Quantity
{
    // In which unit are we expressing the quantity?
    // Example: gram, liter, slices
    /** @var string */
    public $unit_size;

    // How much of the given unit?
    /** @var float */
    public $unit_amount = 0.0;

    // How many pieces?
    /** @var int */
    public $bulk = 1;

    /**
     * @throws \Exception
     */
    public function validate(): bool {
        if($this->current_price < self::MINIMUM_PRICE)
            throw new \Exception("Invalid price");
        return true;
    }

    /**
     * @param $combined
     * @return Quantity
     */
    public static function fromText($combined)
    {
        $quantity = new Quantity();
        $quantity->bulk = $quantity->parseBulk($combined);
        $quantity->unit_amount = $quantity->parseAmount($combined);
        $quantity->unit_size = $quantity->parseUnit($combined);
        return $quantity;
    }

    public function parseAmount(string $input): float
    {
        /** @var NumberFormatter $numberFormatter */
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
        return null;
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

