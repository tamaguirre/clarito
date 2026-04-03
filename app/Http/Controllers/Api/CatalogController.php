<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Condition;
use App\Models\EducationLevel;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function educationLevels(): JsonResponse
    {
        return response()->json([
            'data' => EducationLevel::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function conditions(): JsonResponse
    {
        return response()->json([
            'data' => Condition::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
