<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompleteCompanyRegistrationRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyTypeResource;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\CompanyType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyRegistrationController extends Controller
{
    public function show(Request $request, string $token): JsonResponse
    {
        $invitation = $this->getValidInvitation($token);

        if (! $invitation) {
            return response()->json([
                'message' => 'Invitacion invalida o expirada.',
            ], 404);
        }

        $company = $invitation->company()->with(['companyType', 'dictionaries'])->firstOrFail();

        return response()->json([
            'data' => [
                'company' => new CompanyResource($company),
                'company_types' => CompanyTypeResource::collection(
                    CompanyType::query()->orderBy('name')->get()
                ),
            ],
        ]);
    }

    public function complete(CompleteCompanyRegistrationRequest $request, string $token): JsonResponse
    {
        $invitation = $this->getValidInvitation($token);

        if (! $invitation) {
            return response()->json([
                'message' => 'Invitacion invalida o expirada.',
            ], 404);
        }

        $validated = $request->validated();

        $company = DB::transaction(function () use ($request, $validated, $invitation): Company {
            $company = $invitation->company()->firstOrFail();

            $logoPath = $company->logo_path;

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('company-logos', 'public');
            }

            $company->update([
                'company_type_id' => $validated['company_type_id'],
                'phone' => $validated['phone'],
                'short_description' => $validated['short_description'],
                'logo_path' => $logoPath,
                'registration_completed_at' => now(),
            ]);

            $company->dictionaries()->delete();
            $company->dictionaries()->createMany(
                collect($validated['dictionary'])
                    ->map(fn (array $item): array => [
                        'word' => $item['word'],
                        'definition' => $item['definition'],
                    ])
                    ->all()
            );

            $companyRole = Role::query()->firstOrCreate(['name' => 'company']);

            User::query()->updateOrCreate(
                ['email' => $company->email],
                [
                    'name' => $company->name.' Admin',
                    'password' => Hash::make($validated['password']),
                    'role_id' => $companyRole->id,
                    'company_id' => $company->id,
                ]
            );

            $invitation->update([
                'used_at' => now(),
            ]);

            return $company->fresh(['companyType', 'dictionaries']);
        });

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(200);
    }

    private function getValidInvitation(string $token): ?CompanyInvitation
    {
        return CompanyInvitation::query()
            ->where('token', $token)
            ->whereNull('used_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }
}
