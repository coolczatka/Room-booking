@extends('layouts.app')

@section('content')
    <div class="container">
    <h1>{{$message}}</h1>
    <a href="{{URL::route('home')}}">Return to home page</a>
    </div>
@endsection
