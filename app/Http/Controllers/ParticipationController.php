<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participation;

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $participations = Participation::all();
        return response()->json($participations);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $participation = Participation::find($id);
        return response()->json($participation);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'event_id' => 'required|integer|exists:events,id',
            'time_in' => 'required|date_format:Y-m-d H:i:s',
            'time_out' => 'nullable|date_format:Y-m-d H:i:s|after:time_in',
        ]);
    }

    public function delete(string $id)
    {
        $participation = Participation::find($id);
        if (!$participation) {
            return response()->json(['message' => 'Participation not found'], 404);
        }
        $participation->delete();
        return response()->json(['message' => 'Participation deleted successfully'], 200);
    }

    public function scan(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
    ]);

    $user = $request->user();

    // Ensure the user has a linked student record
    $student = $user->student;
    if (!$student) {
        return response()->json([
            'status' => 'error',
            'message' => 'No student record linked to this user.'
        ], 422);
    }

    $eventId = $request->event_id;

    $participation = Participation::where('student_id', $student->id)
        ->where('event_id', $eventId)
        ->first();

    if ($participation) {
        return response()->json([
            'status' => 'already_in',
            'message' => 'You are already participating in this event.'
        ]);
    }

    Participation::create([
        'student_id' => $student->id,
        'event_id' => $eventId,
        'time_in' => now(),
    ]);

    return response()->json([
        'status' => 'joined',
        'message' => 'Participation recorded!'
    ]);
}


    // Time out from event
    public function timeOut(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $user = $request->user();
        $eventId = $request->event_id;

        $participation = Participation::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if (!$participation) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'You are not participating in this event.'
            ], 404);
        }

        $participation->update([
            'time_out' => now(),
        ]);

        return response()->json([
            'status' => 'timed_out',
            'message' => 'You have timed out of the event.'
        ]);
    }
    
}
