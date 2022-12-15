<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\FileExists;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Spatie\Backtrace\File;

class EventController extends Controller
{
    public function events()
    {
        $search = request('search');
        if ($search) {
            $events = Event::where('event_name', 'like', '%' . $search . '%')->get();
        } else {
            $events = Event::all()->sortBy("event_date")->where('private', '!=', 'secret');
        }

        return view("welcome", ['events' => $events]);
    }

    public function newEvent()
    {
        return view("events.newEvent");
    }
    public function store(Request $request)
    {
        $event = new Event();
        $event->event_name = $request->event_name;
        $event->description = $request->description;
        $event->event_local = $request->event_local;
        $event->event_date = $request->event_date;
        $event->private = $request->private;
        $event->properties = $request->properties;

        if ($request->hasFile('event_image') && $request->file('event_image')->isValid()) {
            $reqImg = $request->event_image;
            $imgExtension = $reqImg->extension();
            $imgName = md5($reqImg->getClientOriginalName()) . strtotime('now') . "." . $imgExtension;
            $event->event_image = $imgName;
            Storage::putFileAs('events', $request->file("event_image"), $imgName);
        }
        $event->user_id = auth()->user()->id;
        $event->save();
        return redirect("/")->with("msgSuccess", "Event was created!");
    }

    public function dashboard()
    {
        $authenticatedUser = auth()->user();
        $events = $authenticatedUser->events;
        $eventAsParticipant = User::findOrFail($authenticatedUser->id)->eventAsParticipant()->wherePivot('user_id', $authenticatedUser->id)->get();
        return view('dashboard', ['authenticatedUser' => $authenticatedUser, 'events' => $events, 'eventAsParticipant' => $eventAsParticipant]);
    }

    public function editEvent($id)
    {
        $event = Event::findOrFail($id);
        if (str($event->user_id) != str(auth()->user()->id)) {
            return redirect("/");
        }
        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $event = Event::findOrFail($request->id);
        if (str($event->user_id) != str(auth()->user()->id)) {
            return redirect("/");
        }
        if ($request->hasFile('event_image') && $request->file('event_image')->isValid()) {
            if (Storage::exists("events/" . $event->event_image)) {
                Storage::delete("events/" . $event->event_image);
            }
            $reqImg = $request->event_image;
            $imgExtension = $reqImg->extension();
            $imgName = md5($reqImg->getClientOriginalName()) . strtotime('now') . "." . $imgExtension;
            $data['event_image'] = $imgName;
            Storage::putFileAs('events', $request->file("event_image"), $imgName);
        }

        $event->update($data);

        return redirect("/dashboard")->with('msgSuccess', 'Event was updated successfully!');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $imgDirectory = ("events/" . $event->event_image);
        if (Storage::exists($imgDirectory)) {
            Storage::delete($imgDirectory);
        }
        $event->delete();
        return redirect("/dashboard")->with('msgSuccess', 'Event deleted!');
    }

    public function event($id)
    {
        $search = request('search');
        if ($search) {
            $users = User::where('name', 'like', '%' . $search . '%')->orWhere('email', '=', $search)->get();
        } else {
            $users = [];
        }
        $event = Event::findOrFail($id);
        $participants = $event->participants()->wherePivot('present', 'requested')->get();
        return view("events.event", ["event" => $event, "users" => $users, 'participants' => $participants]);
    }

    public function joinEvent($id)
    {

        #bd atributos: invited = [accepted, requesting, denied];
        # present = [confirmed, requested, refused]  

        $user = User::findOrFail(auth()->user()->id);
        $event = Event::findOrFail($id);
        $findUserEvent = $user->eventAsParticipant()->wherePivot('event_id', $event->id)->get();
        $msg = "";
        $msgStatus = "";

        if (count($findUserEvent) > 0) {
            foreach ($findUserEvent as $key => $value) {
                if ($value->pivot->user_id == $user->id && ($value->pivot->present == 'confirmed' || $value->pivot->invited == 'denied')) {
                    # Aqui redicionaria para a página do evento com a mensagem de que já está participando
                    $msg = "Already participating!";
                    $msgStatus = "msgWarn";
                } elseif ($value->private == 'public' && $value->pivot->user_id == $user->id && $value->pivot->present != 'confirmed') {
                    # Aqui ira ALTERAR presença no evento publico
                    $user->eventAsParticipant()->updateExistingPivot($id, ['present' => 'confirmed']);
                    $msg = "Attendance at the event held successfully!";
                    $msgStatus = "msgSuccess";
                } elseif ($value->private == 'private' && $value->pivot->user_id == $user->id && $value->pivot->present != 'confirmed' && $value->pivot->invited == 'accepted') {
                    # Aqui ira ALTERAR presença como confirmado em evento privado e invitado para aceitado
                    $user->eventAsParticipant()->updateExistingPivot($id, ['present' => 'confirmed']);
                    $msg = "Invitation accepted!";
                    $msgStatus = "msgSuccess";
                } elseif ($value->private == 'private' && $value->pivot->user_id == $user->id && $value->pivot->invited != 'accepted' && $value->pivot->invited != 'denied') {
                    # Aqui definirá a solicitação para participar do evento
                    $user->eventAsParticipant()->updateExistingPivot($id, ['present' => 'requested']);
                    $msg = "Wait for the response from the person responsible for the event!";
                    $msgStatus = "msgWarn";
                } else {
                    $msg = "Erro, ainda falta fazer os scripts de evento secret";
                    $msgStatus = "msgDanger";
                }
            }
        } else {
            if ($event->private == 'public') {
                # Aqui criará um registro já confirmando presença
                $user->eventAsParticipant()->attach($id, ['present' => 'confirmed', 'invited' => 'accepted']);
                $msg = "Are you now partipating in the event!";
                $msgStatus = "msgSuccess";
            } elseif ($event->private == 'private') {
                # Aqui criará um registro requisitando presença
                $user->eventAsParticipant()->attach($id, ['present' => 'requested', 'invited' => 'requesting']);
                $msg = "Your request to participate in the event has been sent, wait!";
                $msgStatus = "msgSucces";
            } elseif ($event->private == 'secret') {
                # Usuário não pode entrar no evento secret sem um convite
                $msg =  "You cannot attend this event without an invitation!";
                $msgStatus = "msgDanger";
            } else {
                $msg = "Deu ruim!";
                $msgStatus = "msgDanger";
            }
        }
        return redirect("/")->with($msgStatus, $msg);
    }

    public function inviteEvent(Request $request)
    {
        $decryptedId = Crypt::decrypt($request->user_id);
        $user = User::findOrFail($decryptedId);
        $event = Event::findOrFail($request->event_id);
        $findUserEvent = $user->eventAsParticipant()->wherePivot('event_id', $event->id)->get();
        $msg = "";
        $msgStatus = "";

        #bd atributos: invited = [accepted, requesting, denied];
        # present = [confirmed, requested, refused]  

        if (count($findUserEvent) > 0) {
            foreach ($findUserEvent as $value) {
                if ($value->private == 'private' && $value->pivot->invited == 'requesting' && $value->pivot->user_id == $user->id) {
                    $user->eventAsParticipant()->updateExistingPivot($request->event_id, ['invited' => 'accepted', 'present' => 'confirmed']);
                    $msg = "Invitation accepted!";
                    $msgStatus = "msgSuccess";
                } else if ($value->private == 'private' && $value->pivot->present == 'requested' && $value->pivot->user_id == $user->id) {
                    $user->eventAsParticipant()->updateExistingPivot($request->event_id, ['invited' => 'accepted', 'present' => 'confirmed']);
                    $msg = "Invitation accepted!";
                    $msgStatus = "msgSuccess";
                } else if ($value->pivot->present == 'denied') {
                    $msg = "This user declined the invitation!";
                    $msgStatus = "msgWarn";
                } else if ($value->pivot->present == 'confirmed') {
                    $msg = "This user is already in the event!";
                    $msgStatus = "msgWarn";
                } else if ($value->pivot->invited == 'accepted') {
                    $msg = "This user has already been invited, wait for his response!";
                    $msgStatus = "msgWarn";
                } else {
                    $msg = "Erro inesperado";
                    $msgStatus = "msgDanger";
                }
            }
        } else {
            $user->eventAsParticipant()->attach($request->event_id, ['invited' => 'accepted']);
            $msg = "Invitation has been sent!";
            $msgStatus = "msgSuccess";
        }
        return redirect("/evento/$request->event_id")->with($msgStatus, $msg);
    }

    public function inviteDecision(Request $request)
    {
        $decision = $request->decision;
        $user = User::findOrFail(auth()->user()->id);
        $eventId = $request->event_id;
        $findUserEvent = $user->eventAsParticipant()->wherePivot('event_id', $eventId)->get();

        if (isset($decision) && count($findUserEvent) > 0) {
            $user->eventAsParticipant()->updateExistingPivot($eventId, ['present' => $decision]);
            return redirect('dashboard')->with('msg', 'Deu bão');
        } else {
            return redirect('dashboard')->with('msg', 'Deu ruim puts');
        }
    }

    public function requireDecision(Request $request)
    {

        $decision = $request->decision;
        $msg = "";
        if ($decision != 'confirmed' && $decision != 'refused') {
            return redirect("/")->with("msgDanger", "Decision must be accept ou deny");
        } else {
            if ($decision == 'confirmed') {
                $invite = "accepted";
                $msg = "Invitation accepted";
                $msgStatus = "msgSuccess";
            } elseif ($decision == 'refused') {
                $invite = "denied";
                $msg = "Invitation denied";
                $msgStatus = "msgDanger";
            }
        }
        $userId = Crypt::decrypt($request->user_id);
        $user = User::findOrFail($userId);
        $event = Event::findOrFail($request->event_id);
        $findOwnerEvent = $user->eventAsParticipant()->wherePivot('event_id', $event->id)->get();
        foreach ($findOwnerEvent as $key => $value) {
            if ($value->pivot->present == 'requested') {
                $user->eventAsParticipant()->updateExistingPivot($event->id, ['present' => $decision, 'invited' => $invite]);
                return redirect("/evento/$event->id")->with($msgStatus, $msg);
            } else {
                return redirect("/evento/$event->id")->with('msgDanger', "");
            }
        }
        return redirect("/evento/$event->id")->with('msgDanger', "Deu ruim: ");
    }
}
