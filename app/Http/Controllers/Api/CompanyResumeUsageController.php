<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyResumeUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyResumeUsageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'environment_id' => ['nullable', 'integer', 'exists:environments,id'],
        ]);

        $query = CompanyResumeUsage::query()
            ->where('company_id', $request->user()->company_id)
            ->with([
                'environment:id,name',
                'resume:id,token,original_name',
                'user:id,name,email',
            ])
            ->latest('id');

        if (! empty($validated['environment_id'])) {
            $query->where('environment_id', $validated['environment_id']);
        }

        $logs = $query->get()->map(function (CompanyResumeUsage $log): array {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'environment' => $log->environment?->name,
                'resume' => [
                    'id' => $log->resume_id,
                    'token' => $log->resume?->token,
                    'original_name' => $log->resume?->original_name,
                ],
                'user' => [
                    'id' => $log->user_id,
                    'name' => $log->user?->name,
                    'email' => $log->user?->email,
                ],
                'meta' => $log->meta,
                'created_at' => $log->created_at,
            ];
        })->values();

        return response()->json([
            'data' => $logs,
        ]);
    }
}
