<?php

namespace App\Http\Controllers;

use phpDocumentor\Reflection\Types\Integer;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zizaco\Entrust\Entrust;
use Carbon\Carbon;
use App\User;
use App\Room;
use App\Reservation;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->ajax()) {
            $rooms = [];
            if (Auth::user()->hasRole('admin')) {
                $users = [];
                $reservations = Reservation::whereDate('valid_until', '>', Carbon::now())->get();
                foreach ($reservations as $reservation) {
                    $user = User::find($reservation->user_id);
                    $room = Room::find($reservation->room_id);
                    $users[] = $user;
                    $rooms[] = $room;
                }
                return view('home-admin')->with('users', $users)->with('rooms', $rooms)->with('reservations', $reservations);
            }
            $reservations = Reservation::where('user_id', Auth::user()->id)->get();
            if ($reservations != []) {
                foreach ($reservations as $reservation) {
                    $room = Room::find($reservation->room_id);
                    $rooms[] = $room;
                }
            }
            return view('home-user')->with('rooms', $rooms)->with('reservations', $reservations);
        }
        else{
            if (Auth::user()->hasRole('admin')) {
                if($request->active==1)
                    $reservations = Reservation::whereDate('valid_until', '>', Carbon::now())->where('is_active', True)->get();
                else
                    $reservations = Reservation::whereDate('valid_until', '>', Carbon::now())->get();
                foreach ($reservations as $reservation) {
                    $user = User::find($reservation->user_id);
                    $room = Room::find($reservation->room_id);
                    $users[] = $user;
                    $rooms[] = $room;
                }
                return Response::json([$users, $rooms, $reservations]);
            }
            else{
                if($request->active==1)
                    $reservations = Reservation::where('user_id', Auth::user()->id)->whereDate('valid_until', '>', Carbon::now())->where('is_active', True)->get();
                else
                    $reservations = Reservation::where('user_id', Auth::user()->id)->whereDate('valid_until', '>', Carbon::now())->get();
                if ($reservations != []) {
                    foreach ($reservations as $reservation) {
                        $room = Room::find($reservation->room_id);
                        $rooms[] = $room;
                    }
                }
                return Response::json([ $rooms, $reservations]);
            }
        }
    }

    public function create_room(){
        if(Auth::user()->hasRole('admin'))
            return View('createRoomForm');
    }
    public function abort_reservation(Request $request){
        try {
            $reservation = Reservation::find($request->input('reservation_id'));
            $reservation->is_active = 0;
            $reservation->save();
        } catch(\Exception $e) {
            $message = "Something went wrong";
            return View('abort')->with('message'.$message);
        }
        $message = "Resevation has aborted";
        return View('abort')->with('message',$message);
    }
    public function bookingForm($id){
        return View('bookingForm')->with('id',$id);
    }
    public function reservations(Request $request){
        $reservations = Reservation::where('room_id',$request->room)->where('is_active',1)->where('valid_until','>',Carbon::now())->get();
        return Response::json($reservations);
    }
    public function book(Request $request,$id){
        $valid_from_t = explode('/',$request->input('valid_from'));
        $valid_until_t = explode('/',$request->input('valid_until'));
        $valid_from = implode("-",[$valid_from_t[2],$valid_from_t[0],$valid_from_t[1]]);
        $valid_until = implode('-',[$valid_until_t[2],$valid_until_t[0],$valid_until_t[1]]);

        $reservation = new Reservation();
        $reservation->valid_from = Carbon::parse($valid_from);
        $reservation->valid_until = Carbon::parse($valid_until);
        $reservation->user_id = Auth::user()->id;
        $reservation->room_id = $id;
        $reservation->is_active = 1;
        $reservation->save();
        return View('abort')->with('message','Your reservation has been created');
    }
    public function addRoomForm(){
        return View('createRoomForm');
    }
    public function addRoom(Request $request){

        Room::create(['nr'=>$request->nr, 'description'=>$request->description,
            'picture'=>$request->picture]);
        return View('abort')->with('message', 'Room has been created!');
    }
}
