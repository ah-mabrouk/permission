<?php

namespace Mabrouk\RolePermissionGroup\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubPermissionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'display_name',
    ];

    ## Relations

    public function subPermission()
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
        return \explode('_', $this->subPermission->name)[1];
    }
}
