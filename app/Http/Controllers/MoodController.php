<?php

namespace App\Http\Controllers;

use App\Models\Mood;
use App\Models\UserMood;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    // Store today's mood
    public function storeUserMood(Request $request)
    {
        $data = $request->validate([
            'mood_id' => 'required|exists:moods,id',
             'note'    => 'nullable|string'
        ]);

        $user = auth()->user();

        $moodEntry = UserMood::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => now()->toDateString(),
            ],
            [
                'mood_id' => $data['mood_id'],
            ]
        );
          // If a note is provided, create or update it
        if (!empty($data['note'])) {
            $moodEntry->notes()->updateOrCreate(
                ['user_id' => $user->id],
                ['content' => $data['note']]
            );
        }
      
            $user->increment('xp', 10);
      
        return response()->json([
            'message' => 'Mood saved successfully',
            'xp' => $user->xp,
            'mood'    => $moodEntry->load(['mood', 'notes'])
        ]);
    }

    // Get list of all moods user has set
    public function getUserMoods()
        {
            $user = auth()->user();

            $moods = UserMood::where('user_id', $user->id)
                ->with(['mood', 'notes'])
                ->orderBy('date', 'desc')
                ->get();

            return response()->json($moods);
        }

    public function getAllMoods()
    {
        $moods = Mood::all();
        return response()->json($moods);
    }
}
