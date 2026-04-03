<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationLevelResource;
use App\Models\EducationLevel;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EducationLevelController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EducationLevelResource::collection(
            EducationLevel::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
        );
    }
}
