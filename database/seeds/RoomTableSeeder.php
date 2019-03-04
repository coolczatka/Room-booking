<?php

use Illuminate\Database\Seeder;
use App\Room;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rooms = [['nr' => '43a', 
		'description' => 'Lorem ipsum dolor sit amet, consectetur adi
		piscing elit, sed do eiusmod tempor incididunt ut labore et dolor
		e magna aliqua. Ut enim ad minim veniam',
		'picture' => 'http://www.uva.nl/binaries/content/gallery/faculteiten-en-diensten/studenten-services/housing-2018/slideshow-room-types/bedroom-private-shared-room---hesvanhuizen.nl.jpg'
		],
		['nr' => '237',
		'description' => 'There might be a scary visions',
		'picture' => 'https://www.syfy.com/sites/syfy/files/styles/1200x680/public/2017/10/shininghed.jpg'
		]];
		foreach($rooms as $room){
			Room::create($room);
		}
		
    }
}
