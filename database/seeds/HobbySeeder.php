<?php

use App\Hobby;
use Illuminate\Database\Seeder;

class HobbySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hobbiesData = [
            ['name' => 'Reading'],
            ['name' => 'Travelling'],
            ['name' => 'Dancing'],
            ['name' => 'Singing'],
            ['name' => 'Blogging'],
        ];

        for ($i=0; $i < 5; $i++) {
            Hobby::create([
                'hobby_name' => $hobbiesData[$i]['name']
            ]);
        }
    }
}
