<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConditionResource;
use App\Models\Condition;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConditionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ConditionResource::collection(
            Condition::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
        );
    }
}
