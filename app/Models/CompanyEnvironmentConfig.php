<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
class CompanyEnvironmentConfig extends Model
{
    use HasFactory;

    public function configType(): BelongsTo
    {
        return $this->belongsTo(CompanyConfigType::class);
    }

    protected function casts(): array
    {
        return [
            'value_json' => 'array',
        ];
    }
}
