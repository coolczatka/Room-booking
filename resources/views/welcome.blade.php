@extends('layouts.app')

@section('content')
            <div class="container">
                @foreach($rooms->chunk(3) as $rooms_3)
                <div class="row">
                    @foreach($rooms_3 as $room)
                    <div class="col-md-4" style="text-align: center;">
                        <img src="{{$room->picture}}" style="max-height: 100px;" alt="pic"/>
                        <h3>{{$room->nr}}</h3>
                        <p>{{$room->description}}</p>
                        <form method="get" action="{{URL::route('book',$room->id)}}">
                            @csrf
                            <input class="btn btn-primary" type="submit" value="Book it">
                        </form>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
@endsection
