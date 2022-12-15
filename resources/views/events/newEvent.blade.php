@extends('layouts.main')
@section('title', 'New event')
@section('content')
@section('background', 'main-new-event')

    <div id="div-event">

        <form action="/submitnewevent" method="post" enctype="multipart/form-data">
            @csrf
            <label for="event_name">Event name: </label>
            <input type="text" name="event_name" id="event_name" required>
            <br>
            <br>
            <label for="description">Description: </label>
            <textarea name="description" id="" cols="30" rows="5"></textarea>
            <br>
            <br>
            <label for="event_local">Event local: </label>
            <input type="text" name="event_local" id="event_local" required>
            <br>
            <br>
            <label for="event_date">Event time: </label>
            <input type="datetime-local" name="event_date" id="event_time" required>
            <br>
            <br>
            <label for="private">This event will be: </label>
            <select name="private" id="event_privacity" required>
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="secret">Secret</option>
            </select>
            <br>
            <br>
            <div id="opt-div">
                <span class="checkb">
                    <label for="">Options:</label>
                    <input type="checkbox" name="properties[]"  value="open food">
                    <label for="properties">Open food</label>
                </span>
                <span class="checkb">
                    <input type="checkbox" name="properties[]"  value="open bar">
                    <label for="properties">Open bar</label>
                </span>
                <span class="checkb">
                    <input type="checkbox" name="properties[]"  value="chairs">
                    <label for="properties">Chairs</label>
                </span>
                <span class="checkb">
                    <input type="checkbox" name="properties[]"  value="giveaways">
                    <label for="properties">Giveaways</label>
                </span>
            </div>
                <br>
            <br>
            <label for="">Add event image:</label>
            <input type="file" name="event_image" id="event_image" accept="image/png, image/gif, image/jpeg">
            <br>
            <br>
            <button type="submit" class="submit-event">Add event</button>

        </form>

    </div>
    </div>
@endsection
