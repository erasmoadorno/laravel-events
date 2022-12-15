@extends('layouts.main')
@section('title', "Editing: $event->event_name ")
@section('content')

    <div id="main-edit-event">
        <div id="div-event">


            <form action="/submiteditevent/{{ $event->id }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <label for="event_name">Event name: </label>
                <input type="text" name="event_name" required id="event_name" value="{{ $event->event_name }}">
                <br>
                <br>
                <label for="description">Description: </label>
                <textarea name="description" id="" cols="30" rows="10" >{{ $event->description }}</textarea>
                <br>
                <br>
                <label for="event_local">Event Local: </label>
                <input type="text" name="event_local" id="event_local" required value="{{ $event->event_local }}">
                <br>
                <br>
                <label for="event_date">Event time: </label>
                <input type="datetime-local" name="event_date" id="event_time" required value="{{ $event->event_date }}">
                <br>
                <br>
                <label for="private">Event privacity: </label>
                <select name="private" id="event_privacity" required>
                    <option value="public" {{ $event->private == 'public' ? 'selected' : '' }}>Public</option>
                    <option value="private" {{ $event->private == 'private' ? 'selected' : '' }}>Private</option>
                    <option value="secret" {{ $event->private == 'secret' ? 'selected' : '' }}>Secret</option>
                </select>
                <br>
                <br>
                <div id="opt-div">
                    <span>
                        <label for="">Options:</label>
                        <input type="checkbox" name="properties[]" id="" value="open food"
                        @foreach ($event->properties as $item)
                            @if ($item == "open food")
                                checked
                            @endif
                        @endforeach
                        ><label for="">Open
                            food</label>
                    </span>
                    <span><input type="checkbox" name="properties[]" id="" value="open bar"
                        @foreach ($event->properties as $item)
                            @if ($item == "open bar")
                                checked
                            @endif
                            
                        @endforeach
                        ><label
                            for="">Open
                            bar</label></span>
                    <span><input type="checkbox" name="properties[]" id="" value="chairs"
                        @foreach ($event->properties as $item)
                            @if ($item == "chairs")
                                checked
                            @endif
                        @endforeach
                        ><label
                            for="">Chairs</label></span>
                    <span><input type="checkbox" name="properties[]" id="" value="giveaways"
                        @foreach ($event->properties as $item)
                            @if ($item == "giveaways")
                                checked
                            @endif
                        @endforeach
                        ><label
                            for="">Giveaways</label></span>
                </div>
                <br>
                <br>
                @if (isset($event->event_image))
                    <div id="preview_img">
                        <span>Currently event image:</span>
                        <img src="{{ url("storage/events/{$event->event_image}") }}" alt="{{ $event->event_name }}"
                            id="event_img">
                    </div>
                    <br>
                @endif
                <label for="">Add event image:</label>
                <input type="file" name="event_image" id="event_image" accept="image/png, image/gif, image/jpeg">
                <br>
                <br>
                <button type="submit" class="submit-event">Update event</button>

            </form>
        </div>
    </div>

@endsection
