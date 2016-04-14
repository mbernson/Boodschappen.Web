<?php

use Boodschappen\Domain\Product;
use Boodschappen\Domain\Quantity;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitParsingTest extends TestCase
{
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

    public function testParsingGrams()
    {
        $q = new Quantity('100 g');
        $this->assertEquals(100.0, $q->amount);
        $this->assertEquals('g', $q->unit);

        $q = new Quantity('100g');
        $this->assertEquals(100.0, $q->amount);
        $this->assertEquals('g', $q->unit);
    }

    public function testParsingDecimalGrams() {
        $q = new Quantity('15,5g');
        $this->assertEquals(15.5, $q->amount);
        $this->assertEquals('g', $q->unit);

        $q = new Quantity('15,5 g');
        $this->assertEquals(15.5, $q->amount);
        $this->assertEquals('g', $q->unit);

        $q = new Quantity('10 stuks');
        $this->assertEquals(10, $q->amount);
        $this->assertEquals('stuks', $q->unit);
    }

    public function testParsingMultipleUnitSizes() {
        $quantities = [
            new Quantity('6 x 1 l'),
            new Quantity('6x 1 l'),
            new Quantity('6x1 l'),
            new Quantity('6 x1 l'),
            new Quantity('6 x 1l'),
            new Quantity('6x 1l'),
            new Quantity('6x1l'),
            new Quantity('6 x1l'),
        ];
        foreach($quantities as $q) {
            $this->assertEquals(1.0, $q->amount);
            $this->assertEquals(6, $q->bulk);
            $this->assertEquals('l', $q->unit);
        }
    }
}
