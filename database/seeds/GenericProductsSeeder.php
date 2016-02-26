<?php

use Boodschappen\Database\GenericProduct;
use Illuminate\Database\Seeder;

class GenericProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roots = [
            [
                'title' => 'Food',
                'depth' => 0,
            ],
            [
                'title' => 'Non-food',
                'depth' => 0,
            ],
            [
                'title' => 'Onbekend',
                'depth' => 0,
            ],
        ];
        GenericProduct::insert($roots);
    }
}
