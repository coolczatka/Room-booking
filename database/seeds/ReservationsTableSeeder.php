<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Reservation;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$reservations = [[
			'valid_from' => Carbon::parse('2019-01-01'),
			'valid_until' => Carbon::parse('2019-03-08'),
			'user_id' => 2,
			'room_id' => 1,
		],
		[
			'valid_from' => Carbon::parse('2019-01-04'),
			'valid_until' => Carbon::parse('2019-12-12'),
			'user_id' => 3,
			'room_id' => 2,
		],
        [
            'valid_from' => Carbon::parse('2020-01-04'),
            'valid_until' => Carbon::parse('2021-12-12'),
            'user_id' => 4,
            'room_id' => 2,
        ]
        ];
		foreach ($reservations as $r){
		    Reservation::create($r);
        }
    }
}
