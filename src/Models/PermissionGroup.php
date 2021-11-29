<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Mabrouk\Translatable\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionGroup extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = [
        'name',
    ];

    protected $fillable = [
        'id',
    ];

    ## Relations

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_group_id');
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
