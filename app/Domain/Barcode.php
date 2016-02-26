<?php namespace Boodschappen\Domain;

class Barcode
{
    static $barcode_types = [
        'org.gs1.EAN-8',
        'org.gs1.EAN-13',
        'org.gs1.UPC-E',
    ];

    /**
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $type;

    /**
     * Barcode constructor.
     * @param string $value
     * @param string $type
     * @throws \Exception
     */
    public function __construct($value, $type)
    {
        if(!in_array($type, static::$barcode_types)) {
            throw new \Exception("'$type' is not an allowed barcode type.");
        }

        $this->value = $value;
        $this->type = $type;
    }
}