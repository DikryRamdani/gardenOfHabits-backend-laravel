<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Gamification\GardenService;

class GardenController extends Controller
{
    protected GardenService $gardenService;

    public function __construct(GardenService $gardenService)
    {
        $this->gardenService = $gardenService;
    }

    public function show(Request $request)
    {
        $garden = $this->gardenService->applyDecay($request->user());

        if (!$garden) {
            return response()->json(['message' => 'Garden not found'], 404);
        }

        return response()->json([
            'message' => 'Garden retrieved successfully',
            'data'    => $garden,
        ], 200);
    }
}
