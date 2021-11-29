<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionGroupTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'name',
    ];

    ## Relations

    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }
}
