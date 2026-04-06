<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_type' => $this->when(
                $this->relationLoaded('companyType') && $this->companyType,
                [
                    'id' => $this->companyType?->id,
                    'name' => $this->companyType?->name,
                ]
            ),
            'short_description' => $this->short_description,
            'logo_url' => $this->logo_url,
            'registration_completed_at' => $this->registration_completed_at,
            'dictionary' => $this->when(
                $this->relationLoaded('dictionaries'),
                $this->dictionaries->map(fn ($item) => [
                    'id' => $item->id,
                    'word' => $item->word,
                    'definition' => $item->definition,
                ])->values()
            ),
            'created_at' => $this->created_at,
        ];
    }
}
