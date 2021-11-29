<?php

namespace Mabrouk\Permission\Tests\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Mabrouk\Permission\Traits\HasPermission;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use HasPermission, Authorizable, Authenticatable;

    protected $guarded = [];

    protected $table = 'users';
}