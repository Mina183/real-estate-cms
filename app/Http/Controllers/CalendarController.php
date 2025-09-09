<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meeting;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingInvite;
use App\Mail\MeetingUpdated;
use Carbon\Carbon;

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
                'title' => $meeting->title . ' (by ' . ($meeting->creator->name ?? 'Unknown') . ')',
                // SEND ISO (UTC) so the browser can convert to its local time
                'start'       => $start?->toIso8601String(),
                'end'         => $end?->toIso8601String(),
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
                'start' => $start?->toIso8601String(),
                'end' => $end?->toIso8601String(),
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
        'tz' => 'nullable|string', // hidden field from the form
    ]);

    // Use browser tz if sent; fallback to user/app tz
    $tz = $request->input('tz') ?: (auth()->user()->timezone ?? config('app.timezone', 'UTC'));

    // Parse datetime-local in that tz, then convert to UTC
    $startUtc = Carbon::parse($data['start_time'], $tz)->utc();
    $endUtc   = $request->filled('end_time') ? Carbon::parse($data['end_time'], $tz)->utc() : null;

    $meeting = Meeting::create([
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'start_time'  => $startUtc,   // ← save UTC
        'end_time'    => $endUtc,     // ← save UTC
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
        'tz' => 'nullable|string',
    ]);

    $tz = $request->input('tz') ?: (auth()->user()->timezone ?? config('app.timezone', 'UTC'));

     $startUtc = Carbon::parse($data['start_time'], $tz)->utc();
    $endUtc   = $request->filled('end_time') ? Carbon::parse($data['end_time'], $tz)->utc() : null;

    $meeting->update([
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'start_time'     => $startUtc,   // ← save UTC
        'end_time'       => $endUtc,     // ← save UTC
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
