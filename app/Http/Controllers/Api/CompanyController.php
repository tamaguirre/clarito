<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Mail\CompanyRegistrationInvitationMail;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CompanyResource::collection(
            Company::query()
                ->with('companyType')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $company = Company::create($validated);

        $invitation = CompanyInvitation::create([
            'company_id' => $company->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($company->email)->send(new CompanyRegistrationInvitationMail($invitation->load('company')));

        return (new CompanyResource($company))
            ->additional([
                'meta' => [
                    'invitation_sent' => true,
                    'invitation_expires_at' => $invitation->expires_at,
                ],
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Company $company): CompanyResource
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company->id)],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $company->update($validated);

        return new CompanyResource($company);
    }

    public function destroy(Company $company): Response
    {
        User::query()->where('company_id', $company->id)->update(['company_id' => null]);
        $company->delete();

        return response()->noContent();
    }
}
