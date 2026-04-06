<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'birth_date' => $this->birth_date?->toDateString(),
            'education_level' => $this->when(
                $this->relationLoaded('educationLevel') && $this->educationLevel,
                [
                    'id' => $this->educationLevel?->id,
                    'name' => $this->educationLevel?->name,
                ]
            ),
            'role' => $this->when(
                $this->relationLoaded('role') && $this->role,
                [
                    'id' => $this->role?->id,
                    'name' => $this->role?->name,
                ]
            ),
            'company' => $this->when(
                $this->relationLoaded('company') && $this->company,
                [
                    'id' => $this->company?->id,
                    'name' => $this->company?->name,
                ]
            ),
            'conditions' => $this->when(
                $this->relationLoaded('conditions'),
                $this->conditions->map(fn ($condition) => [
                    'id' => $condition->id,
                    'name' => $condition->name,
                ])->values()
            ),
            'created_at' => $this->created_at,
        ];
    }
}
