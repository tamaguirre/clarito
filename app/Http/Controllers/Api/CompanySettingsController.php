<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessMethod;
use App\Models\ActionType;
use App\Models\AiTone;
use App\Models\CompanyConfigType;
use App\Models\CompanyEnvironmentConfig;
use App\Models\CompanyWebhook;
use App\Models\Environment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanySettingsController extends Controller
{
    public function catalogs(): JsonResponse
    {
        return response()->json([
            'data' => [
                'environments' => Environment::query()->orderBy('id')->get(['id', 'name']),
                'action_types' => ActionType::query()->orderBy('name')->get(['id', 'name']),
                'access_methods' => AccessMethod::query()->orderBy('name')->get(['id', 'name']),
                'ai_tones' => AiTone::query()->orderBy('name')->get(['id', 'name']),
            ],
        ]);
    }

    public function show(Request $request, Environment $environment): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $configs = CompanyEnvironmentConfig::query()
            ->where('company_id', $companyId)
            ->where('environment_id', $environment->id)
            ->with('configType')
            ->get();

        $mapped = [];

        foreach ($configs as $config) {
            $key = $config->configType?->key;

            if (! $key) {
                continue;
            }

            $mapped[$key] = $config->value_json;
        }

        return response()->json([
            'data' => [
                'environment' => [
                    'id' => $environment->id,
                    'name' => $environment->name,
                ],
                'settings' => $mapped,
            ],
        ]);
    }

    public function update(Request $request, Environment $environment): JsonResponse
    {
        $validated = $request->validate([
            'action_type_id' => ['required', Rule::exists('action_types', 'id')],
            'allow_multiple_confirmation' => ['nullable', 'boolean'],
            'link_expiration_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'access_method_id' => ['required', Rule::exists('access_methods', 'id')],
            'ai_tone_id' => ['required', Rule::exists('ai_tones', 'id')],
            'return_button' => ['required', 'array'],
            'return_button.text' => ['required', 'string', 'max:120'],
            'return_button.url' => ['required', 'url', 'max:2048'],
            'allow_calendar_dates' => ['required', 'boolean'],
            'send_summary_pdf_by_email' => ['required', 'boolean'],
        ]);

        $companyId = $request->user()->company_id;
        $actionType = ActionType::query()->findOrFail($validated['action_type_id']);

        $allowMultiple = $actionType->name === 'confirmacion'
            ? (bool) ($validated['allow_multiple_confirmation'] ?? false)
            : false;

        $payload = [
            'action_type_id' => $validated['action_type_id'],
            'allow_multiple_confirmation' => $allowMultiple,
            'link_expiration_hours' => $validated['link_expiration_hours'],
            'access_method_id' => $validated['access_method_id'],
            'ai_tone_id' => $validated['ai_tone_id'],
            'return_button' => [
                'text' => $validated['return_button']['text'],
                'url' => $validated['return_button']['url'],
            ],
            'allow_calendar_dates' => (bool) $validated['allow_calendar_dates'],
            'send_summary_pdf_by_email' => (bool) $validated['send_summary_pdf_by_email'],
        ];

        $types = CompanyConfigType::query()
            ->whereIn('key', array_keys($payload))
            ->get()
            ->keyBy('key');

        foreach ($payload as $key => $value) {
            $type = $types->get($key);

            if (! $type) {
                continue;
            }

            CompanyEnvironmentConfig::query()->updateOrCreate(
                [
                    'company_id' => $companyId,
                    'environment_id' => $environment->id,
                    'config_type_id' => $type->id,
                ],
                [
                    'value_json' => $value,
                ]
            );
        }

        return response()->json([
            'data' => [
                'saved' => true,
                'settings' => $payload,
            ],
        ]);
    }

    public function webhooks(Request $request, Environment $environment): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $webhooks = CompanyWebhook::query()
            ->where('company_id', $companyId)
            ->where('environment_id', $environment->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $webhooks,
        ]);
    }

    public function storeWebhook(Request $request, Environment $environment): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'url' => ['required', 'url', 'max:2048'],
            'secret' => ['nullable', 'string', 'max:120'],
            'events' => ['nullable', 'array'],
            'events.*' => ['string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $webhook = CompanyWebhook::query()->create([
            'company_id' => $request->user()->company_id,
            'environment_id' => $environment->id,
            'name' => $validated['name'],
            'url' => $validated['url'],
            'secret' => $validated['secret'] ?? null,
            'events' => $validated['events'] ?? [],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return response()->json([
            'data' => $webhook,
        ], 201);
    }

    public function destroyWebhook(Request $request, Environment $environment, CompanyWebhook $webhook): JsonResponse
    {
        if ((int) $webhook->company_id !== (int) $request->user()->company_id || (int) $webhook->environment_id !== (int) $environment->id) {
            return response()->json([
                'message' => 'Webhook no encontrado.',
            ], 404);
        }

        $webhook->delete();

        return response()->json([], 204);
    }
}
