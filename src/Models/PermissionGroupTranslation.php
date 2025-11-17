<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionGroupTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'name',
    ];

    ## Relations

    public function permissionGroup(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
