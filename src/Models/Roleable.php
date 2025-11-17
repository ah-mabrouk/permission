<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
