<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BreathingExercise;
use App\Models\BreathingTemplate;
use App\Models\UserBreathingSession;
class BreathingExerciseController extends Controller
{

    public function createTemplate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'inhale' => 'required|integer|min:1',
            'exhale' => 'required|integer|min:1',
            'repeat' => 'required|integer|min:1',
        ]);

        $template = BreathingTemplate::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'inhale' => $data['inhale'],
            'exhale' => $data['exhale'],
            'repeat' => $data['repeat'],
        ]);

        return response()->json(['message' => 'Template created', 'template' => $template]);
    }

  public function updateTemplate(Request $request, $id)
{
    $template = BreathingTemplate::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $data = $request->validate([
        'name' => 'sometimes|required|string',
        'inhale' => 'sometimes|required|integer|min:1',
        'exhale' => 'sometimes|required|integer|min:1',
        'repeat' => 'sometimes|required|integer|min:1',
    ]);

    $template->update($data);

    return response()->json(['message' => 'Template updated', 'template' => $template]);
}
    public function deleteTemplate($id)
    {
        $template = BreathingTemplate::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $template->delete();

        return response()->json(['message' => 'Template deleted']);
    }

    public function getUserTemplates()
    {
        $templates = BreathingTemplate::where('user_id', auth()->id())->get();
        return response()->json($templates);
    }


    public function getTemplates()
    {
        $templates = BreathingTemplate::whereNull('user_id')
            ->orWhere('user_id', auth()->id())
            ->get();

        return response()->json($templates);
    }
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
        if ($data['created_at'] == null) {
            $user->increment('xp', 10);
        }

        return response()->json([
            'message' => 'Exercise saved',
            'exercise' => $exercise,
            'xp' => $user->xp
        ]);
    }

      public function completeSession(Request $request)
    {
        $data = $request->validate([
            'template_id' => 'required|exists:breathing_templates,id',
        ]);

        $session = UserBreathingSession::create([
            'user_id' => auth()->id(),
            'template_id' => $data['template_id'],
        ]);

        // Increase XP
        auth()->user()->increment('xp', 10);

        return response()->json(['message' => 'Session completed', 'session' => $session]);
    }

    public function getSessions()
    {
        $user = auth()->user();
        $templates = BreathingTemplate::where('user_id', $user->id)->get();
        $sessions = UserBreathingSession::with('template')->where('user_id', $user->id)->get();
        
        return response()->json($sessions);
    }

  
}
