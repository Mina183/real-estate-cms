<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meeting;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingInvite;
use App\Mail\MeetingUpdated;

class CalendarController extends Controller
{
public function index()
{
    $user = auth()->user();

    $meetings = [];

    if ($user->role === 'channel_partner') {
        // Get meetings this partner is invited to via pivot table
        $meetings = $user->meetings()->withPivot('is_accepted', 'accepted_at')->get();
    }

    return view('calendar.index', compact('meetings'));
}

public function fetchMeetings()
{
    $user = auth()->user();

    if (in_array($user->role, ['admin', 'superadmin'])) {
        $meetings = Meeting::with('attendees', 'creator')->get();

        $events = $meetings->map(function ($meeting) {
            $start = $meeting->start_time; // Carbon (thanks to cast)
            $end   = $meeting->end_time ?: ($start ? $start->copy()->addHour() : null);

            return [
                'id' => $meeting->id,
                'title' => $meeting->titl . ' (by ' . ($meeting->creator->name ?? 'Unknown') . ')',
                'start' => $meeting->start_time,
                'end' => $meeting->end_time ?? null,
                'description' => $meeting->description,
                'is_accepted' => null, // Admins don’t need this
            ];
        });
    } else {
        // ✅ IMPORTANT: withPivot
        $meetings = $user->meetings()->withPivot('is_accepted')->get();

        $events = $meetings->map(function ($meeting) {
            $start = $meeting->start_time;
            $end   = $meeting->end_time ?: ($start ? $start->copy()->addHour() : null);

            return [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'start' => $meeting->start_time,
                'end' => $meeting->end_time ?? null,
                'description' => $meeting->description,
                'is_accepted' => isset($meeting->pivot) ? $meeting->pivot->is_accepted : null,
            ];
        });
    }

    return response()->json($events);
}

public function create()
{
    $partners = User::where('role', 'channel_partner')->get();
    return view('calendar.create', compact('partners'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_time' => 'required|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'attendees' => 'required|array',
    ]);

    // 👇 Pick a timezone (per-user if you store one; otherwise app timezone)
    $tz = auth()->user()->timezone ?? config('app.timezone', 'UTC');

    // 👇 datetime-local (e.g. 2025-09-10T15:04) parsed in user's tz, stored as UTC
    $startUtc = Carbon::parse($data['start_time'], $tz)->utc();
    $endUtc   = isset($data['end_time']) && $data['end_time'] !== null
              ? Carbon::parse($data['end_time'], $tz)->utc()
              : null;

    $meeting = Meeting::create([
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'] ?? null,
        'created_by' => auth()->id(),
    ]);

    $attachData = collect($data['attendees'])->mapWithKeys(function ($partnerId) {
        return [$partnerId => ['is_accepted' => null, 'accepted_at' => null]];
    })->toArray();

    $meeting->attendees()->attach($attachData);

    // Optional: send invite emails
    foreach ($data['attendees'] as $partnerId) {
        $partner = User::find($partnerId);
        Mail::to($partner->email)->send(new MeetingInvite($meeting, $partner));
    }

    return redirect()->route('calendar.index')->with('success', 'Meeting created and invites sent!');
}

public function edit(Meeting $meeting)
{
    $partners = User::where('role', 'channel_partner')->get();
    $meeting->load('attendees');
    return view('calendar.edit', compact('meeting', 'partners'));
}

public function update(Request $request, Meeting $meeting)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'change_comment' => 'required|string|max:1000',
        'start_time' => 'required|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'attendees' => 'required|array|min:1', // <-- validation now handles this
    ]);

    $tz = auth()->user()->timezone ?? config('app.timezone', 'UTC');

    $startUtc = Carbon::parse($data['start_time'], $tz)->utc();
    $endUtc   = isset($data['end_time']) && $data['end_time'] !== null
              ? Carbon::parse($data['end_time'], $tz)->utc()
              : null;

    $meeting->update([
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'] ?? null,
        'change_comment' => $data['change_comment'],
    ]);

    $attachData = collect($data['attendees'])->mapWithKeys(function ($partnerId) {
        return [$partnerId => ['is_accepted' => null, 'accepted_at' => null]];
    })->toArray();

    $meeting->attendees()->sync($attachData);

    // Send update email to all current attendees
    foreach ($meeting->attendees as $partner) {
        Mail::to($partner->email)->send(new \App\Mail\MeetingUpdated($meeting, $partner));
    }

return redirect()->route('calendar.index')->with('success', 'Meeting updated and emails sent!');
}

public function destroy(Meeting $meeting)
{
    $meeting->delete();
    return redirect()->route('calendar.index')->with('success', 'Meeting deleted.');
}

public function respond(Request $request, Meeting $meeting)
{
    $data = $request->validate([
        'response' => 'required|in:0,1',
    ]);

    $user = auth()->user();

    $meeting->attendees()->updateExistingPivot($user->id, [
        'is_accepted' => (bool) $request->response,
        'accepted_at' => now(),
    ]);

    return redirect()->route('calendar.index')->with('success', 'Response recorded.');
}
}
