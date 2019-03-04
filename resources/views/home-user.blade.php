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
                        <label><input type="checkbox" id="active_only"> Active only</label>
                    </div>
                </div>
                <div class="card-header">
                    <div class="header-field">From Until</div>
                    <div class="header-field">Nr</div>
                    <div class="header-field">Picture</div>
                    <div class="header-field">abort</div>
                    <div id="wrapper">
                        @foreach($rooms as $key => $room )
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-body-row">
                                        {{$reservations[$key]->valid_from}}</br>{{$reservations[$key]->valid_until}}
                                    </div>
                                    <div>
                                        <div class="card-body-row">
                                            {{$room->nr}}
                                        </div>
                                        <div class="card-body-row">
                                            <img src="{{$room->picture}}" style="max-height:100px;"/>
                                        </div>
                                        <div class="card-body-row">
                                            <form action="{{URL::route('abort')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="reservation_id" value="{{$reservations[$key]->id}}"/>
                                                <input type="submit"
                                                 @if(!$reservations[$key]->is_active)class="sbmt" disabled @endif
                                                 value="Abort reservation" style="font-size:25px;"/>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let chckd = $('#active_only').prop("checked");

        $('#active_only').change(()=>{
            dbld = document.getElementsByClassName('sbmt');
            if(!chckd) {
                for (let i = 0; i < dbld.length; i++) {
                    dbld[i].parentElement.parentElement.parentElement.parentElement.style.display = "none";
                }
                chckd = true;
            }
            else{
                for (let i = 0; i < dbld.length; i++) {
                    dbld[i].parentElement.parentElement.parentElement.parentElement.removeAttribute("style");
                }
                chckd = false;
            }
        })

    </script>
@endsection
