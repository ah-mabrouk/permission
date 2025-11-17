<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'display_name',
        'description',
    ];

    ## Relations

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    ## Getters & Setters

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = $this->prepareDisplayName($value);
    }

    private function prepareDisplayName($value)
    {
        $name = $value;
        $segments = collect(\explode('/', $name))->filter(function ($segment) {
            return ! Str::contains($segment, '{');
        })->flatten()->toArray();
        $modifiedSegments = [];
        for ($i = 0; $i < (\count($segments)); $i++) {
            switch (true) {
                case $i < \count($segments) - 1 :
                    $modifiedSegments[] = Str::of($segments[$i])->singular();
                    break;
                default:
                    $modifiedSegments[] = $segments[$i];
            }
        }

        return \ucwords(\str_replace('-', ' ', (\implode(' / ', $modifiedSegments))));
    }

    ## Query Scope Methods

    ## Other Methods
}
