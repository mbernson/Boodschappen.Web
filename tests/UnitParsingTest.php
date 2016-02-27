<?php

use Boodschappen\Domain\Product;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitParsingTest extends TestCase
{
    /** @var Product */
    private $product;

    public function testParsingGrams()
    {
        $this->assertEquals(100.0, $this->product->parseAmount('100 g'));
        $this->assertEquals('g', $this->product->parseUnit('100 g'));

        $this->assertEquals(100.0, $this->product->parseAmount('100g'));
        $this->assertEquals('g', $this->product->parseUnit('100g'));
    }

    public function testParsingDecimalGrams() {
        $this->assertEquals(15.5, $this->product->parseAmount('15,5g'));
        $this->assertEquals('g', $this->product->parseUnit('15,5g'));

//        $this->assertEquals(15.5, $this->product->parseAmount('15.5g'));
//        $this->assertEquals('g', $this->product->parseUnit('15.5g'));

        $this->assertEquals(15.5, $this->product->parseAmount('15,5 g'));
        $this->assertEquals('g', $this->product->parseUnit('15,5 g'));

//        $this->assertEquals(15.5, $this->product->parseAmount('15.5 g'));
//        $this->assertEquals('g', $this->product->parseUnit('15.5 g'));
    }

    public function testParsingMultipleUnitSizes() {
        $this->assertEquals(1.0, $this->product->parseAmount('6 x 1 l'));
        $this->assertEquals('l', $this->product->parseUnit('6 x 1 l'));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->product = new Product();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->product = null;
    }


}
