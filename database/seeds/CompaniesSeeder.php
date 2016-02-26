<?php

use Boodschappen\Database\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
            [
                'id' => 1,
                'title' => 'Jumbo',
            ],
            [
                'id' => 2,
                'title' => 'Albert Heijn',
            ],
            [
                'id' => 3,
                'title' => 'Bol.com',
            ],
            [
                'id' => 4,
                'title' => 'OpenFoodFacts',
            ],
        ];
        Company::insert($companies);
    }
}
