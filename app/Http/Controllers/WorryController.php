<?php

namespace App\Http\Controllers;
use App\Models\Worry;
use App\Models\Note;
use Illuminate\Http\Request;

class WorryController extends Controller
{
      public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'note'    => 'nullable|string'
        ]);

        $worry = Worry::create([
            'user_id' => auth()->id(),
            'title'   => $data['title'],
        ]);

        if (!empty($data['note'])) {
            $worry->notes()->create([
                'user_id' => auth()->id(),
                'content' => $data['note']
            ]);
        }

        return response()->json($worry->load('notes'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'note'  => 'nullable|string'
        ]);

        $worry = Worry::where('user_id', auth()->id())->findOrFail($id);

        if (!empty($data['title'])) {
            $worry->update(['title' => $data['title']]);
        }

        if (isset($data['note'])) {
            $note = $worry->notes()->where('user_id', auth()->id())->first();

            if ($note) {
                $note->update(['content' => $data['note']]);
            } else {
                $worry->notes()->create([
                    'user_id' => auth()->id(),
                    'content' => $data['note']
                ]);
            }
        }

        return response()->json($worry->load('notes'));
    }

    public function index()
    {
        $worries = Worry::where('user_id', auth()->id())
            ->with('notes')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($worries);
    }

    public function toggleSolved($id)
    {
        $worry = Worry::where('user_id', auth()->id())->findOrFail($id);
        $worry->update(['is_solved' => !$worry->is_solved]);

        return response()->json($worry);
    }

    public function destroy($id)
    {
        $worry = Worry::where('user_id', auth()->id())->findOrFail($id);
        $worry->delete();

        return response()->json(['message' => 'Worry deleted successfully']);
    }
}
