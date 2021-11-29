<?php

namespace Mabrouk\RolePermissionGroup\Filters;

use Mabrouk\Filterable\Helpers\QueryFilter;

class RoleFilter extends QueryFilter
{
    public function search($search = null)
    {
        return $search ? $this->builder->where(function ($query) use ($search) {
            $query->where('name', $search)
                ->orWhereHas('translations', function ($query) use ($search) {
                    $query->where('display_name', $search);
                });
        }) : $this->builder;
    }
}
