<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $colors = [
            'Red', 'Blue', 'Green', 'Yellow', 'Purple', 'Orange', 'Pink', 'Black', 'White', 'Brown',
            'Gray', 'Cyan', 'Magenta', 'Turquoise', 'Indigo', 'Violet', 'Maroon', 'Gold', 'Silver',
            'Navy', 'Teal', 'Olive', 'Peach', 'Lavender', 'Coral', 'Slate', 'Ivory', 'Tan', 'Burgundy',
            'Mint', 'Charcoal', 'Beige', 'Mustard', 'Ruby', 'Emerald', 'Sapphire', 'Aquamarine', 'Ruby',
            'Garnet', 'Amber', 'Amethyst', 'Topaz', 'Citrine', 'Peridot', 'Opal', 'Onyx', 'Jade',
            'Turquoise', 'Coral', 'Peach', 'Lilac', 'Marigold', 'Lemon', 'Lime', 'Forest Green',
            'Sky Blue', 'Baby Blue', 'Baby Pink', 'Baby Yellow', 'Baby Green', 'Baby Purple',
            'Baby Orange', 'Baby Brown', 'Baby Gray', 'Baby Cyan', 'Baby Magenta', 'Baby Turquoise',
            'Baby Indigo', 'Baby Violet', 'Baby Maroon', 'Baby Gold', 'Baby Silver', 'Baby Navy',
            'Baby Teal', 'Baby Olive', 'Baby Peach', 'Baby Lavender', 'Baby Coral', 'Baby Slate',
            'Baby Ivory', 'Baby Tan', 'Baby Burgundy', 'Baby Mint', 'Baby Charcoal', 'Baby Beige',
            'Baby Mustard', 'Baby Ruby', 'Baby Emerald', 'Baby Sapphire', 'Baby Aquamarine', 'Baby Ruby',
            'Baby Garnet', 'Baby Amber', 'Baby Amethyst', 'Baby Topaz', 'Baby Vitrine', 'Baby Peridot',
            'Baby Opal', 'Baby Onyx', 'Baby Jade'
        ];

        foreach ($colors as $color) {
            DB::table('colors')->insert([
                'colors' => $color,
            ]);
        }
    }
}
