<?php

namespace App\Http\Controllers;

use App\Models\CafeConfig;
use Illuminate\Http\Request;

class CafeController extends Controller
{
    public function index(Request $request)
    {
        return CafeConfig::get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'access_token' => 'required|string',
            'refresh_token' => 'required|string',
        ]);
        
        $CafeConfig = CafeConfig::create($data);
        
        return $CafeConfig;
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'access_token' => 'required|string',
            'refresh_token' => 'required|string',
        ]);
        
        if (!$cafeConfig = CafeConfig::where('id', $id)->first()) {
            return response()->json([
                'message' => 'cafe config not found'
            ], 404);
        }

        $cafeConfig->update($data);
        
        return $cafeConfig;
    }
}
