@extends('layouts.main')
@section('title', 'Events')
@section('background', 'welcome')
@section('content')

    <div id="search-div-bg">
        <div id="search-div">
            <form action="/" method="get">
                <input type="search" placeholder="&#x1F50E;&#xFE0E;" name="search" id="search">
                <button type="submit" id="btn-search">Search</button>
            </form>
        </div>
    </div>
    <div id="main-index">

        <h1>Events</h1>
        @foreach ($events as $event)
            <div id="event-div">
                <div id="card-header">
                    @if ($event->event_image)
                        <img src="{{ url("storage/events/{$event->event_image}") }}" alt="{{ $event->event_name }}"
                            id="event_img">
                    @else
                        <img src="bgimg/bgimg.jpg" alt="Default img" id="event_img">
                    @endif
                    <h3>{{ $event->event_name }}</h3>
                </div>
                <div id="card-body">
                    <p>{{ Str::limit($event->description, 200) }}</p>
                </div>
                <div id="card-link">
                    <a href="/evento/{{ $event->id }}">
                        See details
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
