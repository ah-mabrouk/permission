<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'name',
        'description',
    ];

    ## Relations

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
