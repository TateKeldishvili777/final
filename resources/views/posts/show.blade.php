@extends('layouts.app')

@section('content')
    <h1>{{$post->title}}</h1>
    <img style="width:100%;" src="/storage/cover_images/{{$post->cover_image}}">
    <p>{!!$post->body!!}</p>
    <a href="/posts" class="btn btn-dark">Go back</a>
    <hr>
    <small>Written on {{$post->created_at}} by {{$post->user->name}}</small>
    <hr>
    <!-- @if(!Auth::guest())
        @if(Auth::user()->id == $post->user_id)-->

        @if(Auth::user() == $post->user)
            <a href="/posts/{{$post->id}}/edit" class="btn btn-dark">Edit</a>
            {!!Form::open(['action' => ['App\Http\Controllers\PostsController@destroy', $post->id], 'method' => 'POST'])!!}
                {{Form::hidden('_method', 'DELETE')}}
                {{Form::submit('Delete', ['class' => 'btn btn-danger'])}}
            {!!Form::close()!!}
        @endif
    <!--    @endif
    @endif -->
@endsection