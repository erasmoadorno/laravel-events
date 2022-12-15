@extends('layouts.main')
@section('title', "$event->event_name")
@section('content')

    <div id="event-page">


        <h1>{{ $event->event_name }}</h1>

        <div id="event-page-body">
            <br>
            <div id="event-page-description">
                {{ $event->description }} <br>
            </div>
            <img src="/storage/events/{{ $event->event_image }}" alt="{{ $event->event_name }}" srcset=""
                id="event-page-image">
            Location: <span class="pin"></span>
            <span id="event-location">{{ $event->event_local }}</span><br>
            Date: {{ $event->event_date }} <br>
            Event privacity: <span class="capitalize">{{ $event->private }}</span> <br>
            Event properties:
            @foreach ($event->properties as $item)
                <span id="event-item" class="capitalize">{{ $item }}</span>
            @endforeach <br>
            <br>
            @auth
                @if ($event->user_id == auth()->user()->id)
                    <a href="/evento/editar/{{ $event->id }}" class="link-button">
                        <div class="edit-button">
                            Edit
                        </div>
                    </a>
                    @if (count($participants) > 0)
                        <h1>Requests</h1>
                        <table id="event-table">
                            <tbody>
                                <thead>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </thead>
                                @foreach ($participants as $item)
                                    <tr>
                                        <td>
                                            {{ $item->name }}
                                        </td>
                                        <td>
                                            <form action="/evento/requisicao" method="POST">
                                                @method('PUT')
                                                @csrf
                                                <input type="hidden" name="decision" value="confirmed">
                                                <input type="hidden" name="user_id"
                                                    value="{{ Crypt::encrypt($item->pivot->user_id) }}">
                                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                                <button id="accept-btn">Accept</button>
                                            </form>
                                            <form action="/evento/requisicao" method="post">
                                                @method('PUT')
                                                @csrf
                                                <input type="hidden" name="decision" value="refused">
                                                <input type="hidden" name="user_id"
                                                    value="{{ \Crypt::encrypt($item->pivot->user_id) }}">
                                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                                <button id="deny-btn">Deny</button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <form action="/evento/{{ $event->id }}" method="get">
                        <h1>Invite</h1>
                        <label for="search">Search guest to invite:</label>
                        <input type="search" name="search" id="" onchange="this.form.submit()"
                            placeholder="&#x1F50E;&#xFE0E;">
                        <button id="btn-search">Search</button>
                        <br>
                        <br>
                    </form>
                    @if (count($users)>0)
                    <table id="invitation-table">                        
                    @endif
                    @foreach ($users as $user)
                            <form action="/evento/convidar" method="post">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <input type="hidden" name="user_id" id="hidden_input"value="{{ \Crypt::encrypt($user->id) }}">
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ $user->name }}

                                        </td>
                                        <td>
                                            @if ($user->id == auth()->user()->id)
                                                <button disabled id="invite-btn">Convidar</button>
                                            @else
                                                <button type="submit" id="invite-btn">Convidar</button>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </form>
                    @endforeach
                    </table>
                @else
                    @switch($event->private)
                        @case('public')
                            <a href="/evento/entrar/{{ $event->id }}" class="link-button">
                                <div class="edit-button">
                                    To participate
                                </div>
                            </a>
                        @break

                        @case('private')
                            <a href="/evento/entrar/{{ $event->id }}" class="link-button">
                                <div class="edit-button">
                                    Request an invite
                                </div>
                            </a>
                        @break

                        @case('secret')
                            <h1>Você precisa de convite para participar do evento</h1>
                        @break

                        <h1>......</h1>

                        @default
                    @endswitch
                @endif
            @endauth
            @guest
                <h1>Logue para participar</h1>
            @endguest
        </div>
    </div>


@endsection
