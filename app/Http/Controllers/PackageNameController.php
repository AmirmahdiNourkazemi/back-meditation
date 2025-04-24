<?php

namespace App\Http\Controllers;

use App\Models\CafeConfig;
use App\Models\PackageName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageNameController extends Controller
{
    public function index(Request $request)
    {
        $packageNames = PackageName::get();
        return $packageNames;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'string|required',
            'title' => 'string|required',
            'avatar' => 'image',
            'v1_identifier' => 'string|nullable',
            'myket_access_token' => 'string|nullable',
            'cafe_config_id' => 'integer|nullable',
            'web_app_url' => 'string|nullable',
            'tries' => 'integer'
        ]);

        if ($packageName = PackageName::where('name', $data['name'])->first()) {
            return response()->json([
                'message' => 'package exists'
            ], 400);
        }

        if (isset($data['avatar'])) {
            Storage::disk('public')->put("package-names/{$data['name']}.png", file_get_contents($data['avatar']->path()));
            $data['image'] = "storage/package-names/{$data['name']}.png";
            unset($data['avatar']);
        }

        if (isset($data['cafe_config_id']) && !$CafeConfig = CafeConfig::where('id', $data['cafe_config_id'])->first()) {
            return response()->json([
                'message' => 'cafe config not found'
            ], 404);
        }

        $packageName = PackageName::create($data);
        return $packageName;
    }

    public function update(Request $request, $name)
    {
        $data = $request->validate([
            'title' => 'string|required',
            'avatar' => 'image',
            'v1_identifier' => 'string|nullable',
            'myket_access_token' => 'string|nullable',
            'cafe_config_id' => 'integer|nullable',
            'web_app_url' => 'string|nullable',
            'tries' => 'integer'
        ]);

        if (isset($data['avatar'])) {
            Storage::disk('public')->put("package-names/{$name}.png", file_get_contents($data['avatar']->path()));
            $data['image'] = "storage/package-names/{$name}.png";
            unset($data['avatar']);
        }


        if (isset($data['cafe_config_id']) && !$CafeConfig = CafeConfig::where('id', $data['cafe_config_id'])->first()) {
            return response()->json([
                'message' => 'cafe config not found'
            ], 404);
        }

        if (!$packageName = PackageName::where('name', $name)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        $packageName->update($data);

        return $packageName;
    }
}
