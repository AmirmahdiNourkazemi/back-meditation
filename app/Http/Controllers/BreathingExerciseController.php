<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BreathingExercise;


class BreathingExerciseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string',
            'duration' => 'required|integer',
            'created_at' => 'nullable|date',
        ]);

        $user = auth()->user();

        $exercise = BreathingExercise::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'duration' => $data['duration'],
            'created_at' => $data['created_at'] ?? now(),
        ]);

        // Give XP (e.g., +10 per session)
        $user->increment('xp', 10);

        return response()->json([
            'message' => 'Exercise saved',
            'exercise' => $exercise,
            'xp' => $user->xp
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        $user->load('breathingExercises');

        return response()->json([
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'xp' => $user->xp,
            'breathing_exercises' => $user->breathingExercises
        ]);
    }
}
