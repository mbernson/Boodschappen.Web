<?php

use Boodschappen\Database\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
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
        Category::insert($roots);
    }
}
