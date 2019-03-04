<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
class PagesController extends Controller
{
    public function index(){
        $rooms = Room::all();
        return view('welcome')->with('rooms',$rooms);
    }
}
