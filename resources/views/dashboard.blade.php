@extends('layouts.main')
@section('title', 'Perfil')
@section('content')
@section('background', 'bg-dashboard')
    {{-- <div id="bg-dashboard"> --}}
        <div id="div-event">

            <h1>Welcome {{ $authenticatedUser->name }}</h1>
            <br>

            <div id="div-invitations">
                <h2>Invitations</h2>
                <table id="tb-invitation">
                    @foreach ($eventAsParticipant as $item)
                        @if ($item->pivot->invited == 'accepted' &&
                            $item->pivot->present !== 'confirmed' &&
                            $item->pivot->present !== 'refused')
                            <tr>
                                <td>
                                    <a href="/evento/{{ $item->id }}" class="edit-link">
                                        {{ $item->event_name }}
                                    </a>
                                </td>
                                <td>
                                    <form action="/evento/convite" method="POST">
                                        @method('PUT')
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $item->pivot->event_id }}">
                                        <input type="hidden" name="decision" value="confirmed">
                                        <button class="accept-btn"> Accept </button>
                                    </form>
                                    <form action="/evento/convite" method="post">
                                        @method('PUT')
                                        @csrf
                                        <input type="hidden" name="decision" value="refused">
                                        <input type="hidden" name="event_id" value="{{ $item->pivot->event_id }}">
                                        <button class="deny-btn"> Refuse </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
            <br>
            <h2> Your events</h2>
            <div>
                <table id="tb-invitation">
                    <thead>
                        <th class="link-cell">See the page</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>
                                    <p>
                                        <a href="/evento/{{ $event->id }}" class="edit-link">{{ $event->event_name }}</a>
                                    </p>
                                </td>
                                <td>
                                    <a href="/evento/editar/{{ $event->id }}" class="edit-link">
                                        <div class="edit-event">
                                            Edit
                                        </div>
                                    </a>
                                    <form action="/evento/delete/{{ $event->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="deny-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
