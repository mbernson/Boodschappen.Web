<?php namespace Boodschappen\Domain;

use NumberFormatter;

class Quantity
{
    // In which unit are we expressing the quantity?
    // Example: gram, liter, slices
    /** @var string */
    public $unit;

    // How much of the given unit?
    /** @var float */
    public $amount = 0.0;

    // How many pieces?
    /** @var int */
    public $bulk = 1;

    /**
     * @throws \Exception
     */
    public function validate(): bool {
        if($this->amount < 0)
            throw new \Exception("Invalid amount");
        if($this->bulk < 1)
            throw new \Exception("Invalid bulk");
        if(is_null($this->unit) || empty($this->unit))
            throw new \Exception("Unit size may not be null/empty");
            
        return true;
    }
    
    public function __construct($text)
    {
        if(is_string($text)) {
            $this->parseQuantity($text);
        }
    }

    public function parseQuantity(string $text)
    {
        $this->bulk = $this->parseBulk($text);
        $this->amount = $this->parseAmount($text);
        $this->unit = $this->parseUnit($text);
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
        /** @var NumberFormatter $numberFormatter */
        $numberFormatter = app('\NumberFormatter');
        $matches = [];
        if(preg_match('/[\d|\.|,]+\ ?(x|stuk|stuks)+/', $input, $matches) > 0) {
            return $numberFormatter->parse($matches[0]);
        }
        return 1;
    }
}

