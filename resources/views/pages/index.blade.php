@extends('layouts.app')

@section('content')
    <div class="jumbotron text-center">
        <h1>{{$title}}</h1>
        <p>Want to be part of our community ?</p>
        <p>
            <a class="btn btn-primary btn-lg" role="button" href="/login">Log in</a>
            <a class="btn btn-success btn-lg" role="button" href="/register">Register</a>
        </p>
    </div>     
@endsection