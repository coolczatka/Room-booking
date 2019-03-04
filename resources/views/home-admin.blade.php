@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10">
            <div>
                <div class="header-field" style="color:transparent;">a</div>
                <div class="header-field" style="color:transparent;">b</div>
                <div class="header-field" style="color:transparent;">c</div>
                <div class="header-field">
                    <label><input type="checkbox" id="cb"> Active only</label>
                </div>
            </div>
            <div class="card-header">
                <div class="header-field">Name</div>
                <div class="header-field">Valid until</div>
                <div class="header-field">Nr</div>
                <div class="header-field">Photo</div>
            <div id="wrapper">
            @foreach($rooms as $key => $room )
            <div class="card">
                <div class="card-body">
                        <div class="card-body-row">
                            {{$users[$key]->name}}
                        </div>
                        <div class="card-body-row">
                            {{$reservations[$key]->valid_until}}
                        </div>
                        <div>
                            <div class="card-body-row">
                        {{$room->nr}}
                            </div>
                            <div class="card-body-row">
                        <img src="{{$room->picture}}" style="max-height:100px;"/>
                            </div>
                        </div>
                </div>

            </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
