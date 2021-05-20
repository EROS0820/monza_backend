<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => 1, 'name' => 'szt'],
            ['id' => 2, 'name' => 'kg'],
            ['id' => 3, 'name' => 'litr'],

        ];
        foreach($items as $item)
        {
            \App\Models\Unit::create($item);
        }
    }
}
