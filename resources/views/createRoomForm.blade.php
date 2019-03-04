@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="post" action="{{URL::route('book_act',$id)}}">
            @csrf
            <div class="form-group">
                <label for="nr">Room number</label>
                <input type="text" name="nr" class="form-control" id="nr">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="textarea" name="description" class="form-control" id="description">
            </div>
            <div class="form-group">
                <label for="picture">Picture link</label>
                <input type="text" name="picture" class="form-control" id="picture">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

@endsection
