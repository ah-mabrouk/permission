<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubPermissionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'display_name',
    ];

    ## Relations

    public function subPermission(): BelongsTo
    {
        return $this->belongsTo(SubPermission::class, 'sub_permission_id');
    }

    ## Getters & Setters

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = $this->prepareDisplayName($value);
    }

    private function prepareDisplayName($value)
    {
        return \explode('_', $value)[1];
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
