<?php

namespace Mabrouk\RolePermissionGroup\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Roleable extends Pivot
{
    public $table = 'roleables';

    protected $fillable = [
        'role_id',
        'roleable_type',
        'roleable_id',
    ];

    ## Relations

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roleable()
    {
        return $this->morphTo();
    }
}
