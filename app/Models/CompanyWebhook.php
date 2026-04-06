<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Guarded([])]
class CompanyWebhook extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
