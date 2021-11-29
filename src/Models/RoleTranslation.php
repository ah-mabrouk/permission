<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'name',
        'description',
    ];

    ## Relations

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
