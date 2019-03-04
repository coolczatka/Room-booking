@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="post" action="{{URL::route('book_act',$id)}}">
            @csrf
            <div class="form-group">
                <label for="start">Start</label>
                <input name="valid_from" class="form-control" id="start">
            </div>
            <div class="form-group">
                <label for="end">End</label>
                <input name="valid_until" class="form-control" id="end">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        var array = ["2019-03-14", "2019-03-15", "2019-03-16"]
        Date.prototype.yyyymmdd = function() {
            var mm = this.getMonth() + 1; // getMonth() is zero-based
            var dd = this.getDate();

            return [this.getFullYear(),
                (mm>9 ? '' : '0') + mm,
                (dd>9 ? '' : '0') + dd
            ].join('-');
        };
        $('#valid_from').ready(()=>{
            dates = fetch("{{URL::route('reservations')}}?room={{$id}}").then(resp => {
                resp=resp.json();
                return resp;
            }).then(resp=>{
                let disableDates = [];
                resp.forEach((reservation, index) => {
                    while (new Date(reservation.valid_from).getTime() <= new Date(reservation.valid_until).getTime()) {
                        disableDates.push(reservation.valid_from);
                        reservation.valid_from = new Date(new Date(reservation.valid_from).getTime()+(24*3600*1000)).yyyymmdd();
                    }
                });
                $('input').datepicker({
                    beforeShowDay: function(date) {
                        var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                        return [disableDates.indexOf(string) == -1]
                    },
                    minDate: new Date()
                });
                $('#start').change(()=>{
                    let datestr = $('#start').val().split('/');
                    let date = new Date(parseInt(datestr[2]),parseInt(datestr[0])-1,parseInt(datestr[1]));
                    let border_date = new Date(disableDates.filter((d,i)=>d>date.yyyymmdd())[0]);
                    console.log(border_date);
                    if(!isNaN(date.getTime())) {
                        $('#end').datepicker('option', 'minDate', date);
                        $('#end').datepicker('option', 'maxDate', border_date);
                    }
                    else{
                        $('#end').datepicker('option', 'minDate', Date.now());
                        $('#end').datepicker('option', 'maxDate', Infinity);
                    }
                })
                $('#end').change(()=>{
                    let datestr = $('#end').val().split('/');
                    let date = new Date(parseInt(datestr[2]),parseInt(datestr[0])-1,parseInt(datestr[1]));
                    let border_date = new Date(disableDates.filter((d,i)=>d<date.yyyymmdd()).pop());
                    if(!isNaN(date.getTime())) {
                        $('#start').datepicker('option', 'maxDate', date);
                        $('#start').datepicker('option', 'minDate', border_date);
                    }
                    else{
                        $('#start').datepicker('option', 'maxDate', Infinity);
                        $('#start').datepicker('option', 'minDate', Date.now());
                    }
                })
                $('#start').trigger('change');
                $('#end').trigger('change');


            })
        })

    </script>
@endsection
