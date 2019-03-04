<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['valid_from', 'valid_until', 'is_active'];
    protected $table='reservations';

	public function room(){
		return $this->belongsTo('App\Room');
	}
	public function user(){
		return $this->belongsTo('App\User');
	}
}
